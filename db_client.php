<?php
include('./config/mysql.php');
include_once('variables.php');
function debug_to_console($data) {
    $output = $data;
    if (is_array($output))
        $output = implode(',', $output);

    echo "<script>console.log('Debug Objects: " . $output . "' );</script>";
}
// Fetch data from the 'polygons' table (replace with your actual table name)
// Modify the SQL query to filter based on user_mail (replace 'user@example.com' with your email variable)
$email = $_COOKIE['LOGGED_USER'];
debug_to_console($email);
$stmt_ = null;
try {
    // Prepare and execute a SQL query to retrieve polygon data filtered by user_mail
    $query = "SELECT * FROM polygons WHERE user_mail = :email";
    $stmt_ = $mysqlClient->prepare($query);

    if (!$stmt_) {
        throw new Exception("Query preparation failed: " . $mysqlClient->errorInfo()[2]);
    }

    // Bind the email parameter
    $stmt_->bindParam(':email', $email, PDO::PARAM_STR);

    // Execute the query
    $result = $stmt_->execute();

    if (!$result) {
        throw new Exception("Query execution failed: " . $stmt_->errorInfo()[2]);
    }

} catch (Exception $e) {
    // Handle and log the exception
    echo "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>MySQL Database Visualizer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column; /* Stack header and map vertically */
            height: 100vh; /* Use the full viewport height */
        }
        #container {
            flex: 1; /* Expand to fill available space */
        }
    </style>
</head>
<body>
<?php include_once('header.php'); ?>
<?php echo $result; ?>
<div class="container">
    <h1 class="mt-5">Database Visualizer</h1>
    <table class="table table-striped mt-4">
        <thead>
            <tr>
                <th>ID</th>
                <th>Geometry</th>
                <th>Time</th>
                <th>User Email</th>
                <th>Comment</th>
                <!-- Add more headers for your columns -->
            </tr>
        </thead>
        <tbody>
            <?php
            if (isset($result)) {
                if ($stmt_->rowCount() > 0) {
                    while ($row = $stmt_->fetch(PDO::FETCH_ASSOC)) {
                        echo "<tr>";
                        echo "<td>" . $row["id"] . "</td>";
                        echo "<td>" . $row["geometry"] . "</td>";
                        echo "<td>" . $row["time"] . "</td>";
                        echo "<td>" . $row["user_mail"] . "</td>";
                        echo "<td>" . $row["comment"] . "</td>";
                        // Add more table cells for additional columns as needed
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>No data available</td></tr>";
                }
            }
            ?>
        </tbody>
    </table>
</div>
</body>
</html>

 <?php include_once('footer.php'); ?>
