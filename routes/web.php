<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\WorkUnitController;
use App\Http\Controllers\Admin\AssetCategoryController;
use App\Http\Controllers\Admin\TicketPriorityController;
use App\Http\Controllers\Admin\RejectionReasonController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\LocationController;
use App\Http\Controllers\Admin\AssetController;
use App\Http\Controllers\UserDashboardController;
use App\Http\Controllers\OperatorDashboardController;

Route::get('/', function () {
    return redirect()->route('login');
});

// TEMPORARY DEBUG ROUTE - remove after diagnosis
Route::get('/debug-admin-xk29zq', function () {
    try {
        $stats = [
            'total' => \App\Models\Ticket::count(),
            'waiting' => \App\Models\Ticket::where('status', 'waiting_approval')->count(),
            'in_progress' => \App\Models\Ticket::whereIn('status', ['assigned', 'checking'])->count(),
            'sla_breached' => \App\Models\Ticket::where('sla_breached', true)->count(),
        ];
        $tickets = \App\Models\Ticket::with(['asset', 'creator', 'priority'])->latest()->paginate(6);

        // Check user roles
        $user = auth()->user();
        $roles = $user ? $user->getRoleNames() : 'not logged in';
        $permissions = $user ? $user->getAllPermissions()->pluck('name') : [];

        return response()->json([
            'status' => 'ok',
            'stats' => $stats,
            'ticket_count' => $tickets->count(),
            'user' => $user ? $user->email : null,
            'roles' => $roles,
            'permissions' => $permissions,
            'bootstrap_path' => app()->bootstrapPath(),
            'storage_path' => storage_path(),
        ]);
    } catch (\Throwable $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => collect(explode("\n", $e->getTraceAsString()))->take(10)->values(),
        ], 500);
    }
})->middleware('auth');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth', 'verified', \Spatie\Permission\Middleware\PermissionMiddleware::class . ':divisions.manage'])
    ->prefix('admin/work-units')
    ->name('admin.work-units.')
    ->group(function () {
        Route::get('/', [WorkUnitController::class, 'index'])->name('index');
        Route::get('/create', [WorkUnitController::class, 'create'])->name('create');
        Route::post('/', [WorkUnitController::class, 'store'])->name('store');
        Route::get('/{workUnit}/edit', [WorkUnitController::class, 'edit'])->name('edit');
        Route::put('/{workUnit}', [WorkUnitController::class, 'update'])->name('update');
        Route::patch('/{workUnit}/toggle', [WorkUnitController::class, 'toggleActive'])->name('toggle');
        Route::delete('/{workUnit}', [WorkUnitController::class, 'destroy'])->name('destroy');
    });

// Redirect lama /admin/divisions ke /admin/work-units agar tidak 404
Route::redirect('/admin/divisions', '/admin/work-units')->middleware(['auth']);

Route::middleware(['auth', 'verified', \Spatie\Permission\Middleware\PermissionMiddleware::class . ':divisions.manage'])
    ->prefix('admin/work-unit-assets')
    ->name('admin.work-unit-assets.')
    ->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\WorkUnitAssetController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\WorkUnitAssetController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\WorkUnitAssetController::class, 'store'])->name('store');
        Route::get('/export', [\App\Http\Controllers\Admin\WorkUnitAssetController::class, 'exportCsv'])->name('export');
        Route::get('/export-pdf', [\App\Http\Controllers\Admin\WorkUnitAssetController::class, 'exportPdf'])->name('export-pdf');
        Route::get('/trash', [\App\Http\Controllers\Admin\WorkUnitAssetController::class, 'trash'])->name('trash');
        Route::post('/{id}/restore', [\App\Http\Controllers\Admin\WorkUnitAssetController::class, 'restore'])->name('restore');
        Route::delete('/{id}/force-delete', [\App\Http\Controllers\Admin\WorkUnitAssetController::class, 'forceDelete'])->name('force-delete');
        Route::get('/{workUnitAsset}/edit', [\App\Http\Controllers\Admin\WorkUnitAssetController::class, 'edit'])->name('edit');
        Route::put('/{workUnitAsset}', [\App\Http\Controllers\Admin\WorkUnitAssetController::class, 'update'])->name('update');
        Route::delete('/{workUnitAsset}', [\App\Http\Controllers\Admin\WorkUnitAssetController::class, 'destroy'])->name('destroy');
        Route::get('/{workUnitAsset}/export-single', [\App\Http\Controllers\Admin\WorkUnitAssetController::class, 'exportSingle'])->name('export-single');
        Route::get('/{workUnitAsset}/export-single-pdf', [\App\Http\Controllers\Admin\WorkUnitAssetController::class, 'exportSinglePdf'])->name('export-single-pdf');
    });

