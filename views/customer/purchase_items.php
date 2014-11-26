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
 
 if($_SERVER["REQUEST_METHOD"] == "POST") {
 		
      if (isset($_POST["purchase_items"]) && $_POST["purchase_items"] == "SUBMIT") {
		checkValsThenInsertIntoDB($_SESSION['user_id'], 
								  $_POST['user_ccno'],
		      					  $_POST['user_ccex'],
		      					  $_POST['cart']);
      }
 }
 
 
 function checkValsThenInsertIntoDB($user_id, $cc_no, $cc_ex, $all){
	 
	$error = checkValues($cc_no, $cc_ex, $all); 
	$expected_date = calculateExpectedDate();
	
	if(!$error){
		insertIntoDB($user_id, $cc_no, $cc_ex, $expected_date, $all);
	}
}

function calculateExpectedDate(){
	// TODO make a funcion that returns the expected delivery date for the order. 
	$deliveries_per_day = 5;
	$result = getMaxExpectedDate();
	
	if ($result['count'] < $deliveries_per_day) {
		$expectedDate = $result['expectedDate'];
	} else {
		$expectedDate = $result['expectedDate']->add(new DateInterval('P1D'));
	}
	return $expectedDate;
}

function getMaxExpectedDate(){
	global $error_stack;
	
	global $connection;
	if ($stmt = $connection->query("SELECT expectedDate
									FROM purchase
									WHERE expectedDate = (
									SELECT MAX(expectedDate) 
									FROM purchase);")) {
		$count = $stmt->num_rows;
		//var_dump($count);
		if($count == 0){
			array_push($error_stack, "Sorry bud, no expected dates");
			exit();
		} 
		$row = $stmt->fetch_assoc();
		$result = array();
		$today = new DateTime();
		$maxExpected = new DateTime($row['expectedDate']);
		if ($maxExpected < $today) {
			$result['expectedDate'] = $today->format('Y-m-d H:i:s');
			$result['count'] = 0;	
		} else {
			$result['expectedDate'] = $maxExpected->format('Y-m-d H:i:s');
			$result['count'] = $count;	
		}
		
		return $result;						
	}
	
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
function checkValues($cc_no, $cc_ex, $all){
	global $error_stack;
	
	$errors = false;
	
	if(empty($cc_no)){
		array_push($error_stack, 'Please go back enter a credit card number.');
		$errors = true;
	}
	
	if(empty($cc_ex)){
		array_push($error_stack, 'Please go back enter a credit card expiry date.');
		$errors = true;
	}
	
	if(!is_numeric($cc_no) || strlen($cc_no) != 16){
		array_push($error_stack, 'Please enter a valid credit card number');
		$errors = true;
	}
	
	return $errors;
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
function insertIntoDB($user_id, $cc_no, $cc_ex, $expected_date, $all){

	global $connection;
	global $error_stack;
	
 	$stmt = $connection->prepare("INSERT INTO purchase (p_date, p_cid, cardNo, expiryDate, expectedDate) VALUES (?,?,?,?,?)");
    
    // Bind the title and pub_id parameters, 'sss' indicates 3 strings
    $stmt->bind_param("sssss", date('Y-m-d h:i:s', time()), $user_id, $cc_no, $cc_ex, $expected_date);
    
    // Execute the insert statement
    $stmt->execute();
    
    // Print any errors if they occured
    if($stmt->error) {       
      array_push($error_stack, $stmt->error. ' at line number '.__LINE__);
    }     
    
    $receiptId = $stmt->insert_id;
    
    if(isset($all)){
	    foreach($all as $key => $value) {
		    
			    $upc = $key;
			    $qty = $qty;
			    
				if ($qty > 0){
				  	
				$stmt = $connection->prepare("INSERT INTO purchaseitem (pi_receiptId, pi_upc, pi_quantity) VALUES (?,?,?)");
				  // Bind the title and pub_id parameters, 'sss' indicates 3 strings
				$stmt->bind_param("sss",$receiptId, $upc, $qty);
				  // Execute the insert statement
				$stmt->execute();
				  // Print any errors if they occured
				if($stmt->error) {       
				  array_push($error_stack, $stmt->error.__LINE__);
				}    
			}
		}
	}
	
	if(empty($error_stack) || $receiptId){
		renderReceipt($receiptId);
	}         
 }
 
 function renderReceipt($receiptID){
	 print 'Receipt #:'.$receiptID;
 }