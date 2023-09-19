<?php

header('Content-Type: application/json');

// Parse the JSON data sent from the client
$data = json_decode(file_get_contents('php://input'), true);

// Check if the data is valid
if (isset($data['features'][0]['geometry'])) {
    $name = 'Polygon Name'; // You can set a name for the polygon

    // Connect to the database (Replace with your database credentials)
    $mysqli = new mysqli('localhost', 'root', 'root', 'we_love_food', 3306);

    // Insert or update the polygon data
    $geometry = json_encode($data['features'][0]['geometry']);
    $sql = "INSERT INTO polygons (name, coordinates) VALUES (?, ?)
            ON DUPLICATE KEY UPDATE coordinates = VALUES(coordinates)";
    
    if ($stmt = $mysqli->prepare($sql)) {
        $stmt->bind_param('ss', $name, $geometry);
        $stmt->execute();
        $stmt->close();
    }

    // Close the database connection
    $mysqli->close();

    echo json_encode(['message' => 'Polygon saved successfully']);
} else {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid polygon data']);
}
?>

