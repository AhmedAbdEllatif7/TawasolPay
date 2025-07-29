<!DOCTYPE html>
<html>
<head>
    <title>Pusher Test</title>
    @livewireStyles
    <script src="https://js.pusher.com/8.4.0/pusher.min.js"></script>
    <script>
        // Enable Pusher logging (remove in production)
        Pusher.logToConsole = true;

        var pusher = new Pusher('545c4089fb87d401a0ee', {
            cluster: 'eu'
        });

        var channel = pusher.subscribe('my-channel');
        channel.bind('my-event', function(data) {
            alert(JSON.stringify(data));
        });
    </script>
</head>
<body>
    <h1>Pusher Test</h1>
    <livewire:counter />
    <p>
        Try publishing an event to channel <code>my-channel</code>
        with event name <code>my-event</code>.
    </p>
    @livewireScripts
</body>
</html>