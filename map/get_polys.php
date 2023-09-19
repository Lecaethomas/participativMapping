<?php
include('./../config/mysql.php');

$email = $_COOKIE['LOGGED_USER'];

try {
    // Prepare and execute a SQL query to retrieve polygon data filtered by user_mail
    $query = "SELECT geometry FROM polygons WHERE user_mail = :email";
    $stmt = $mysqlClient->prepare($query);

    if (!$stmt) {
        throw new Exception("Query preparation failed: " . $mysqlClient->errorInfo()[2]);
    }

    // Bind the email parameter
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);

    // Execute the query
    $result = $stmt->execute();

    if (!$result) {
        throw new Exception("Query execution failed: " . $stmt->errorInfo()[2]);
    }
    if (!$result) {
        // Print SQL error details for debugging
        print_r($stmt->errorInfo());
        throw new Exception("Query execution failed");
    }

    // Initialize an empty array to store GeoJSON features
    $features = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // Decode the "geometry" field as JSON
        $geometryData = json_decode($row['geometry'], true);

        if ($geometryData === null) {
            throw new Exception("JSON decoding error: " . json_last_error_msg());
        }

        // Check if the GeoJSON structure is valid
        if (
            isset($geometryData['type']) && $geometryData['type'] === 'FeatureCollection' &&
            isset($geometryData['features']) && is_array($geometryData['features'])
        ) {
            // Iterate through the features in the FeatureCollection
            foreach ($geometryData['features'] as $featureData) {
                // Extract geometry and properties
                $geometry = $featureData['geometry'];

                $properties = $featureData['properties'];
                $id = $featureData['id'];


                // Add the feature to the features array
                $features[] = [
                    'type' => 'Feature',
                    'geometry' => $geometry,
                    'properties' => $properties,
                    'id'=> $id
                ];
            }
        } else {
            throw new Exception("Invalid GeoJSON structure.");
        }
    }

    // Create a GeoJSON FeatureCollection
    $featureCollection = [
        'type' => 'FeatureCollection',
        'features' => $features,
    ];

    // Encode as GeoJSON
    $geojsonData = json_encode($featureCollection);

    if ($geojsonData === false) {
        throw new Exception("JSON encoding error: " . json_last_error_msg());
    }

    // Return the GeoJSON data
    header('Content-Type: application/json');
    echo $geojsonData;
} catch (Exception $e) {
    // Handle and log the exception
    echo "Error: " . $e->getMessage();
}

?> 