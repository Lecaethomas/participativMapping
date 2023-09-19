<?php
include('./../config/mysql.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Get the JSON data from the request.
        $data = file_get_contents('php://input');
        $requestData = json_decode($data);

        if ($requestData && isset($requestData->polygonId)) {
            $polygonId = $requestData->polygonId;

            // Prepare the SQL statement for deletion.
            $sql = "DELETE FROM polygons WHERE geo_id = :polygonId";
            $stmt = $mysqlClient->prepare($sql);
            $stmt->bindParam(':polygonId', $polygonId, PDO::PARAM_STR);

            if ($stmt->execute()) {
                echo 'Polygon deleted successfully.';
            } else {
                echo 'Error deleting polygon: ' . $stmt->errorInfo()[2];
            }
        } else {
            echo 'Invalid data.';
        }
    } catch (PDOException $e) {
        echo 'Connection failed: ' . $e->getMessage();
    }
}
?>
