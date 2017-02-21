<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- <meta name="viewport" content="width=device-width, initial-scale=1"> -->

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css">

    <!-- Scripts -->
    <script>
        window.Laravel = {!! json_encode([
            'csrfToken' => csrf_token(),
        ]) !!};
    </script>
</head>
<body>
    <div id="app">
        <nav class="my-nav">
            <ul class="d-flex flex-column nav nav-pills">
                <li class="nav-item">
                    <router-link to="/router1" class="nav-link">router#1</router-link>
                    <router-link to="/router2" class="nav-link">router#2</router-link>
                </li>
            </ul>
        </nav>
        <div class="my-main">
            <router-view></router-view>
        </div>
    </div>
    <script src="{{ mix('js/vue.js') }}"></script>
</body>
</html>
