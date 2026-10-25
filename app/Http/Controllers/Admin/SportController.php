<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sport;
use Illuminate\Http\Request;

class SportController extends Controller
{
    public function index()
    {
        $sports = Sport::withCount('events')->orderBy('name')->get();
        return view('admin.sports.index', compact('sports'));
    }

    public function create()
    {
        return view('admin.sports.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:sports',
            'icon' => 'required|string|max:10',
        ]);

        Sport::create($request->only('name', 'icon'));

        return redirect()->route('admin.sports.index')->with('success', 'Deporte creado correctamente.');
    }

    public function edit(Sport $sport)
    {
        return view('admin.sports.edit', compact('sport'));
    }

    public function update(Request $request, Sport $sport)
    {
        $request->validate([
            'name'      => 'required|string|max:100|unique:sports,name,' . $sport->id,
            'icon'      => 'required|string|max:10',
            'is_active' => 'boolean',
        ]);

        $sport->update([
            'name'      => $request->name,
            'icon'      => $request->icon,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.sports.index')->with('success', 'Deporte actualizado correctamente.');
    }

    public function destroy(Sport $sport)
    {
        $sport->delete();
        return redirect()->route('admin.sports.index')->with('success', 'Deporte eliminado.');
    }
}
