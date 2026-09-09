<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Ticket;
use Illuminate\View\View;

class UserDashboardController extends Controller
{
    public const STATUS_LABELS = [
        'waiting_approval' => 'Menunggu Persetujuan',
        'assigned' => 'Ditugaskan',
        'checking' => 'Sedang Dikerjakan',
        'completed' => 'Selesai',
        'closed' => 'Ditutup',
        'rejected' => 'Ditolak',
        'cancelled' => 'Dibatalkan',
    ];

    public function index(): View
    {
        $userId = auth()->id();

        $statusCounts = Ticket::where('created_by', $userId)
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $summary = collect(self::STATUS_LABELS)->map(fn ($label, $status) => [
            'status' => $status,
            'label' => $label,
            'count' => $statusCounts->get($status, 0),
        ])->values();

        $tickets = Ticket::with(['asset', 'priority'])
            ->where('created_by', $userId)
            ->latest()
            ->paginate(6);

        $myAssets = Asset::with(['category', 'brand', 'location'])
            ->where('current_user_id', $userId)
            ->where('status', '!=', 'disposed')
            ->orderBy('name')
            ->get();
            
        $availableAssets = Asset::where('status', 'active')
            ->whereNull('current_user_id')
            ->whereNull('work_unit_id')
            ->where('code', 'like', 'ADKOR-%')
            ->orderBy('name')
            ->get();

        return view('user.dashboard', compact('summary', 'tickets', 'myAssets', 'availableAssets'));
    }
}

