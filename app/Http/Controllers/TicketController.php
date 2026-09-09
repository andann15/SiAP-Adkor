<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\RejectionReason;
use App\Models\Ticket;
use App\Models\User;
use App\Models\TicketPriority;
use App\Services\TicketStateMachine;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Response;
use Barryvdh\DomPDF\Facade\Pdf;

class TicketController extends Controller
{
    public function __construct(private TicketStateMachine $stateMachine)
    {
    }

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Ticket::class);

        $baseQuery = Ticket::with(['asset', 'priority']);

        if ($request->user()->can('tickets.view-all')) {
            $baseQuery->with(['creator']);
            if ($request->user()->hasRole('operator') && !$request->user()->hasRole('admin')) {
                $baseQuery->whereNotIn('status', ['rejected', 'cancelled']);
            }
        } else {
            $baseQuery->where('created_by', $request->user()->id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $baseQuery->where(function ($q) use ($search) {
                $q->where('ticket_number', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('asset', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('creator', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Calculate counts for each tab
        $counts = [
            'all'         => $baseQuery->clone()->count(),
            'waiting'     => $baseQuery->clone()->where('status', 'waiting_approval')->count(),
            'in_progress' => $baseQuery->clone()->whereIn('status', ['assigned', 'checking'])->count(),
            'completed'   => $baseQuery->clone()->whereIn('status', ['completed', 'closed'])->count(),
            'cancelled'   => $baseQuery->clone()->whereIn('status', ['cancelled', 'rejected'])->count(),
        ];

        // Apply tab filter
        $activeTab = $request->query('tab', 'all');
        $query = $baseQuery->clone();

        switch ($activeTab) {
            case 'waiting':
                $query->where('status', 'waiting_approval');
                break;
            case 'in_progress':
                $query->whereIn('status', ['assigned', 'checking']);
                break;
            case 'completed':
                $query->whereIn('status', ['completed', 'closed']);
                break;
            case 'cancelled':
                $query->whereIn('status', ['cancelled', 'rejected']);
                break;
            default:
                break;
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('ticket_priority_id', $request->priority);
        }

        $tickets   = $query->latest()->paginate(15)->withQueryString();
        $priorities = TicketPriority::where('is_active', true)->orderBy('name')->get();

        return view('tickets.index', compact('tickets', 'counts', 'activeTab', 'priorities'));
    }

    public function create(Request $request): View
    {
        $this->authorize('create', Ticket::class);

        $myAssets = Asset::where('status', '!=', 'disposed')
            ->whereNull('work_unit_id')  // hanya aset individu, bukan aset unit kerja
            ->where('current_user_id', $request->user()->id)
            ->orderBy('name')
            ->get();

        $otherAssets = Asset::where('status', '!=', 'disposed')
            ->whereNull('work_unit_id')  // hanya aset individu, bukan aset unit kerja
            ->where(function ($query) use ($request) {
                $query->where('current_user_id', '!=', $request->user()->id)
                      ->orWhereNull('current_user_id');
            })
            ->orderBy('name')
            ->get();
            
        $preselectedAssetId = $request->query('asset_id');

        return view('tickets.create', compact('myAssets', 'otherAssets', 'preselectedAssetId'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Ticket::class);

        $validated = $request->validate([
            'asset_id'    => ['required', 'exists:assets,id'],
            'description' => ['required', 'string', 'max:2000'],
            'photo'       => ['required', 'file', 'mimes:jpeg,png,jpg,webp,heic,pdf', 'max:8192'],
        ]);

        $file = $request->file('photo');
        $uploadResult = cloudinary()->uploadApi()->upload($file->getRealPath(), [
            'folder'        => 'siap/tickets/reports',
            'resource_type' => 'image',
            'format'        => $file->extension(),
        ]);
        $photoUrl = $uploadResult['secure_url'];

        $ticket = new Ticket([
            'asset_id'    => $validated['asset_id'],
            'created_by'  => $request->user()->id,
            'description' => $validated['description'],
            'photo_url'   => $photoUrl,
            'status'      => 'waiting_approval',
        ]);

        $ticket->ticket_number = $this->generateTicketNumber();
        $ticket->save();

        return redirect()->route('tickets.index')->with('success', 'Tiket berhasil dibuat, menunggu persetujuan admin.');
    }

    public function show(Ticket $ticket): View
    {
        $this->authorize('view', $ticket);

        $ticket->load(['asset', 'creator', 'assignedOperator', 'priority', 'rejectionReason', 'histories.actor']);
        $operators = User::role('operator')->get();
        $priorities = TicketPriority::where('is_active', true)->get();
        $rejectionReasons = RejectionReason::where('is_active', true)->get();

        return view('tickets.show', compact('ticket', 'operators', 'priorities', 'rejectionReasons'));
    }

    public function approve(Request $request, Ticket $ticket): RedirectResponse
    {
        $this->authorize('approve', $ticket);

        $validated = $request->validate([
            'assigned_operator_id' => ['required', 'exists:users,id'],
            'ticket_priority_id' => ['required', 'exists:ticket_priorities,id'],
        ]);

        $this->stateMachine->transitionTo($ticket, 'assigned', $request->user(), $validated);

        $operator = \App\Models\User::find($validated['assigned_operator_id']);
        if ($operator) {
            $operator->notify(new \App\Notifications\TicketAssignedNotification($ticket));
        }

        return back()->with('success', 'Tiket disetujui dan ditugaskan ke operator.');
    }

    public function reject(Request $request, Ticket $ticket): RedirectResponse
    {
        $this->authorize('reject', $ticket);

        $validated = $request->validate([
            'rejection_reason_id' => ['required', 'exists:rejection_reasons,id'],
        ]);

        $this->stateMachine->transitionTo($ticket, 'rejected', $request->user(), $validated);

        return back()->with('success', 'Tiket ditolak.');
    }

    public function startChecking(Request $request, Ticket $ticket): RedirectResponse
    {
        $this->authorize('updateStatus', $ticket);

        $this->stateMachine->transitionTo($ticket, 'checking', $request->user());

        return back()->with('success', 'Tiket mulai diperiksa.');
    }

    public function complete(Request $request, Ticket $ticket): RedirectResponse
    {
        $this->authorize('updateStatus', $ticket);

        $validated = $request->validate([
            'proof_photo' => ['required', 'file', 'mimes:jpeg,png,jpg,webp,heic,pdf', 'max:8192'],
        ]);

        $file = $request->file('proof_photo');
        $uploadResult = cloudinary()->uploadApi()->upload($file->getRealPath(), [
            'folder'        => 'siap/tickets/proofs',
            'resource_type' => 'image',
            'format'        => $file->extension(),
        ]);
        $proofUrl = $uploadResult['secure_url'];

        $this->stateMachine->transitionTo($ticket, 'completed', $request->user(), [
            'proof_photo_url' => $proofUrl,
        ]);

        return back()->with('success', 'Tiket ditandai selesai dikerjakan.');
    }

    public function close(Request $request, Ticket $ticket): RedirectResponse
    {
        $this->authorize('close', $ticket);

        $this->stateMachine->transitionTo($ticket, 'closed', $request->user());

        return back()->with('success', 'Tiket ditutup, terima kasih konfirmasinya.');
    }

    public function cancel(Request $request, Ticket $ticket): RedirectResponse
    {
        $this->authorize('cancel', $ticket);

        $this->stateMachine->transitionTo($ticket, 'cancelled', $request->user());

        return back()->with('success', 'Tiket dibatalkan.');
    }

    public function destroy(Ticket $ticket): RedirectResponse
    {
        $this->authorize('delete', $ticket);

        $ticket->delete();

        return redirect()->route('tickets.index')->with('success', 'Tiket berhasil dihapus (soft delete).');
    }

    public function restore($id): RedirectResponse
    {
        $ticket = Ticket::onlyTrashed()->findOrFail($id);
        $this->authorize('delete', $ticket);
        $ticket->restore();

        return back()->with('success', 'Tiket berhasil dikembalikan.');
    }

    public function forceDelete($id): RedirectResponse
    {
        try {
            $ticket = Ticket::onlyTrashed()->findOrFail($id);
            $this->authorize('delete', $ticket);
            $ticket->forceDelete();

            return back()->with('success', 'Tiket dihapus secara permanen.');
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() === '23000') {
                return back()->with('error', 'Tiket tidak bisa dihapus permanen karena masih terkait dengan data riwayat.');
            }
            return back()->with('error', 'Terjadi kesalahan saat menghapus tiket secara permanen.');
        }
    }

    private function generateTicketNumber(): string
    {
        $year = now()->year;

        $lastNumber = Ticket::withTrashed()
            ->where('ticket_number', 'like', "TK-{$year}-%")
            ->orderByDesc('ticket_number')
            ->value('ticket_number');

        $nextSequence = $lastNumber ? ((int) substr($lastNumber, -4)) + 1 : 1;

        return sprintf('TK-%d-%04d', $year, $nextSequence);
    }

    public function exportCsv(Request $request)
    {
        $this->authorize('viewAny', Ticket::class);
        $query = Ticket::with(['asset', 'priority', 'creator', 'assignedOperator']);
        if ($request->user()->can('tickets.view-all')) {
            if ($request->user()->hasRole('operator') && !$request->user()->hasRole('admin')) {
                $query->whereNotIn('status', ['rejected', 'cancelled']);
            }
        } else {
            $query->where('created_by', $request->user()->id);
        }
        if ($request->filled('status'))   $query->where('status', $request->status);
        if ($request->filled('priority')) $query->where('ticket_priority_id', $request->priority);
        $tickets = $query->latest()->get();
        return $this->generateCsv($tickets, "daftar_tiket_" . date('Y-m-d_H-i') . ".csv");
    }

    private function generateCsv($tickets, $filename)
    {
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = [
            'Nomor Tiket',
            'Aset',
            'Pembuat',
            'Operator',
            'Prioritas',
            'Status',
            'Dibuat Tanggal'
        ];

        $callback = function() use($tickets, $columns) {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF");
            fputcsv($file, $columns, ';');
            
            foreach ($tickets as $ticket) {
                $statusMap = [
                    'waiting_approval' => 'Menunggu Persetujuan',
                    'assigned' => 'Ditugaskan',
                    'checking' => 'Sedang Diperiksa',
                    'completed' => 'Selesai',
                    'closed' => 'Ditutup',
                    'rejected' => 'Ditolak',
                    'cancelled' => 'Dibatalkan',
                ];
                $statusName = $statusMap[$ticket->status] ?? $ticket->status;
                
                $row = [
                    $ticket->ticket_number,
                    $ticket->asset->name ?? '-',
                    $ticket->creator->name ?? '-',
                    $ticket->assignedOperator->name ?? '-',
                    $ticket->priority->name ?? '-',
                    $statusName,
                    $ticket->created_at->format('d/m/Y')
                ];
                fputcsv($file, $row, ';');
            }
            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    public function exportPdf(Request $request)
    {
        $this->authorize('viewAny', Ticket::class);
        $query = Ticket::with(['asset', 'priority', 'creator', 'assignedOperator']);
        if ($request->user()->can('tickets.view-all')) {
            if ($request->user()->hasRole('operator') && !$request->user()->hasRole('admin')) {
                $query->whereNotIn('status', ['rejected', 'cancelled']);
            }
        } else {
            $query->where('created_by', $request->user()->id);
        }
        if ($request->filled('status'))   $query->where('status', $request->status);
        if ($request->filled('priority')) $query->where('ticket_priority_id', $request->priority);
        $tickets = $query->latest()->get();

        $statusMap = [
            'waiting_approval' => 'Menunggu Persetujuan',
            'assigned'         => 'Ditugaskan',
            'checking'         => 'Sedang Diperiksa',
            'completed'        => 'Selesai',
            'closed'           => 'Ditutup',
            'rejected'         => 'Ditolak',
            'cancelled'        => 'Dibatalkan',
        ];

        $pdf = Pdf::loadView('pdf.tickets', [
            'tickets'   => $tickets,
            'statusMap' => $statusMap
        ]);
        
        return $pdf->stream("daftar_tiket_" . date('Y-m-d_H-i') . ".pdf");
    }
}
