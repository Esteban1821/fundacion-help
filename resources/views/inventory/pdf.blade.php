<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><title>Reporte</title><style>body{font-family:sans-serif;font-size:12px;}table{width:100%;border-collapse:collapse;}th,td{border:1px solid #ddd;padding:8px;}</style></head>
<body>
    <h2>REPORTE DE INVENTARIO</h2>
    <p>Filtro: {{ $estadoFiltro }} | Fecha: {{ date('d/m/Y') }}</p>
    <table>
        <tr style="background:#f3f4f6;"><th>Categoría</th><th>Descripción</th><th>Serie</th><th>Estado</th></tr>
        @foreach($inventario as $item)
        <tr><td>{{ $item->category }}</td><td>{{ $item->description }}</td><td>{{ $item->serial_number ?? 'N/A' }}</td>
        <td>{{ $item->user ? 'Asignado a '.$item->user->name : 'En Bodega' }}</td></tr>
        @endforeach
    </table>
</body>
</html>