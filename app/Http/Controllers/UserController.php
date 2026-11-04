<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View|Response
    {
        $search = $request->input('search');
        $perPage = $request->input('perPage', 5);

        $users = User::with('roles')
            ->when($search, function ($query, $search) {
                return $query->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%");
            })
            ->paginate($perPage);

        if ($request->ajax()) {
            return response()->view('user._table', compact('users'));
        }

        return view('user.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $user = new User;
        $roles = Role::all();

        return view('user.create', compact('user', 'roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);

        $user = User::create($data);
        $user->roles()->sync([$request->input('role_id')]);

        return Redirect::route('users.index')->with('success', 'User created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $user = User::with('roles')->find($id);

        return view('user.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $user = User::with('roles')->find($id);
        $roles = Role::all();

        return view('user.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserRequest $request): RedirectResponse
    {
        // /$data = $request->validated();
        // traer el modelo User junto con la relación roles
        // find->Busca un registro en la tabla users cuyo id coincida con el
        // parámetro user que viene en el request.
        $user = User::with('roles')->find($request->route('user'));

        $data = $request->validated();

        // Solo procesamos la contraseña si viene
        if ($request->filled('password')) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password'], $data['password_confirmation']);
        }

        $user->update($data);
        $user->roles()->sync([$request->role_id]);

        return redirect()->route('users.index')->with('success', 'Usuario actualizado');
    }

    public function destroy($id): RedirectResponse
    {
        $user = User::findOrFail($id);

        if ($user->is(auth()->user())) {
            return redirect()->route('users.index')
                ->with('error', 'No puede eliminar su propia cuenta.');
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'Usuario eliminado correctamente.');
    }
}
