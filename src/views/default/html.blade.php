<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8" />

        <title>{{ config('health.title') }}</title>

        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.5/css/bootstrap.min.css" integrity="sha384-AysaV+vQoT3kOAXZkl02PThvDr8HYKPZhNT5h/CXfBThSRXQ6jW5DO2ekP5ViFdi" crossorigin="anonymous">

        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" crossorigin="anonymous"></link>

        <link href="https://fonts.googleapis.com/css?family=Ubuntu" rel="stylesheet">
	</head>

	<body>
        @include('pragmarx/health::default.partials.style')

        @yield('html.header')

        @yield('html.body')

        @yield('html.footer')

        <script src="https://code.jquery.com/jquery-3.1.1.min.js" crossorigin="anonymous"></script>

        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.5/js/bootstrap.min.js" integrity="sha384-BLiI7JTZm+JWlgKa0M0kGRpJbF2J8q+qreVrKBC47e3K6BW78kGLrCkeRX6I9RoK" crossorigin="anonymous"></script>
	</body>
</html>
