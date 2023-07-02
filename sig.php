<?php
error_reporting(1);
$name=$_POST['u'];
$pass=$_POST['v'];
$email=$_POST['b'];
$con=new mysqli('localhost','root','','log');
if(isset($_POST['SUBMIT']))
{
$sql="INSERT INTO logi(username,password) value ('$name','$pass')";
$con->query($sql);
echo "inserted";
$to = $email;
$subject = "Mail From website";
$txt ="Name = ". $name . "\r\n  Email = " . $email . "\r\n password =" . $pass;
$headers = "From: mpgayathri001@gmail.com" . "\r\n" .
"CC: somebodyelse@example.com";
else if($email!=NULL){
    mail($to,$subject,$txt,$headers);

header("Location: https://mpgayathri001.github.io/myweb-f/login.html");
}
}
?>
