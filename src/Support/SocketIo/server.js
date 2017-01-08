var request = require('request');

var server = require('http').Server();

var io = require('socket.io')(server);

var Redis = require('ioredis');

var redis = new Redis();

redis.subscribe('pragmarx-health-broadcasting-channel');

redis.on('message', function (channel, message) {
    message = JSON.parse(message);

    if (message.event == 'PragmaRX\\Health\\Events\\HealthPing') {
        request.get(message.data.callbackUrl);
    }
});

server.listen(3000);
