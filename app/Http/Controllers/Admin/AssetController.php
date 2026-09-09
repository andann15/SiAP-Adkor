<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\Brand;
use App\Models\Location;
use App\Models\User;
use App\Models\WorkUnit;
use App\Models\WorkUnitAssetStatus;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Response;
use Barryvdh\DomPDF\Facade\Pdf;

class AssetController extends Controller
{
    public function index(Request $request): View
    {
        $query = Asset::with(['category', 'brand', 'location', 'user'])->whereNull('work_unit_id');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('model', 'like', "%{$search}%")
                  ->orWhereHas('category', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('user', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('category')) {
            $query->where('asset_category_id', $request->category);
        }

        if ($request->filled('location')) {
            $query->where('location_id', $request->location);
        }

        if ($request->filled('user_id')) {
            $query->where('current_user_id', $request->user_id);
        }

        $assets     = $query->orderBy('name')->paginate(10)->withQueryString();
        $statuses   = WorkUnitAssetStatus::orderBy('order')->orderBy('name')->get()->keyBy('slug');
        $categories = AssetCategory::orderBy('name')->get();
        $locations  = Location::orderBy('name')->get();
        $users      = User::orderBy('name')->get();

        return view('admin.assets.index', compact('assets', 'statuses', 'categories', 'locations', 'users'));
    }

    public function create(): View
    {
        return view('admin.assets.create', $this->formOptions());
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateAsset($request);
        $validated['work_unit_id'] = null;

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
        $asset->delete();

        return redirect()->route('admin.assets.index')->with('success', 'Aset berhasil dihapus (soft delete).');
    }

    private function validateAsset(Request $request, ?Asset $asset = null): array
    {
        $codeRule = 'unique:assets,code' . ($asset ? ',' . $asset->id : '');

        return $request->validate([
            'code'              => ['nullable', 'string', 'max:255', $codeRule],
            'name'              => ['required', 'string', 'max:255'],
            'asset_category_id' => ['required', 'exists:asset_categories,id'],
            'brand_id'          => ['nullable', 'exists:brands,id'],
            'model'             => ['nullable', 'string', 'max:255'],
            'serial_number'     => ['nullable', 'string', 'max:255'],
            'purchase_date'     => ['nullable', 'date'],
            'warranty_end'      => ['nullable', 'date', 'after_or_equal:purchase_date'],
            'location_id'       => ['required', 'exists:locations,id'],
            'status'            => ['required', 'exists:work_unit_asset_statuses,slug'],
            'current_user_id'   => ['nullable', 'exists:users,id'],
        ]);
    }

    private function generateCode(): string
    {
        $year   = now()->format('Y');
        $prefix = 'ASET-' . $year . '-';
        $last   = Asset::withTrashed()
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
            'brands'     => Brand::where('is_active', true)->orderBy('name')->get(),
            'locations'  => Location::where('is_active', true)->orderBy('name')->get(),
            'users'      => User::orderBy('name')->get(),
            'workUnits'  => WorkUnit::with('department.compartment')->where('is_active', true)->get(),
            'statuses'   => WorkUnitAssetStatus::where('is_active', true)
                                ->orderBy('order')->orderBy('name')
                                ->pluck('name', 'slug'),
        ];
    }

    public function trash(): View
    {
        $assets   = Asset::onlyTrashed()
            ->with(['category', 'brand', 'location'])
            ->orderBy('deleted_at', 'desc')
            ->paginate(10);
        $statuses = WorkUnitAssetStatus::all()->keyBy('slug');

        return view('admin.assets.trash', compact('assets', 'statuses'));
    }

    public function restore($id): RedirectResponse
    {
        $asset = Asset::onlyTrashed()->findOrFail($id);
        $asset->restore();

        return back()->with('success', 'Aset berhasil dikembalikan.');
    }

    public function forceDelete($id): RedirectResponse
    {
        try {
            $asset = Asset::onlyTrashed()->findOrFail($id);
            $asset->forceDelete();

            return back()->with('success', 'Aset dihapus secara permanen.');
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() === '23000') {
                return back()->with('error', 'Aset tidak bisa dihapus permanen karena masih terkait dengan riwayat tiket atau data lainnya.');
            }
            return back()->with('error', 'Terjadi kesalahan saat menghapus aset secara permanen.');
        }
    }

    public function exportCsv(Request $request)
    {
        $query = Asset::with(['category', 'brand', 'location', 'user'])->whereNull('work_unit_id');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")->orWhere('code', 'like', "%{$s}%");
            });
        }
        if ($request->filled('status'))   $query->where('status', $request->status);
        if ($request->filled('category')) $query->where('asset_category_id', $request->category);
        if ($request->filled('location')) $query->where('location_id', $request->location);

        $assets = $query->orderBy('name')->get();

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=daftar_aset_" . date('Y-m-d_H-i') . ".csv",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['Kode', 'Nama', 'Kategori', 'Merek', 'Lokasi', 'Status', 'Pengguna'];

        $callback = function() use($assets, $columns) {
            $statuses = WorkUnitAssetStatus::all()->keyBy('slug');
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF");
            fputcsv($file, $columns, ';');
            foreach ($assets as $asset) {
                $statusName = $statuses[$asset->status]->name ?? $asset->status;
                fputcsv($file, [
                    $asset->code,
                    $asset->name,
                    $asset->category->name ?? '-',
                    $asset->brand->name ?? '-',
                    $asset->location->name ?? '-',
                    $statusName,
                    $asset->user->name ?? '-',
                ], ';');
            }
            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    public function exportPdf(Request $request)
    {
        $query = Asset::with(['category', 'brand', 'location', 'user'])->whereNull('work_unit_id');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")->orWhere('code', 'like', "%{$s}%");
            });
        }
        if ($request->filled('status'))   $query->where('status', $request->status);
        if ($request->filled('category')) $query->where('asset_category_id', $request->category);
        if ($request->filled('location')) $query->where('location_id', $request->location);

        $assets   = $query->orderBy('name')->get();
        $statuses = WorkUnitAssetStatus::all()->keyBy('slug');

        $pdf = Pdf::loadView('pdf.assets', compact('assets', 'statuses'));

        return $pdf->stream("daftar_aset_" . date('Y-m-d_H-i') . ".pdf");
    }
}
