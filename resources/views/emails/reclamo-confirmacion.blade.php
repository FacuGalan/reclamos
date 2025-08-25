<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmación de Reclamo</title>
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
        .reclamo-id {
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
        <h1>Confirmación de Reclamo</h1>
        <div class="reclamo-id">#{{ $reclamo->id }}</div>
    </div>
    
    <div class="content">
        <p>Estimado/a <strong>{{ $persona->nombre }} {{ $persona->apellido }}</strong>,</p>
        
        <p>Su reclamo ha sido registrado exitosamente en nuestro sistema. A continuación encontrará los detalles:</p>
        
        <div class="info-item">
            <span class="label">Número de Reclamo:</span> #{{ $reclamo->id }}
        </div>
        
        <div class="info-item">
            <span class="label">Fecha:</span> {{ $reclamo->fecha }}
        </div>
        
        <div class="info-item">
            <span class="label">Categoría:</span> {{ $categoria->nombre }}
        </div>
        
        @if($reclamo->direccion)
        <div class="info-item">
            <span class="label">Dirección:</span> {{ $reclamo->direccion }}
        </div>
        @endif
        
        @if($reclamo->edificio)
        <div class="info-item">
            <span class="label">Edificio:</span> {{ $reclamo->edificio->nombre }}
        </div>
        @endif
        
        @if($reclamo->descripcion)
        <div class="info-item">
            <span class="label">Descripción:</span> {{ $reclamo->descripcion }}
        </div>
        @endif
        
        <div class="info-item">
            <span class="label">Estado:</span> {{ $reclamo->estado->nombre }}
        </div>
        
        <p>Podrá realizar el seguimiento de su reclamo utilizando el número de referencia <strong>#{{ $reclamo->id }}</strong>.</p>
        
        <p>Le notificaremos por este medio cualquier actualización en el estado de su reclamo.</p>
    </div>
    
    <div class="footer">
        <p>Este es un email automático, por favor no responda a este mensaje.</p>
        <p>Si tiene alguna consulta, póngase en contacto con nosotros a través de nuestros canales oficiales.</p>
    </div>
</body>
</html>