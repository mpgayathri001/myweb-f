<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Dashboard | BMMS Motors</title>

  <!-- ✅ Font Awesome -->
  <link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
      font-family: 'Poppins', sans-serif;
      background: url('dashboardimage1.png') no-repeat center center fixed;
      background-size: cover;
      color: #fff;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }

    header {
      width: 100%;
      background: rgba(0, 0, 0, 0.7);
      backdrop-filter: blur(6px);
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 15px 35px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.3);
    }

    .logo {
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .logo img {
      width: 50px;
      height: 50px;
      border-radius: 50%;
    }

    .logo h1 {
      color: #ffcc00;
      font-size: 24px;
    }

    .nav-buttons {
      display: flex;
      gap: 14px;
      flex-wrap: wrap;
      align-items: center;
    }

    .nav-buttons a,
    .dropdown-btn {
      text-decoration: none;
      color: #fff;
      background: #ff9800;
      padding: 10px 16px;
      border-radius: 6px;
      font-weight: 500;
      display: flex;
      align-items: center;
      gap: 8px;
      transition: 0.3s;
      cursor: pointer;
    }

    .nav-buttons a:hover,
    .dropdown-btn:hover {
      background: #e68900;
      transform: scale(1.03);
    }

    .dropdown {
      position: relative;
    }

    .dropdown-content {
      display: none;
      position: absolute;
      top: 45px;
      left: 0;
      background: rgba(0, 0, 0, 0.95);
      border-radius: 8px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.3);
      min-width: 200px;
      z-index: 10;
      flex-direction: column;
      overflow: hidden;
    }

    .dropdown-content a {
      color: #fff;
      padding: 10px 14px;
      text-decoration: none;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .dropdown-content a:hover {
      background: #ff9800;
      color: #000;
    }

    .dropdown.show .dropdown-content {
      display: flex;
    }

    main {
      flex: 1;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    main h2 {
      background: rgba(0, 0, 0, 0.6);
      padding: 20px 40px;
      border-radius: 12px;
      font-size: 26px;
      color: #ffeb3b;
    }

    footer {
      background: rgba(0, 0, 0, 0.6);
      text-align: center;
      padding: 15px;
      font-size: 14px;
      color: #ccc;
    }
  </style>
</head>
<body>

  <header>
    <div class="logo">
      <img src="bmmslogo.png" alt="BMMS Logo">
      <h1><i class="fa-solid fa-bolt"></i> BMMS MOTORS</h1>
    </div>

    <div class="nav-buttons">
      <!-- Customer -->
      <div class="dropdown" id="customerDropdown">
        <div class="dropdown-btn"> Customer ▾</div>
        <div class="dropdown-content">
          <a href="customer.php"><i class="fa-solid fa-user-plus"></i> Add Customer</a>
          <a href="view_customers.php"><i class="fa-solid fa-eye"></i> View Customers</a>
          <a href="edit_customer.php"><i class="fa-solid fa-user-pen"></i> Edit Customer</a>
        </div>
      </div>

      <!-- Service -->
      <div class="dropdown" id="serviceDropdown">
        <div class="dropdown-btn"> Service ▾</div>
        <div class="dropdown-content">
          <a href="job_card.php"><i class="fa-solid fa-file-lines"></i> Create Job Card</a>
          <a href="view_service_history.php"><i class="fa-solid fa-list"></i> View Service History</a>
        </div>
      </div>

      <!-- Lead -->
      <div class="dropdown" id="leadDropdown">
        <div class="dropdown-btn"> Lead ▾</div>
        <div class="dropdown-content">
          <a href="import_leads.php"><i class="fa-solid fa-file-import"></i> Import Leads</a>
          <a href="editleads.php"><i class="fa-solid fa-pen"></i> Edit Lead</a>
          <a href="viewleads.php"><i class="fa-solid fa-eye"></i> View Lead</a>
        </div>
      </div>

      <!-- Marketing -->
      <div class="dropdown" id="marketingDropdown">
        <div class="dropdown-btn">Marketing ▾</div>
        <div class="dropdown-content">
          <a href="addmarketing.php"><i class="fa-solid fa-plus"></i> Add Marketing</a>
          <a href="view_marketing.php"><i class="fa-solid fa-chart-line"></i> View Marketing</a>
        </div>
      </div>

      <!-- Attendance -->
      <div class="dropdown" id="attendanceDropdown">
        <div class="dropdown-btn"> Attendance ▾</div>
        <div class="dropdown-content">
          <a href="attendance_management.php"><i class="fa-solid fa-user-clock"></i> Manage Attendance</a>
          <a href="editattendance.php"><i class="fa-solid fa-pen-to-square"></i> View / Edit Attendance</a>
        </div>
      </div>

      <!-- Notification -->
      <a href="notification.php"> Notification</a>

      <!-- About -->
      <a href="about.php">About</a>

      <!-- Logout -->
      <a href="logout.php"> Logout</a>
    </div>
  </header>

  <main>
  </main>

  <footer>
    © <?php echo date("Y"); ?> BMMS Motors. All Rights Reserved.
  </footer>

  <script>
    // Dropdown toggle function
    document.querySelectorAll('.dropdown').forEach(dropdown => {
      const btn = dropdown.querySelector('.dropdown-btn');
      btn.addEventListener('click', (e) => {
        e.stopPropagation();
        document.querySelectorAll('.dropdown').forEach(d => {
          if (d !== dropdown) d.classList.remove('show');
        });
        dropdown.classList.toggle('show');
      });
    });

    // Close dropdowns when clicking outside
    document.addEventListener('click', () => {
      document.querySelectorAll('.dropdown').forEach(d => d.classList.remove('show'));
    });
  </script>

</body>
</html>
