<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>{{ $item->name() }}</title>
    <style>
        body { font-family: sans-serif; max-width: 600px; margin: 60px auto; padding: 0 20px; }
        label { display:block; margin-top:16px; font-weight:bold; }
        input[type=text], input[type=email], textarea, select { width:100%; padding:8px; margin-top:4px; border:1px solid #ccc; border-radius:4px; box-sizing:border-box; }
        button { margin-top:20px; padding:10px 24px; background:#27ae60; color:#fff; border:none; border-radius:4px; cursor:pointer; font-size:15px; }
        .marble-form-success { background:#d4edda; border:1px solid #c3e6cb; color:#155724; padding:16px; border-radius:4px; }
    </style>
</head>
<body>
    <h1>{{ $item->name() }}</h1>

    <x-marble::marble-form :item="$item">
        <button type="submit">Absenden</button>
    </x-marble::marble-form>
</body>
</html>
