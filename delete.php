<?php
require('db_connection_mysqli.php');
session_start();

// Check if the admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}

if (isset($_GET['delete_id'])) {
    $deleteId = $_GET['delete_id'];

    // Prepare delete query for watches table
    $deleteQuery = "DELETE FROM watches WHERE watch_id = ?"; 
    $deleteStmt = mysqli_prepare($dbc, $deleteQuery);

    if ($deleteStmt) {
        // Bind the parameter to the delete query
        mysqli_stmt_bind_param($deleteStmt, 'i', $deleteId);
        
        // Execute the statement and check for success
        if (mysqli_stmt_execute($deleteStmt)) {
            header("Location: index.php"); // Redirect back to the index page after deletion
            exit;
        } else {
            echo "Error deleting record: " . mysqli_error($dbc);
        }

        // Close the statement
        mysqli_stmt_close($deleteStmt);
    } else {
        echo "Error preparing statement: " . mysqli_error($dbc);
    }
} else {
    // Redirect to index.php if no delete_id is set
    header("Location: index.php");
    exit;
}
?>