Route::middleware(['auth', 'verified', \Spatie\Permission\Middleware\PermissionMiddleware::class . ':divisions.manage'])
    ->prefix('admin/work-unit-asset-statuses')
    ->name('admin.work-unit-asset-statuses.')
    ->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\WorkUnitAssetStatusController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\WorkUnitAssetStatusController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\WorkUnitAssetStatusController::class, 'store'])->name('store');
        Route::get('/{workUnitAssetStatus}/edit', [\App\Http\Controllers\Admin\WorkUnitAssetStatusController::class, 'edit'])->name('edit');
        Route::put('/{workUnitAssetStatus}', [\App\Http\Controllers\Admin\WorkUnitAssetStatusController::class, 'update'])->name('update');
        Route::delete('/{workUnitAssetStatus}', [\App\Http\Controllers\Admin\WorkUnitAssetStatusController::class, 'destroy'])->name('destroy');
        Route::patch('/{workUnitAssetStatus}/toggle', [\App\Http\Controllers\Admin\WorkUnitAssetStatusController::class, 'toggle'])->name('toggle');
    });
Route::middleware(['auth', 'verified', \Spatie\Permission\Middleware\PermissionMiddleware::class . ':divisions.manage'])
    ->prefix('admin/brands')
    ->name('admin.brands.')
    ->group(function () {
        Route::get('/', [BrandController::class, 'index'])->name('index');
        Route::get('/create', [BrandController::class, 'create'])->name('create');
        Route::post('/', [BrandController::class, 'store'])->name('store');
        Route::get('/{brand}/edit', [BrandController::class, 'edit'])->name('edit');
        Route::put('/{brand}', [BrandController::class, 'update'])->name('update');
        Route::patch('/{brand}/toggle', [BrandController::class, 'toggleActive'])->name('toggle');
    });

Route::middleware(['auth', 'verified', \Spatie\Permission\Middleware\PermissionMiddleware::class . ':divisions.manage'])
    ->prefix('admin/locations')
    ->name('admin.locations.')
    ->group(function () {
        Route::get('/', [LocationController::class, 'index'])->name('index');
        Route::get('/create', [LocationController::class, 'create'])->name('create');
        Route::post('/', [LocationController::class, 'store'])->name('store');
        Route::get('/{location}/edit', [LocationController::class, 'edit'])->name('edit');
        Route::put('/{location}', [LocationController::class, 'update'])->name('update');
        Route::patch('/{location}/toggle', [LocationController::class, 'toggleActive'])->name('toggle');
    });
Route::middleware(['auth', 'verified', \Spatie\Permission\Middleware\PermissionMiddleware::class . ':divisions.manage'])
    ->prefix('admin/asset-categories')
    ->name('admin.asset-categories.')
    ->group(function () {
        Route::get('/', [AssetCategoryController::class, 'index'])->name('index');
        Route::get('/create', [AssetCategoryController::class, 'create'])->name('create');
        Route::post('/', [AssetCategoryController::class, 'store'])->name('store');
        Route::get('/{assetCategory}/edit', [AssetCategoryController::class, 'edit'])->name('edit');
        Route::put('/{assetCategory}', [AssetCategoryController::class, 'update'])->name('update');
        Route::patch('/{assetCategory}/toggle', [AssetCategoryController::class, 'toggleActive'])->name('toggle');
    });

