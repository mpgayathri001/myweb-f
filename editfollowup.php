<?php
// view_followup.php
include("db.php");
date_default_timezone_set('Asia/Kolkata');

// Get search query from GET
$q = isset($_GET['q']) ? trim($_GET['q']) : '';

// Build query with optional search
if ($q !== '') {
    // search by name or phone
    $sql = "SELECT id, customer_name, customer_phone, followup_date, vehicle_type, remaining_amount
            FROM followup
            WHERE customer_name LIKE ? OR customer_phone LIKE ?
            ORDER BY followup_date DESC";
    $stmt = $conn->prepare($sql);
    $like = "%{$q}%";
    $stmt->bind_param("ss", $like, $like);
} else {
    $sql = "SELECT id, customer_name, customer_phone, followup_date, vehicle_type, remaining_amount
            FROM followup
            ORDER BY followup_date DESC";
    $stmt = $conn->prepare($sql);
}

$stmt->execute();
$result = $stmt->get_result();

// Compute grand remaining total for displayed rows
$grand_remaining = 0;
$rows = $result->fetch_all(MYSQLI_ASSOC);

// If you want to compute again for display after fetch, iterate
foreach ($rows as $r) {
    $grand_remaining += (float)($r['remaining_amount'] ?? 0);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>View Follow-ups | BMMS Motors</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <style>
    body { font-family: Poppins, sans-serif; background:#f5f7fb; color:#222; padding:24px; }
    .container { max-width:1100px; margin:0 auto; }
    h1 { color:#ff8c00; text-align:center; margin-bottom:18px; }
    .top-bar { display:flex; gap:12px; align-items:center; margin-bottom:16px; }
    .top-bar form { flex:1; display:flex; gap:8px; }
    input[type="search"] { flex:1; padding:8px 10px; border-radius:6px; border:1px solid #ccc; }
    button { padding:8px 12px; border-radius:6px; border:none; cursor:pointer; background:#ff9800; color:white; }
    .add-btn { background:#28a745; }
    table { width:100%; border-collapse:collapse; background:white; box-shadow:0 6px 18px rgba(0,0,0,0.06); border-radius:8px; overflow:hidden; }
    th, td { padding:12px 10px; text-align:left; border-bottom:1px solid #eef2f6; }
    th { background:#f7fafc; color:#333; }
    tr:hover td { background:#fcfcfd; }
    .actions { text-align:center; }
    .edit-btn { background:#007bff; color:white; padding:6px 10px; border-radius:6px; text-decoration:none; display:inline-block; }
    .summary { text-align:right; margin-top:12px; font-weight:700; color:#2d6a4f; }
    @media (max-width:800px) {
      table, thead, tbody, th, td, tr { display:block; }
      thead tr { display:none; }
      tr { margin-bottom:12px; border-radius:8px; overflow:hidden; background:white; box-shadow:0 4px 10px rgba(0,0,0,0.03); }
      td { display:flex; justify-content:space-between; padding:12px; border-bottom:none; }
      td::before { font-weight:600; color:#666; }
      td:nth-child(1)::before { content: "Customer"; }
      td:nth-child(2)::before { content: "Phone"; }
      td:nth-child(3)::before { content: "Follow-up Date"; }
      td:nth-child(4)::before { content: "Vehicle Type"; }
      td:nth-child(5)::before { content: "Remaining"; }
      td:nth-child(6)::before { content: "Edit"; }
    }
  </style>
</head>
<body>
  <div class="container">
    <h1>Follow-ups</h1>

    <div class="top-bar">
      <form method="GET" action="">
        <input type="search" name="q" placeholder="Search by customer name or phone" value="<?php echo htmlspecialchars($q); ?>">
        <button type="submit">Search</button>
        <a href="view_followup.php" style="text-decoration:none;"><button type="button">Reset</button></a>
      </form>

      <a href="followup.php"><button type="button" class="add-btn">+ New Follow-up</button></a>
    </div>

    <table>
      <thead>
        <tr>
          <th>Customer Name</th>
          <th>Phone</th>
          <th>Follow-up Date</th>
          <th>Vehicle Type</th>
          <th>Remaining Amount (₹)</th>
          <th class="actions">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if (count($rows) === 0): ?>
          <tr><td colspan="6" style="text-align:center; padding:20px;">No follow-ups found.</td></tr>
        <?php else: ?>
          <?php foreach ($rows as $r): ?>
            <tr>
              <td><?php echo htmlspecialchars($r['customer_name']); ?></td>
              <td><?php echo htmlspecialchars($r['customer_phone']); ?></td>
              <td><?php echo htmlspecialchars($r['followup_date']); ?></td>
              <td><?php echo htmlspecialchars($r['vehicle_type']); ?></td>
              <td>₹ <?php echo number_format((float)($r['remaining_amount'] ?? 0), 2); ?></td>
              <td class="actions">
                <a class="edit-btn" href="edittfollowup.php?id=<?php echo intval($r['id']); ?>">Edit</a>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>

    <div class="summary">
      Grand Remaining Total: ₹ <?php echo number_format((float)$grand_remaining, 2); ?>
    </div>
  </div>
</body>
</html>
