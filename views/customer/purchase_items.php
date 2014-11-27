<?php

/**
 * Provides content and functionatilty for Customers to Purchase Items. 
 *
 * <long description>
 *
 * PHP version 5
 *
 * @author     Gordon, Mike
 * @since     1.0
 *
 */
 date_default_timezone_set('America/Los_Angeles');
 
 if($_SERVER["REQUEST_METHOD"] == "POST") {
 		
      if (isset($_POST["purchase_items"]) && $_POST["purchase_items"] == "SUBMIT") {
		checkValsThenInsertIntoDB($_SESSION['user_id'], 
								  $_POST['user_ccno'],
		      					  $_POST['user_ccex'],
		      					  $_SESSION['cart']);
      }
 }
 
 
 function checkValsThenInsertIntoDB($user_id, $cc_no, $cc_ex, $all){
	 
	$error = checkValues($cc_no, &$cc_ex, $all); 
	$expected_date = calculateExpectedDate();
	
	$obj_date = DateTime::createFromFormat('m/Y', $cc_ex);
	$cc_ex =  $obj_date->getTimestamp();
	
	if(!$error){
		insertIntoDB($user_id, $cc_no, $cc_ex, $expected_date, $all);
	}
}

function calculateExpectedDate(){ 
	global $connection;
 	global $error_stack;

	$deliveries_per_day = 5;
	$result = $connection->query("SELECT count(*)
									FROM purchase
									WHERE deliveredDate is NULL;");

	if (!$result){
 		array_push($error_stack, $connection->error );
 	} else {
 		$row = $result->fetch_assoc();
		$days = floor($row['count(*)'] / $deliveries_per_day);
		$expectedDate = date('Y-m-d', strtotime('+'.$days.' days'));
		return $expectedDate;
 	}
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
	
	if(isset($cc_ex)){
		$cc_ex = date("Y-m-d H:i:s", strtotime($cc_ex));
	}
	
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
	$connection->autocommit(FALSE);
	$connection->begin_transaction();
	$transactions = array();
	
 	$stmt = $connection->prepare("INSERT INTO purchase (p_date, p_cid, cardNo, expiryDate, expectedDate) VALUES (?,?,?,?,?)");
    
    // Bind the title and pub_id parameters, 'sss' indicates 3 strings
    $stmt->bind_param("sssss", date('Y-m-d h:i:s', time()), $user_id, $cc_no, $cc_ex, $expected_date);
    
    // Execute the insert statement
    $stmt->execute();
    
    // Print any errors if they occured
    if($stmt->error) {       
      array_push($error_stack, $stmt->error. ' at line number '.__LINE__);
		$connection->rollback();
		$connection->autocommit(TRUE);
		array_push($error_stack, "This transaction was rolled back");
		return;
    }     
    
    $receiptId = $stmt->insert_id;
    
    if(isset($all)){
	    foreach($all as $key => $value) {
		    
			    $upc = $key;
			    $qty = $value;
			    
				if ($qty > 0){
				  	
					$stmt = $connection->prepare("INSERT INTO purchaseitem (pi_receiptId, pi_upc, pi_quantity) VALUES (?,?,?)");
					$stmt2 = $connection->prepare("UPDATE item SET stock = stock - ? WHERE it_upc = ?");
					// Bind the title and pub_id parameters, 'sss' indicates 3 strings
					$stmt->bind_param("sss",$receiptId, $upc, $qty);
					$stmt2->bind_param("is",$qty, $upc);
					// Execute the insert statement
					$stmt->execute();
					$stmt2->execute();
					// Print any errors if they occured
					if($stmt->error) {       
						array_push($error_stack, $stmt->error.__LINE__);
						$connection->rollback();
						$connection->autocommit(TRUE);
						array_push($error_stack, "This transaction was rolled back");
						return;
					} else if($stmt2->error){
						array_push($error_stack, $stmt2->error.__LINE__);
						$connection->rollback();
						$connection->autocommit(TRUE);
						array_push($error_stack, "This transaction was rolled back");
					} 
				}
			}
	}
	
	$connection->commit();
	$connection->autocommit(TRUE);
	$_SESSION['cart'] = NULL; //TODO: IS THIS OK???????
	if(empty($error_stack) || $receiptId){
		renderReceipt($receiptId, $all, $expected_date);
	}         
 }
 
 function renderReceipt($receiptId, $all, $expected_date){
	 renderTablePrefix($receiptId);
	 $cart = getAllReceiptItems($receiptId);
	 renderTablePostfix($expected_date);
	 renderBill($cart);
 }
 
 function renderBill($cart){
 		$costs = getCost($cart);

 		print '<table>
 				 <tr>
 					 <td>Subtotal: </td>
 					 <td style="text-align:right">$'.$costs['$cost'].'</td>
 				 </tr>
 				 <tr>
 					 <td>Tax: </td>
 					 <td style="text-align:right">$'.$costs['$tax'].'</td>
 				 </tr>
 				 <tr>
 					 <td>Total Charged: </td>
 					 <td style="text-align:right">$'.$costs['$total'].'</td>
 				 </tr>
 			 </table>';
 }
 
 function getCost($cart){
	 global $connection;
	 global $error_stack;

	 $cost = 0.0;
	 foreach($cart as $key => $value){
		 $upc = $value['upc'];
		 $result = $connection->query("SELECT * FROM item WHERE it_upc = $upc");
		 if(!$result){
			 array_push($error_stack,  $connection->error);
		 }else if($result->num_rows != 0){
			 while($row = $result->fetch_assoc()) {
				 $cost += intval($value) * floatval($row["price"]);
			 }
			 $result->free();
		 }
	 }

	 $cost = number_format($cost, 2);
	 $tax = number_format(round($cost * 0.05, 2), 2);
	 $total = number_format(($cost + round($cost*0.05, 2)), 2);
	 return array('$cost'=>$cost, '$tax'=>$tax, '$total'=>$total);
 }
 
 function getAllReceiptItems($receiptId){
 	global $connection;
 	global $error_stack;
	
 	$stmt = $connection->prepare("SELECT * FROM purchaseitem WHERE pi_receiptId =  ?");
 	$stmt->bind_param("s",  $receiptId);
 	$stmt->execute();
	    
 	if($stmt->error) {       
 		array_push($error_stack, $stmt->error);
		return NULL;
 	} 
	    
 	$stmt->store_result();
 	$count = $stmt->num_rows;
 	$stmt->bind_result($receiptId, $upc, $qty);
		
 	if($count == 0){
 		array_push($error_stack,"This receipt doesn't have any items.");
		return NULL;
 	}
 	$i=0;
	$cart = array();
	
 	while($stmt->fetch()){
		$values = array();
		$values['upc'] = $upc;
		$values['qty'] = $qty;
		$cart[$i] = $values;
		$row = getItemInfo($upc);
    	print '<tr>';
	   		print '<td>'.$qty.'</td>';
	    	print '<td>'.$row["it_upc"].'</td>';
	    	print '<td>'.$row["it_title"].'</td>';
	    	print '<td>'.$row["type"].'</td>';
	    	print '<td>'.$row["category"].'</td>';
	    	print '<td>'.$row["company"].'</td>';
	    	print '<td style="text-align:right">'.$row["year"].'</td>';
	    	print '<td style="text-align:right">'.$row["price"].'</td></tr>';
 		$i++;
 	}
 	print  '</table>';
	return $cart;
 }
 
 function getItemInfo($upc){
 	global $connection;
 	global $error_stack;
	
 	$stmt = $connection->prepare("SELECT * FROM item WHERE it_upc =  ?");
 	$stmt->bind_param("s", $upc);
 	$stmt->execute();
	    
 	if($stmt->error) {       
 		array_push($error_stack, $stmt->error);
 	}
	     
 	$stmt->store_result();
 	$count = $stmt->num_rows;

 	$stmt->bind_result($it_upc, $item_name, $type, $category, $company,
 	$year, $price, $stock);
		
 	if
	($count == 0){
 		array_push($error_stack,"Sorry bud, can't find that item.");
 	} 
 	$stmt->fetch();
	$result = array();
	$result["it_upc"] = $it_upc;
	$result["it_title"] = $item_name;
	$result["type"] = $type;
	$result["category"] = $category;
	$result["company"] = $company;
	$result["year"] = $year;
	$result["price"] = $price;

 	return $result;
 }
 
 function renderTablePrefix($receiptId){
	 $border = "1";
	 $width = "100%";
	  print "<h2>Receipt #$receiptId</h2>
			 <table border=$border width=$width;>
			 <tr>
				 <th>QTY</th>
				 <th>UPC</th>
				 <th>Title</th>
				 <th>Type</th>
				 <th>Category</th>
				 <th>Company</th>
				 <th>Year</th>
				 <th>Price</th>
			 </tr>";
 }
 
 
 function renderTablePostfix($expected_date){
 	print  '<table>';
 	print '<tr>';
 		print '<td>Please expect delivery of items by approximately: </td>';
 		print '<td>'.$expected_date.'</td>';
 	print '</tr>';
	print  ' </table><br /><br />';
}