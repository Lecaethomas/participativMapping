<!DOCTYPE html>
<html>
<head>
    <script src='https://api.mapbox.com/mapbox-gl-js/v2.6.1/mapbox-gl.js'></script>
    <link href='https://api.mapbox.com/mapbox-gl-js/v2.6.1/mapbox-gl.css' rel='stylesheet' />
    <script src='https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-draw/v1.4.2/mapbox-gl-draw.js'></script>

    <link rel='stylesheet' href='https://unpkg.com/mapbox-gl-draw/dist/mapbox-gl-draw.css' type='text/css'/>
</head>
<body>

<div id='map' style='width: 800px; height: 600px;'></div>

<script>
    mapboxgl.accessToken = 'pk.eyJ1IjoidGxlY2FlIiwiYSI6ImNreHJqOGFwaTAzN3Ayd281dTBmb3VzYTYifQ.8fwOWabWWbcfcUxi1rIxAQ';

    var map = new mapboxgl.Map({
        container: 'map',
        style: 'mapbox://styles/mapbox/streets-v11',
        center: [-74.006, 40.7128],
        zoom: 10
    });

    var Draw = new MapboxDraw({
        displayControlsDefault: false,
        controls: {
            polygon: true,
            trash: true
        }
    });

    map.addControl(Draw);

    map.on('draw.create', function(e) {
        var data = Draw.getAll();
        savePolygonToServer(data);
    });

    map.on('draw.update', function(e) {
        var data = Draw.getAll();
        savePolygonToServer(data);
    });

    function savePolygonToServer(data) {
        // Send the polygon data to the server using an AJAX request (e.g., Fetch API).
        // You'll need to implement the server-side PHP code to handle this request.
        fetch('map/map2.php', {
            method: 'POST',
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(result => {
            console.log('Polygon saved:', result);
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }
</script>

</body>
</html>

