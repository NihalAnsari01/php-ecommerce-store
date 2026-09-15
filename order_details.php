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
// 2. Check order ID
// ------------------------------------

if (!isset($_GET["id"])) {

    die("Order ID is missing.");

}


$orderId = (int) $_GET["id"];

$userId = $_SESSION["user_id"];


// ------------------------------------
// 3. Get the order
// ------------------------------------

$stmt = $pdo->prepare(
    "SELECT *
     FROM orders
     WHERE id = ?
     AND user_id = ?"
);

$stmt->execute([
    $orderId,
    $userId
]);

$order = $stmt->fetch(PDO::FETCH_ASSOC);


// ------------------------------------
// 4. Check if order exists
// ------------------------------------

if (!$order) {

    die("Order not found.");

}


// ------------------------------------
// 5. Get order items
// ------------------------------------

$stmt = $pdo->prepare(
    "SELECT
        order_items.*,
        products.name,
        products.category
     FROM order_items
     JOIN products
        ON order_items.product_id = products.id
     WHERE order_items.order_id = ?"
);

$stmt->execute([$orderId]);

$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Order #<?= $order["id"] ?> - Ecom Store
    </title>

    <link
        rel="stylesheet"
        href="style.css"
    >

</head>


<body>


<!-- =========================
     NAVBAR
========================= -->

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

        <li>
            <a href="my_orders.php">
                My Orders
            </a>
        </li>

        <li>
            <a href="logout.php">
                Logout
            </a>
        </li>

    </ul>

</nav>


<!-- =========================
     ORDER DETAILS
========================= -->

<section class="products-section">


    <h2>
        Order #<?= $order["id"] ?>
    </h2>


    <br>


    <div class="product-card">


        <p>

            <strong>
                Order Date:
            </strong>

            <?= date(
                "d M Y, h:i A",
                strtotime($order["created_at"])
            ) ?>

        </p>


        <br>


        <p>

            <strong>
                Status:
            </strong>

            <?= htmlspecialchars($order["status"]) ?>

        </p>


    </div>


    <br>


    <h3>
        Products
    </h3>


    <br>


    <?php foreach ($items as $item): ?>


        <?php

        $subtotal =
            $item["price"] *
            $item["quantity"];

        ?>


        <div class="product-card">


            <h3>
                <?= htmlspecialchars($item["name"]) ?>
            </h3>


            <p class="category">

                Category:

                <?= htmlspecialchars($item["category"]) ?>

            </p>


            <br>


            <p>

                Price:

                ₹<?= number_format(
                    $item["price"],
                    2
                ) ?>

            </p>


            <p>

                Quantity:

                <?= $item["quantity"] ?>

            </p>


            <p>

                Subtotal:

                <strong>

                    ₹<?= number_format(
                        $subtotal,
                        2
                    ) ?>

                </strong>

            </p>


        </div>


        <br>


    <?php endforeach; ?>


    <!-- =========================
         TOTAL
    ========================= -->


    <div class="product-card">


        <h2>

            Total:

            ₹<?= number_format(
                $order["total_amount"],
                2
            ) ?>

        </h2>


    </div>


    <br>


    <a
        href="my_orders.php"
        class="product-btn"
    >
        ← Back to My Orders
    </a>


</section>


</body>

</html>