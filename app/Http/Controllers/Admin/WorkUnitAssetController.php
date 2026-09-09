<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\Brand;
use App\Models\Location;
use App\Models\WorkUnit;
use App\Models\WorkUnitAssetStatus;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Response;
use Barryvdh\DomPDF\Facade\Pdf;

class WorkUnitAssetController extends Controller
{
    public function index(Request $request): View
    {
        $query = Asset::with(['workUnit.department.compartment', 'location'])
            ->whereNotNull('work_unit_id');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%")
                  ->orWhereHas('workUnit', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('work_unit')) {
            $query->where('work_unit_id', $request->work_unit);
        }

        if ($request->filled('location')) {
            $query->where('location_id', $request->location);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $assets   = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();
        $statuses  = WorkUnitAssetStatus::all()->keyBy('slug');
        $workUnits = \App\Models\WorkUnit::with('department.compartment')
                        ->where('is_active', true)
                        ->get()
                        ->sort(function ($a, $b) {
                            if ($c = strcmp($a->department?->compartment?->name ?? '', $b->department?->compartment?->name ?? '')) return $c;
                            if ($c = strcmp($a->department?->name ?? '', $b->department?->name ?? '')) return $c;
                            return strcmp($a->name ?? '', $b->name ?? '');
                        })
                        ->values();
        $locations = Location::orderBy('name')->get();

        return view('admin.work-unit-assets.index', compact('assets', 'statuses', 'workUnits', 'locations'));
    }

    public function create(): View
    {
        return view('admin.work-unit-assets.create', $this->formOptions());
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateAsset($request);
        $validated['current_user_id'] = null;

        if (empty($validated['code'])) {
            $validated['code'] = $this->generateCode();
        }

        Asset::create($validated);

        return redirect()->route('admin.work-unit-assets.index')->with('success', 'Aset Unit Kerja berhasil ditambahkan.');
    }

    public function edit(Asset $workUnitAsset): View
    {
        return view('admin.work-unit-assets.edit', array_merge(
            ['asset' => $workUnitAsset],
            $this->formOptions()
        ));
    }

    public function update(Request $request, Asset $workUnitAsset): RedirectResponse
    {
        $validated = $this->validateAsset($request, $workUnitAsset);
        $validated['current_user_id'] = null;

        $workUnitAsset->update($validated);

        return redirect()->route('admin.work-unit-assets.index')->with('success', 'Aset Unit Kerja berhasil diperbarui.');
    }

    public function destroy(Asset $workUnitAsset): RedirectResponse
    {
        $workUnitAsset->delete();
        return redirect()->route('admin.work-unit-assets.index')->with('success', 'Aset Unit Kerja berhasil dihapus.');
    }

    private function validateAsset(Request $request, ?Asset $asset = null): array
    {
        $codeRule = 'unique:assets,code' . ($asset ? ',' . $asset->id : '');

        return $request->validate([
            'code' => ['nullable', 'string', 'max:255', $codeRule],
            'name' => ['required', 'string', 'max:255'],
            'asset_category_id' => ['required', 'exists:asset_categories,id'],
            'brand_id' => ['nullable', 'exists:brands,id'],
            'model' => ['nullable', 'in:Limbah,Non Limbah'],
            'serial_number' => ['nullable', 'string', 'max:255'],
            'purchase_date' => ['nullable', 'date'],
            'warranty_end' => ['nullable', 'date', 'after_or_equal:purchase_date'],
            'location_id' => ['required', 'exists:locations,id'],
            'status' => ['required', 'exists:work_unit_asset_statuses,slug'],
            'work_unit_id' => ['required', 'exists:work_units,id'],
            'notes' => ['nullable', 'string'],
        ]);
    }

    private function formOptions(): array
    {
        return [
            'categories' => AssetCategory::where('is_active', true)->orderBy('name')->get(),
            'brands'     => Brand::where('is_active', true)->orderBy('name')->get(),
            'locations'  => Location::where('is_active', true)->orderBy('name')->get(),
            'workUnits'  => WorkUnit::with('department.compartment')
                                ->where('is_active', true)
                                ->get()
                                ->sort(function ($a, $b) {
                                    if ($c = strcmp($a->department?->compartment?->name ?? '', $b->department?->compartment?->name ?? '')) return $c;
                                    if ($c = strcmp($a->department?->name ?? '', $b->department?->name ?? '')) return $c;
                                    return strcmp($a->name ?? '', $b->name ?? '');
                                })
                                ->values(),
            'statuses'   => WorkUnitAssetStatus::where('is_active', true)
                                ->orderBy('order')->orderBy('name')
                                ->pluck('name', 'slug'),
        ];
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

    public function exportCsv(Request $request)
    {
        $query = Asset::with(['workUnit.department.compartment', 'location'])
            ->whereNotNull('work_unit_id');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
            });
        }
        if ($request->filled('work_unit')) $query->where('work_unit_id', $request->work_unit);
        if ($request->filled('location'))  $query->where('location_id', $request->location);
        if ($request->filled('status'))    $query->where('status', $request->status);

