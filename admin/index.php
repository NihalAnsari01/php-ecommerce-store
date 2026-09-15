<?php

require "auth.php";
require "../db_pdo.php";


// ===============================
// TOTAL PRODUCTS
// ===============================

$stmt = $pdo->prepare(
    "SELECT COUNT(*) FROM products"
);

$stmt->execute();

$totalProducts = $stmt->fetchColumn();


// ===============================
// TOTAL USERS
// ===============================

$stmt = $pdo->prepare(
    "SELECT COUNT(*) FROM users WHERE role = 'user'"
);

$stmt->execute();

$totalUsers = $stmt->fetchColumn();


// ===============================
// TOTAL ORDERS
// ===============================

$stmt = $pdo->prepare(
    "SELECT COUNT(*) FROM orders"
);

$stmt->execute();

$totalOrders = $stmt->fetchColumn();


// ===============================
// TOTAL SALES
// ===============================
// Cancelled orders are not counted as sales.

$stmt = $pdo->prepare(
    "SELECT COALESCE(SUM(total_amount), 0)
     FROM orders
     WHERE status != 'Cancelled'"
);

$stmt->execute();

$totalSales = $stmt->fetchColumn();


// ===============================
// ORDER STATUS COUNTS
// ===============================

$stmt = $pdo->prepare(
    "SELECT COUNT(*) FROM orders WHERE status = 'Pending'"
);

$stmt->execute();

$pendingOrders = $stmt->fetchColumn();


$stmt = $pdo->prepare(
    "SELECT COUNT(*) FROM orders WHERE status = 'Processing'"
);

$stmt->execute();

$processingOrders = $stmt->fetchColumn();


$stmt = $pdo->prepare(
    "SELECT COUNT(*) FROM orders WHERE status = 'Shipped'"
);

$stmt->execute();

$shippedOrders = $stmt->fetchColumn();


$stmt = $pdo->prepare(
    "SELECT COUNT(*) FROM orders WHERE status = 'Delivered'"
);

$stmt->execute();

$deliveredOrders = $stmt->fetchColumn();

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
        Admin Dashboard - Ecom Store
    </title>

    <style>

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }


        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            color: #222;
        }


        /* ===============================
           NAVBAR
        =============================== */

        .navbar {
            background: #111;
            color: white;
            padding: 18px 8%;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }


        .logo {
            font-size: 24px;
            font-weight: bold;
        }


        .nav-links {
            display: flex;
            gap: 25px;
            list-style: none;
        }


        .nav-links a {
            color: white;
            text-decoration: none;
        }


        .nav-links a:hover {
            text-decoration: underline;
        }


        /* ===============================
           DASHBOARD
        =============================== */

        .dashboard {
            padding: 50px 8%;
        }


        .dashboard h1 {
            margin-bottom: 10px;
        }


        .welcome {
            color: #666;
            margin-bottom: 35px;
        }


        /* ===============================
           STAT CARDS
        =============================== */

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 25px;
            margin-bottom: 40px;
        }


        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.08);
        }


        .stat-card h3 {
            color: #666;
            font-size: 16px;
            margin-bottom: 12px;
        }


        .stat-card .number {
            font-size: 32px;
            font-weight: bold;
        }


        /* ===============================
           ORDER STATUS
        =============================== */

        .section-title {
            margin-bottom: 20px;
        }


        .status-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
        }


        .status-card {
            background: white;
            padding: 22px;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.08);
        }


        .status-card h3 {
            font-size: 16px;
            margin-bottom: 10px;
        }


        .status-card p {
            font-size: 28px;
            font-weight: bold;
        }


        /* ===============================
           QUICK LINKS
        =============================== */

        .quick-links {
            margin-top: 40px;
        }


        .quick-links a {
            display: inline-block;
            background: #111;
            color: white;
            padding: 12px 20px;
            margin-right: 10px;
            margin-bottom: 10px;
            text-decoration: none;
            border-radius: 6px;
        }


        .quick-links a:hover {
            background: #333;
        }


        /* ===============================
           RESPONSIVE
        =============================== */

        @media (max-width: 900px) {

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .status-grid {
                grid-template-columns: repeat(2, 1fr);
            }

        }


        @media (max-width: 600px) {

            .navbar {
                flex-direction: column;
                gap: 15px;
                padding: 15px 5%;
            }


            .nav-links {
                gap: 12px;
                flex-wrap: wrap;
                justify-content: center;
            }


            .dashboard {
                padding: 35px 5%;
            }


            .stats-grid {
                grid-template-columns: 1fr;
            }


            .status-grid {
                grid-template-columns: 1fr;
            }

        }

    </style>

</head>


<body>


<!-- ===============================
     ADMIN NAVBAR
================================ -->

<nav class="navbar">

    <div class="logo">
        Ecom Store Admin
    </div>


    <ul class="nav-links">

        <li>
            <a href="index.php">
                Dashboard
            </a>
        </li>


        <li>
            <a href="products.php">
                Products
            </a>
        </li>


        <li>
            <a href="add_product.php">
                Add Product
            </a>
        </li>


        <li>
            <a href="orders.php">
                Orders
            </a>
        </li>


        <li>
            <a href="logout.php">
                Logout
            </a>
        </li>

    </ul>

</nav>



<!-- ===============================
     DASHBOARD CONTENT
================================ -->

<section class="dashboard">

    <h1>
        Admin Dashboard
    </h1>


    <p class="welcome">

        Welcome,
        <?= htmlspecialchars($_SESSION["user_name"]) ?>

    </p>



    <!-- ===============================
         MAIN STATISTICS
    ================================ -->

    <div class="stats-grid">


        <div class="stat-card">

            <h3>
                Total Products
            </h3>

            <div class="number">
                <?= (int) $totalProducts ?>
            </div>

        </div>



        <div class="stat-card">

            <h3>
                Total Users
            </h3>

            <div class="number">
                <?= (int) $totalUsers ?>
            </div>

        </div>



        <div class="stat-card">

            <h3>
                Total Orders
            </h3>

            <div class="number">
                <?= (int) $totalOrders ?>
            </div>

        </div>



        <div class="stat-card">

            <h3>
                Total Sales
            </h3>

            <div class="number">

                ₹<?= number_format($totalSales, 2) ?>

            </div>

        </div>


    </div>



    <!-- ===============================
         ORDER STATUS
    ================================ -->

    <h2 class="section-title">
        Order Status
    </h2>


    <div class="status-grid">


        <div class="status-card">

            <h3>
                Pending
            </h3>

            <p>
                <?= (int) $pendingOrders ?>
            </p>

        </div>



        <div class="status-card">

            <h3>
                Processing
            </h3>

            <p>
                <?= (int) $processingOrders ?>
            </p>

        </div>



        <div class="status-card">

            <h3>
                Shipped
            </h3>

            <p>
                <?= (int) $shippedOrders ?>
            </p>

        </div>



        <div class="status-card">

            <h3>
                Delivered
            </h3>

            <p>
                <?= (int) $deliveredOrders ?>
            </p>

        </div>


    </div>



    <!-- ===============================
         QUICK LINKS
    ================================ -->

    <div class="quick-links">

        <h2 class="section-title">
            Quick Actions
        </h2>


        <a href="products.php">
            Manage Products
        </a>


        <a href="add_product.php">
            Add Product
        </a>


        <a href="orders.php">
            Manage Orders
        </a>


        <a href="../index.php">
            View Store
        </a>

    </div>


</section>


</body>

</html>