<?php
include 'db.php';

$id = intval($_POST['id'] ?? 0);
$mode = $_POST['mode'] ?? '';
$index = intval($_POST['index'] ?? 0);

if (!$id || !$mode) {
    exit("❌ Invalid request");
}

// Fetch current payment details
$q = mysqli_query($conn, "SELECT payment_details FROM customer WHERE id=$id");
if (!$q) exit("❌ Database error: " . mysqli_error($conn));

$row = mysqli_fetch_assoc($q);
if (!$row) exit("❌ Customer not found");

$payments = json_decode($row['payment_details'], true);
if (!isset($payments[$mode][$index])) exit("❌ Payment entry not found");

// Remove that entry
array_splice($payments[$mode], $index, 1);
if (empty($payments[$mode])) unset($payments[$mode]);

$updated_json = mysqli_real_escape_string($conn, json_encode($payments));

// Save back
if (mysqli_query($conn, "UPDATE customer SET payment_details='$updated_json' WHERE id=$id")) {
    echo "✅ Payment entry deleted successfully.";
} else {
    echo "❌ Failed to delete payment entry.";
}
?>
