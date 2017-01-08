<!DOCTYPE html>
<html>
    <head>
        <title>Pusher Test</title>
        <script src="https://js.pusher.com/3.2/pusher.min.js"></script>
        <script>
            var pusher = new Pusher('YOUR-PUSHER-KEY', {
                encrypted: true
            });

            var channel = pusher.subscribe('pragmarx-health-broadcasting-channel');

            channel.bind('PragmaRX\\Health\\Events\\HealthPing', function(data) {
                var request = (new XMLHttpRequest());

                request.open("GET", data.callbackUrl + '?data=' + JSON.stringify(data));

                request.send();
            });
        </script>
    </head>

    <body>
        Pusher waiting for events...
    </body>
</html>
