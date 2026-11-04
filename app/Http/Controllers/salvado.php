<?php

// ROL USER

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class RoleUserController extends Controller
{
    public function index(Request $request): View
    {
        $users = User::with('roles')->paginate();

        return view('user.index', compact('users'))->with('i', ($request->input('page', 1) - 1) * $users->perPage());
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

        return Redirect::route('users.index')
            ->with('success', 'User created successfully.');
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
        if ($request->filled('password')) {
            $data = $request->validated();
            $data['password'] = Hash::make($data['password']);
        } else {
            $data = $request->except('password', 'password_confirmation');
        }

        //
        $user->update($data);
        $user->roles()->sync([$request->input('role_id')]);

        return Redirect::route('users.index')
            ->with('success', 'User updated successfully');
    }

    public function destroy($id): RedirectResponse
    {
        User::find($id)->delete();

        return Redirect::route('users.index')
            ->with('success', 'User deleted successfully');
    }
}
