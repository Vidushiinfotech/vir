<?php
/**
 * ezIGBT Logout file
 */
if (!session_id())
    session_start();

if (session_id() && isset($_SESSION['user_id']) && ((int) $_SESSION['user_id'])) {
    session_unset();
    session_destroy();
    header("Location: ./index.php");
    exit();
} else {
    die('No access!');
}
