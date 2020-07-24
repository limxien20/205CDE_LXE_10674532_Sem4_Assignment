<?php
session_start();

$username = "";
$email    = "";
$errors = array(); 

$db = mysqli_connect('localhost', 'root', '', 'allinus');

// for user sign up
if (isset($_POST['sign_up'])) {
  // receive all input values from the form
  $username = mysqli_real_escape_string($db, $_POST['username']);
  $email = mysqli_real_escape_string($db, $_POST['email']); 
  $password1 = mysqli_real_escape_string($db, $_POST['password1']);
  $password2 = mysqli_real_escape_string($db, $_POST['password2']);

  // form validation: ensure that the form is correctly filled ...
  // (array_push()) corresponding error into $errors array
  if (empty($username)) { array_push($errors, "Caution: Username is required"); }
  if (empty($email)) { array_push($errors, "Caution: Email is required"); }
  if (empty($password1)) { array_push($errors, "Caution: Password is required"); }
  if ($password1 != $password2) {
	array_push($errors, "Error: Two passwords do not match with each other!");
  }

  // check the database to make sure user does not already exist with the same username and/or email
  
  $user_check_query = "SELECT * FROM users WHERE username='$username' OR email='$email' LIMIT 1";
  $result = mysqli_query($db, $user_check_query);
  $user = mysqli_fetch_assoc($result);
  
  if ($user) { // if user exists
    if ($user['username'] === $username) {
      array_push($errors, "Username existed");
    }

    if ($user['email'] === $email) {
      array_push($errors, "E-mail existed");
    }
  }

  // Finally, register user if there are no errors in the form
  if (count($errors) == 0) {
  	$password = ($password1);
  	$query = "INSERT INTO users (username, email, password) 
  			  VALUES('$username', '$email', '$password')";
  	mysqli_query($db, $query);
  	$_SESSION['username'] = $username;
  	$_SESSION['success'] = "Logged in. Welcome and enjoy the latest music ";
  	header('location: welcome.php');
  }
}


// LOGIN USER
if (isset($_POST['login_user'])) {
  $username = mysqli_real_escape_string($db, $_POST['username']);
  $password = mysqli_real_escape_string($db, $_POST['password']);

  if (empty($username)) {
  	array_push($errors, "Caution: Username is required");
  }
  if (empty($password)) {
  	array_push($errors, "Caution: Password is required");
  }

  if (count($errors) == 0) {
  	$password = ($password);
  	$query = "SELECT * FROM users WHERE username='$username' AND password='$password'";
  	$results = mysqli_query($db, $query);
  	if (mysqli_num_rows($results) == 1) {
  	  $_SESSION['username'] = $username;
  	  $_SESSION['success'] = "Logged in SUCCESSFULLY";
  	  header('location: welcome.php');
  	}else {
  		array_push($errors, "Error: Wrong username/password !!! Please re-enter.");
  	}
  }
}

?>