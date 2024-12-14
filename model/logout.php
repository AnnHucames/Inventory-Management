<?php
class Session {
    // Method to start the session
    public function start() {
        session_start();
    }

    // Method to destroy the session
    public function destroy() {
        session_destroy();
    }

    // Method to redirect to a specific URL
    public function redirect($url) {
        header("Location: $url");
        exit();
    }
}

// Using the Session class for logout
$session = new Session();
$session->start();   // Start the session
$session->destroy(); // Destroy the session
$session->redirect('../view/login.php'); // Redirect to login page
?>
