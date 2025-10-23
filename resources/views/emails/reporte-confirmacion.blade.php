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
        .seguimiento {
            background-color: #e7f3ff;
            padding: 20px;
            border-radius: 5px;
            margin-top: 20px;
            text-align: center;
        }
        .btn-seguimiento {
            display: inline-block;
            padding: 12px 30px;
            background-color: #007bff;
            color: #ffffff;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            margin-top: 15px;
            transition: background-color 0.3s ease;
        }
        .btn-seguimiento:hover {
            background-color: #0056b3;
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
        
        @if($domicilio->direccion)
            <div class="info-item">
                <span class="label">Dirección:</span> {{ $domicilio->direccion }}
            </div>
        @endif
        @if($domicilio->entre_calles)
            <div class="info-item">
                <span class="label">Entre Calles:</span> {{ $domicilio->entre_calles }}
            </div>
        @endif
        @if($domicilio->direccion_rural)
            <div class="info-item">
                <span class="label">Aclaraciones:</span> {{ $domicilio->direccion_rural }}
            </div>
        @endif
        
        
        @if($reporte->observaciones)
        <div class="info-item">
            <span class="label">Descripción:</span> {{ $reporte->observaciones }}
        </div>
        @endif
        
    </div>
    
    <div class="seguimiento">
        <p><strong>Para ver más detalles y realizar el seguimiento de su reclamo</strong>, debe iniciar sesión en el Panel de Atención Ciudadana.</p>
        <a href="https://atencionciudadana.mercedes.gob.ar/login" class="btn-seguimiento">Acceder al Panel</a>
    </div>
    
    <div class="footer">
        <p>Este es un email automático, por favor no responda a este mensaje.</p>
        <p>Si tiene alguna consulta, póngase en contacto con nosotros a través de nuestros canales oficiales.</p>
    </div>
</body>
</html>