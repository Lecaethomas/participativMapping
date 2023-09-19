<!DOCTYPE html>
<html>
<head>
    <title>Mapbox Polygon Drawing</title>
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" 
        rel="stylesheet"
    >
    <link href='https://api.mapbox.com/mapbox-gl-js/v2.6.1/mapbox-gl.css' rel='stylesheet' />
    <script src='https://api.mapbox.com/mapbox-gl-js/v2.6.1/mapbox-gl.js'></script>
    <script src='https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-draw/v1.4.2/mapbox-gl-draw.js'></script>
    <link rel="stylesheet" href="https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-draw/v1.4.0/mapbox-gl-draw.css" type="text/css">
    <style>
        body {
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column; /* Stack header and map vertically */
            height: 100vh; /* Use the full viewport height */
        }
        #map {
            flex: 1; /* Expand to fill available space */
        }
    </style>
</head>
<body>
     <!-- Navigation -->
     <?php include_once('header.php'); ?>
    <div id='map'></div>
    <script type="text/javascript" src="map/map.js"></script>
    </body>
</html>
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Get the JSON data from the request.
        $data = file_get_contents('php://input');
        $postData = json_decode($data);

        // Log the received data to the error log for debugging.
        error_log('Received data: ' . print_r($postData, true));

        if ($postData) {
            $geojson = $postData->geojson;
            $polygonId = $postData->polygonId;

            // Delete the corresponding polygon from the database.
            $sqlDelete = "DELETE FROM polygons WHERE geo_id = :polygonId";
            $stmtDelete = $mysqlClient->prepare($sqlDelete);
            $stmtDelete->bindParam(':polygonId', $polygonId, PDO::PARAM_INT);
            $stmtDelete->execute();

            // Access the 'features' property correctly
            if (is_array($geojson) && isset($geojson[0]->features)) {
                $comment = $geojson[0]->features[count($geojson[0]->features) - 1]->properties->comment; // Get the comment

                // Prepare the SQL statement for insertion
                $email = $loggedUser['email'];

                $sqlInsert = "INSERT INTO polygons (geometry, user_mail, comment, geo_id) VALUES (:geometry, :user, :comment, :geo_id)";
                $stmtInsert = $mysqlClient->prepare($sqlInsert);

                $geometryJson = json_encode($geojson[0]);
                $stmtInsert->bindParam(':geometry', $geometryJson, PDO::PARAM_STR);
                $stmtInsert->bindParam(':user', $email, PDO::PARAM_STR);
                $stmtInsert->bindParam(':comment', $comment, PDO::PARAM_STR);
                $stmtInsert->bindParam(':geo_id', $polygonId, PDO::PARAM_INT);

                if ($stmtInsert->execute()) {
                    echo 'Polygon saved successfully.';
                } else {
                    echo 'Error saving polygon: ' . $stmtInsert->errorInfo()[2];
                }
            } else {
                echo 'Invalid GeoJSON data structure.';
            }
        } else {
            echo 'Invalid JSON data.';
        }
    } catch (PDOException $e) {
        echo 'Connection failed: ' . $e->getMessage();
    }
}

?>
     <?php include_once('footer.php'); ?> 