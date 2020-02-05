<?php

require_once('../variables.php');

if (   !isset($_POST['username'])
	|| !isset($_POST['email'])
	|| !isset($_POST['password'])
	|| !isset($_POST['confirm_password'])
	|| !isset($_POST['phone_number'])
	|| !isset($_POST['birthday'])
	|| !isset($_POST['first_name'])
	|| !isset($_POST['last_name'])
	 ) {
	die("Error: Invalid Parameters");
}

//Sanitize & Validate All Inputs
$username = $_POST['username'];
$email = strtolower($_POST['email']);
$password = $_POST['password'];
$confirm_password = $_POST['confirm_password'];
$phone_number = $_POST['phone_number'];
$birthday = $_POST['birthday'];
$first_name = $_POST['first_name'];
$last_name = $_POST['last_name'];

//Make sure 'username' only includes a-z, A-Z, numbers, and dashes.
if( preg_match("/^[a-zA-Z0-9]*$/u", $username) != 1 ){
	die("Username can only contain letters and numbers.");
}

//Clean email and check if valid
$email = filter_var($email, FILTER_SANITIZE_EMAIL); //Remove illegal characters
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { //Validate email
	die("Email address is invalid.");
}

//Make sure the password is the correct length
if( !(strlen($password) > 9 && strlen($password) < 50) ){
	die("That password is not long enough.");
}

//Make sure that the password fields match
if( $password != $confirm_password ){
	die("Passwords do not match.");
}

//Make sure the phone number is 10 digits
$phone_number = preg_replace('/[^0-9]/', '', $phone_number);
if(strlen($phone) != 10) {
	die("Phone number is invalid.");
}

//Check that the birthday is valid
function validateDate($date, $format = 'Y-m-d'){
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) === $date;
}
if( !validateDate($birthday) ){
	die("Birthdate is invalid.");
}

//Check their age requirement
if (time() < strtotime('+18 years', strtotime($birthday))) {
   die('Sorry, you are not old enough to sign up.');
}

//Make sure 'first_name' and 'last_name' only includes a-z, A-Z, and spaces.
if( preg_match("/^[a-zA-Z ]*$/u", $first_name) != 1 ){
	die("First name can only contain letters.");
}
if( preg_match("/^[a-zA-Z ]*$/u", $last_name) != 1 ){
	die("Last name can only contain letters.");
}

try {

	//Create connection
	$con = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);

	//Check if that USERNAME exists
	$stmt = $con->prepare("SELECT * FROM users WHERE username = ? OR email = ? LIMIT 1");
	$stmt->execute([$username, $email]);
    if($stmt->fetchColumn()){
    	die("That username or email already exists.");
    }

	//Create the user
    $stmt = $con->prepare("INSERT INTO users (username, email, password, phone_number, birthday, first_name, last_name, join_date) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
	$success = $stmt->execute([ $username, strtolower($email), $password, $phone_number, $birthday, $first_name, $last_name ]);
	if(  $success  ){
		die("success");
	}else{
		die("Something went wrong. Please try again later.");
	}

} catch(PDOException $e) {
	die("Failed: Something went wrong..");
	// die( "Failed to add break - " . $e->getMessage());
}

?>