Route::middleware(['auth', 'verified', \Spatie\Permission\Middleware\PermissionMiddleware::class . ':divisions.manage'])
    ->prefix('admin/ticket-priorities')
    ->name('admin.ticket-priorities.')
    ->group(function () {
        Route::get('/', [TicketPriorityController::class, 'index'])->name('index');
        Route::get('/create', [TicketPriorityController::class, 'create'])->name('create');
        Route::post('/', [TicketPriorityController::class, 'store'])->name('store');
        Route::get('/{ticketPriority}/edit', [TicketPriorityController::class, 'edit'])->name('edit');
        Route::put('/{ticketPriority}', [TicketPriorityController::class, 'update'])->name('update');
        Route::patch('/{ticketPriority}/toggle', [TicketPriorityController::class, 'toggleActive'])->name('toggle');
    });

Route::middleware(['auth', 'verified', \Spatie\Permission\Middleware\PermissionMiddleware::class . ':divisions.manage'])
    ->prefix('admin/rejection-reasons')
    ->name('admin.rejection-reasons.')
    ->group(function () {
        Route::get('/', [RejectionReasonController::class, 'index'])->name('index');
        Route::get('/create', [RejectionReasonController::class, 'create'])->name('create');
        Route::post('/', [RejectionReasonController::class, 'store'])->name('store');
        Route::get('/{rejectionReason}/edit', [RejectionReasonController::class, 'edit'])->name('edit');
        Route::put('/{rejectionReason}', [RejectionReasonController::class, 'update'])->name('update');
        Route::patch('/{rejectionReason}/toggle', [RejectionReasonController::class, 'toggleActive'])->name('toggle');
    });

Route::middleware(['auth', 'verified', \Spatie\Permission\Middleware\RoleMiddleware::class . ':admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/users/export-csv', [\App\Http\Controllers\Admin\UserController::class, 'exportCsv'])->name('users.export-csv');
        Route::get('/users/export-pdf', [\App\Http\Controllers\Admin\UserController::class, 'exportPdf'])->name('users.export-pdf');
        Route::resource('users', \App\Http\Controllers\Admin\UserController::class)->except(['show']);
    });

Route::middleware(['auth'])->prefix('tickets')->name('tickets.')->group(function () {
    Route::get('/', [TicketController::class, 'index'])->name('index');
    Route::get('/export-csv', [TicketController::class, 'exportCsv'])->name('export-csv');
    Route::get('/export-pdf', [TicketController::class, 'exportPdf'])->name('export-pdf');
    Route::get('/create', [TicketController::class, 'create'])->name('create');
    Route::post('/', [TicketController::class, 'store'])->name('store');
    Route::post('/{id}/restore', [TicketController::class, 'restore'])
        ->middleware([\Spatie\Permission\Middleware\RoleMiddleware::class . ':admin'])
        ->name('restore');
    Route::delete('/{id}/force-delete', [TicketController::class, 'forceDelete'])
        ->middleware([\Spatie\Permission\Middleware\RoleMiddleware::class . ':admin'])
        ->name('force-delete');
    Route::get('/{ticket}', [TicketController::class, 'show'])->name('show');
    Route::patch('/{ticket}/approve', [TicketController::class, 'approve'])->name('approve');
    Route::patch('/{ticket}/reject', [TicketController::class, 'reject'])->name('reject');
    Route::patch('/{ticket}/start-checking', [TicketController::class, 'startChecking'])->name('start-checking');
    Route::patch('/{ticket}/complete', [TicketController::class, 'complete'])->name('complete');
    Route::patch('/{ticket}/close', [TicketController::class, 'close'])->name('close');
    Route::patch('/{ticket}/cancel', [TicketController::class, 'cancel'])->name('cancel');
    Route::delete('/{ticket}', [TicketController::class, 'destroy'])->name('destroy');
    });

