<?php

session_start();

require "db_pdo.php";


// User must be logged in
if (!isset($_SESSION["user_id"])) {

    header("Location: login.php");
    exit;

}


// Cart must contain products
if (
    !isset($_SESSION["cart"]) ||
    empty($_SESSION["cart"])
) {

    header("Location: cart.php");
    exit;

}


$cart = $_SESSION["cart"];

$total = 0;

$cartProducts = [];


// Get products from database
foreach ($cart as $productId => $quantity) {

    $productId = (int) $productId;
    $quantity = (int) $quantity;


    $stmt = $pdo->prepare(
        "SELECT * FROM products WHERE id = ?"
    );

    $stmt->execute([$productId]);

    $product = $stmt->fetch(PDO::FETCH_ASSOC);


    if (!$product) {
        continue;
    }


    // Check stock
    if ($quantity > $product["stock"]) {

        die(
            "Not enough stock for: " .
            htmlspecialchars($product["name"])
        );

    }


    $subtotal = $product["price"] * $quantity;

    $total += $subtotal;


    $cartProducts[] = [
        "id" => $product["id"],
        "name" => $product["name"],
        "price" => $product["price"],
        "quantity" => $quantity,
        "subtotal" => $subtotal
    ];
}

?>


<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Checkout - Ecom Store</title>

    <link
        rel="stylesheet"
        href="style.css"
    >

</head>


<body>


<nav class="navbar">

    <div class="logo">
        Ecom Store
    </div>


    <ul class="nav-links">

        <li>
            <a href="index.php">
                Home
            </a>
        </li>

        <li>
            <a href="products.php">
                Products
            </a>
        </li>

        <li>
            <a href="cart.php">
                Cart 🛒
            </a>
        </li>

    </ul>

</nav>


<section class="products-section">


    <h2>
        Checkout
    </h2>


    <h3>
        Order Summary
    </h3>

    <br>


    <?php foreach ($cartProducts as $item): ?>

        <div class="product-card">

            <h3>
                <?= htmlspecialchars($item["name"]) ?>
            </h3>


            <p>
                Price:
                ₹<?= number_format($item["price"], 2) ?>
            </p>


            <p>
                Quantity:
                <?= $item["quantity"] ?>
            </p>


            <p>
                Subtotal:
                ₹<?= number_format($item["subtotal"], 2) ?>
            </p>

        </div>

        <br>

    <?php endforeach; ?>


    <h2>
        Total:
        ₹<?= number_format($total, 2) ?>
    </h2>


    <br>


    <form
        action="place_order.php"
        method="POST"
    >

        <button
            type="submit"
            class="product-btn"
        >
            Place Order
        </button>

    </form>


</section>


</body>

</html>