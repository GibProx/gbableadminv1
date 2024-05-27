<?php
// Start the session
session_start();

// Unset all session variables
$_SESSION = array();

// If you are using cookies for sessions, delete the session cookie as well
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params['path'], $params['domain'],
        $params['secure'], $params['httponly']
    );
}

// Destroy the session
session_destroy();

// Redirect to the login page or any other page you want
header('Location: /pages/login.php');
exit;
?>
