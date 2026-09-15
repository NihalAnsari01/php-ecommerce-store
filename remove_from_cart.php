<?php

session_start();

if (isset($_GET["id"])) {

    $productId = (int) $_GET["id"];

    if (isset($_SESSION["cart"][$productId])) {
        unset($_SESSION["cart"][$productId]);
    }
}

header("Location: cart.php");
exit;

?>