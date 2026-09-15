<?php

session_start();
require "db_pdo.php";

/*
|--------------------------------------------------------------------------
| Fetch Latest Products
|--------------------------------------------------------------------------
| ORDER BY id DESC = newest products first
| LIMIT 4 = show only the latest 4 products
*/

$stmt = $pdo->prepare(
    "SELECT * FROM products
     ORDER BY id DESC
     LIMIT 4"
);

$stmt->execute();

$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Ecom Store</title>

    <link rel="stylesheet" href="style.css">

</head>

<body>


<!-- =========================================================
     NAVBAR
========================================================= -->

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
                Cart
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


<!-- =========================================================
     HERO SECTION
========================================================= -->

<section class="hero">

    <h1>
        Welcome to Ecom Store
    </h1>

    <p>
        Discover quality products at great prices.
    </p>

    <a
        href="products.php"
        class="btn"
    >
        Shop Now
    </a>

</section>


<!-- =========================================================
     LATEST PRODUCTS
========================================================= -->

<section class="products-section">

    <h2>
        Latest Products
    </h2>


    <?php if (count($products) > 0): ?>

        <div class="product-grid">


            <?php foreach ($products as $product): ?>

                <div class="product-card">


                    <!-- PRODUCT IMAGE -->

                    <?php if (!empty($product["image"])): ?>

                        <img
                            src="images/<?= htmlspecialchars($product["image"]) ?>"
                            alt="<?= htmlspecialchars($product["name"]) ?>"
                            style="
                                width: 100%;
                                height: 200px;
                                object-fit: cover;
                                border-radius: 8px;
                                margin-bottom: 15px;
                            "
                        >

                    <?php else: ?>

                        <div class="product-image">
                            No Image
                        </div>

                    <?php endif; ?>


                    <!-- PRODUCT NAME -->

                    <h3>
                        <?= htmlspecialchars($product["name"]) ?>
                    </h3>


                    <!-- CATEGORY -->

                    <p class="category">
                        <?= htmlspecialchars($product["category"]) ?>
                    </p>


                    <!-- PRICE -->

                    <p class="price">
                        ₹<?= htmlspecialchars($product["price"]) ?>
                    </p>


                    <!-- STOCK -->

                    <?php if ($product["stock"] > 0): ?>

                        <p>
                            Stock:
                            <?= htmlspecialchars($product["stock"]) ?>
                        </p>

                    <?php else: ?>

                        <p>
                            Out of Stock
                        </p>

                    <?php endif; ?>


                    <!-- VIEW PRODUCT -->

                    <a
                        href="product.php?id=<?= $product["id"] ?>"
                        class="product-btn"
                    >
                        View Product
                    </a>


                </div>

            <?php endforeach; ?>


        </div>


    <?php else: ?>

        <p style="text-align:center;">
            No products available yet.
        </p>

    <?php endif; ?>


    <!-- VIEW ALL PRODUCTS -->

    <div style="text-align:center; margin-top:40px;">

        <a
            href="products.php"
            class="btn"
        >
            View All Products
        </a>

    </div>

</section>


</body>

</html>