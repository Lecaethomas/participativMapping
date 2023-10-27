mapboxgl.accessToken = 'blabla';

var map = new mapboxgl.Map({
            container: 'map',
            style: 'mapbox://styles/mapbox/streets-v11', // Replace with your preferred map style
            center: [1.433333,  43.600000], // Initial map center [longitude, latitude]
            zoom: 13 // Initial zoom level
        });
//// ON Load 
        // CREATE polygons
    var draw = new MapboxDraw({
        displayControlsDefault: false,
        controls: {
            polygon: true,
            trash: true
        }
    });
    // Add a tile layer from your Flask API
    map.on('load', () => {
        map.addSource('coms_2020', {
            type: 'vector',
            url: 'mapbox://tlecae.1ayn961h',
        });

        map.addLayer({
            'id': 'coms_2020_fill',
            'type': 'fill',
            'source': 'coms_2020',
            'source-layer': 'coms_2020_2-9tmh3b',
            'paint': {
                "fill-color": "hsla(0, 0%, 0%, 0)",
                "fill-outline-color": "hsl(0, 75%, 3%)"
            }
        });
        // LOAD User's polygons 
        // Add this code inside your JavaScript <script> block
data= null
// Load and display the user-created polygons
function loadPolygons() {
    fetch('map/get_polys.php')
        .then((response) => response.json())
        .then((data) => {
            console.log(data);
            // Check if the GeoJSON data has the expected structure
            if (data.type === 'FeatureCollection' && Array.isArray(data.features)) {
                // Add a unique ID to each user-loaded feature
                data.features.forEach(function (feature) {
                    feature.properties['draw:id'] = 'user-' + feature.id; // Unique ID
                });

                map.addSource('user_polygons', {
                    type: 'geojson',
                    data: data,
                    generateId: true,
                });

                map.addLayer({
                    id: 'user_polygons',
                    type: 'fill',
                    source: 'user_polygons',
                    paint: {
                        'fill-color': 'rgba(255, 0, 0, 0.5)', // Adjust fill color as needed
                        'fill-outline-color': 'red', // Adjust outline color as needed
                    },
                });

                // After adding the source and layer, add them to Mapbox Draw
                draw.add(data);

            } else {
                console.error('Invalid GeoJSON data structure.');
            }
        })
        .catch((error) => {
            console.error('Error loading polygons:', error);
        });
        }
        // Call the function to load and display user-created polygons
        loadPolygons();
        }); //END on load   

        
        // CREATE polygons
        var draw = new MapboxDraw({
            displayControlsDefault: false,
            controls: {
                polygon: true,
                trash: true
            }
        });
        // ADD CONTROLS
        map.addControl(new mapboxgl.NavigationControl());
        map.addControl(draw);
        var featureId = null
        // // Add a click event listener to enable editing when a user clicks on a polygon
        //     map.on('click', 'user_polygons', function (e) {
        //     var featureId = e.features[0]
        //     draw.add(featureId );
        // }); 
        

                map.on('draw.create', function (e) {
                    console.log('create : ', e)});
                map.on('draw.delete', function (e) {
                    console.log('delete : ', e)});
                map.on('draw.update', function (e) {
                        console.log('update : ', e)});



        map.on('draw.create', function(event) {
            var comment = prompt('Enter a comment for this polygon:');
            var geojson = draw.getAll();
            var id = geojson.features[geojson.features.length - 1].id;

            geojson.features[geojson.features.length - 1].properties.comment = comment;
            geojson.features[geojson.features.length - 1].id = id;

            savePolygon(geojson);
        });

        map.on('draw.update', function (e) {
            if (e.features && e.features.length > 0) {
                var updatedPolygonId = e.features[0].id;
                console.log('Updated Polygon ID:', updatedPolygonId);
                
                // Now you can send the 'updatedPolygonId' to the server along with the GeoJSON data.
                var updatedFeatures = e.features;
                savePolygon(updatedFeatures, updatedPolygonId);
            }
            else {
                console.log('No updated features found.');
            }
        });
        
        function savePolygon(geojson, polygonId) {
            // Send the geojson and polygonId to the PHP server for saving to MySQL.
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'map.php', true);
            xhr.setRequestHeader('Content-Type', 'application/json;charset=UTF-8');
            xhr.onload = function() {
                if (xhr.status === 200) {
                    console.log(xhr);
                    console.log(xhr.status);
                    alert('Polygon saved successfully.');
                } else {
                    alert('Error saving polygon: ' + xhr.responseText);
                }
            };
            var data = {
                geojson: geojson,
                polygonId: polygonId
            };
            xhr.send(JSON.stringify(data));
        }
        
