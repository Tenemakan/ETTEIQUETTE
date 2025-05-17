<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Étiquettes</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .etiquette {
            border: 1px solid #ddd;
            padding: 10px;
            margin: 10px;
            display: inline-block;
            width: 200px;
        }
        .logo {
            width: 40px;
            height: 40px;
            margin-right: 10px;
        }
        .prix {
            font-size: 16px;
            font-weight: bold;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    @for ($i = 0; $i < $nombre; $i++)
    <div class="etiquette">
        <div style="display: flex; align-items: center;">
            <img src="{{ public_path('images/logo-iu.png') }}" class="logo" alt="IU Logo">
            <div>{!! DNS1D::getBarcodeHTML($code, 'C128') !!}</div>
        </div>
        <div class="prix">{{ $prix }}</div>
    </div>
    @endfor
</body>
</html> 