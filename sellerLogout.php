<?php
session_start();
session_destroy();
header("Location: sellerLogin.php"); // or home page
exit();
?>
