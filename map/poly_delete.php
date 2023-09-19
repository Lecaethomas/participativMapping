<?php

include_once('./../config/mysql.php');
include_once('./../config/user.php');
include_once('./../variables.php');
// Handle the AJAX request to delete the polygon
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Get the comment from the AJAX request data
        $comment = $_POST['comment'];

        // Prepare the SQL statement to delete the polygon based on the comment
        $sql = "DELETE FROM polygons WHERE comment = :comment";
        $stmt = $mysqlClient->prepare($sql);
        $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);

        if ($stmt->execute()) {
            // Successfully deleted the polygon
            http_response_code(200);
        } else {
            // Failed to delete the polygon
            http_response_code(500);
        }
    } catch (PDOException $e) {
        // Handle any database connection errors
        http_response_code(500);
    }
} else {
    // Handle invalid requests
    http_response_code(400);
}
?>
