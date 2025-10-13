<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estadísticas de Reclamos</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #333;
            line-height: 1.4;
            margin: 0;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 25px;
            border-bottom: 3px solid #77BF43;
            padding-bottom: 15px;
        }
        
        .header h1 {
            color: #77BF43;
            margin: 0 0 8px 0;
            font-size: 24px;
        }
        
        .header p {
            margin: 3px 0;
            color: #666;
            font-size: 10px;
        }
        
        .section {
            margin-bottom: 20px;
            page-break-inside: avoid;
        }
        
        .section-title {
            background-color: #77BF43;
            color: white;
            padding: 8px 12px;
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 12px;
        }
        
        .filtros-grid {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }
        
        .filtro-item {
            display: table-row;
        }
        
        .filtro-label {
            display: table-cell;
            font-weight: bold;
            padding: 4px 10px 4px 0;
            width: 30%;
        }
        
        .filtro-value {
            display: table-cell;
            padding: 4px 0;
        }
        
        .stats-grid {
            display: table;
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        
        .stat-row {
            display: table-row;
        }
        
        .stat-cell {
            display: table-cell;
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
            width: 16.66%;
        }
        
        .stat-value {
            font-size: 20px;
            font-weight: bold;
            color: #77BF43;
            display: block;
            margin-bottom: 3px;
        }
        
        .stat-label {
            font-size: 9px;
            color: #666;
            text-transform: uppercase;
        }
        
        .progress-bar-container {
            background-color: #e5e7eb;
            border-radius: 10px;
            height: 20px;
            margin: 10px 0;
            overflow: hidden;
        }
        
        .progress-bar {
            background: linear-gradient(to right, #10B981, #77BF43);
            height: 100%;
            border-radius: 10px;
            text-align: center;
            line-height: 20px;
            color: white;
            font-size: 10px;
            font-weight: bold;
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        
        .table th {
            background-color: #f3f4f6;
            padding: 8px;
            text-align: left;
            border: 1px solid #ddd;
            font-size: 10px;
            font-weight: bold;
        }
        
        .table td {
            padding: 6px 8px;
            border: 1px solid #ddd;
            font-size: 10px;
        }
        
        .table tr:nth-child(even) {
            background-color: #f9fafb;
        }
        
        .bar-chart {
            margin: 8px 0;
        }
        
        .bar-item {
            margin-bottom: 8px;
        }
        
        .bar-label {
            font-size: 10px;
            margin-bottom: 3px;
            display: flex;
            justify-content: space-between;
        }
        
        .bar {
            background-color: #e5e7eb;
            height: 18px;
            border-radius: 4px;
            overflow: hidden;
        }
        
        .bar-fill {
            background-color: #77BF43;
            height: 100%;
            transition: width 0.3s ease;
        }
        
        .footer {
            position: fixed;
            bottom: 15px;
            left: 20px;
            right: 20px;
            text-align: center;
            font-size: 9px;
            color: #999;
            border-top: 1px solid #ddd;
            padding-top: 8px;
        }
        
        .two-columns {
            display: table;
            width: 100%;
        }
        
        .column {
            display: table-cell;
            width: 50%;
            padding: 0 10px;
            vertical-align: top;
        }
        
        .column:first-child {
            padding-left: 0;
        }
        
        .column:last-child {
            padding-right: 0;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>Estadísticas de Reclamos</h1>
        <p><strong>Período:</strong> {{ $periodo['desde'] }} - {{ $periodo['hasta'] }}</p>
        <p><strong>Generado por:</strong> {{ $usuario }} | <strong>Fecha:</strong> {{ $fecha_exportacion }}</p>
    </div>

    <!-- Filtros Aplicados -->
    <div class="section">
        <div class="section-title">Filtros Aplicados</div>
        <div class="filtros-grid">
            <div class="filtro-item">
                <span class="filtro-label">Área:</span>
                <span class="filtro-value">{{ $filtros_aplicados['area'] }}</span>
            </div>
            <div class="filtro-item">
                <span class="filtro-label">Categoría:</span>
                <span class="filtro-value">{{ $filtros_aplicados['categoria'] }}</span>
            </div>
            <div class="filtro-item">
                <span class="filtro-label">Barrio:</span>
                <span class="filtro-value">{{ $filtros_aplicados['barrio'] }}</span>
            </div>
            <div class="filtro-item">
                <span class="filtro-label">Cuadrilla:</span>
                <span class="filtro-value">{{ $filtros_aplicados['cuadrilla'] }}</span>
            </div>
            @if(isset($filtros_aplicados['edificio']))
            <div class="filtro-item">
                <span class="filtro-label">Edificio:</span>
                <span class="filtro-value">{{ $filtros_aplicados['edificio'] }}</span>
            </div>
            @endif
        </div>
    </div>

    <!-- Vista Rápida del Período -->
    @if(!empty($estadisticas_rendimiento))
    <div class="section">
        <div class="section-title">Vista Rápida del Período</div>
        <div class="stats-grid">
            <div class="stat-row">
                <div class="stat-cell">
                    <span class="stat-value">{{ $estadisticas_rendimiento['total_reclamos'] }}</span>
                    <span class="stat-label">Total</span>
                </div>
                <div class="stat-cell">
                    <span class="stat-value">{{ $estadisticas_rendimiento['finalizados'] }}</span>
                    <span class="stat-label">Finalizados</span>
                </div>
                <div class="stat-cell">
                    <span class="stat-value">{{ $estadisticas_rendimiento['cancelados'] }}</span>
                    <span class="stat-label">Cancelados</span>
                </div>
                <div class="stat-cell">
                    <span class="stat-value">{{ $estadisticas_rendimiento['activos'] }}</span>
                    <span class="stat-label">Activos</span>
                </div>
                <div class="stat-cell">
                    <span class="stat-value">{{ $estadisticas_rendimiento['sin_asignar'] }}</span>
                    <span class="stat-label">Sin Asignar</span>
                </div>
                <div class="stat-cell">
                    <span class="stat-value">{{ str_replace('.', ',', $estadisticas_rendimiento['promedio_dias_resolucion']) }}</span>
                    <span class="stat-label">Días Promedio</span>
                </div>
            </div>
        </div>
        
        <div style="margin-top: 15px;">
            <strong style="font-size: 11px;">Progreso de Resolución: {{ $estadisticas_rendimiento['porcentaje_finalizados'] }}%</strong>
            <div class="progress-bar-container">
                @php
                    $porcentaje = $estadisticas_rendimiento['porcentaje_finalizados'];
                    $color = $porcentaje >= 70 ? '#10B981' : ($porcentaje >= 40 ? '#F59E0B' : '#EF4444');
                @endphp
                <div style="background: {{ $color }}; height: 100%; width: {{ $porcentaje }}%; border-radius: 10px; text-align: center; line-height: 20px; color: white; font-size: 10px; font-weight: bold;">
                    {{ $porcentaje }}%
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Resumen Estadístico -->
    @if(!empty($resumen))
    <div class="section">
        <div class="section-title">Análisis Detallado</div>
        
        <div class="two-columns">
            <!-- Top Categorías -->
            @if(isset($resumen['por_categoria']) && count($resumen['por_categoria']) > 0)
            <div class="column">
                <h3 style="font-size: 12px; margin: 0 0 10px 0;">Top Categorías</h3>
                <div class="bar-chart">
                    @foreach(array_slice($resumen['por_categoria'], 0, 5, true) as $categoria => $cantidad)
                    <div class="bar-item">
                        <div class="bar-label">
                            <span>{{ $categoria }}</span>
                            <span><strong>{{ $cantidad }}</strong></span>
                        </div>
                        <div class="bar">
                            <div class="bar-fill" style="width: {{ ($cantidad / $total_reclamos) * 100 }}%"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Por Estado -->
            @if(isset($resumen['por_estado']) && count($resumen['por_estado']) > 0)
            <div class="column">
                <h3 style="font-size: 12px; margin: 0 0 10px 0;">Distribución por Estado</h3>
                <div class="bar-chart">
                    @foreach($resumen['por_estado'] as $estado => $cantidad)
                    <div class="bar-item">
                        <div class="bar-label">
                            <span>{{ $estado }}</span>
                            <span><strong>{{ $cantidad }}</strong></span>
                        </div>
                        <div class="bar">
                            <div class="bar-fill" style="width: {{ ($cantidad / $total_reclamos) * 100 }}%"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Tabla de Áreas -->
    @if(isset($resumen['por_area']) && count($resumen['por_area']) > 0)
    <div class="section">
        <div class="section-title">Distribución por Área</div>
        <table class="table">
            <thead>
                <tr>
                    <th>Área</th>
                    <th style="text-align: center;">Cantidad</th>
                    <th style="text-align: center;">Porcentaje</th>
                </tr>
            </thead>
            <tbody>
                @foreach($resumen['por_area'] as $area => $cantidad)
                <tr>
                    <td>{{ $area }}</td>
                    <td style="text-align: center;">{{ $cantidad }}</td>
                    <td style="text-align: center;">{{ round(($cantidad / $total_reclamos) * 100, 1) }}%</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
    @endif

    <!-- Footer -->
    <div class="footer">
        Sistema de Gestión de Reclamos - Documento generado automáticamente
    </div>
</body>
</html>