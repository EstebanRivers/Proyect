<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    /**
     * Mostrar lista de roles
     */
    public function index()
    {
        $roles = Role::withCount('users')->orderBy('name')->get();
        return view('admin.roles.index', compact('roles'));
    }

    /**
     * Mostrar formulario para crear rol
     */
    public function create()
    {
        return view('admin.roles.create');
    }

    /**
     * Crear nuevo rol
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:50|unique:roles,name|alpha_dash',
            'display_name' => 'required|string|max:100',
            'description' => 'nullable|string|max:255',
        ], [
            'name.required' => 'El nombre del rol es obligatorio.',
            'name.unique' => 'Ya existe un rol con este nombre.',
            'name.alpha_dash' => 'El nombre solo puede contener letras, números, guiones y guiones bajos.',
            'display_name.required' => 'El nombre para mostrar es obligatorio.',
        ]);

        try {
            Role::create([
                'name' => strtolower($request->name),
                'display_name' => $request->display_name,
                'description' => $request->description,
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Rol creado exitosamente.',
                    'redirect' => route('admin.roles.index')
                ]);
            }

            return redirect()->route('admin.roles.index')
                           ->with('success', 'Rol creado exitosamente.');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al crear el rol: ' . $e->getMessage()
                ], 500);
            }

            return back()->withInput()
                        ->withErrors(['error' => 'Error al crear el rol.']);
        }
    }

    /**
     * Mostrar formulario para editar rol
     */
    public function edit(Role $role)
    {
        return view('admin.roles.edit', compact('role'));
    }

    /**
     * Actualizar rol
     */
    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|string|max:50|alpha_dash|unique:roles,name,' . $role->id,
            'display_name' => 'required|string|max:100',
            'description' => 'nullable|string|max:255',
        ], [
            'name.required' => 'El nombre del rol es obligatorio.',
            'name.unique' => 'Ya existe un rol con este nombre.',
            'name.alpha_dash' => 'El nombre solo puede contener letras, números, guiones y guiones bajos.',
            'display_name.required' => 'El nombre para mostrar es obligatorio.',
        ]);

        try {
            $role->update([
                'name' => strtolower($request->name),
                'display_name' => $request->display_name,
                'description' => $request->description,
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Rol actualizado exitosamente.',
                    'redirect' => route('admin.roles.index')
                ]);
            }

            return redirect()->route('admin.roles.index')
                           ->with('success', 'Rol actualizado exitosamente.');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al actualizar el rol: ' . $e->getMessage()
                ], 500);
            }

            return back()->withInput()
                        ->withErrors(['error' => 'Error al actualizar el rol.']);
        }
    }

    /**
     * Eliminar rol
     */
    public function destroy(Role $role)
    {
        try {
            // Verificar si el rol tiene usuarios asignados
            if ($role->users()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar el rol porque tiene usuarios asignados.'
                ], 400);
            }

            $role->delete();

            return response()->json([
                'success' => true,
                'message' => 'Rol eliminado exitosamente.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el rol: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mostrar usuarios con sus roles
     */
    public function users()
    {
        $users = User::with('roles')->orderBy('name')->get();
        $roles = Role::orderBy('display_name')->get();
        
        return view('admin.roles.users', compact('users', 'roles'));
    }

    /**
     * Asignar rol a usuario
     */
    public function assignRole(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'role_id' => 'required|exists:roles,id',
        ]);

        try {
            $user = User::findOrFail($request->user_id);
            $role = Role::findOrFail($request->role_id);

            if ($user->hasRole($role->name)) {
                return response()->json([
                    'success' => false,
                    'message' => 'El usuario ya tiene este rol asignado.'
                ], 400);
            }

            $user->roles()->attach($role->id);

            return response()->json([
                'success' => true,
                'message' => "Rol '{$role->display_name}' asignado a {$user->name} exitosamente."
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al asignar el rol: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remover rol de usuario
     */
    public function removeRole(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'role_id' => 'required|exists:roles,id',
        ]);

        try {
            $user = User::findOrFail($request->user_id);
            $role = Role::findOrFail($request->role_id);

            if (!$user->hasRole($role->name)) {
                return response()->json([
                    'success' => false,
                    'message' => 'El usuario no tiene este rol asignado.'
                ], 400);
            }

            $user->roles()->detach($role->id);

            return response()->json([
                'success' => true,
                'message' => "Rol '{$role->display_name}' removido de {$user->name} exitosamente."
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al remover el rol: ' . $e->getMessage()
            ], 500);
        }
    }
}