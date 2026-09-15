<?php

require "auth.php";
require "../db_pdo.php";


// Get all orders with customer information

$stmt = $pdo->prepare(
    "SELECT
        orders.*,
        users.name,
        users.email
     FROM orders
     JOIN users
        ON orders.user_id = users.id
     ORDER BY orders.id DESC"
);

$stmt->execute();

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

    <title>
        Manage Orders - Ecom Store
    </title>

    <link
        rel="stylesheet"
        href="../style.css"
    >

</head>


<body>


<!-- =========================
     NAVBAR
========================= -->

<nav class="navbar">

    <div class="logo">
        Ecom Store Admin
    </div>


    <ul class="nav-links">

        <li>
            <a href="../index.php">
                Store
            </a>
        </li>


        <li>
            <a href="products.php">
                Products
            </a>
        </li>


        <li>
            <a href="orders.php">
                Orders
            </a>
        </li>


        <li>
            <a href="add_product.php">
                Add Product
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
        Manage Orders
    </h2>


    <br>


    <?php if (empty($orders)): ?>


        <div class="product-card">

            <h3>
                No orders found.
            </h3>

        </div>


    <?php else: ?>


        <div style="overflow-x: auto;">


            <table
                border="1"
                cellpadding="12"
                cellspacing="0"
                width="100%"
            >


                <thead>

                    <tr>

                        <th>
                            Order ID
                        </th>

                        <th>
                            Customer
                        </th>

                        <th>
                            Email
                        </th>

                        <th>
                            Total
                        </th>

                        <th>
                            Status
                        </th>

                        <th>
                            Date
                        </th>

                        <th>
                            Action
                        </th>

                    </tr>

                </thead>


                <tbody>


                    <?php foreach ($orders as $order): ?>


                        <tr>


                            <td>

                                #<?= $order["id"] ?>

                            </td>


                            <td>

                                <?= htmlspecialchars(
                                    $order["name"]
                                ) ?>

                            </td>


                            <td>

                                <?= htmlspecialchars(
                                    $order["email"]
                                ) ?>

                            </td>


                            <td>

                                ₹<?= number_format(
                                    $order["total_amount"],
                                    2
                                ) ?>

                            </td>


                            <td>

                                <?= htmlspecialchars(
                                    $order["status"]
                                ) ?>

                            </td>


                            <td>

                                <?= date(
                                    "d M Y, h:i A",
                                    strtotime(
                                        $order["created_at"]
                                    )
                                ) ?>

                            </td>


                            <td>

                                <a
                                    href="update_order_status.php?id=<?= $order["id"] ?>"
                                >
                                    Manage
                                </a>

                            </td>


                        </tr>


                    <?php endforeach; ?>


                </tbody>


            </table>


        </div>


    <?php endif; ?>


</section>


</body>

</html>