<?php

session_start();

require "db_pdo.php";

$stmt = $pdo->prepare(
    "SELECT * FROM products ORDER BY id DESC"
);

$stmt->execute();

$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Products - Ecom Store</title>

    <link rel="stylesheet" href="style.css">

</head>

<body>


<!-- ================= NAVBAR ================= -->

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


        <?php if (isset($_SESSION["user_id"])): ?>

            <li>
                <a href="my_orders.php">
                    My Orders
                </a>
            </li>

            <li>
                Welcome,
                <?= htmlspecialchars($_SESSION["user_name"]) ?>
            </li>

            <li>
                <a href="logout.php">
                    Logout
                </a>
            </li>


        <?php else: ?>

            <li>
                <a href="login.php">
                    Login
                </a>
            </li>

            <li>
                <a href="register.php">
                    Register
                </a>
            </li>

        <?php endif; ?>

    </ul>

</nav>


<!-- ================= PRODUCTS ================= -->

<section class="products-section">

    <h2>
        All Products
    </h2>


    <div class="product-grid">


        <?php if (count($products) > 0): ?>


            <?php foreach ($products as $product): ?>

                <div class="product-card">


                    <div class="product-image">

                        <?php if (!empty($product["image"])): ?>

                            <img
                                src="images/<?= htmlspecialchars($product["image"]) ?>"
                                alt="<?= htmlspecialchars($product["name"]) ?>"
                                style="width:100%; height:100%; object-fit:cover; border-radius:8px;"
                            >

                        <?php else: ?>

                            No Image

                        <?php endif; ?>

                    </div>


                    <h3>
                        <?= htmlspecialchars($product["name"]) ?>
                    </h3>


                    <p class="category">
                        <?= htmlspecialchars($product["category"]) ?>
                    </p>


                    <p class="price">
                        ₹<?= number_format($product["price"], 2) ?>
                    </p>


                    <p>
                        Stock:
                        <?= (int) $product["stock"] ?>
                    </p>


                    <a
                        href="product.php?id=<?= (int) $product["id"] ?>"
                        class="product-btn"
                    >
                        View Product
                    </a>


                </div>

            <?php endforeach; ?>


        <?php else: ?>

            <p>
                No products available.
            </p>

        <?php endif; ?>


    </div>

</section>


</body>

</html>