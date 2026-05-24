<?php namespace App\Modules\RolePermission\Controllers;
use App\Kernel\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;

class RoleWebController extends Controller {
    public function index() {
        $roles = Role::with('permissions')->orderBy('name')->get();
        return view('roles.index', compact('roles'));
    }

    public function create() {
        $permissions = Permission::orderBy('name')->get()->groupBy(function($p) {
            return explode('-', $p->name)[0] ?? 'other';
        });
        return view('roles.form', compact('permissions'));
    }

    public function store(Request $r) {
        $d = $r->validate(['name' => 'required|string|max:255|unique:roles,name', 'guard_name' => 'nullable|string']);
        $role = Role::create(['name' => $d['name'], 'guard_name' => $d['guard_name'] ?? 'web']);
        if ($r->permissions) {
            $role->syncPermissions($r->permissions);
        }
        activity()->performedOn($role)->log('created role: ' . $role->name);
        return redirect()->route('roles.index')->with('success', 'Role berhasil dibuat');
    }

    public function edit(Role $role) {
        $permissions = Permission::orderBy('name')->get()->groupBy(function($p) {
            return explode('-', $p->name)[0] ?? 'other';
        });
        $rolePermissions = $role->permissions->pluck('name')->toArray();
        return view('roles.form', compact('role', 'permissions', 'rolePermissions'));
    }

    public function update(Request $r, Role $role) {
        if ($role->name === 'super-admin') {
            return back()->with('error', 'Super Admin tidak bisa diedit');
        }
        $d = $r->validate(['name' => 'required|string|max:255|unique:roles,name,' . $role->id]);
        $role->update(['name' => $d['name']]);
        $role->syncPermissions($r->permissions ?? []);
        activity()->performedOn($role)->log('updated role: ' . $role->name);
        return redirect()->route('roles.index')->with('success', 'Role berhasil diperbarui');
    }

    public function destroy(Role $role) {
        if (in_array($role->name, ['super-admin', 'admin', 'siswa', 'guru', 'orang-tua'])) {
            return back()->with('error', 'Role sistem tidak bisa dihapus');
        }
        $roleName = $role->name;
        $role->delete();
        activity()->log('deleted role: ' . $roleName);
        return redirect()->route('roles.index')->with('success', 'Role berhasil dihapus');
    }
}
