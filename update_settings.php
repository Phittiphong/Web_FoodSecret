<?php
session_start(); // Start the session to use session variables

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "You must be logged in to view this page.";
    exit;
}

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['theme'])) {
        $theme = $_POST['theme'];

        // Validate the theme value
        if ($theme === 'light' || $theme === 'dark') {
            $_SESSION['theme'] = $theme;
        } else {
            $_SESSION['theme'] = 'light'; // Default to light if invalid value
        }
    }

    // Redirect back to settings page or any other page
    header('Location: settings.php');
    exit;
}
?>
