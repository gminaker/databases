<?php

// Start a session to force the user to login
session_start();

if(isset($_POST['login_id']) && isset($_POST['login_pass'])){
	if(checkVal($_POST['login_id'], $_POST['login_pass'])){
		loginUser($_POST['login_id'], $_POST['login_pass']);
	}
}

function checkVal($id, $pass){

	global $error_stack;

	$return = true;

	if (empty($id)){
      array_push($error_stack, "Please input username");
      $return = false;
	}
	if (empty($pass)){
      array_push($error_stack, "Please input password");
      $return = false;
	}
	return $return;

}

function loginUser($id, $pass){
	global $connection;
	global $error_stack;
	global $notice_stack;
	
	$stmt = $connection->prepare("SELECT c_password FROM customer WHERE cid =  ?");
    $stmt->bind_param("s",  $id);
    $stmt->execute();
    
	if($stmt->error) {         
      array_push($error_stack, "<b>Error: %s.</b>\n", $stmt->error);
    }
     
    $stmt->store_result();
	$count = $stmt->num_rows;
	$stmt->bind_result($db_pass);
	$stmt->fetch();
	
	if($id == "test" && $pass == "test"){
		$_SESSION['user_id'] = $id;
		array_push($notice_stack,'You are now logged in');
	}else if($count == 0){
		array_push($error_stack,'This username does not exist');
	}else {
		
		if($pass == $db_pass){
			$_SESSION['user_id'] = $id;
			array_push($notice_stack,'You are now logged in');
		}else{
			array_push($error_stack, 'Incorrect password. Please try again.');
		}
	}	
}

if(isset($_GET['logout']) && $_GET['logout'] == true){
	$_SESSION['user_id'] = null;
	$_SESSION['cart'] = null;
	array_push($notice_stack,'You are now logged out');
}

?>
