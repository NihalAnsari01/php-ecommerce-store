<?php

session_start();

require "db_pdo.php";


// ------------------------------------
// 1. Check if user is logged in
// ------------------------------------

if (!isset($_SESSION["user_id"])) {

    header("Location: login.php");
    exit;

}


// ------------------------------------
// 2. Check if cart exists
// ------------------------------------

if (
    !isset($_SESSION["cart"]) ||
    empty($_SESSION["cart"])
) {

    header("Location: cart.php");
    exit;

}


$userId = $_SESSION["user_id"];

$cart = $_SESSION["cart"];


try {

    // ------------------------------------
    // 3. Start database transaction
    // ------------------------------------

    $pdo->beginTransaction();


    $total = 0;

    $orderItems = [];


    // ------------------------------------
    // 4. Get products from database
    // ------------------------------------

    foreach ($cart as $productId => $quantity) {

        $productId = (int) $productId;
        $quantity = (int) $quantity;


        $stmt = $pdo->prepare(
            "SELECT * FROM products
             WHERE id = ?
             FOR UPDATE"
        );

        $stmt->execute([$productId]);

        $product = $stmt->fetch(PDO::FETCH_ASSOC);


        // Product doesn't exist
        if (!$product) {

            throw new Exception(
                "Product not found."
            );

        }


        // ------------------------------------
        // 5. Check stock
        // ------------------------------------

        if ($quantity <= 0) {

            throw new Exception(
                "Invalid quantity."
            );

        }


        if ($quantity > $product["stock"]) {

            throw new Exception(
                "Not enough stock for " .
                $product["name"]
            );

        }


        // ------------------------------------
        // 6. Calculate subtotal
        // ------------------------------------

        $subtotal =
            $product["price"] * $quantity;


        $total += $subtotal;


        // Store item information
        $orderItems[] = [

            "product_id" => $product["id"],

            "quantity" => $quantity,

            "price" => $product["price"]

        ];

    }


    // ------------------------------------
    // 7. Create order
    // ------------------------------------

    $stmt = $pdo->prepare(
        "INSERT INTO orders
        (user_id, total_amount, status)
        VALUES (?, ?, ?)"
    );


    $stmt->execute([

        $userId,

        $total,

        "Pending"

    ]);


    // Get newly created order ID

    $orderId = $pdo->lastInsertId();


    // ------------------------------------
    // 8. Create order items
    // ------------------------------------

    $stmt = $pdo->prepare(
        "INSERT INTO order_items
        (order_id, product_id, quantity, price)
        VALUES (?, ?, ?, ?)"
    );


    foreach ($orderItems as $item) {

        $stmt->execute([

            $orderId,

            $item["product_id"],

            $item["quantity"],

            $item["price"]

        ]);

    }


    // ------------------------------------
    // 9. Reduce product stock
    // ------------------------------------

    $stmt = $pdo->prepare(
        "UPDATE products
         SET stock = stock - ?
         WHERE id = ?"
    );


    foreach ($orderItems as $item) {

        $stmt->execute([

            $item["quantity"],

            $item["product_id"]

        ]);

    }


    // ------------------------------------
    // 10. Finish transaction
    // ------------------------------------

    $pdo->commit();


    // ------------------------------------
    // 11. Empty shopping cart
    // ------------------------------------

    $_SESSION["cart"] = [];


    // ------------------------------------
    // 12. Show confirmation
    // ------------------------------------

    ?>

    <!DOCTYPE html>

    <html lang="en">

    <head>

        <meta charset="UTF-8">

        <meta
            name="viewport"
            content="width=device-width, initial-scale=1.0"
        >

        <title>Order Confirmed - Ecom Store</title>

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

        <div class="product-card">

            <h1>
                🎉 Order Placed Successfully!
            </h1>

            <br>

            <p>
                Thank you for your order.
            </p>

            <br>

            <p>
                Your Order ID is:
                <strong>
                    #<?= $orderId ?>
                </strong>
            </p>

            <br>

            <p>
                Total Amount:
                <strong>
                    ₹<?= number_format($total, 2) ?>
                </strong>
            </p>

            <br>

            <p>
                Status:
                <strong>
                    Pending
                </strong>
            </p>

            <br><br>

            <a
                href="products.php"
                class="product-btn"
            >
                Continue Shopping
            </a>

        </div>

    </section>


    </body>

    </html>


    <?php


} catch (Exception $e) {


    // ------------------------------------
    // Rollback if something fails
    // ------------------------------------

    if ($pdo->inTransaction()) {

        $pdo->rollBack();

    }


    die(
        "Order failed: " .
        htmlspecialchars($e->getMessage())
    );

}

?>