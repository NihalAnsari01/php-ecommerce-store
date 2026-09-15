<?php

session_start();

require "db_pdo.php";


// Check whether product ID exists

if (!isset($_GET["id"])) {

    die("Product ID is missing.");

}


// Convert ID to integer

$id = (int) $_GET["id"];


// Get product from database

$stmt = $pdo->prepare(
    "SELECT * FROM products WHERE id = ?"
);

$stmt->execute([$id]);

$product = $stmt->fetch(PDO::FETCH_ASSOC);


// Check whether product exists

if (!$product) {

    die("Product not found.");

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

    <title>
        <?= htmlspecialchars($product["name"]) ?> - Ecom Store
    </title>

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



<!-- ================= PRODUCT DETAILS ================= -->

<section class="products-section">


    <h2>
        Product Details
    </h2>


    <div
        class="product-card"
        style="max-width:600px; margin:0 auto;"
    >


        <!-- Product Image -->

        <div
            class="product-image"
            style="height:350px;"
        >

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



        <!-- Product Name -->

        <h3>
            <?= htmlspecialchars($product["name"]) ?>
        </h3>



        <!-- Category -->

        <p class="category">

            Category:

            <?= htmlspecialchars($product["category"]) ?>

        </p>



        <!-- Price -->

        <p class="price">

            ₹<?= number_format($product["price"], 2) ?>

        </p>



        <!-- Description -->

        <p>

            <?= nl2br(htmlspecialchars($product["description"])) ?>

        </p>


        <br>



        <!-- Stock -->

        <p>

            <strong>
                Stock:
            </strong>

            <?= (int) $product["stock"] ?>

        </p>



        <br>



        <!-- Add To Cart -->

        <?php if ($product["stock"] > 0): ?>


            <a
                href="add_to_cart.php?id=<?= (int) $product["id"] ?>"
                class="product-btn"
            >
                Add to Cart 🛒
            </a>


        <?php else: ?>


            <p>
                <strong>
                    Out of Stock
                </strong>
            </p>


        <?php endif; ?>


        <br><br>



        <!-- Back -->

        <a
            href="products.php"
            class="btn"
        >
            ← Back to Products
        </a>


    </div>


</section>


</body>

</html>