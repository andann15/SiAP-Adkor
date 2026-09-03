<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\Brand;
use App\Models\Location;
use App\Models\User;
use App\Models\WorkUnit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Response;
use Barryvdh\DomPDF\Facade\Pdf;

class AssetController extends Controller
{
    public const STATUSES = [
        'active' => 'Aktif Digunakan',
        'in_storage' => 'Di Gudang',
        'maintenance' => 'Dalam Perbaikan',
        'damaged' => 'Rusak',
        'disposed' => 'Dihapuskan',
    ];

    public function index(Request $request): View
    {
        $query = Asset::with(['category', 'brand', 'location'])->whereNull('work_unit_id');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhereHas('category', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $assets = $query->orderBy('name')->paginate(10)->withQueryString();

        return view('admin.assets.index', [
            'assets' => $assets,
            'statuses' => self::STATUSES,
        ]);
    }

    public function create(): View
    {
        return view('admin.assets.create', $this->formOptions());
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateAsset($request);
        $validated['work_unit_id'] = null;

        // Auto-generate kode aset jika tidak diisi
        if (empty($validated['code'])) {
            $validated['code'] = $this->generateCode();
        }

        Asset::create($validated);

        return redirect()->route('admin.assets.index')->with('success', 'Aset berhasil ditambahkan.');
    }

    public function edit(Asset $asset): View
    {
        return view('admin.assets.edit', array_merge(
            ['asset' => $asset],
            $this->formOptions()
        ));
    }

    public function update(Request $request, Asset $asset): RedirectResponse
    {
        $validated = $this->validateAsset($request, $asset);
        $validated['work_unit_id'] = null;

        $asset->update($validated);

        return redirect()->route('admin.assets.index')->with('success', 'Aset berhasil diperbarui.');
    }

    public function destroy(Asset $asset): RedirectResponse
    {
        $asset->delete(); // Soft delete because of the trait

        return redirect()->route('admin.assets.index')->with('success', 'Aset berhasil dihapus (soft delete).');
    }

    private function validateAsset(Request $request, ?Asset $asset = null): array
    {
        $codeRule = 'unique:assets,code' . ($asset ? ',' . $asset->id : '');

        return $request->validate([
            'code'             => ['nullable', 'string', 'max:255', $codeRule],
            'name'             => ['required', 'string', 'max:255'],
            'asset_category_id'=> ['required', 'exists:asset_categories,id'],
            'brand_id'         => ['nullable', 'exists:brands,id'],
            'model'            => ['nullable', 'string', 'max:255'],
            'serial_number'    => ['nullable', 'string', 'max:255'],
            'purchase_date'    => ['nullable', 'date'],
            'warranty_end'     => ['nullable', 'date', 'after_or_equal:purchase_date'],
            'location_id'      => ['required', 'exists:locations,id'],
            'status'           => ['required', 'in:' . implode(',', array_keys(self::STATUSES))],
            'current_user_id'  => ['nullable', 'exists:users,id'],
        ]);
    }

    private function generateCode(): string
    {
        $year = now()->format('Y');
        $prefix = 'ASET-' . $year . '-';
        // Cari nomor urut terakhir untuk tahun ini, termasuk yang sudah dihapus (soft delete)
        $last = Asset::withTrashed()
            ->where('code', 'like', $prefix . '%')
            ->orderByRaw('CAST(SUBSTRING(code, ' . (strlen($prefix) + 1) . ') AS UNSIGNED) DESC')
            ->value('code');
        $next = $last ? ((int) substr($last, strlen($prefix))) + 1 : 1;
        return $prefix . str_pad($next, 4, '0', STR_PAD_LEFT);
    }

    private function formOptions(): array
    {
        return [
            'categories' => AssetCategory::where('is_active', true)->orderBy('name')->get(),
            'brands' => Brand::where('is_active', true)->orderBy('name')->get(),
            'locations' => Location::where('is_active', true)->orderBy('name')->get(),
            'users' => User::orderBy('name')->get(),
            'workUnits' => WorkUnit::with('department.compartment')->where('is_active', true)->get(),
            'statuses' => self::STATUSES,
        ];
    }

    public function trash(): View
    {
        $assets = Asset::onlyTrashed()
            ->with(['category', 'brand', 'location'])
            ->orderBy('deleted_at', 'desc')
            ->paginate(10);

        return view('admin.assets.trash', [
            'assets' => $assets,
            'statuses' => self::STATUSES,
        ]);
    }

    public function restore($id): RedirectResponse
    {
        $asset = Asset::onlyTrashed()->findOrFail($id);
        $asset->restore();

        return back()->with('success', 'Aset berhasil dikembalikan.');
    }

    public function forceDelete($id): RedirectResponse
    {
        $asset = Asset::onlyTrashed()->findOrFail($id);
        $asset->forceDelete();

        return back()->with('success', 'Aset dihapus secara permanen.');
    }

    public function exportCsv()
    {
        $assets = Asset::with(['category', 'brand', 'location'])->orderBy('name')->get();
        return $this->generateCsv($assets, "daftar_aset_" . date('Y-m-d_H-i') . ".csv");
    }

    private function generateCsv($assets, $filename)
    {
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = [
            'Kode Aset',
            'Nama Aset',
            'Kategori',
            'Merek',
            'Lokasi',
            'Status'
        ];

        $callback = function() use($assets, $columns) {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF");
            fputcsv($file, $columns, ';');
            
            foreach ($assets as $asset) {
                $statusName = self::STATUSES[$asset->status] ?? $asset->status;
                $row = [
                    $asset->code,
                    $asset->name,
                    $asset->category->name ?? '-',
                    $asset->brand->name ?? '-',
                    $asset->location->name ?? '-',
                    $statusName
                ];
                fputcsv($file, $row, ';');
            }
            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    public function exportPdf()
    {
        $assets = Asset::with(['category', 'brand', 'location'])->whereNull('work_unit_id')->orderBy('name')->get();
        $pdf = Pdf::loadView('pdf.assets', [
            'assets' => $assets,
            'statuses' => self::STATUSES
        ]);
        
        return $pdf->download("daftar_aset_" . date('Y-m-d_H-i') . ".pdf");
    }
}