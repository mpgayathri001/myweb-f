<?php
include 'db.php'; // database connection

$search = "";
if (isset($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, $_GET['search']);
}

$query = "SELECT * FROM customer 
          WHERE name LIKE '%$search%' 
          OR vehicle_type LIKE '%$search%' 
          OR vehicle_no LIKE '%$search%' 
          ORDER BY id DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>View Customers | BMMS Motors</title>
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
      text-shadow: 2px 2px 4px #000;
      letter-spacing: 1px;
    }

    .search-container {
      text-align: center;
      margin: 20px 0;
    }

    .search-container input[type="text"] {
      width: 60%;
      padding: 10px;
      border-radius: 8px;
      border: none;
      outline: none;
      font-size: 16px;
    }

    .search-container button {
      padding: 10px 18px;
      background: #ff9800;
      border: none;
      border-radius: 8px;
      color: white;
      font-weight: 500;
      cursor: pointer;
      transition: 0.3s;
    }

    .search-container button:hover {
      background: #e68900;
      transform: scale(1.05);
    }

    /* Card Container */
    .cards {
      display: grid;
      grid-template-columns: repeat(4, 1fr); /* ✅ 4 per row */
      gap: 20px; /* ✅ small consistent gap */
      padding: 30px 60px; /* space from edges */
      justify-items: center;
      box-sizing: border-box;
    }

    /* Square Card */
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
      transition: 0.3s;
      text-align: center;
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

    .view-btn {
      background: #ff9800;
      color: white;
      padding: 8px 15px;
      border-radius: 6px;
      text-decoration: none;
      transition: 0.3s;
      font-weight: 500;
    }

    .view-btn:hover {
      background: #e68900;
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

    /* ✅ Responsive fix for small screens */
    @media (max-width: 1200px) {
      .cards {
        grid-template-columns: repeat(3, 1fr);
      }
    }

    @media (max-width: 900px) {
      .cards {
        grid-template-columns: repeat(2, 1fr);
      }
    }

    @media (max-width: 600px) {
      .cards {
        grid-template-columns: 1fr;
      }
    }

  </style>
</head>
<body>

  <header>👁 View Customers - BMMS Motors</header>

  <div class="search-container">
    <form method="GET" action="">
      <input type="text" name="search" placeholder="Search by Name or Vehicle Type or Vehicle No" value="<?php echo htmlspecialchars($search); ?>">
      <button type="submit">Search</button>
    </form>
  </div>

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
              <a href="customer_details.php?id=' . $row['id'] . '" class="view-btn">View Full</a>
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
