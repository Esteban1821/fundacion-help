<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class InventoryController extends Controller
{
    /**
     * Muestra la lista del inventario con buscador integrado.
     */
    public function index(Request $request)
    {
        $buscar = $request->input('buscar');

        // Query base para Equipos Asignados
        $queryAsignados = Inventory::with('user')->whereNotNull('user_id');

        // Query base para Resumen de Bodega
        $queryBodega = Inventory::whereNull('user_id')
            ->select('category', 'description', DB::raw('count(*) as cantidad_disponible'));

        // Aplicar filtro si el usuario ingresó algo en el buscador
        if ($buscar) {
            $queryAsignados->where(function ($q) use ($buscar) {
                $q->where('category', 'LIKE', "%{$buscar}%")
                  ->orWhere('description', 'LIKE', "%{$buscar}%")
                  ->orWhere('serial_number', 'LIKE', "%{$buscar}%")
                  ->orWhereHas('user', function ($u) use ($buscar) {
                      $u->where('name', 'LIKE', "%{$buscar}%")
                        ->orWhere('email', 'LIKE', "%{$buscar}%");
                  });
            });

            $queryBodega->where(function ($q) use ($buscar) {
                $q->where('category', 'LIKE', "%{$buscar}%")
                  ->orWhere('description', 'LIKE', "%{$buscar}%")
                  ->orWhere('serial_number', 'LIKE', "%{$buscar}%");
            });
        }

        // Ejecutar consultas
        $asignados = $queryAsignados->orderBy('updated_at', 'desc')->get();

        $enBodega = $queryBodega->groupBy('category', 'description')
            ->orderBy('category', 'asc')
            ->get();

        return view('inventory.index', compact('asignados', 'enBodega', 'buscar'));
    }

    /**
     * Muestra el formulario para crear un nuevo equipo en inventario.
     */
    public function create()
    {
        $users = User::orderBy('name', 'asc')->get();
        return view('inventory.create', compact('users'));
    }

    /**
     * Guarda un nuevo equipo en la base de datos.
     */
    public function store(Request $request)
    {
        // Se guardan los datos validados en una variable para una inserción más limpia
        $validated = $request->validate([
            'category'      => 'required|string|max:255',
            'description'   => 'required|string|max:255',
            'serial_number' => 'nullable|string|max:255|unique:inventories,serial_number',
            'user_id'       => 'nullable|exists:users,id',
        ]);

        Inventory::create($validated);

        return redirect()->route('inventory.index')->with('success', 'Equipo registrado con éxito.');
    }

    /**
     * Muestra el formulario para asignar un equipo de bodega a un usuario.
     */
    public function asignar()
    {
        $users = User::orderBy('name', 'asc')->get();
        // Carga los equipos que no están asignados (disponibles en bodega)
        $equiposDisponibles = Inventory::whereNull('user_id')->orderBy('category', 'asc')->get();

        return view('inventory.assign', compact('users', 'equiposDisponibles'));
    }

    /**
     * Procesa la asignación de un equipo de bodega a un usuario.
     */
    public function storeAssign(Request $request)
    {
        $request->validate([
            'user_id'      => 'required|exists:users,id',
            'inventory_id' => 'required|exists:inventories,id',
        ]);

        $inventory = Inventory::findOrFail($request->inventory_id);
        
        $inventory->update([
            'user_id' => $request->user_id,
        ]);

        return redirect()->route('inventory.index')->with('success', 'Equipo asignado con éxito al usuario.');
    }

    /**
     * Muestra el formulario para editar o reasignar un equipo.
     */
    public function edit($id)
    {
        $inventory = Inventory::findOrFail($id);
        $users = User::orderBy('name', 'asc')->get();

        return view('inventory.edit', compact('inventory', 'users'));
    }

    /**
     * Actualiza la información o asignación del equipo.
     */
    public function update(Request $request, $id)
    {
        $inventory = Inventory::findOrFail($id);

        $validated = $request->validate([
            'category'      => 'required|string|max:255',
            'description'   => 'required|string|max:255',
            'serial_number' => 'nullable|string|max:255|unique:inventories,serial_number,' . $inventory->id,
            'user_id'       => 'nullable|exists:users,id',
        ]);

        $inventory->update($validated);

        return redirect()->route('inventory.index')->with('success', 'Inventario actualizado correctamente.');
    }

    /**
     * Devuelve un equipo asignado a la bodega (desvincula el usuario).
     */
    public function devolver($id)
    {
        $inventory = Inventory::findOrFail($id);
        
        $inventory->update([
            'user_id' => null,
        ]);

        return redirect()->back()->with('success', 'El equipo ha sido devuelto a bodega con éxito.');
    }

    /**
     * Elimina un registro de inventario.
     */
    public function destroy($id)
    {
        $inventory = Inventory::findOrFail($id);
        $inventory->delete();

        return redirect()->route('inventory.index')->with('success', 'Equipo eliminado del inventario.');
    }

    /**
     * Genera y descarga el reporte en formato PDF con filtros opcionales.
     */
    public function generatePDF(Request $request)
    {
        $estado = $request->input('estado');
        $categoria = $request->input('categoria');

        $query = Inventory::with('user');

        if ($estado === 'asignados') {
            $query->whereNotNull('user_id');
        } elseif ($estado === 'bodega') {
            $query->whereNull('user_id');
        }

        if ($categoria) {
            $query->where('category', $categoria);
        }

        $inventarios = $query->orderBy('category', 'asc')->get();

        $pdf = Pdf::loadView('reports.inventory_pdf', compact('inventarios', 'estado', 'categoria'));

        return $pdf->download('reporte_inventario_' . date('Y-m-d') . '.pdf');
    }
}