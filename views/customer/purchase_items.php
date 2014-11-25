<?php

/**
 * Provides content and functionatilty for Customers to Purchase Items. 
 *
 * <long description>
 *
 * PHP version 5
 *
 * @author     Gordon
 * @since     1.0
 *
 */
 
  if ($_SERVER["REQUEST_METHOD"] == "POST") {
 		
      if (isset($_POST["purchase_items"]) && $_POST["purchase_items"] == "SUBMIT") {
		checkValsThenInsertIntoDB($_POST['user_ccno'],
		      					  $_POST['user_ccex']);
      }
 }
 
 
 function checkValsThenInsertIntoDB($cc_no, $cc_ex){
	 
	$msg = checkValues($cc_no, $cc_ex); 
	$expected_date = calculateExpectedDate();
	
	if($msg){
		printf("%s", $msg);
	}else{
		insertIntoDB($cc_no, $cc_ex, $expected_date);
	}
}

function calculateExpectedDate(){
	// TODO make a funcion that returns the expected delivery date for the order. 
	return 0;
}

/**
 * here we should verify the user's id, password, 
 * and credit card information. 
 *
 * @param string $user_id
 * @param string $user_pass
 * @param string $cc_no
 * @param string $cc_ex
 * @param string $all
 *
 */
function checkValues($cc_no, $cc_ex){
	
	//TODO check user id & password.
	return null;
}


 /**
 * Insert the values received from the form into the customer 
 * table of our current DB connection. 
 *
 * @param string $user_id
 * @param string $user_pass
 * @param string $cc_no
 * @param string $cc_ex
 * @param string $all
 *
 */
function insertIntoDB($cc_no, $cc_ex, $expected_date){

	global $connection;
 	$stmt = $connection->prepare("INSERT INTO purchase (p_date, p_cid, cardNo, expiryDate, expectedDate) VALUES (?,?,?,?,?)");
    
    // Bind the title and pub_id parameters, 'sss' indicates 3 strings
    $stmt->bind_param("sssss", date('Y-m-d h:i:s', time()), $_SESSION['user_id'], $cc_no, $cc_ex, $expected_date);
    
    // Execute the insert statement
    $stmt->execute();
    
    $error_stack = array();
    
    // Print any errors if they occured
    if($stmt->error) {       
      array_push($error_stack, $stmt->error);
    }     
    
    $receiptId = $stmt->insert_id;
    
    if(isset($_SESSION['cart'])){
	    foreach($_SESSION['cart'] as $key => $value) {
		    
			    $upc = $key;
			    $qty = $value;
			    
				if ($qty > 0){
				  	
				$stmt = $connection->prepare("INSERT INTO purchaseitem (pi_receiptId, pi_upc, pi_quantity) VALUES (?,?,?)");
				  // Bind the title and pub_id parameters, 'sss' indicates 3 strings
				$stmt->bind_param("sss",$receiptId, $upc, $qty);
				  // Execute the insert statement
				$stmt->execute();
				  // Print any errors if they occured
				if($stmt->error) {       
				  array_push($error_stack, $stmt->error);
				}    
			}
		}
	}
	
	if(count($error_stack) > 0){
		print("Errors occurred:");
		print_r($error_stack);
	}else{
		print("Order was submitted successfully!");
	}
              
    
 }
 
 	 