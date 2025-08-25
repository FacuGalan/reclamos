<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orden de Servicio - {{ $datos['numero_reclamo'] }}</title>
    <style>
        @page {
            margin: 15mm;
            size: A4;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 20px;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }
        
        .header {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }
        
        .logo-section {
            flex: 1;
        }

        .logo-container {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 5px;
}

.logo-img {
    height: 50px;
    width: auto;
    
}

.logo-text-small {
    font-size: 18px;
    font-weight: bold;
    color: #2c5aa0;
}

        
        .logo-text {
            font-size: 24px;
            font-weight: bold;
            color: #2c5aa0;
            margin-bottom: 5px;
        }
        
        .subtitle {
            font-size: 12px;
            color: #666;
            margin-bottom: 5px;
        }
        
        .fecha-impresion {
            font-size: 15px;
            color: #888;
        }
        
        .titulo-orden {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            margin: 20px 0;
            text-decoration: underline;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 5px;
        }
        
        .info-item {
            margin-bottom: 8px;
        }
        
        .info-label {
            font-weight: bold;
            display: inline-block;
            width: 140px;
        }
        
        .descripcion-section {
            margin: 5px 0;
        }
        
        .descripcion-box {
            border: 1px solid #333;
            padding: 15px;
            min-height: 60px;
            background-color: #f9f9f9;
            margin-top: 10px;
        }
        
        .status-section {
            text-align: center;
            margin: 0px 0;
            padding: 5px;
            /*border: 1px solid #333;*/
        }
        
        .status-options {
            display: flex;
            justify-content: space-around;
            margin-top: 10px;
        }
        
        .checkbox-item {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .checkbox {
            width: 15px;
            height: 15px;
            border: 2px solid #333;
            display: inline-block;
        }
        
        .novedades-section {
            margin: 25px 0;
        }
        
        .lineas {
            border-bottom: 1px solid #666;
            height: 20px;
            margin: 5px 0;
        }
        
        .firmas-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 50px;
            margin-top: 40px;
            margin-bottom: 30px;
        }
        
        .firma-box {
            text-align: center;
            border-bottom: 1px solid #333;
            padding-bottom: 40px;
            margin-bottom: 10px;
        }
        
        .firma-label {
            font-weight: bold;
            margin-top: 10px;
        }
        
        .historial-section {
            margin-top: 30px;
            border-top: 1px solid #333;
            padding-top: 20px;
        }
        
        .historial-item {
            margin: 8px 0;
            padding: 5px;
            background-color: #f5f5f5;
            border-left: 3px solid #2c5aa0;
            padding-left: 10px;
        }
        
        .footer {
            position: fixed;
            bottom: 15mm;
            left: 15mm;
            right: 15mm;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ccc;
            padding-top: 10px;
        }
        
        .no-print {
            position: fixed;
            top: 10px;
            right: 10px;
            z-index: 1000;
        }
        
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-left: 10px;
            font-weight: bold;
        }
        
        .btn-print {
            background: #2c5aa0;
            color: white;
        }
        
        .btn-close {
            background: #dc3545;
            color: white;
        }
        
        @media print {
            body { margin: 0; padding: 15px; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
    <div class="logo-section">
        <div class="logo-container">
            <img src="{{ asset('logo-muni.svg') }}" alt="Mercedes" class="logo-img">
        </div>
        <div class="fecha-impresion">Fecha: {{ $datos['fecha_impresion'] }}</div>
    </div>
</div>
    
    <!-- Título -->
    <div class="titulo-orden">ORDEN DE SERVICIO NRO. {{ $datos['numero_orden'] }}</div>
    
    <!-- Información principal -->
    <div class="info-grid">
        <div>
            <div class="info-item">
                <span class="info-label">Sector:</span> {{ $datos['sector'] }}
            </div>
            <div class="info-item">
                <span class="info-label">Motivo:</span> {{ $datos['motivo'] }}
            </div>
            <div class="info-item">
                <span class="info-label">Reclamo Nro:</span> {{ $datos['numero_reclamo'] }}
            </div>
            <div class="info-item">
                <span class="info-label">Reclamo tomado:</span> {{ $datos['fecha_reclamo'] }}
            </div>
        </div>
        <div>
            <div class="info-item">
                <span class="info-label">Apellido y nombre:</span> {{ $datos['persona_nombre'] }}
            </div>
            <div class="info-item">
                <span class="info-label">Teléfono:</span> {{ $datos['persona_telefono'] }}
            </div>
            <div class="info-item">
                <span class="info-label">Dirección:</span> {{ $datos['direccion'] }}
            </div>
            @if($datos['entre_calles'])
                <div class="info-item">
                    <span class="info-label">Entre calles:</span> {{ $datos['entre_calles'] }}
                </div>
            @endif
        </div>
    </div>
    
    <!-- Observaciones/Descripción -->
    <div class="descripcion-section">
        <strong>Observaciones:</strong>
        <div class="descripcion-box">
            {{ $datos['descripcion'] }}
        </div>
    </div>
    
    <!-- Status -->
    <div class="status-section">
        <div class="status-options">
            <div class="checkbox-item">
                <span class="checkbox"></span>
                <span>Ejecutado</span>
            </div>
            <div class="checkbox-item">
                <span class="checkbox"></span>
                <span>Pendiente</span>
            </div>
            <div class="checkbox-item">
                <span class="checkbox"></span>
                <span>No ejecutado</span>
            </div>
        </div>
    </div>
    
    <!-- Novedades -->
    <div class="novedades-section">
        <strong>Novedades:</strong>
        <div class="lineas"></div>
        <div class="lineas"></div>
    </div>
    
    <!-- Firmas -->
    <div class="firmas-section">
        <div>
            <div class="firma-box"></div>
            <div class="firma-label">Firma del Encargado/Chofer</div>
        </div>
        <div>
            <div class="firma-box"></div>
            <div class="firma-label">Firma del Coordinador</div>
        </div>
    </div>
    
    <!-- Historial del reclamo -->
    <div class="historial-section">
        <strong>Historial del reclamo</strong>
        @foreach($datos['historial'] as $item)
            <div class="historial-item">
                <strong>{{ $item['fecha'] }}</strong> - {{ $item['descripcion'] }} <em>({{ $item['usuario'] }})</em>
            </div>
        @endforeach
    </div>
    
    <!-- Footer -->
    <!--div class="footer">
        <div style="text-align: left;">
            <strong>Municipalidad de Mercedes (B)</strong><br>
            147 - Sistema único de reclamos<br>
            www.mercedes.gob.ar<br>
            <small>Reclamo: {{ $datos['numero_reclamo'] }}</small>
        </div>
    </div-->
    
    <!-- Botones de acción (no se imprimen) -->
    <!--div class="no-print">
        <button onclick="window.print()" class="btn btn-print">
            Imprimir
        </button>
        <button onclick="window.close()" class="btn btn-close">
            Cerrar
        </button>
    </div-->
    
    <!-- Auto-imprimir cuando se carga la página -->
    <script>
window.addEventListener('load', function() {
    // Auto-imprimir cuando se carga la página
    setTimeout(function() {
        window.print();
        // Opcional: cerrar la ventana después de imprimir
        setTimeout(function() {
            window.close();
        }, 1000);
    }, 500);
});
</script>
    
    
</body>
</html>