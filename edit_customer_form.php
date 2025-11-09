<?php
include 'db.php';

$id = $_GET['id'] ?? 0;

// Fetch customer
$query = "SELECT * FROM customer WHERE id = $id";
$result = mysqli_query($conn, $query);
$customer = mysqli_fetch_assoc($result);

if (!$customer) {
    die("<h2 style='color:red;text-align:center;'>Customer not found!</h2>");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $aadhar = mysqli_real_escape_string($conn, $_POST['aadhar']);
    $vehicle_type = mysqli_real_escape_string($conn, $_POST['vehicle_type']);
    $ownership_type = mysqli_real_escape_string($conn, $_POST['ownership_type']);
    $model = mysqli_real_escape_string($conn, $_POST['model']);
    $vehicle_no = mysqli_real_escape_string($conn, $_POST['vehicle_no']);
    $motor_no = mysqli_real_escape_string($conn, $_POST['motor_no']);
    $chassis_no = mysqli_real_escape_string($conn, $_POST['chassis_no']);
    $controller_no = mysqli_real_escape_string($conn, $_POST['controller_no']);
    $source = mysqli_real_escape_string($conn, $_POST['source']);
    $registration_date = $_POST['registration_date'] ?? null;
    $delivery_date = $_POST['delivery_date'] ?? null;

    $payment_type = $_POST['payment_type'] ?? '';
    $discount_amount = $_POST['discount_amount'] ?? 0;
    $discount_given_by = mysqli_real_escape_string($conn, $_POST['discount_given_by'] ?? '');
    $bank_name = mysqli_real_escape_string($conn, $_POST['bank_name'] ?? '');
    $branch_name = mysqli_real_escape_string($conn, $_POST['branch_name'] ?? '');
    $bank_address = mysqli_real_escape_string($conn, $_POST['bank_address'] ?? '');

    // Payment details
    $payment_details = [];
    if (!empty($_POST['payment_mode'])) {
        $key = ($payment_type == 'cash') ? 'cash' : 'finance';
        for ($i = 0; $i < count($_POST['payment_mode']); $i++) {
            if (!empty($_POST['payment_mode'][$i]) || !empty($_POST['amount'][$i])) {
                $payment_details[$key][] = [
                    "amount" => $_POST['amount'][$i] ?? '0',
                    "date" => $_POST['payment_date'][$i] ?? '',
                    "mode" => $_POST['payment_mode'][$i] ?? ''
                ];
            }
        }
    }

    $payment_details_json = json_encode($payment_details, JSON_UNESCAPED_SLASHES);

    // Photo upload
    $photo = $customer['photo'];
    if (!empty($_FILES['photo']['name'])) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) mkdir($target_dir);
        $file_name = time() . "_" . basename($_FILES["photo"]["name"]);
        $target_file = $target_dir . $file_name;
        move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file);
        $photo = $target_file;
    }

    $update = "UPDATE customer SET 
        name='$name',
        phone='$phone',
        address='$address',
        aadhar='$aadhar',
        vehicle_type='$vehicle_type',
        ownership_type='$ownership_type',
        model='$model',
        vehicle_no='$vehicle_no',
        motor_no='$motor_no',
        chassis_no='$chassis_no',
        controller_no='$controller_no',
        source='$source',
        registration_date='$registration_date',
        delivery_date='$delivery_date',
        payment_type='$payment_type',
        discount_percent='$discount_amount',
        discount_given_by='$discount_given_by',
        bank_name='$bank_name',
        branch_name='$branch_name',
        bank_address='$bank_address',
        payment_details='$payment_details_json',
        photo='$photo'
        WHERE id=$id";

    if (mysqli_query($conn, $update)) {
        echo "<script>alert('✅ Customer updated successfully!'); window.location='edit_customer_form.php?id=$id';</script>";
    } else {
        echo "<script>alert('❌ Error updating customer: " . mysqli_error($conn) . "');</script>";
    }
}

