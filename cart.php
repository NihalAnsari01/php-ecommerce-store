
<?php

session_start();

require "db_pdo.php";

if (!isset($_SESSION["cart"])) {
    $_SESSION["cart"] = [];
}

$cart = $_SESSION["cart"];

?>

<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Cart - Ecom Store</title>

    <link rel="stylesheet" href="style.css">

</head>

<body>

<nav class="navbar">

    <div class="logo">
        Ecom Store
    </div>

    <ul class="nav-links">

        <li>
            <a href="index.php">Home</a>
        </li>

        <li>
            <a href="products.php">Products</a>
        </li>

        <li>
            <a href="cart.php">Cart 🛒</a>
        </li>

    </ul>

</nav>


<section class="products-section">

    <h2>Your Shopping Cart</h2>


    <?php if (empty($cart)): ?>

        <p>Your cart is empty.</p>

        <br>

        <a href="products.php" class="product-btn">
            Continue Shopping
        </a>


    <?php else: ?>

        <?php

        $total = 0;

        foreach ($cart as $productId => $quantity):

            $productId = (int) $productId;

            $stmt = $pdo->prepare(
    "SELECT * FROM products WHERE id = ?"
);

$stmt->execute([$productId]);

$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    continue;
}
            $subtotal = $product["price"] * $quantity;

            $total += $subtotal;

        ?>


            <div class="product-card">

                <h3>
                    <?= htmlspecialchars($product["name"]) ?>
                </h3>


                <p class="category">

                    Category:

                    <?= htmlspecialchars($product["category"]) ?>

                </p>


                <p class="price">

                    Price:

                    ₹<?= number_format($product["price"], 2) ?>

                </p>


                <div class="quantity-controls">


                    <!-- Decrease quantity -->

                    <form action="update_cart.php" method="POST">

                        <input
                            type="hidden"
                            name="product_id"
                            value="<?= $productId ?>"
                        >

                        <input
                            type="hidden"
                            name="action"
                            value="decrease"
                        >

                        <button type="submit">
                            −
                        </button>

                    </form>


                    <!-- Current quantity -->

                    <span>
                        <?= $quantity ?>
                    </span>


                    <!-- Increase quantity -->

                    <form action="update_cart.php" method="POST">

                        <input
                            type="hidden"
                            name="product_id"
                            value="<?= $productId ?>"
                        >

                        <input
                            type="hidden"
                            name="action"
                            value="increase"
                        >

                        <button type="submit">
                            +
                        </button>

                    </form>


                </div>


                <p>

                    Available Stock:

                    <?= $product["stock"] ?>

                </p>


                <p>

                    Subtotal:

                    ₹<?= number_format($subtotal, 2) ?>

                </p>


                <a
                    href="remove_from_cart.php?id=<?= $productId ?>"
                    class="product-btn"
                >
                    Remove
                </a>


            </div>


        <?php endforeach; ?>


        <h2>

            Total:

            ₹<?= number_format($total, 2) ?>

        </h2>


        <br>


        <a
            href="clear_cart.php"
            class="product-btn"
        >
            Clear Cart
        </a>


        <br><br>


        <a
            href="products.php"
            class="product-btn"
        >
            Continue Shopping
        </a>

        <br><br>

<a href="checkout.php" class="product-btn">
    Proceed to Checkout
</a>


    <?php endif; ?>


</section>

</body>

</html>

