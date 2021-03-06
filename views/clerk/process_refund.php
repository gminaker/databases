<?php

/**
* Provides content and functionatilty for Clerks to Return Items. 
*
* <long description>
*
* PHP version 5
*
* @author   Mike, Gordon
* @since    1.0
*
*/

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	
	$renderReceiptCollector = false;
	$renderReceiptTable = false;
	
	if (isset($_POST["return_receiptno"]) && $_POST["return_receiptno"] == "true") {
		checkReceiptDisplayContents();
	}else if (isset($_POST["process_refund"]) && $_POST["process_refund"] == "true") {
		refundItems(returned());
	}
	
	if($renderReceiptCollector){
		renderReceiptCollector();
	}
	
}else{
	renderReceiptCollector();
}
	
function returned(){
	global $connection;
	global $error_stack;
	
	$receiptId = $_POST['receipt_id'];	
	
	$stmt = $connection->prepare("SELECT ri_upc, ri_quantity, ret_receiptId FROM returnrecord, returnitem WHERE ret_receiptId =  ? AND retId = ri_retId");
	$stmt->bind_param("s",  $receiptId);
	$stmt->execute();
	    
	if($stmt->error) {       
		array_push($error_stack, $stmt->error);
	}
	     
	$stmt->store_result();
	$count = $stmt->num_rows;
		
	$total_quantities = array();	
		
	if ($count == 0) {
		return $total_quantities;
	}

	$stmt->bind_result($upc, $qty, $recId);   
	    
	$i=0;
	$upcs = array();
	if(isset($_POST['return'])){
		foreach($_POST['return'] as $key => $value) {
			$upcs[$i] = $value['upc'];
			$i++;
		}
	}
			
	while($stmt->fetch()){
		if (in_array($upc, $upcs)) {
			if (array_key_exists($upc, $total_quantities)) {
				$total_quantities[$upc] += $qty;
			} else {
				$total_quantities[$upc] = $qty;
			}			
		}
	}
	return $total_quantities;
}
	
	
function refundItems($total_quantities){
	$receiptId = $_POST['receipt_id'];
		
	global $connection;
	global $error_stack;
	global $notice_stack;
		
	if(isset($_POST['return'])){
		global $renderReceiptCollector;
		$firstReturn = true;
		$all_ok = true;
		$connection->autocommit(FALSE);
		$connection->begin_transaction();
		$transactions = array();
		$n = 0;
			
		foreach($_POST['return'] as $key => $value) {
			$upc = $value['upc'];
			$purchase_qty = $value['pqty'];
			$qty = $value['qty'];
			if (array_key_exists($upc, $total_quantities)) {
				array_push($notice_stack, "$total_quantities[$upc] of UPC, $upc, have/has already been returned on this receipt.");
				$not_returned_qty = $purchase_qty - $total_quantities[$upc];
			} else {
				$not_returned_qty = $purchase_qty;
			}
					
			if ($qty > 0 && $qty <= $not_returned_qty){
				if ($firstReturn) {
					$stmt = $connection->prepare("INSERT INTO returnrecord (ret_date, ret_receiptId) VALUES (?,?)");
	    			$stmt2 = $connection->prepare("UPDATE item SET stock = stock + ? WHERE it_upc = ?");


					// Bind the statement parameters, 'ss' indicates 2 strings
					$stmt->bind_param("ss", date('Y-m-d h:i:s', time()),$receiptId);
	    			$stmt2->bind_param("is",$qty, $upc);

					// Execute the insert statement
					$stmt->execute();
					$stmt2->execute();
					// Print any errors if they occured
					if($stmt->error) {      
						array_push($error_stack, $stmt->error);
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
	    
					$returnReceiptId = $stmt->insert_id;
					$firstReturn = false;
				}
					  	
				$stmt = $connection->prepare("INSERT INTO returnitem (ri_retid, ri_upc, ri_quantity) VALUES (?,?,?)");
	
				$stmt->bind_param("sss",$returnReceiptId, $upc, $qty);
			
				$stmt->execute();
	
				if($stmt->error) {       
					array_push($error_stack, $stmt->error);

					$connection->rollback();
					$connection->autocommit(TRUE);
					array_push($error_stack, "This transaction was rolled back");
					return;
				} else {
					// Transaction would work add it to the array.
					$transactions[$n] = $value;	
					$n++;				
				} 
					
			} else if ($qty == ""){
				// No return attempted.
			} else {
				array_push($error_stack, "Sorry, can't return $qty of UPC: $upc.");
			}
		}
		if($connection->commit()) {
			foreach($transactions as $value) {
				$qty = $value['qty'];
				$upt = $value['upc'];
				$cardNo = $value['cardNo'];
				array_push($notice_stack, "Return processed successfully for: $qty of UPC: $upc on Credit Card No. $cardNo");
				$renderReceiptCollector = true;
			}
		};			
		$connection->autocommit(TRUE);
	}			
}
	
function checkReceiptDisplayContents(){
	global $error_stack;
	global $notice_stack;
	
	$oldReceipt = false;
	$receiptId = $_POST["invoice_no"];
	$purchase = getPurchaseInfo($receiptId);
	if ($purchase == NULL) {
		return;
	}
		
	$r_date = new DateTime($purchase['date']);
	$now = new DateTime();
	$diff = $now->diff($r_date);
		
	if($diff->days > 15) {
		$oldReceipt = true;
		array_push($notice_stack, 'This receipt was issued more than 15 days ago and cannot be returned');
	}
	    
	print '<table>';
	print '<form method="post">';
	print '<input type="hidden" name="process_refund" value="true">';
	print '<input type="hidden" name="receipt_id" value="'.$purchase['receiptId'].'">';
	print '<tr>';
	print '<td>Receipt ID:</td>';
	print '<td>'.$purchase['receiptId'].'</td>';
	print '</tr>';
	print '<tr>';
	print '<td>Receipt Date:</td>';
	print '<td>'. date('Y-m-d' , $r_date->getTimestamp()) .'</td>';
	print '</tr>';
	print '<tr>';
	print '<td>Customer ID:</td>';
	print '<td>'.$purchase['cid'].'</td>';
	print '</tr>';
	print '<tr>';
	print '<td>Delivery Date:</td>';
	print '<td>'.$purchase['deliveredDate'].'</td>';		
	print '</tr>';
	if (!$oldReceipt) {
		print '<tr><th>UPC</th><th>Title</th><th>Order QTY</th><th>Return QTY</th></tr>';
	} else {
		print '<tr><th>UPC</th><th>Title</th><th>Order QTY</th></tr>';
	}
	    
	getAllReceiptItems($purchase['receiptId'], $oldReceipt, $purchase['cardNo']);
	    
	if (!$oldReceipt) {
		print '<tr><td colspan=3></td><td><input type="submit" value="Process Return"></td></tr>';
	}
	print '</table>';
}  
     
function getAllReceiptItems($receiptId, $oldReceipt, $cardNo){
	global $connection;
	global $error_stack;
	
	$stmt = $connection->prepare("SELECT * FROM purchaseitem WHERE pi_receiptId =  ?");
	$stmt->bind_param("s",  $receiptId);
	$stmt->execute();
	    
	if($stmt->error) {       
		array_push($error_stack, $stmt->error);
	} 
	    
	$stmt->store_result();
	$count = $stmt->num_rows;
	$stmt->bind_result($receiptId, $upc, $quantity);
		
	if($count == 0){
		array_push($error_stack,"This receipt don't have no items.");
	}
	$i=0;
	while($stmt->fetch()){
		print '<tr><td>'.$upc.'</td>
			<input type="hidden" name="return['.$i.'][upc]" value='.$upc.'>
		<td>'.getItemInfo($upc).'</td>
		<td>'.$quantity.'</td>
		<input type="hidden" name="return['.$i.'][pqty]" value='.$quantity.'>
		<input type="hidden" name="return['.$i.'][cardNo]" value='.$cardNo.'>';
		if (!$oldReceipt) {
			print '<td><input type="text" name="return['.$i.'][qty]"></td>';
		}	               
		print '</tr>';
		$i++;
	}
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
		
	if($count == 0){
		array_push($error_stack,"Sorry bud, can't find that item.");
	} 
	$stmt->fetch();
	return $item_name;
}
	
function renderReceiptCollector(){
		
	print' <h1>Return Items</h1>
		<form method="post">
	<input type="hidden" name="return_receiptno" value="true">
	<p>Original Receipt Number: <input type="text" name="invoice_no"><input type=submit></p>
	</form>';
}

function getPurchaseInfo($receiptId){
	
	global $connection;
	global $error_stack;
	
	$stmt = $connection->prepare("SELECT * FROM purchase WHERE p_receiptId =  ?");
	$stmt->bind_param("s",  $receiptId);
	$stmt->execute();
	    
	if($stmt->error) {       
		array_push($error_stack, $stmt->error);
	}
	     
	$stmt->store_result();
	$count = $stmt->num_rows;

	$stmt->bind_result($receiptId, $date, $cid, $cardNo, $expiryDate, $expectedDate, $deliveredDate);
		
	if($count == 0){
		array_push($error_stack,"Sorry bud, can't find receipt #$receiptId.");
		$a = NULL;

	} else {
		$stmt->fetch();
		$a = array();
		$a['receiptId'] = $receiptId;
		$a['date'] = $date;
		$a['cid'] = $cid;
		$a['cardNo']  = $cardNo;
		$a['expiryDate'] = $expiryDate;
		$a['expectedDate'] = $expectedDate;
		$a['deliveredDate'] = $deliveredDate;
	} 
	return $a;
}
 
?>
 
