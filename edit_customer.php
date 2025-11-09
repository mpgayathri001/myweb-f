<?php
include 'db.php';

// Fetch customers
$query = "SELECT * FROM customer ORDER BY id DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Edit Customers | BMMS Motors</title>
<style>
  body {
    font-family: 'Poppins', sans-serif;
    background-color: #000;
    color: #fff;
    margin: 0;
    padding: 0;
    min-height: 100vh;
  }

  header {
    text-align: center;
    background: #111;
    padding: 20px;
    font-size: 24px;
    color: #ffcc00;
    letter-spacing: 1px;
  }

  .cards {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 25px;
    padding: 20px;
    justify-items: center;
  }

  .card {
    background: #1a1a1a;
    border-radius: 15px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.6);
    width: 250px;
    height: 250px;
    padding: 15px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: space-between;
    text-align: center;
    transition: 0.3s;
  }

  .card:hover {
    transform: scale(1.05);
    background: #222;
  }

  .card img {
    width: 100px;
    height: 100px;
    object-fit: cover;
    border-radius: 50%;
    border: 2px solid #ff9800;
    margin-bottom: 10px;
  }

  .card-details h3 {
    font-size: 18px;
    color: #ffcc00;
    margin: 5px 0;
  }

  .card-details p {
    font-size: 14px;
    color: #ddd;
    margin: 3px 0;
  }

  .edit-btn {
    background: #00bcd4;
    color: white;
    padding: 8px 15px;
    border-radius: 6px;
    text-decoration: none;
    font-weight: 500;
    transition: 0.3s;
  }

  .edit-btn:hover {
    background: #0097a7;
  }

  footer {
    text-align: center;
    padding: 15px;
    background: #111;
    color: #aaa;
    position: fixed;
    width: 100%;
    bottom: 0;
    font-size: 14px;
  }
</style>
</head>
<body>

<header>✏️ Edit Customers - BMMS Motors</header>

<div class="cards">
  <?php
  if (mysqli_num_rows($result) > 0) {
      while ($row = mysqli_fetch_assoc($result)) {
          $photoPath = !empty($row['photo']) ? $row['photo'] : 'default_user.png';
          echo '
          <div class="card">
            <img src="' . htmlspecialchars($photoPath) . '" alt="Customer Photo">
            <div class="card-details">
              <h3>' . htmlspecialchars($row['name']) . '</h3>
              <p>📞 ' . htmlspecialchars($row['phone']) . '</p>
              <p>🏠 ' . htmlspecialchars($row['address']) . '</p>
            </div>
            <a href="edit_customer_form.php?id=' . $row['id'] . '" class="edit-btn">Edit</a>
          </div>';
      }
  } else {
      echo "<p style='text-align:center; color:#ffcc00;'>No customers found.</p>";
  }
  ?>
</div>

<footer>© <?php echo date('Y'); ?> BMMS Motors. All Rights Reserved.</footer>

</body>
</html>
