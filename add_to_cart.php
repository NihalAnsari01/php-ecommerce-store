<?php

session_start();

if (!isset($_GET["id"])) {
    die("Product ID is missing.");
}

$productId = (int) $_GET["id"];

if (!isset($_SESSION["cart"])) {
    $_SESSION["cart"] = [];
}

if (isset($_SESSION["cart"][$productId])) {

    $_SESSION["cart"][$productId]++;

} else {

    $_SESSION["cart"][$productId] = 1;

}

header("Location: cart.php");
exit;

?>