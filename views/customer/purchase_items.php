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
		checkValsThenInsertIntoDB(	$_POST['user_id'],
		      						$_POST['user_pass'],
		      						$_POST['user_ccno'],
		      						$_POST['user_ccex'],
		      						$_POST);
      }
 }
 
 
 function checkValsThenInsertIntoDB($user_id, $user_pass, $cc_no, $cc_ex, $all){
	 
	$msg = checkValues($user_id, $user_pass, $cc_no, $cc_ex, $all); 
	$expected_date = calculateExpectedDate();
	
	if($msg){
		printf("%s", $msg);
	}else{
		insertIntoDB($user_id, $user_pass, $cc_no, $cc_ex, $expected_date, $all);
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
function checkValues($user_id, $user_pass, $cc_no, $cc_ex, $all){
	
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
function insertIntoDB($user_id, $user_pass, $cc_no, $cc_ex, $expected_date, $all){

	global $connection;
 	$stmt = $connection->prepare("INSERT INTO purchase (p_date, p_cid, cardNo, expiryDate, expectedDate) VALUES (?,?,?,?,?)");
    
    // Bind the title and pub_id parameters, 'sss' indicates 3 strings
    $stmt->bind_param("sssss", $user_id, $user_pass, $cc_no, $cc_ex, $expected_date);
    
    // Execute the insert statement
    $stmt->execute();
    
    // Print any errors if they occured
    if($stmt->error) {       
      printf("<b>Error: %s.</b>\n", $stmt->error);
    } else {
      echo "<b>Successfully added ".$title."</b>";
      unset($_POST);
    }      
    
    $i=0;
    $receipt_id = $stmt->insert_id;
    
    foreach($_POST as $key => $value) {
	  $pos = strpos($key , "purchase");
	  if ($pos === 0){
		  $stmt = $connection->prepare("INSERT INTO purchaseitem (pi_receiptId, pi_upc, pi_quantity) VALUES (?,?,?)");
		  // Bind the title and pub_id parameters, 'sss' indicates 3 strings
		  $stmt->bind_param("sss",$receiptId, $_POST['purchase['.$i.'][upc]'], $_POST['purchase['.$i.'][qty]']);
		  // Execute the insert statement
		  $stmt->execute();
		  $i++;
	    //TODO get all items wanting to be purchased, their qty, and add into purchaseItem table
	  }
	}
              
    $id = $mysqli->insert_id;
    
 }
 
 

 function getAllItemRows(){
	global $connection;
 	$results = $connection->query("SELECT * FROM item");
 	
 	if($results->num_rows == 0){
	 	print '<tr><td colspan=9>No Items Found</td</tr>';
 	}

 	$i = 0;
	while($row = $results->fetch_assoc()) {
	    print '<tr>';
		    print '<td><input type="text" size="5" name="purchase['.$i.'][qty]"></td>';
		    print '<input type="hidden" size="5" name="purchase['.$i.'][upc]" value="'.$row["it_upc"].'">';
		    print '<td>'.$row["it_upc"].'</td>';
		    print '<td>'.$row["it_title"].'</td>';
		    print '<td>'.$row["type"].'</td>';
		    print '<td>'.$row["category"].'</td>';
		    print '<td>'.$row["company"].'</td>';
		    print '<td>'.$row["year"].'</td>';
		    print '<td>'.$row["price"].'</td>';
		    print '<td>'.$row["stock"].'</td>';
	    print '</tr>';
	    
	    $i++;
	}  

	$results->free();
 }
 
 ?>
 
 <form method=post name="purchase_item_form">
 <input type="hidden" name="purchase_items" value="SUBMIT">
 <table>
	 <tr> 
		 <td>User ID:</td>
		 <td><input type="text" name="user_id"></td>
	 </tr>
	 <tr>
		 <td>User Password</td>
		 <td><input type="password" name="user_pass"></td>
	 </tr>
	 <tr>
		 <td>Credit Card Number</td>
		 <td><input type="text" name="user_ccno"></td>
	 </tr>
	 <tr>
		 <td>Credit Card Expiry</td>
		 <td><input type=date name="user_ccex"></td>
	 </tr>
 </table>
 
 <table border="1">
	 <tr>
		 <th>QTY</th>
		 <th>UPC</th>
		 <th>Title</th>
		 <th>Type</th>
		 <th>Category</th>
		 <th>Company</th>
		 <th>Year</th>
		 <th>Price</th>
		 <th>Stock</th>
	 </tr>
	 
	 <?php getAllItemRows(); ?>
	 
	 <tr>
		 <td colspan=8></td>
		 <td><input type=submit value="Place Order"></td>
	 </tr>
 </table>
 </form>
	 