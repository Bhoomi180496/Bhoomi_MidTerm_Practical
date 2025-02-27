<?php
session_start();
require('db_connection_mysqli.php');

// Initialize variables
$username = $password = "";
$usernameErr = $passwordErr = "";

// Function to sanitize form inputs
function cleanInput($data) {
    return htmlspecialchars(trim($data));
}

// Validate login inputs
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST["username"])) {
        $usernameErr = "Username is required";
    } else {
        $username = cleanInput($_POST["username"]);
    }

    if (empty($_POST["password"])) {
        $passwordErr = "Password is required";
    } else {
        $password = cleanInput($_POST["password"]);
    }

    // If all validations pass, proceed with login
    if (empty($usernameErr) && empty($passwordErr)) {
        $query = "SELECT * FROM admins WHERE username = ?";
        $stmt = mysqli_prepare($dbc, $query);
        mysqli_stmt_bind_param($stmt, 's', $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($row = mysqli_fetch_assoc($result)) {
            if (password_verify($password, $row['password'])) {
                $_SESSION['admin_logged_in'] = true;
                header("Location: index.php"); 
                exit;
            } else {
                $passwordErr = "Invalid username or password.";
            }
        } else {
            $usernameErr = "Invalid username or password.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #74ebd5, #ACB6E5);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .login-container {
            background-color: white;
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
            max-width: 450px;
            width: 100%;
        }
        .login-container h2 {
            color: #6c757d;
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 30px;
            text-align: center;
        }
        .form-label {
            color: #495057;
            font-weight: 500;
        }
        .form-control {
            border-radius: 10px;
            padding: 10px;
        }
        .btn-primary {
            background-color: #00c6ff;
            border: none;
            padding: 10px;
            border-radius: 10px;
            transition: background-color 0.3s ease;
        }
        .btn-primary:hover {
            background-color: #0072ff;
        }
        .text-danger {
            font-size: 0.9rem;
        }
        .text-center a {
            color: #0072ff;
            text-decoration: none;
        }
        .text-center a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="login-container">
    <h2>Admin Login</h2>
    <form method="POST" action="login.php">
        <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input type="text" class="form-control" name="username" value="<?php echo $username; ?>" required>
            <span class="text-danger"><?php echo $usernameErr; ?></span>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" name="password" required>
            <span class="text-danger"><?php echo $passwordErr; ?></span>
        </div>

        <button type="submit" class="btn btn-primary w-100">Login</button>
    </form>
    <div class="text-center mt-3">
        <p>Don't have an account? <a href="register.php">Register here</a></p>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