Route::middleware(['auth', 'verified', \Spatie\Permission\Middleware\RoleMiddleware::class . ':admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/recovery', [\App\Http\Controllers\Admin\RecoveryController::class, 'index'])->name('recovery.index');
    });

Route::middleware(['auth', 'verified', \Spatie\Permission\Middleware\RoleMiddleware::class . ':operator'])
    ->prefix('operator')
    ->name('operator.')
    ->group(function () {
        Route::get('/dashboard', [OperatorDashboardController::class, 'index'])->name('dashboard');
    });

Route::middleware(['auth', 'verified', \Spatie\Permission\Middleware\RoleMiddleware::class . ':user'])
    ->prefix('user')
    ->name('user.')
    ->group(function () {
        Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('dashboard');
    });

Route::middleware('auth')->group(function () {
    Route::get('/my-assets', [\App\Http\Controllers\MyAssetController::class, 'index'])->name('my-assets');
    Route::post('/my-assets/claim', [\App\Http\Controllers\MyAssetController::class, 'claim'])->name('my-assets.claim');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/admin/assets', [AssetController::class, 'index'])
    ->middleware(['auth', 'verified', \Spatie\Permission\Middleware\PermissionMiddleware::class . ':assets.view'])
    ->name('admin.assets.index');

Route::get('/admin/assets/export-csv', [AssetController::class, 'exportCsv'])
    ->middleware(['auth', 'verified', \Spatie\Permission\Middleware\PermissionMiddleware::class . ':assets.view'])
    ->name('admin.assets.export-csv');

Route::get('/admin/assets/export-pdf', [AssetController::class, 'exportPdf'])
    ->middleware(['auth', 'verified', \Spatie\Permission\Middleware\PermissionMiddleware::class . ':assets.view'])
    ->name('admin.assets.export-pdf');

Route::get('/admin/assets/trash', [AssetController::class, 'trash'])
    ->middleware(['auth', 'verified', \Spatie\Permission\Middleware\RoleMiddleware::class . ':admin'])
    ->name('admin.assets.trash');

Route::post('/admin/assets/{id}/restore', [AssetController::class, 'restore'])
    ->middleware(['auth', 'verified', \Spatie\Permission\Middleware\RoleMiddleware::class . ':admin'])
    ->name('admin.assets.restore');

Route::delete('/admin/assets/{id}/force-delete', [AssetController::class, 'forceDelete'])
    ->middleware(['auth', 'verified', \Spatie\Permission\Middleware\RoleMiddleware::class . ':admin'])
    ->name('admin.assets.force-delete');

Route::get('/admin/assets/create', [AssetController::class, 'create'])
    ->middleware(['auth', 'verified', \Spatie\Permission\Middleware\PermissionMiddleware::class . ':assets.create'])
    ->name('admin.assets.create');

Route::post('/admin/assets', [AssetController::class, 'store'])
    ->middleware(['auth', 'verified', \Spatie\Permission\Middleware\PermissionMiddleware::class . ':assets.create'])
    ->name('admin.assets.store');

Route::get('/admin/assets/{asset}/edit', [AssetController::class, 'edit'])
    ->middleware(['auth', 'verified', \Spatie\Permission\Middleware\PermissionMiddleware::class . ':assets.edit'])
    ->name('admin.assets.edit');

Route::put('/admin/assets/{asset}', [AssetController::class, 'update'])
    ->middleware(['auth', 'verified', \Spatie\Permission\Middleware\PermissionMiddleware::class . ':assets.edit'])
    ->name('admin.assets.update');

Route::delete('/admin/assets/{asset}', [AssetController::class, 'destroy'])
    ->middleware(['auth', 'verified', \Spatie\Permission\Middleware\RoleMiddleware::class . ':admin'])
    ->name('admin.assets.destroy');

require __DIR__.'/auth.php';