$paymentData = json_decode($customer['payment_details'], true) ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Edit Customer | BMMS Motors</title>
<style>
body { font-family: 'Poppins', sans-serif; background-color: #000; color: #fff; margin: 0; padding: 0; }
.container { width: 80%; margin: 40px auto; background: #111; padding: 30px; border-radius: 12px; box-shadow: 0 0 15px rgba(255,255,255,0.1); }
h2 { text-align: center; color: #ffcc00; margin-bottom: 20px; }
form { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
label { display: block; margin-bottom: 5px; color: #ffcc00; }
input, textarea, select { width: 100%; padding: 10px; border: none; border-radius: 8px; background: #222; color: #fff; font-size: 15px; }
input[type="file"] { background: none; }
.photo-preview { grid-column: span 2; text-align: center; }
.photo-preview img { width: 150px; height: 150px; border-radius: 10px; border: 2px solid #ff9800; object-fit: cover; }
.payment-box { grid-column: span 2; border: 2px dashed #ff9800; padding: 15px; margin-top: 20px; border-radius: 10px; }
.payment-item { border: 1px solid #444; padding: 10px; margin-top: 10px; border-radius: 8px; background: #1b1b1b; position: relative; }
.delete-btn { position: absolute; top: 6px; right: 8px; background: none; border: none; color: red; font-size: 18px; cursor: pointer; transition: 0.2s; }
.delete-btn:hover { color: #ff4444; transform: scale(1.2); }
button { grid-column: span 2; background: #ff9800; color: white; border: none; padding: 12px; font-size: 16px; border-radius: 8px; cursor: pointer; transition: 0.3s; }
button:hover { background: #e68900; }
.add-btn { background: #333; color: #ffcc00; border: 1px dashed #ffcc00; padding: 6px 12px; cursor: pointer; border-radius: 6px; margin-top: 8px; }
</style>
</head>
<body>

<div class="container">
  <h2>✏️ Edit Customer - BMMS Motors</h2>
  <form method="POST" enctype="multipart/form-data">

    <div><label>Name</label><input type="text" name="name" value="<?= $customer['name']; ?>" required></div>
    <div><label>Phone</label><input type="text" name="phone" value="<?= $customer['phone']; ?>" required></div>
    <div style="grid-column: span 2;"><label>Address</label><textarea name="address" rows="2" required><?= $customer['address']; ?></textarea></div>
    <div><label>Aadhar</label><input type="text" name="aadhar" value="<?= $customer['aadhar']; ?>" required></div>
    <div><label>Vehicle Type</label><input type="text" name="vehicle_type" value="<?= $customer['vehicle_type']; ?>"></div>
    <div><label>Ownership Type</label><input type="text" name="ownership_type" value="<?= $customer['ownership_type']; ?>"></div>
    <div><label>Model</label><input type="text" name="model" value="<?= $customer['model']; ?>"></div>
    <div><label>Vehicle No</label><input type="text" name="vehicle_no" value="<?= $customer['vehicle_no']; ?>"></div>
    <div><label>Motor No</label><input type="text" name="motor_no" value="<?= $customer['motor_no']; ?>"></div>
    <div><label>Chassis No</label><input type="text" name="chassis_no" value="<?= $customer['chassis_no']; ?>"></div>
    <div><label>Controller No</label><input type="text" name="controller_no" value="<?= $customer['controller_no']; ?>"></div>
    <div><label>Source</label><input type="text" name="source" value="<?= $customer['source']; ?>"></div>
    <div><label>Registration Date</label><input type="date" name="registration_date" value="<?= $customer['registration_date']; ?>"></div>
    <div><label>Delivery Date</label><input type="date" name="delivery_date" value="<?= $customer['delivery_date']; ?>"></div>

    <div class="payment-box">
      <h3 style="color:#ffcc00;">💰 Payment Section</h3>
      <label><input type="radio" name="payment_type" value="cash" <?= ($customer['payment_type']=='cash')?'checked':''; ?>> Cash</label>
      <label><input type="radio" name="payment_type" value="finance" <?= ($customer['payment_type']=='finance')?'checked':''; ?>> Finance</label>

      <div id="cashBox" style="display:none;">
        <div id="cashPayments">
        <?php
        if (isset($paymentData['cash'])) {
            foreach ($paymentData['cash'] as $index => $pay) {
                echo '<div class="payment-item">
                    <button type="button" class="delete-btn" onclick="deletePayment('.$customer['id'].', \'cash\', '.$index.')">✖</button>
                    <label>Payment Mode</label>
                    <select name="payment_mode[]">
                        <option value="cash" '.($pay['mode']=='cash'?'selected':'').'>Cash</option>
                        <option value="upi" '.($pay['mode']=='upi'?'selected':'').'>UPI</option>
                        <option value="cheque" '.($pay['mode']=='cheque'?'selected':'').'>Cheque</option>
                    </select>
                    <label>Amount</label>
                    <input type="number" name="amount[]" value="'.$pay['amount'].'">
                    <label>Date of Payment</label>
                    <input type="date" name="payment_date[]" value="'.$pay['date'].'">
                </div>';
            }
        }
        ?>
        </div>
        <button type="button" class="add-btn" onclick="addPayment()">+ Add Another Payment</button>
      </div>

      <div id="financeBox" style="display:none;">
        <label>Bank Name</label><input type="text" name="bank_name" value="<?= $customer['bank_name']; ?>">
        <label>Branch Name</label><input type="text" name="branch_name" value="<?= $customer['branch_name']; ?>">
        <label>Bank Address</label><textarea name="bank_address" rows="2"><?= $customer['bank_address']; ?></textarea>

        <div id="financePayments">
        <?php
        if (isset($paymentData['finance'])) {
            foreach ($paymentData['finance'] as $index => $pay) {
                echo '<div class="payment-item">
                    <button type="button" class="delete-btn" onclick="deletePayment('.$customer['id'].', \'finance\', '.$index.')">✖</button>
                    <label>Payment Mode</label>
                    <select name="payment_mode[]">
                        <option value="cash" '.($pay['mode']=='cash'?'selected':'').'>Cash</option>
                        <option value="upi" '.($pay['mode']=='upi'?'selected':'').'>UPI</option>
                        <option value="cheque" '.($pay['mode']=='cheque'?'selected':'').'>Cheque</option>
                    </select>
                    <label>Amount</label>
                    <input type="number" name="amount[]" value="'.$pay['amount'].'">
                    <label>Date of Payment</label>
                    <input type="date" name="payment_date[]" value="'.$pay['date'].'">
                </div>';
            }
        }
        ?>
        </div>
        <button type="button" class="add-btn" onclick="addFinancePayment()">+ Add Another Payment</button>
      </div>

      <label>Discount Amount</label>
      <input type="number" name="discount_amount" step="0.01" value="<?= $customer['discount_percent']; ?>">
      <label>Discount Given By</label>
      <input type="text" name="discount_given_by" value="<?= $customer['discount_given_by']; ?>">
    </div>

    <div class="photo-preview">
      <label>Current Photo</label><br>
      <img id="preview" src="<?= $customer['photo'] ?: 'default_user.png'; ?>" alt="Customer Photo"><br><br>
      <input type="file" name="photo" accept="image/*" onchange="previewImage(event)">
    </div>

    <button type="submit">💾 Update Customer</button>
  </form>
</div>

<script>
function previewImage(e){var r=new FileReader();r.onload=function(){document.getElementById('preview').src=r.result};r.readAsDataURL(e.target.files[0]);}
function addPayment(){let c=document.getElementById('cashPayments');let d=document.createElement('div');d.className='payment-item';d.innerHTML=`<label>Payment Mode</label><select name="payment_mode[]"><option value="cash">Cash</option><option value="upi">UPI</option><option value="cheque">Cheque</option></select><label>Amount</label><input type="number" name="amount[]" placeholder="Enter Amount"><label>Date of Payment</label><input type="date" name="payment_date[]">`;c.appendChild(d);}
function addFinancePayment(){let c=document.getElementById('financePayments');let d=document.createElement('div');d.className='payment-item';d.innerHTML=`<label>Payment Mode</label><select name="payment_mode[]"><option value="cash">Cash</option><option value="upi">UPI</option><option value="cheque">Cheque</option></select><label>Amount</label><input type="number" name="amount[]" placeholder="Enter Amount"><label>Date of Payment</label><input type="date" name="payment_date[]">`;c.appendChild(d);}
document.querySelectorAll('input[name="payment_type"]').forEach(r=>r.addEventListener('change',toggleBox));
function toggleBox(){const c=document.getElementById('cashBox'),f=document.getElementById('financeBox');if(document.querySelector('input[name="payment_type"]:checked').value==='cash'){c.style.display='block';f.style.display='none';}else{f.style.display='block';c.style.display='none';}}
toggleBox();

function deletePayment(id, mode, index){
  if(confirm('Are you sure you want to delete this payment entry?')){
    fetch('delete_payment_entry.php',{
      method:'POST',
      headers:{'Content-Type':'application/x-www-form-urlencoded'},
      body:`id=${id}&mode=${mode}&index=${index}`
    })
    .then(res=>res.text())
    .then(data=>{alert(data);location.reload();});
  }
}
</script>
</body>
</html>
