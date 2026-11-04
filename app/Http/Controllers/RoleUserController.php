<?php

namespace App\Http\Controllers;

use App\Http\Requests\RoleUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class RoleUserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View|Response
    {
        $search = $request->input('search');
        $perPage = $request->input('perPage', 5);

        $user = User::when($search, function ($query, $search) {
            return $query->where('name', 'LIKE', "%{$search}%")
                ->orWhere('email', 'LIKE', "%{$search}%");
        })
            ->paginate($perPage);

        if ($request->ajax()) {
            return response()->view('role-user._table', compact('user'));
        }

        return view('role-user.index', compact('user'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $user = new User;

        return view('role-user.create', compact('user'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RoleUserRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);

        User::create($data);

        return Redirect::route('role-users.index')
            ->with('success', 'User created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $user = User::find($id);

        return view('role-user.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $user = User::find($id);

        return view('role-user.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(RoleUserRequest $request, $id): RedirectResponse
    {
        $user = User::findOrFail($id);
        $data = $request->validated();

        if ($request->filled('password')) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        return Redirect::route('role-users.index')
            ->with('success', 'User updated successfully');
    }

    public function destroy($id): RedirectResponse
    {
        $user = User::findOrFail($id);

        if ($user->is(auth()->user())) {
            return redirect()->route('role-users.index')
                ->with('error', 'No puede eliminar su propia cuenta.');
        }

        $user->delete();

        return redirect()->route('role-users.index')
            ->with('success', 'Usuario eliminado correctamente.');
    }
}
