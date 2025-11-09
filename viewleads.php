<?php
include 'db.php';

// --- FILTER HANDLING ---
$where = [];
if (!empty($_GET['name'])) $where[] = "client_name LIKE '%" . $_GET['name'] . "%'";
if (!empty($_GET['location'])) $where[] = "location LIKE '%" . $_GET['location'] . "%'";
if (!empty($_GET['phone'])) $where[] = "phone LIKE '%" . $_GET['phone'] . "%'";
if (!empty($_GET['status'])) $where[] = "status='" . $_GET['status'] . "'";
$where_sql = $where ? "WHERE " . implode(" AND ", $where) : "";

// --- PAGINATION ---
$limit = 20;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

$total_query = mysqli_query($conn, "SELECT COUNT(*) AS total FROM leads $where_sql");
$total = mysqli_fetch_assoc($total_query)['total'];
$total_pages = ceil($total / $limit);

// Fetch leads with limit
$query = "SELECT * FROM leads $where_sql ORDER BY id DESC LIMIT $start, $limit";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>View Leads | BMMS Motors</title>
<style>
body { font-family: 'Poppins', sans-serif; background: #111; color: #fff; }
.container { width: 95%; margin: 20px auto; background: #1e1e1e; padding: 20px; border-radius: 10px; }
h2 { text-align: center; color: #ffcc00; }
table { width: 100%; border-collapse: collapse; margin-top: 15px; }
th, td { border: 1px solid #444; padding: 8px; text-align: center; }
th { background: #222; color: #ffcc00; }
input, select { background: #222; color: #fff; border: 1px solid #444; border-radius: 4px; padding: 5px; }
button { background: #ff9800; color: white; border: none; border-radius: 5px; padding: 6px 12px; cursor: pointer; }
button:hover { background: #e68900; }
.print-btn { background: #4caf50; margin-left: 10px; }
.pagination { text-align: center; margin-top: 15px; }
.pagination a { color: #ffcc00; margin: 0 5px; text-decoration: none; }
.pagination a.active { font-weight: bold; text-decoration: underline; }
.filter-form { text-align: center; margin-bottom: 10px; }
.filter-form input, .filter-form select { margin-right: 10px; }
@media print {
  .filter-form, .pagination, .print-btn { display: none; }
  body { background: #fff; color: #000; }
  table, th, td { border: 1px solid #000; color: #000; }
}
</style>
</head>
<body>
<div class="container">
  <h2>📋 View Leads - BMMS Motors</h2>

  <form method="GET" class="filter-form">
    <input type="text" name="name" placeholder="Name" value="<?= $_GET['name'] ?? '' ?>">
    <input type="text" name="location" placeholder="Location" value="<?= $_GET['location'] ?? '' ?>">
    <input type="text" name="phone" placeholder="Phone" value="<?= $_GET['phone'] ?? '' ?>">
    <select name="status">
      <option value="">All Status</option>
      <option value="Interested" <?= (($_GET['status'] ?? '')=='Interested')?'selected':'' ?>>Interested</option>
      <option value="Waiting List" <?= (($_GET['status'] ?? '')=='Waiting List')?'selected':'' ?>>Waiting List</option>
      <option value="Not Interested" <?= (($_GET['status'] ?? '')=='Not Interested')?'selected':'' ?>>Not Interested</option>
    </select>
    <button type="submit">🔍 Filter</button>
    <button type="button" class="print-btn" onclick="window.print()">🖨️ Print</button>
  </form>

  <table>
    <thead>
      <tr>
        <th>ID</th>
        <th>Client Name</th>
        <th>Location</th>
        <th>Phone</th>
        <th>Source</th>
        <th>Reminder Date</th>
        <th>Reminder Time</th>
        <th>Status</th>
        <th>Created At</th>
      </tr>
    </thead>
    <tbody>
      <?php if (mysqli_num_rows($result) > 0): ?>
        <?php while($row = mysqli_fetch_assoc($result)): ?>
          <tr>
            <td><?= $row['id'] ?></td>
            <td><?= htmlspecialchars($row['client_name']) ?></td>
            <td><?= htmlspecialchars($row['location']) ?></td>
            <td><?= htmlspecialchars($row['phone']) ?></td>
            <td><?= htmlspecialchars($row['source']) ?></td>
            <td><?= $row['reminder_date'] ?></td>
            <td><?= $row['reminder_time'] ?></td>
            <td><?= $row['status'] ?></td>
            <td><?= $row['created_at'] ?></td>
          </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr><td colspan="9">No leads found</td></tr>
      <?php endif; ?>
    </tbody>
  </table>

  <div class="pagination">
    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
      <a href="?page=<?= $i ?>&<?= http_build_query(array_merge($_GET, ['page'=>$i])) ?>" 
         class="<?= ($i == $page) ? 'active' : '' ?>"><?= $i ?></a>
    <?php endfor; ?>
  </div>
</div>
</body>
</html>
