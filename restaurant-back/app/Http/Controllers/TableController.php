<?php

namespace App\Http\Controllers;

use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TableController extends Controller
{
    public function index()
    {
        // Kthimi i të gjitha tavolinave
        return Table::all();
    }

    public function store(Request $request)
    {
        // Validimi i të dhënave
        $request->validate([
            'name' => 'required|string',
            'seats' => 'required|integer',
            'image' => 'required|image|mimes:jpg,png,jpeg,gif|max:2048',  // Validimi për imazh
        ]);

        // Ruajmë imazhin dhe marrim URL-në
        $imagePath = $request->file('image')->store('tables', 'public');  // Ruajmë imazhin në storage

        // Krijojmë tavolinën dhe ruajmë URL-në e imazhit
        $table = Table::create([
            'name' => $request->name,
            'seats' => $request->seats,
            'image' => $imagePath,  // Ruajmë path-in e imazhit
        ]);

        return response()->json($table, 201);  // Kthejmë të dhënat e tavolinës të krijuar
    }

    public function show($id)
    {
        // Kthejmë një tavolinë nga ID
        return Table::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        // Gjejmë tavolinën
        $table = Table::findOrFail($id);

        // Validimi i të dhënave
        $request->validate([
            'name' => 'required|string',
            'seats' => 'required|integer',
            'image' => 'nullable|image|mimes:jpg,png,jpeg,gif|max:2048', // Imazhi është i opsional
        ]);

        // Nëse është ngarkuar një imazh i ri, ruajmë atë
        if ($request->hasFile('image')) {
            // Fshijmë imazhin e vjetër nga storage
            Storage::delete('public/' . $table->image);
            
            // Ruajmë imazhin e ri dhe marrim URL-në
            $imagePath = $request->file('image')->store('tables', 'public');
            $table->image = $imagePath;
        }

        // Përditësojmë të dhënat e tavolinës
        $table->update([
            'name' => $request->name,
            'seats' => $request->seats,
        ]);

        return response()->json($table);  // Kthejmë tavolinën e përditësuar
    }

    public function destroy($id)
    {
        // Gjejmë tavolinën për ta fshirë
        $table = Table::findOrFail($id);

        // Fshijmë imazhin nga storage
        Storage::delete('public/' . $table->image);

        // Fshijmë tavolinën nga databaza
        Table::destroy($id);

        return response()->json(['message' => 'Tavolina u fshi me sukses']);
    }
}
