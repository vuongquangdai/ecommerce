<?php
require('connection.inc.php');
require('functions.inc.php');

$name=get_safe_value($con,$_POST['name']);
$email=get_safe_value($con,$_POST['email']);
$password=get_safe_value($con,$_POST['password']);

$check_user=mysqli_num_rows(mysqli_query($con,"select * from users where email='$email'"));

if ($check_user>0) {
	echo "email_present";
} else {
	date_default_timezone_set('Asia/Ho_Chi_Minh');
	$added_on=date('Y-m-d h:i:s');
	mysqli_query($con,"insert into users(name,email,mobile,password,added_on) values('$name','$email','$mobile','$password','$added_on')");
	echo "insert";
}
?>