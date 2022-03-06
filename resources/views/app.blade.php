<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title inertia>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">

        @production
            @php
                $manifest = json_decode(file_get_contents(public_path('dist/manifest.json')), true);
            @endphp
            <script type="module" src="/dist/{{ $manifest['resources/js/app.js']['file'] }}"></script>
            <link rel="stylesheet" href="/dist/{{ $manifest['resources/js/app.js']['css'][0] }}">
        @else
            <script type="module" src="http://localhost:3030/resources/js/app.js"></script>
        @endproduction

        @routes
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html>
