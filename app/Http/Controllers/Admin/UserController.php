<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\WorkUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Response;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::with('workUnit.department.compartment', 'roles');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhereHas('workUnit', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $users = $query->latest()->paginate(15)->withQueryString();
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $workUnits = WorkUnit::with('department.compartment')->where('is_active', true)->get();
        return view('admin.users.create', compact('workUnits'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nik'          => ['required', 'string', 'max:255', 'unique:users'],
            'name'         => ['required', 'string', 'max:255'],
            'email'        => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password'     => ['required', 'confirmed', Rules\Password::defaults()],
            'role'         => ['required', 'string', 'in:admin,operator,user'],
            'work_unit_id' => ['nullable', 'exists:work_units,id'],
        ]);

        $user = User::create([
            'nik'          => $validated['nik'],
            'name'         => $validated['name'],
            'email'        => $validated['email'],
            'password'     => Hash::make($validated['password']),
            'role'         => $validated['role'],
            'work_unit_id' => $validated['work_unit_id'],
        ]);

        $user->assignRole($validated['role']);

        return redirect()->route('admin.users.index')->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        $workUnits = WorkUnit::with('department.compartment')->where('is_active', true)->get();
        return view('admin.users.edit', compact('user', 'workUnits'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'nik'          => ['required', 'string', 'max:255', 'unique:users,nik,' . $user->id],
            'name'         => ['required', 'string', 'max:255'],
            'email'        => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password'     => ['nullable', 'confirmed', Rules\Password::defaults()],
            'role'         => ['required', 'string', 'in:admin,operator,user'],
            'work_unit_id' => ['nullable', 'exists:work_units,id'],
        ]);

        $data = [
            'nik'          => $validated['nik'],
            'name'         => $validated['name'],
            'email'        => $validated['email'],
            'role'         => $validated['role'],
            'work_unit_id' => $validated['work_unit_id'],
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($validated['password']);
        }

        $user->update($data);
        $user->syncRoles([$validated['role']]);

        return redirect()->route('admin.users.index')->with('success', 'Pengguna berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'Pengguna berhasil dihapus.');
    }

    public function exportCsv()
    {
        $users = User::with('workUnit.department.compartment')->orderBy('name')->get();
        return $this->generateCsv($users, "daftar_pengguna_" . date('Y-m-d_H-i') . ".csv");
    }

    private function generateCsv($users, $filename)
    {
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = [
            'Nama Lengkap',
            'NIK / NPK',
            'Email',
            'Role',
            'Unit Kerja'
        ];

        $callback = function() use($users, $columns) {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF");
            fputcsv($file, $columns, ';');
            
            foreach ($users as $user) {
                $roleMap = [
                    'admin' => 'Admin',
                    'operator' => 'Operator',
                    'user' => 'User',
                ];
                $roleName = $roleMap[$user->role] ?? $user->role;
                
                $row = [
                    $user->nik,
                    $user->name,
                    $user->email,
                    $roleName,
                    $user->workUnit->name ?? '-'
                ];
                fputcsv($file, $row, ';');
            }
            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    public function exportPdf()
    {
        $users = User::with('workUnit.department.compartment')->orderBy('name')->get();
        
        $roleMap = [
            'admin' => 'Admin',
            'operator' => 'Operator',
            'user' => 'User',
        ];

        $pdf = Pdf::loadView('pdf.users', [
            'users' => $users,
            'roleMap' => $roleMap
        ]);
        
        return $pdf->download("daftar_pengguna_" . date('Y-m-d_H-i') . ".pdf");
    }
}
