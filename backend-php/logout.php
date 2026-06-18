<?php
// 1. Core connection initialization
require_once 'config/db.php';

// 2. Clear all session variables from memory
$_SESSION = array();

// 3. Destroy the cookie tracking session inside the browser completely
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 4. Terminate the server-side session object
session_destroy();

// 5. Redirect the user back to the login gateway
header("Location: login.php");
exit;
?>