<?php     
$name = $_POST['u'];
$message= $_POST['v'];
$email= $_POST['b'];
$to = $email;
$subject = "Mail From website";
$txt ="Name = ". $name . "\r\n  Email = " . $email . "\r\n Message =" . $message;
$headers = "From: noreply@yoursite.com" . "\r\n" .
"CC: somebodyelse@example.com";
if($email!=NULL){
    mail($to,$subject,$txt,$headers);
}
//redirect
header("Location:https://mpgayathri001.github.io/myweb-f/call.html");
?>   
 
