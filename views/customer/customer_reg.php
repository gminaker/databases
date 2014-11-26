<?php

/**
 * Provides content and functionatilty for Users to
 * register themselves on the site. 
 *
 * This is a PHP file. PHP files allow for both PHP and HTML 
 * code in the same files. php code is indicated by the start 
 * tag <?php and continues until its corresponding end tag ?>
 * All other code is assumed to be HTML. You can see that until
 * around line 80, it is all php code, and you must explicity 
 * delcare output (using echo, printf, etc.)
 *
 * PHP version 5
 *
 * @author     Gordon
 * @since      1.0
 *
 */
 

 
 // CHECK FOR AN INCOMING REQUEST BEFORE RENDERING THE PAGE. 
 // sorrry for yelling. 
 if ($_SERVER["REQUEST_METHOD"] == "POST") {
 		
      if (isset($_POST["UserReg"]) && $_POST["UserReg"] == "SUBMIT") {
		checkValsThenInsertIntoDB(	$_POST['user_name'],
		      						$_POST['user_address'],
		      						$_POST['user_phone'],
		      						$_POST['user_id'],
		      						$_POST['user_password'] );
      }
 }
 
 /**
 * Here we check the values that have just arrived from the form to make sure 
 * they are valid inputs, and if we decide they are, then we will insert them
 * into the DB. 
 *
 * @param string $name
 * @param string $addr
 * @param string $tel
 * @param string $id
 * @param string $pass
 *
 */
 function checkValsThenInsertIntoDB($name, $addr, $tel, $id, $pass){
	 
	$error = checkValues($name, $addr, $tel, $id, $pass); 
	
	if(!$error){
		insertIntoDB($name, $addr, $tel, $id, $pass);
	}
}

 /**
 * if we wanted to do a pre-check for NULL values, 
 * or check password strength, etc. this would be the place.
 *
 * @param string $name
 * @param string $addr
 * @param string $tel
 * @param string $id
 * @param string $pass
 *
 */
function checkValues($name, $addr, $tel, $id, $pass){
	global $error_stack;
	global $connection;
	
	$errors = false;
	
	if(empty($name)){
		array_push($error_stack, 'Please enter your name');
		$errors = true;
	}
	
	if(empty($addr)){
		array_push($error_stack, 'Please enter your address');
		$errors = true;
	}
	
	if(empty($tel)){
		array_push($error_stack, 'Please enter your phone number');
		$errors = true;
	}
	
	if(empty($id)){
		array_push($error_stack, 'Please enter an ID');
		$errors = true;
	}else{
		$results = $connection->query("SELECT * FROM customer WHERE cid = '$id';");
		
		if(!$results){
			array_push($error_stack, $connection->error);
		}else if($results->num_rows > 0){
			array_push($error_stack, 'This ID has already been used. Please try another one.');
			$errors = true;
			$results->free();
		} 
	}

	if(empty($pass)){
		array_push($error_stack, 'Please enter a password');
		$errors = true;
	}
	
	if(!empty($pass) && strlen($pass) < 4){
		array_push($error_stack, 'Please enter a password that is longer than four characters');
		$errors = true;
	}
		
	return $errors;
}


 /**
 * Insert the values received from the form into the customer 
 * table of our current DB connection. 
 *
 * @param string $name
 * @param string $addr
 * @param string $tel
 * @param string $id
 * @param string $pass
 *
 */
function insertIntoDB($name, $addr, $tel, $id, $pass){

	global $connection;
	global $error_stack;
	global $notice_stack;
	
 	$stmt = $connection->prepare("INSERT INTO customer (cid, c_password, c_name, address, phone) VALUES (?,?,?,?,?)");
          
    // Bind the title and pub_id parameters, 'sss' indicates 3 strings
    $stmt->bind_param("sssss", $id, $pass, $name, $addr, $tel);
    
    // Execute the insert statement
    $stmt->execute();
    
    // Print any errors if they occured
    if($stmt->error) {       
      array_push($error_stack, $stmt->error);
    } else {
      array_push($notice_stack, "Your user account: ".$id." has been created. Please login.");
      unset($_POST);
    }      
    
 }
 
 function postValue($key){
	 if(isset($_POST[$key])){
		 print $_POST[$key];
	 }
 }
 
 ?>
 
 <!-- PAGE RENDER BEGINS HERE -->
 
 <!-- Headers are wrapped in h1, h2, or h3 for styling purposes -->
 <h1>Customer Registration Page</h1>
    
 <!-- HTML forms are used to gather information that we are about to send via HTTP
	  Form inputs usually start with <input  and the type="hidden" input is used 
	  to identify the form as when it arrives. Note you need an <input type="submit"> to 
	  be able to actually send a form -->   
 <form method="post" name="register_user" action="">
 <input type="hidden" name="UserReg" value="SUBMIT">
	 <table>
		 <tr>
		 	<td>Name:</td>
		 	<td><input type="text" name="user_name" value="<?php postValue('user_name'); ?>" maxlength="20"></td>
		 </tr>
	 	 <tr>
		 	<td>Address:</td>
		 	<td><input type="text" name="user_address" value="<?php postValue('user_address'); ?>" maxlength="40"></td>
		 </tr>
	  	 <tr>
		 	<td>Phone:</td>
		 	<td><input type="tel" name="user_phone" value="<?php postValue('user_phone'); ?>"></td>
		 </tr>
		 <tr>
		 	<td>User ID:</td>
		 	<td><input type="text" name="user_id" maxlength="20" value="<?php postValue('user_id'); ?>"></td>
		 </tr>
		 <tr>
		 	<td>Password:</td>
		 	<td><input type="password" name="user_password" maxlength="20"></td>
		 </tr>
		 <tr>
		 	<td></td>
		 	<td><input type="submit"></td>
		 </tr>
	 </table>
 </form>