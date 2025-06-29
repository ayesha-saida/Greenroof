<?php
session_start();
if (!isset($_SESSION['seller_name'])) {
    header("Location: sellerLogin.php?redirect= sellerUpload.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Seller Product Upload</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="sellerUpload.css">
</head>
<body class="container py-5">

  <div class="top-bar">
    <h2 class="mb-0">Upload New Product</h2>
  </div>

  <form action="uploadProductHandler.php" method="POST" enctype="multipart/form-data" class="row g-4">
    <div class="col-sm-12 col-md-6">
      <label for="name" class="form-label">Product Name</label>
      <input type="text" name="name" id="name" class="form-control" required />
    </div>

    <div class="col-sm-12 col-md-6">
      <label for="price" class="form-label">Price (Tk)</label>
      <input type="number" name="price" id="price" class="form-control" step="0.01" required />
    </div>

    <div class="col-sm-12 col-md-6">
      <label for="category" class="form-label">Category</label>
      <select name="category" id="category" class="form-select" required>
        <option value="vegetables">Vegetables</option>
        <option value="leaf-vegetables">Leaf Vegetables</option>
        <option value="fruits">Fruits</option>
        <option value="dairy">Dairy</option>
        <option value="dry-fruits-&-nuts">Dry Fruits & Nuts</option>
      </select>
    </div>

    <div class="col-sm-12 col-md-6">
      <label for="area" class="form-label">Area</label>
      <select name="area" id="area" class="form-select" required>
        <option value="banani">Banani</option>
        <option value="mohakhali">Mohakhali</option>
        <option value="gulshan">Gulshan</option>
           <option value="Mirpur">Mirpur</option>
        <option value="uttara">Uttara</option>
        <option value="notun_bazar">Notun Bazar</option>
        <option value="mogbazar">Mogbazar</option>
        <option value="rampura">Rampura</option>
        <option value="bashaboo">Bashaboo</option>
        <option value="khilgaon">Khilgaon</option>
      </select>
    </div>

    <div class="col-sm-12 col-md-6">
      <label for="image" class="form-label">Product Image</label>
      <input type="file" name="image" id="image" class="form-control" accept="image/*" required />
    </div>

    <div class="col-sm-12 col-md-6">
      <label for="seller" class="form-label">Seller Name</label>
      <input type="text" name="seller" id="seller" class="form-control" required
             value="<?php echo isset($_SESSION['seller_name']) ? htmlspecialchars($_SESSION['seller_name']) : ''; ?>" />
    </div>

    <div class="col-12 text-end">
      <button type="submit" class="btn btn-success px-4">Upload Product</button>
      <button class="btn btn-success px-4" onclick="window.location.href='sellerDashboard.php'">Back </button>
    </div>
  </form>

</body>
</html>
