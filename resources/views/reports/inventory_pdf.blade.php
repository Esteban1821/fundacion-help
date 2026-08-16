<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Reporte de Inventario</title>
    <style>
        body { font-family: Helvetica, Arial, sans-serif; font-size: 11px; color: #1f2937; margin: 0; }

        .header { background: #b91c1c; color: #ffffff; padding: 16px 24px; }
        .header h1 { margin: 0; font-size: 18px; }
        .header p { margin: 4px 0 0; font-size: 11px; color: #fecaca; }

        .meta { padding: 14px 24px 0; }
        .meta table { width: 100%; border-collapse: collapse; }
        .meta td { padding: 2px 0; font-size: 11px; }
        .meta .label { color: #6b7280; width: 140px; }
        .meta .value { font-weight: bold; color: #111827; }

        .content { padding: 14px 24px 24px; }

        table.data { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table.data th {
            background: #f3f4f6; color: #374151; text-align: left;
            padding: 7px 8px; font-size: 10px; text-transform: uppercase;
            border-bottom: 2px solid #d1d5db;
        }
        table.data td { padding: 7px 8px; border-bottom: 1px solid #e5e7eb; font-size: 11px; }
        table.data tr:nth-child(even) td { background: #fafafa; }

        .badge { display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: 9px; font-weight: bold; }
        .badge-asignado { background: #dbeafe; color: #1e40af; }
        .badge-bodega { background: #fef3c7; color: #92400e; }

        .empty { text-align: center; padding: 24px; color: #9ca3af; font-style: italic; }

        .footer { margin-top: 16px; font-size: 9px; color: #9ca3af; text-align: right; }
    </style>
</head>
<body>

    <div class="header">
        <h1>Reporte de Inventario Tecnológico</h1>
        <p>Fundación Univalle &mdash; Mesa de Ayuda</p>
    </div>

    <div class="meta">
        <table>
            <tr>
                <td class="label">Fecha de generación:</td>
                <td class="value">{{ now()->translatedFormat('d \d\e F \d\e Y, h:i A') }}</td>
            </tr>
            <tr>
                <td class="label">Filtro de estado:</td>
                <td class="value">
                    @if($estado === 'asignados') Solo equipos asignados
                    @elseif($estado === 'bodega') Solo equipos en bodega
                    @else Todos los equipos
                    @endif
                </td>
            </tr>
            <tr>
                <td class="label">Filtro de categoría:</td>
                <td class="value">{{ $categoria ?: 'Todas las categorías' }}</td>
            </tr>
            <tr>
                <td class="label">Total de registros:</td>
                <td class="value">{{ $inventarios->count() }}</td>
            </tr>
        </table>
    </div>

    <div class="content">
        @if($inventarios->count() > 0)
            <table class="data">
                <thead>
                    <tr>
                        <th style="width: 22%;">Categoría</th>
                        <th style="width: 34%;">Descripción</th>
                        <th style="width: 18%;">N&uacute;mero de Serie</th>
                        <th style="width: 26%;">Estado / Asignación</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($inventarios as $item)
                        <tr>
                            <td>{{ $item->category }}</td>
                            <td>{{ $item->description }}</td>
                            <td>{{ $item->serial_number ?? 'N/A' }}</td>
                            <td>
                                @if($item->user)
                                    <span class="badge badge-asignado">Asignado</span>
                                    &nbsp;{{ $item->user->name }} {{ $item->user->last_name }}
                                @else
                                    <span class="badge badge-bodega">En bodega</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p class="empty">No hay equipos que coincidan con los filtros seleccionados.</p>
        @endif

        <p class="footer">Fundación Univalle &mdash; Sistema de Mesa de Ayuda &middot; Reporte generado autom&aacute;ticamente</p>
    </div>

</body>
</html>
