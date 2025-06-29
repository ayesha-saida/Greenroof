<?php
session_start();

unset($_SESSION['cart']); // clear cart
session_unset();     // Remove all  session variables
session_destroy(); // Destroy the session
header("Location: home.php");
exit();



session_destroy();   

?>
