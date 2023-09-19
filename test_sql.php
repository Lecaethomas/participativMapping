<?php
try {
    $mysqlClient = new PDO('mysql:host=127.0.0.1;dbname=we_love_food;charset=utf8;port=3306', 'root', '');
    $mysqlClient->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Enable error reporting
} catch (Exception $e) {
    die('Erreur de connexion à la base de données : ' . $e->getMessage());
}

try {
    $sqlQuery = 'SELECT * FROM recipes';
    $recipesStatement = $mysqlClient->prepare($sqlQuery);
    $recipesStatement->execute();
    $recipes = $recipesStatement->fetchAll();
} catch (Exception $e) {
    die('Erreur lors de l\'exécution de la requête : ' . $e->getMessage());
}

// Check if any recipes were retrieved
if (empty($recipes)) {
    echo "Aucune recette n'a été trouvée dans la base de données.";
} else {
    // Loop through and display recipes
    foreach ($recipes as $recipe) {
        echo "<p>{$recipe['author']}</p>";
    }
}

// Si tout va bien, on peut continuer

// On récupère tout le contenu de la table recipes
$sqlQuery = 'SELECT * FROM recipes';
$recipesStatement = $mysqlClient->prepare($sqlQuery);
$recipesStatement->execute();
$recipes = $recipesStatement->fetchAll();

// On affiche chaque recette une à une
foreach ($recipes as $recipe) {
?>
    <p><?php echo $recipe['author']; ?></p>
<?php
}
?>