<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmación de Reporte de seguridad</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .content {
            background-color: #ffffff;
            padding: 20px;
            border: 1px solid #dee2e6;
            border-radius: 5px;
        }
        .reporte-id {
            font-size: 24px;
            font-weight: bold;
            color: #007bff;
        }
        .info-item {
            margin-bottom: 10px;
        }
        .label {
            font-weight: bold;
            color: #495057;
        }
        .footer {
            margin-top: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 5px;
            font-size: 14px;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Confirmación de Reporte de seguridad</h1>
        <div class="reporte-id">#{{ $reporte->id }}</div>
    </div>
    
    <div class="content">
        
        <p>Reporte de seguridad generado desde la web. A continuación encontrará los detalles:</p>

        @if($reporte->persona_id)
            <div class="info-item">
                <span class="label">Denunciante: </span>{{ $reporte->persona->nombre }} {{ $reporte->persona->apellido }}
            </div>
        @else 
            <div class="info-item">
                <span class="label">Denunciante: </span> Anónimo
            </div>
        @endif

        <div class="info-item">
            <span class="label">Número de Reporte:</span> #{{ $reporte->id }}
        </div>
        
        <div class="info-item">
            <span class="label">Fecha:</span> {{ $reporte->fecha }}
        </div>
        
        <div class="info-item">
            <span class="label">Categoría:</span> {{ $categoria->nombre }}
        </div>
        
        @if($reporte->direccion)
        <div class="info-item">
            <span class="label">Dirección:</span> {{ $reporte->direccion }}
        </div>
        @endif
        
        
        @if($reporte->observaciones)
        <div class="info-item">
            <span class="label">Descripción:</span> {{ $reporte->observaciones }}
        </div>
        @endif
        
    </div>
    
    <div class="footer">
        <p>Este es un email automático, por favor no responda a este mensaje.</p>
        <p>Si tiene alguna consulta, póngase en contacto con nosotros a través de nuestros canales oficiales.</p>
    </div>
</body>
</html>