        $assets = $query->orderBy('created_at', 'desc')->get();

        return $this->generateCsv($assets, "monitoring_aset_unit_kerja_" . date('Y-m-d_H-i') . ".csv");
    }

    public function exportSingle(Asset $workUnitAsset)
    {
        $workUnitAsset->load(['workUnit.department.compartment', 'location']);
        return $this->generateCsv([$workUnitAsset], "aset_" . $workUnitAsset->code . "_" . date('Y-m-d_H-i') . ".csv");
    }

    public function exportSinglePdf(Asset $workUnitAsset)
    {
        $workUnitAsset->load(['workUnit.department.compartment', 'location']);
        $statuses = WorkUnitAssetStatus::all()->keyBy('slug');

        $pdf = Pdf::loadView('pdf.work-unit-assets', [
            'assets'   => collect([$workUnitAsset]),
            'statuses' => $statuses,
        ]);

        return $pdf->download('aset_' . $workUnitAsset->code . '_' . date('Y-m-d_H-i') . '.pdf');
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
            'Nomor BAPBS/BAPBSAT',
            'Unit Kerja',
            'Lokasi',
            'Penugasan Unit Kerja',
            'Status',
            'Keterangan'
        ];

        $callback = function() use($assets, $columns) {
            $statuses = WorkUnitAssetStatus::all()->keyBy('slug');
            
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF"); // BOM for excel
            fputcsv($file, $columns, ';');
            fclose($file);
            
            $file = fopen('php://output', 'a');
            foreach ($assets as $asset) {
                $parts = array_filter([
                    $asset->workUnit?->department?->compartment?->name,
                    $asset->workUnit?->department?->name,
                    $asset->workUnit?->name,
                ]);
                $workUnitName = $asset->workUnit ? implode(' › ', $parts) : '-';
                $statusName = isset($statuses[$asset->status]) ? $statuses[$asset->status]->name : $asset->status;
                
                $row = [
                    '="' . ($asset->code ?? '') . '"', // Force Excel to treat as string to keep leading zeros
                    $asset->name ?? '-',         // Unit Kerja = nama aset
                    $asset->location->name ?? '-',
                    $workUnitName,               // Penugasan Unit Kerja
                    $statusName,
                    $asset->notes ?? '-'
                ];

                fputcsv($file, $row, ';');
            }
            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    public function exportPdf(Request $request)
    {
        $query = Asset::with(['workUnit.department.compartment', 'location'])
            ->whereNotNull('work_unit_id');

        if ($request->filled('work_unit')) $query->where('work_unit_id', $request->work_unit);
        if ($request->filled('location'))  $query->where('location_id', $request->location);
        if ($request->filled('status'))    $query->where('status', $request->status);

        $assets   = $query->orderBy('created_at', 'desc')->get();
        $statuses = WorkUnitAssetStatus::all()->keyBy('slug');

        $pdf = Pdf::loadView('pdf.work-unit-assets', [
            'assets'   => $assets,
            'statuses' => $statuses
        ]);
        
        return $pdf->stream("monitoring_aset_unit_kerja_" . date('Y-m-d_H-i') . ".pdf");
    }

    public function trash(): View
    {
        $assets = Asset::onlyTrashed()
            ->with(['workUnit.department.compartment', 'location'])
            ->whereNotNull('work_unit_id')
            ->orderBy('deleted_at', 'desc')
            ->paginate(15);
        $statuses = WorkUnitAssetStatus::all()->keyBy('slug');

        return view('admin.work-unit-assets.trash', compact('assets', 'statuses'));
    }

    public function restore($id): RedirectResponse
    {
        $asset = Asset::onlyTrashed()->whereNotNull('work_unit_id')->findOrFail($id);
        $asset->restore();

        return back()->with('success', 'Aset Unit Kerja berhasil dikembalikan.');
    }

    public function forceDelete($id): RedirectResponse
    {
        $asset = Asset::onlyTrashed()->whereNotNull('work_unit_id')->findOrFail($id);
        $asset->forceDelete();

        return back()->with('success', 'Aset Unit Kerja dihapus secara permanen.');
    }
}
