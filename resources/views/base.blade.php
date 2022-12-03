<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('page_title')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="m-0 m-auto max-w-4xl px-4 py-5">
    @yield('body')
</body>

</html>
