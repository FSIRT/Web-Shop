<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

//Server Connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "webShopFSI";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Verbindung fehlgeschlagen: " . $conn->connect_error);
}

// Stellen Sie sicher, dass die Benutzer-ID in der Session gespeichert ist
if (!isset($_SESSION['userId'])) {
    die("Benutzer-ID nicht gefunden");
}

$userId = $_SESSION['userId'];

// SQL-Abfrage, um die Favoriten des Benutzers zu laden
$sql = "SELECT products.* FROM favorites JOIN products ON favorites.productID = products.productID WHERE favorites.userID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();


// Überprüfen, ob Ergebnisse vorhanden sind
if ($result->num_rows > 0) {
    // Produkte in Produktkarten anzeigen
    echo '<div class="product-container" >';
    while ($row = $result->fetch_assoc()) {
        echo '<div class="product-card-text" >';
        echo '<div class="product-card" data-product-id="' . $row['productID'] . '" onclick="redirectToPage(event)">';

        echo '<img id="product_images" src="../assets/images/produkts/' . $row['pathName'] . '.png" alt="">';
        echo "<div id='add-to-cart-button'></div>";
        echo "<div id='add-to-cart-wishlist'></div>";
        echo '</div>';
        echo '<h4 style="color:black;">' . $row['productName'] . '</h2>';
        echo '<h3>' . $row['price'] . ' €</h1>';
        echo '<p>zzgl. Versandkosten</p>';
        echo '</div>';
    }
    echo '</div>';
    echo '<script>
        function redirectToPage() {
            var productId = event.currentTarget.getAttribute("data-product-id");
            window.location.href = "../views/productDetailsV2.php?id=" + productId;
        }
        </script>';
} else {
    echo '<br>';
    echo '<br>';
    echo '<br>';
    echo '<p style="text-align:center;">Aktuell hast du keine Favoriten, schau dir jetzt unsere Produkte an:</p>';
    echo '<div style=" display: flex;
    justify-content: center;
    align-items: center;">
    
    <small class="d-block text-right mt-3">
      <a href="products.php" class="btn btn-outline-dark">zur Atrikelübersicht</a>
    </small></div>';
}

// Verbindung schließen
$conn->close();