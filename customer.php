<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}
include("db.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Basic details
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $aadhar = $_POST['aadhar'];
    $vehicle_type = $_POST['vehicle_type'];
    $ownership_type = $_POST['ownership_type'];
    $model = $_POST['model'];
    $vehicle_no = $_POST['vehicle_no'];
    $registration_date = $_POST['registration_date'];
    $delivery_date = $_POST['delivery_date'];
    $motor_no = $_POST['motor_no'];
    $chassis_no = $_POST['chassis_no'];
    $controller_no = $_POST['controller_no'];
    $source = $_POST['source'];

    // Payment details
    $payment_type = $_POST['payment_type'] ?? '';
    $discount_amount = $_POST['discount_amount'] ?? 0;
    $discount_given_by = $_POST['discount_given_by'] ?? '';
    $bank_name = $_POST['bank_name'] ?? '';
    $branch_name = $_POST['branch_name'] ?? '';
    $bank_address = $_POST['bank_address'] ?? '';

    // ✅ Build JSON for payment details
    $payment_details = [];

    if ($payment_type === "cash") {
        $cash_amounts = $_POST['cash_amount'] ?? [];
        $cash_dates = $_POST['cash_date'] ?? [];
        $cash_modes = $_POST['cash_mode'] ?? [];

        for ($i = 0; $i < count($cash_amounts); $i++) {
            if (!empty($cash_amounts[$i])) {
                $payment_details['cash'][] = [
                    "amount" => $cash_amounts[$i],
                    "date" => $cash_dates[$i] ?? '',
                    "mode" => $cash_modes[$i] ?? ''
                ];
            }
        }
    }

    if ($payment_type === "finance") {
        $finance_amounts = $_POST['finance_amount'] ?? [];
        $finance_dates = $_POST['finance_date'] ?? [];
        $finance_modes = $_POST['finance_mode'] ?? [];

        for ($i = 0; $i < count($finance_amounts); $i++) {
            if (!empty($finance_amounts[$i])) {
                $payment_details['finance'][] = [
                    "amount" => $finance_amounts[$i],
                    "date" => $finance_dates[$i] ?? '',
                    "mode" => $finance_modes[$i] ?? ''
                ];
            }
        }
    }

    $payment_details_json = json_encode($payment_details);

    // ✅ Handle photo upload
    $target_dir = "uploads/";
    if (!file_exists($target_dir)) mkdir($target_dir, 0777, true);

    $photo_path = "";
    if (!empty($_FILES["photo"]["name"])) {
        $photo_path = $target_dir . time() . "_" . basename($_FILES["photo"]["name"]);
        move_uploaded_file($_FILES["photo"]["tmp_name"], $photo_path);
    }

    // ✅ Insert into DB
    $stmt = $conn->prepare("INSERT INTO customer 
        (name, phone, address, aadhar, vehicle_type, ownership_type, model, motor_no, chassis_no, controller_no, source,
         vehicle_no, registration_date, delivery_date, photo, payment_type, discount_percent, discount_given_by,
         bank_name, branch_name, bank_address, payment_details)
        VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");

    $stmt->bind_param("ssssssssssssssssssssss",
        $name, $phone, $address, $aadhar, $vehicle_type, $ownership_type, $model, $motor_no, $chassis_no,
        $controller_no, $source, $vehicle_no, $registration_date, $delivery_date, $photo_path, $payment_type,
        $discount_amount, $discount_given_by, $bank_name, $branch_name, $bank_address, $payment_details_json
    );

    if ($stmt->execute()) {
        echo "<script>alert('✅ Customer added successfully!'); window.location='view_customers.php';</script>";
    } else {
        echo "<script>alert('❌ Database Error: ".$conn->error."');</script>";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Customer Form | BMMS Motors</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
body {
     font-family: 'Poppins', sans-serif;
    margin:0; padding:0;
    background: url('addcustomer1.jpg') no-repeat center center fixed;
    background-size: cover;
    color: #fff;
}

header {
    background: #1a0451ff;
    padding: 15px 40px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.logo { display:flex; align-items:center; gap:12px; }
.logo img { width:50px; border-radius:50%; }
.logo h1 { font-size:26px; color:#ffcc00; }

.nav-buttons a {
    text-decoration:none;
    background:#ff9800;
    padding:10px 18px;
    color:white;
    border-radius:6px;
    margin-left:10px;
}
.nav-buttons a:hover { background:#e68900; }

main { display:flex; justify-content:center; align-items:center; padding:30px; }

.form-box {
    background: #111;
    padding:30px 40px;
    border-radius:10px;
    width:800px;
}

h2 { text-align:center; margin-bottom:25px; color:#ffcc00; }

.form-row { display:flex; align-items:center; margin-bottom:15px; }
.form-row label { width:200px; display:flex; align-items:center; gap:10px; font-weight:bold; color:#ffeb3b; }
.form-row input, .form-row textarea, .form-row select { flex:1; padding:10px; border:none; border-radius:6px; background: rgba(255,255,255,0.1); color:white; outline:none; }
textarea { resize:none; height:60px; }

.btn {
    display:block;
    width:100%;
    padding:12px;
    background-color:#ff9800;
    border:none;
    border-radius:8px;
    color:white;
    font-size:18px;
    font-weight:600;
    cursor:pointer;
    transition:0.3s;
}
.btn:hover { background-color:#e68900; transform:scale(1.03); }

footer { background:#111; text-align:center; padding:15px; font-size:14px; color:#ccc; margin-top:20px; }
</style>
</head>
<body>

<header>
    <div class="logo">
        <img src="bmmslogo.png" alt="BMMS Logo">
        <h1>BMMS MOTORS ⚡</h1>
    </div>
    <div class="nav-buttons">
        <a href="dashboard.php">Home</a>
       
    </div>
</header>

<main>
<div class="form-box">
    <h2>Customer Details Form</h2>
    <form action="" method="POST" enctype="multipart/form-data">

        <div class="form-row">
            <label><i class="fa fa-user"></i> Name *</label>
            <input type="text" name="name" required>
        </div>

        <div class="form-row">
            <label><i class="fa fa-phone"></i> Phone *</label>
            <input type="text" name="phone" required>
        </div>

        <div class="form-row">
            <label><i class="fa fa-map-marker-alt"></i> Address *</label>
            <textarea name="address" required></textarea>
        </div>

        <div class="form-row">
            <label><i class="fa fa-id-card"></i> Aadhar *</label>
            <input type="text" name="aadhar" minlength="12" maxlength="12" required>
        </div>

        <div class="form-row">
            <label><i class="fa fa-bolt"></i> Vehicle Type *</label>
            <input type="text" name="vehicle_type" required>
        </div>

        <div class="form-row">
            <label><i class="fa fa-car"></i> Ownership Type *</label>
            <select name="ownership_type" required>
                <option value="">Select Type</option>
                <option value="T Board">T Board</option>
                <option value="Own Board">Own Board</option>
            </select>
        </div>

        <div class="form-row">
            <label><i class="fa fa-cogs"></i> Model *</label>
            <select name="model" required>
                <option value="">Select Model</option>
                <option value="Loader 600PV">Loader 600PV</option>
                <option value="Loader 600DV">Loader 600DV</option>
                <option value="Metro XL">Metro XL</option>
            </select>
        </div>

        <div class="form-row">
            <label><i class="fa fa-microchip"></i> Motor No</label>
            <input type="text" name="motor_no">
        </div>

        <div class="form-row">
            <label><i class="fa fa-id-card-alt"></i> Chassis No</label>
            <input type="text" name="chassis_no">
        </div>

        <div class="form-row">
            <label><i class="fa fa-microchip"></i> Controller No</label>
            <input type="text" name="controller_no">
        </div>

        <div class="form-row">
            <label><i class="fa fa-users"></i> Source</label>
            <input type="text" name="source" placeholder="Marketing / Lead / Broker">
        </div>

        <div class="form-row">
            <label><i class="fa fa-car"></i> Vehicle No</label>
            <input type="text" name="vehicle_no">
        </div>

        <div class="form-row">
            <label><i class="fa fa-calendar-alt"></i> Registration Date</label>
            <input type="date" name="registration_date">
        </div>

        <div class="form-row">
            <label><i class="fa fa-truck"></i> Delivery Date</label>
            <input type="date" name="delivery_date">
        </div>

        <!-- Payment Section -->
        <div class="form-box" style="border:2px solid #ffcc00; padding:20px; margin-top:25px;">
            <h3 style="color:#ffcc00; text-align:center;">Payment Details</h3>

            <div class="form-row">
                <label><i class="fa fa-hand-holding-usd"></i> Payment Type *</label>
                <div>
                    <label><input type="radio" name="payment_type" value="cash" required onclick="togglePayment('cash')"> Ready Cash
                    <input type="radio" name="payment_type" value="finance" onclick="togglePayment('finance')" style="margin-left:15px;"> Finance</label>
                </div>
            </div>

            <div id="cashSection" style="display:none;">
                <div id="cashPayments">
                    <div class="cashRow">
                        <div class="form-row">
                            <label><i class="fa fa-rupee-sign"></i> Amount *</label>
                            <input type="number" name="cash_amount[]" step="0.01">
                        </div>

                        <div class="form-row">
                            <label><i class="fa fa-calendar"></i> Date *</label>
                            <input type="date" name="cash_date[]">
                        </div>

                        <div class="form-row">
                            <label><i class="fa fa-credit-card"></i> Mode *</label>
                            <label><input type="radio" name="cash_mode[0]" value="cash"> Cash</label>
                            <label><input type="radio" name="cash_mode[0]" value="upi" style="margin-left:15px;"> UPI</label>
                            <label><input type="radio" name="cash_mode[0]" value="cheque" style="margin-left:15px;"> Cheque</label>
                        </div>
                        <hr style="border:1px dashed #555;">
                    </div>
                </div>
                <button type="button" class="btn" style="background:#007bff;" onclick="addCashPayment()">+ Add Another</button>
            </div>

            <div id="financeSection" style="display:none;">
                <div class="form-row">
                    <label><i class="fa fa-university"></i> Bank Name *</label>
                    <input type="text" name="bank_name">
                </div>

                <div class="form-row">
                    <label><i class="fa fa-code-branch"></i> Branch *</label>
                    <input type="text" name="branch_name">
                </div>

                <div class="form-row">
                    <label><i class="fa fa-map"></i> Bank Address *</label>
                    <textarea name="bank_address"></textarea>
                </div>

                <div id="financePayments">
                    <div class="financeRow">
                        <div class="form-row">
                            <label><i class="fa fa-rupee-sign"></i> Amount *</label>
                            <input type="number" name="finance_amount[]" step="0.01">
                        </div>

                        <div class="form-row">
                            <label><i class="fa fa-calendar"></i> Date *</label>
                            <input type="date" name="finance_date[]">
                        </div>

                        <div class="form-row">
                            <label><i class="fa fa-credit-card"></i> Mode *</label>
                            <label><input type="radio" name="finance_mode[0]" value="cash"> Cash</label>
                            <label><input type="radio" name="finance_mode[0]" value="upi" style="margin-left:15px;"> UPI</label>
                            <label><input type="radio" name="finance_mode[0]" value="cheque" style="margin-left:15px;"> Cheque</label>
                        </div>
                        <hr style="border:1px dashed #555;">
                    </div>
                </div>
                <button type="button" class="btn" style="background:#007bff;" onclick="addFinancePayment()">+ Add Another</button>
            </div>

            <div class="form-row">
                <label><i class="fa fa-percentage"></i> Discount Amount (₹)</label>
                <input type="number" step="0.01" name="discount_amount">
            </div>

            <div class="form-row">
                <label><i class="fa fa-user-tie"></i> Discount Given By</label>
                <input type="text" name="discount_given_by">
            </div>
        </div>

        <div class="form-row">
            <label><i class="fa fa-image"></i> Customer Photo *</label>
            <input type="file" name="photo" accept="image/*" required>
        </div>

        <button class="btn" type="submit">Submit</button>
    </form>
</div>
</main>

<footer>
    © <?php echo date("Y"); ?> BMMS Motors. All Rights Reserved.
</footer>

<script>
function togglePayment(type) {
    document.getElementById('cashSection').style.display = (type === 'cash') ? 'block' : 'none';
    document.getElementById('financeSection').style.display = (type === 'finance') ? 'block' : 'none';
}

let cashIndex = 1;
function addCashPayment() {
    const div = document.createElement('div');
    div.className = 'cashRow';
    div.innerHTML = `
        <div class="form-row">
            <label><i class="fa fa-rupee-sign"></i> Amount *</label>
            <input type="number" name="cash_amount[]" step="0.01">
        </div>
        <div class="form-row">
            <label><i class="fa fa-calendar"></i> Date *</label>
            <input type="date" name="cash_date[]">
        </div>
        <div class="form-row">
            <label><i class="fa fa-credit-card"></i> Mode *</label>
            <label><input type="radio" name="cash_mode[${cashIndex}]" value="cash"> Cash</label>
            <label><input type="radio" name="cash_mode[${cashIndex}]" value="upi" style="margin-left:15px;"> UPI</label>
            <label><input type="radio" name="cash_mode[${cashIndex}]" value="cheque" style="margin-left:15px;"> Cheque</label>
        </div>
        <hr style="border:1px dashed #555;">`;
    document.getElementById('cashPayments').appendChild(div);
    cashIndex++;
}

let financeIndex = 1;
function addFinancePayment() {
    const div = document.createElement('div');
    div.className = 'financeRow';
    div.innerHTML = `
        <div class="form-row">
            <label><i class="fa fa-rupee-sign"></i> Amount *</label>
            <input type="number" name="finance_amount[]" step="0.01">
        </div>
        <div class="form-row">
            <label><i class="fa fa-calendar"></i> Date *</label>
            <input type="date" name="finance_date[]">
        </div>
        <div class="form-row">
            <label><i class="fa fa-credit-card"></i> Mode *</label>
            <label><input type="radio" name="finance_mode[${financeIndex}]" value="cash"> Cash</label>
            <label><input type="radio" name="finance_mode[${financeIndex}]" value="upi" style="margin-left:15px;"> UPI</label>
            <label><input type="radio" name="finance_mode[${financeIndex}]" value="cheque" style="margin-left:15px;"> Cheque</label>
        </div>
        <hr style="border:1px dashed #555;">`;
    document.getElementById('financePayments').appendChild(div);
    financeIndex++;
}
</script>
</body>
</html>
