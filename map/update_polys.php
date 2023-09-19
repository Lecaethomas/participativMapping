<?php
require_once('./../config/mysql.php'); // Include your MySQL client setup

// Ensure that this script only accepts POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Get the JSON data from the request
        $data = file_get_contents('php://input');
        $geojson = json_decode($data);

        if ($geojson) {
            // Loop through the features and update each one
            foreach ($geojson->features as $feature) {
                // Extract necessary data from the feature
                $featureId = $feature->id;
                $geometry = json_encode($feature->geometry); // Convert the geometry to JSON

                // Prepare the SQL statement for updating the polygon
                $sql = "UPDATE polygons SET geometry = :geometry WHERE geo_id = :featureId";
                $stmt = $mysqlClient->prepare($sql);
                $stmt->bindParam(':geometry', $geometry, PDO::PARAM_STR);
                $stmt->bindParam(':featureId', $featureId, PDO::PARAM_INT);

                // Execute the update query
                if ($stmt->execute()) {
                    // Successfully updated the polygon
                    echo 'Polygon with ID ' . $featureId . ' updated successfully.';
                } else {
                    // Failed to update the polygon
                    echo 'Error updating polygon with ID ' . $featureId;
                }
            }
        } else {
            echo 'Invalid JSON data.';
        }
    } catch (PDOException $e) {
        echo 'Connection failed: ' . $e->getMessage();
    }
} else {
    echo 'Invalid request method. Only POST requests are allowed.';
}
?>
