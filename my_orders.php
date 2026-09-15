<?php

session_start();

require "db_pdo.php";


// ------------------------------------
// Check if user is logged in
// ------------------------------------

if (!isset($_SESSION["user_id"])) {

    header("Location: login.php");
    exit;

}


$userId = $_SESSION["user_id"];


// ------------------------------------
// Get user's orders
// ------------------------------------

$stmt = $pdo->prepare(
    "SELECT *
     FROM orders
     WHERE user_id = ?
     ORDER BY id DESC"
);

$stmt->execute([$userId]);

$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>


<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>My Orders - Ecom Store</title>

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
     ORDERS
========================= -->

<section class="products-section">


    <h2>
        My Orders
    </h2>


    <br>


    <?php if (empty($orders)): ?>


        <div class="product-card">

            <h3>
                No orders yet.
            </h3>

            <br>

            <p>
                You haven't placed any orders.
            </p>

            <br>

            <a
                href="products.php"
                class="product-btn"
            >
                Start Shopping
            </a>

        </div>


    <?php else: ?>


        <?php foreach ($orders as $order): ?>


            <div class="product-card">


                <h3>
                    Order #<?= $order["id"] ?>
                </h3>


                <br>


                <p>
                    Total:
                    <strong>
                        ₹<?= number_format($order["total_amount"], 2) ?>
                    </strong>
                </p>


                <br>


                <p>
                    Status:
                    <strong>
                        <?= htmlspecialchars($order["status"]) ?>
                    </strong>
                </p>


                <br>


                <p>
                    Date:
                    <?= date(
                        "d M Y, h:i A",
                        strtotime($order["created_at"])
                    ) ?>
                </p>


                <br>


                <a
                    href="order_details.php?id=<?= $order["id"] ?>"
                    class="product-btn"
                >
                    View Order
                </a>


            </div>


            <br>


        <?php endforeach; ?>


    <?php endif; ?>


</section>


</body>

</html>