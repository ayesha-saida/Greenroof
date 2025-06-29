<!--Form handling script-->
<?php
include 'connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = isset($_POST['phone']) ? $_POST['phone'] : ''; // ✅ Safely handle missing phone
    $location = $_POST['location'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

        // Optional: Stop if phone is empty
        if (empty($phone)) {
            echo "<p style='color:red;'>❌ Phone number is required.</p>";
            exit();
        }

    // Check if passwords match
    if ($password !== $confirm_password) {
        echo "<h3 style='color:red;'>❌ Passwords do not match. Please try again.</h3>";
        echo "<a href='buyerSignup.php'>Go back to registration</a>";
        exit(); // Stop further execution

    }
  
     // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

     // Insert into DB
    $sql = "INSERT INTO buyers (name, email, phone, location, password)
            VALUES ('$name', '$email',  '$phone', '$location', '$hashed_password')";

    if ($conn->query($sql) === TRUE) {
       // Redirect to login page
    header("Location: buyerLogin.php");
    exit(); // Always exit after a redirect

    } else {
        echo "❌ Error: " . $conn->error;
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>User Signup</title>
  <link rel="stylesheet" href="buyerSignup.css">
</head>

<body>
  <div class="form-container">
    <h2>User Account Register</h2>
    <form action="buyerSignup.php" method="POST">

      <label for="name">Name:</label>
      <input type="text" id="name" name="name" required>

      <label for="email">Email:</label>
      <input type="email" id="email" name="email" required>

      <label for="phone">Phone Number:</label>
      <input type="text" id="phone" name="phone" required>

      <label for="location">Location:</label>
      <select id="location" name="location" required>
        <option value="">Select your location</option>
        <option value="banani">Banani</option>
        <option value="mohakhali">Mohakhali</option>
        <option value="gulshan">Gulshan</option>
        <option value="mirpur">Mirpur</option>
        <option value="uttara">Uttara</option>
        <option value="notun_bazar">Notun Bazar</option>
        <option value="mogbazar">Mogbazar</option>
        <option value="bashaboo">Bashaboo</option>
        <option value="khilgaon">Khilgaon</option>
      </select>
      <label for="password">Password:</label>
      <input type="password" id="password" name="password" required>

      <label for="confirm_password">Confirm Password:</label>
      <input type="password" id="confirm_password" name="confirm_password" required>

      <button type="submit">Sign In</button>
    </form>
    <p>Already have an account? <a href="buyerLogin.php">Login here</a></p>
  </div>
</body>

</html>