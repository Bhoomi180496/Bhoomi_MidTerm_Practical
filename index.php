<?php
require('db_connection_mysqli.php');
session_start();

// Check if the admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}

// Fetch existing watches from the database
$query = "SELECT * FROM watches";
$result = mysqli_query($dbc, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Watches</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #74ebd5, #ACB6E5);
            height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .navbar {
            background-color: #00c6ff;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        .navbar-brand, .nav-link {
            color: white !important;
        }
        .container {
            background-color: white;
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
            margin-top: 30px;
            width: 100%;
            max-width: 1000px;
        }
        table th, table td {
            text-align: center;
            vertical-align: middle;
        }
        .btn-warning {
            background-color: #f0ad4e;
            border: none;
        }
        .btn-danger {
            background-color: #d9534f;
            border: none;
        }
        footer {
            background-color: #00c6ff;
            color: white;
            padding: 20px;
            text-align: center;
            margin-top: auto;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php">Watch Store</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link active" href="index.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="products.php">Add Products</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Existing Watch Products</h2>
        <a href="products.php" class="btn btn-primary">Add Product</a>
    </div>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Brand Name</th>
                <th>Description</th>
                <th>Category</th>
                <th>Stock</th>
                <th>Price</th>
                <th>Strap Material</th>
                <th>Actions</th> <!-- Edit and Delete buttons -->
            </tr>
        </thead>
        <tbody>
            <?php
            // Display each watch in a table row
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['brand_name']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['description']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['category']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['stock']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['price']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['strap_material']) . "</td>";
                    echo "<td>
                            <a href='edit.php?watch_id=" . $row['watch_id'] . "' class='btn btn-warning btn-sm'><i class='fas fa-pencil-alt'></i></a>
                            <a href='delete.php?delete_id=" . $row['watch_id'] . "' class='btn btn-danger btn-sm' onclick='return confirm(\"Are you sure?\");'><i class='fas fa-trash-alt'></i></a>
                          </td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='7' class='text-center'>No watches found.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<footer class="text-center mt-5">
    <p>© 2024 Watch Store. All rights reserved.</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
