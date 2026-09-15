
<?php

session_start();

require "db_pdo.php";


// Make sure cart exists
if (!isset($_SESSION["cart"])) {
    $_SESSION["cart"] = [];
}


// Check that required POST data exists
if (!isset($_POST["product_id"]) || !isset($_POST["action"])) {
    header("Location: cart.php");
    exit;
}


// Get product ID and action
$productId = (int) $_POST["product_id"];
$action = $_POST["action"];


// Check if product exists in cart
if (!isset($_SESSION["cart"][$productId])) {
    header("Location: cart.php");
    exit;
}


// Get stock from database
$stmt = $pdo->prepare(
    "SELECT stock FROM products WHERE id = ?"
);

$stmt->execute([$productId]);

$product = $stmt->fetch(PDO::FETCH_ASSOC);


// If product doesn't exist in database
if (!$product) {

    unset($_SESSION["cart"][$productId]);

    header("Location: cart.php");
    exit;
}


// Get available stock
$stock = (int) $product["stock"];


// Increase quantity
if ($action === "increase") {

    if ($_SESSION["cart"][$productId] < $stock) {

        $_SESSION["cart"][$productId]++;

    }
}


// Decrease quantity
elseif ($action === "decrease") {

    $_SESSION["cart"][$productId]--;

    // Remove product if quantity reaches 0
    if ($_SESSION["cart"][$productId] <= 0) {

        unset($_SESSION["cart"][$productId]);

    }
}


// Go back to cart
header("Location: cart.php");

exit;

?>

