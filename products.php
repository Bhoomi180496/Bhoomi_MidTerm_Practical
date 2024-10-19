<?php
require('db_connection_mysqli.php');
// session_start();

// if (!isset($_SESSION['admin_logged_in'])) {
//     header("Location: login.php");
//     exit;
// }

function cleanInput($data) {
    return htmlspecialchars(trim($data));
}

// Initialize variables and error messages
$brandName = $description = $category = $stock = $price = $strapMaterial = "";
$brandNameErr = $descriptionErr = $categoryErr = $stockErr = $priceErr = $strapMaterialErr = "";

$productAddedBy = "Admin";  

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate Brand Name
    if (empty($_POST["brandName"])) {
        $brandNameErr = "Brand Name is required";
    } else {
        $brandName = cleanInput($_POST["brandName"]);
        if (!preg_match("/^[a-zA-Z0-9 ]*$/", $brandName)) {
            $brandNameErr = "Only letters, numbers, and white space allowed";
        }
    }

    // Validate Description
    if (empty($_POST["description"])) {
        $descriptionErr = "Description is required";
    } else {
        $description = cleanInput($_POST["description"]);
    }

    // Validate Category
    if (empty($_POST["category"])) {
        $categoryErr = "Category is required";
    } else {
        $category = cleanInput($_POST["category"]);
    }

    // Validate Stock (must be a positive integer)
    if (empty($_POST["stock"])) {
        $stockErr = "Stock is required";
    } else {
        $stock = cleanInput($_POST["stock"]);
        if (!filter_var($stock, FILTER_VALIDATE_INT, array("options" => array("min_range" => 1)))) {
            $stockErr = "Stock must be a positive integer";
        }
    }

    // Validate Price (must be a positive decimal number)
    if (empty($_POST["price"])) {
        $priceErr = "Price is required";
    } else {
        $price = cleanInput($_POST["price"]);
        if (!filter_var($price, FILTER_VALIDATE_FLOAT) || $price <= 0) {
            $priceErr = "Price must be a positive number";
        }
    }

    // Validate Strap Material
    if (empty($_POST["strapMaterial"])) {
        $strapMaterialErr = "Strap Material is required";
    } else {
        $strapMaterial = cleanInput($_POST["strapMaterial"]);
        if (!preg_match("/^[a-zA-Z ]*$/", $strapMaterial)) {
            $strapMaterialErr = "Only letters and white space allowed";
        }
    }

    // If no errors, insert the data into the database
    if (empty($brandNameErr) && empty($descriptionErr) && empty($categoryErr) && empty($stockErr) && empty($priceErr) && empty($strapMaterialErr)) {
        $stmt = $dbc->prepare("INSERT INTO watches (brand_name, description, category, stock, price, strap_material) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $brandName, $description, $category, $stock, $price, $strapMaterial);

        if ($stmt->execute()) {
            // Redirect to the index page after successful insert
            header("Location: index.php");
            exit;
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Watch</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
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
                    <a class="nav-link active" aria-current="page" href="index.php">Home</a>
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
    <h2>Add New Watch Product</h2>
    <form action="products.php" method="POST">
        <div class="mb-3">
            <label for="brandName" class="form-label">Brand Name</label>
            <input type="text" class="form-control" id="brandName" name="brandName" value="<?php echo $brandName; ?>">
            <span class="text-danger"><?php echo $brandNameErr; ?></span>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea class="form-control" id="description" name="description"><?php echo $description; ?></textarea>
            <span class="text-danger"><?php echo $descriptionErr; ?></span>
        </div>

        <div class="mb-3">
            <label for="category" class="form-label">Category</label>
            <select class="form-control" id="category" name="category">
                <option value="">Select Category</option>
                <option value="Luxury" <?php if ($category == 'Luxury') echo 'selected'; ?>>Luxury</option>
                <option value="Casual" <?php if ($category == 'Casual') echo 'selected'; ?>>Casual</option>
                <option value="Sports" <?php if ($category == 'Sports') echo 'selected'; ?>>Sports</option>
            </select>
            <span class="text-danger"><?php echo $categoryErr; ?></span>
        </div>

        <div class="mb-3">
            <label for="stock" class="form-label">Stock</label>
            <input type="number" class="form-control" id="stock" name="stock" value="<?php echo $stock; ?>">
            <span class="text-danger"><?php echo $stockErr; ?></span>
        </div>

        <div class="mb-3">
            <label for="price" class="form-label">Price</label>
            <input type="number" step="0.01" class="form-control" id="price" name="price" value="<?php echo $price; ?>">
            <span class="text-danger"><?php echo $priceErr; ?></span>
        </div>

        <div class="mb-3">
            <label for="strapMaterial" class="form-label">Strap Material</label>
            <select class="form-control" id="strapMaterial" name="strapMaterial">
                <option value="">Select Strap Material</option>
                <option value="Leather" <?php if ($strapMaterial == 'Leather') echo 'selected'; ?>>Leather</option>
                <option value="Metal" <?php if ($strapMaterial == 'Metal') echo 'selected'; ?>>Metal</option>
                <option value="Rubber" <?php if ($strapMaterial == 'Rubber') echo 'selected'; ?>>Rubber</option>
                <option value="Nylon" <?php if ($strapMaterial == 'Nylon') echo 'selected'; ?>>Nylon</option>
            </select>
            <span class="text-danger"><?php echo $strapMaterialErr; ?></span>
        </div>

        <button type="submit" class="btn btn-primary">Add Watch</button>
    </form>
</div>

<footer class="text-center mt-5">
    <p>© 2024 Watch Store. All rights reserved.</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
