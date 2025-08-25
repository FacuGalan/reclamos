<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Actualización de Reclamo</title>
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
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .content {
            background-color: #ffffff;
            padding: 20px;
            border: 1px solid #e9ecef;
            border-radius: 8px;
        }
        .footer {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
            font-size: 12px;
            color: #6c757d;
        }
        .highlight {
            background-color: #fff3cd;
            padding: 10px;
            border-radius: 4px;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>Actualización de Reclamo #{{ $reclamo->id }}</h2>
    </div>

    <div class="content">
        <p>Estimado/a cliente,</p>
        
        <p>Le informamos que su reclamo ha sido actualizado:</p>
        
        <div class="highlight">
            <strong>Número de Reclamo:</strong> #{{ $reclamo->id }}<br>
            <strong>Estado Actual:</strong> {{ $reclamo->estado->nombre ?? 'Sin estado' }}<br>
            <strong>Fecha de Actualización:</strong> {{ date('d/m/Y H:i') }}
        </div>
        @if($reclamo->no_aplica)
            <p style="color:red;"><strong>No Aplica:</strong> Este reclamo no aplica para resolución municipal.</p>
        @endif
        @if($observaciones)
            <p><strong>Observaciones:</strong></p>
            <p>{{ $observaciones }}</p>
        @endif

        <p>Gracias por su paciencia. Estamos trabajando para resolver su consulta lo antes posible.</p>
    </div>

    <div class="footer">
        <p>Este es un mensaje automático, por favor no responda a este correo.</p>
        <p>Si tiene alguna consulta adicional, puede contactarnos a través de nuestros canales oficiales.</p>
    </div>
</body>
</html>