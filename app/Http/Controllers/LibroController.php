<?php

namespace App\Http\Controllers;

use App\Http\Requests\LibroRequest;
use App\models\Categoria;
use App\Models\Libro;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class LibroController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $libros = Libro::paginate();

        return view('libro.index', compact('libros'))
            ->with('i', ($request->input('page', 1) - 1) * $libros->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $libro = new Libro;
        $categorias = Categoria::all();

        return view('libro.create', compact('libro', 'categorias'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(LibroRequest $request): RedirectResponse
    {
        Libro::create($request->validated());

        return Redirect::route('libros.index')
            ->with('success', 'Libro created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $libro = Libro::find($id);

        return view('libro.show', compact('libro'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $libro = Libro::find($id);
        $categorias = Categoria::all();

        return view('libro.edit', compact('libro', 'categorias'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(LibroRequest $request, Libro $libro): RedirectResponse
    {
        $libro->update($request->validated());

        return Redirect::route('libros.index')
            ->with('success', 'Libro updated successfully');
    }

    public function destroy($id): RedirectResponse
    {
        Libro::find($id)->delete();

        return Redirect::route('libros.index')
            ->with('success', 'Libro deleted successfully');
    }
}
