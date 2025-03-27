<?php
session_start();

if (isset($_SESSION['user'])) {
header("Location: index.php");
} else {
header("Location: landing.php");
}
exit();
?>