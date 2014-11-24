<?php

/**
 * Provides content and functionatilty for Clerks to Return Items. 
 *
 * <long description>
 *
 * PHP version 5
 *
 * @author     Gordon
 * @since    1.0
 *
 */
 
 
   if ($_SERVER["REQUEST_METHOD"] == "POST") {
 		
      if (isset($_POST["return_receiptno"]) && $_POST["return_receiptno"] == "true") {
		checkReceiptDisplayContents();
      }else if (isset($_POST["process_refund"]) && $_POST["process_refund"] == "true") {
		refundItems(returned());
      }else {
	    renderReceiptCollector();
      }
	}else{
		renderReceiptCollector();
	}
	
	function returned(){
		$receiptId = $_POST['receipt_id'];
		
		global $connection;
		$stmt = $connection->prepare("SELECT ri_upc, ri_quantity, ret_receiptId FROM returnrecord, returnitem WHERE ret_receiptId =  ? AND retId = ri_retId");
	    $stmt->bind_param("s",  $receiptId);
	    $stmt->execute();
	    
		if($stmt->error) {       
	      printf("<b>Error: %s.</b>\n", $stmt->error);
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
			//var_dump($upc);
			//var_dump($recId);
		    if (in_array($upc, $upcs)) {
		    	print("Found a return for this item.\n");
				//var_dump($upc);
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
		$error_stack = array();
		
		if(isset($_POST['return'])){
			$firstReturn = true;
			foreach($_POST['return'] as $key => $value) {
			    	
				//var_dump($value);
				$upc = $value['upc'];
				$purchase_qty = $value['pqty'];
				$qty = $value['qty'];
				if (array_key_exists($upc, $total_quantities)) {
					printf("<br>%d of UPC, %s, have/has already been returned on this receipt.</br>", $total_quantities[$upc], $upc);
					$not_returned_qty = $purchase_qty - $total_quantities[$upc];
				} else {
					$not_returned_qty = $purchase_qty;
				}
				// var_dump($not_returned_qty);
					
				if ($qty > 0 && $qty <= $not_returned_qty){
				 	if ($firstReturn) {
					 	$stmt = $connection->prepare("INSERT INTO returnrecord (ret_date, ret_receiptId) VALUES (?,?)");
	    
					    // Bind the title and pub_id parameters, 'sss' indicates 3 strings
					    $stmt->bind_param("ss", date('Y-m-d h:i:s', time()),$receiptId);
	    
					    // Execute the insert statement
					    $stmt->execute();

					    // Print any errors if they occured
					    if($stmt->error) {       
					      array_push($error_stack, $stmt->error);
					    }     
	    
					    $returnReceiptId = $stmt->insert_id;
					    //var_dump($returnReceiptId);
						$firstReturn = false;
				 	}
					  	
					$stmt = $connection->prepare("INSERT INTO returnitem (ri_retid, ri_upc, ri_quantity) VALUES (?,?,?)");
	
					$stmt->bind_param("sss",$returnReceiptId, $upc, $qty);
			
					$stmt->execute();
	
					if($stmt->error) {       
						array_push($error_stack, $stmt->error);
					} else if (count($error_stack) == 0){
						print("Return processed successfully.");
					} 
					
				} else {
					printf("Sorry, can't return %d UPC %s.", $qty, $upc);
				}
			}
		}
		
		if(count($error_stack) > 0){
			print("Errors occurred:");
			print_r($error_stack);
		}	
	}
	
	function checkReceiptDisplayContents(){
		$receiptId = $_POST["invoice_no"];
		$oldReceipt = false;
		
		global $connection;
		$stmt = $connection->prepare("SELECT * FROM purchase WHERE p_receiptId =  ?");
	    $stmt->bind_param("s",  $receiptId);
	    $stmt->execute();
	    
		if($stmt->error) {       
	      printf("<b>Error: %s.</b><br />", $stmt->error);
	    }
	     
	    $stmt->store_result();
		$count = $stmt->num_rows;

		$stmt->bind_result($receiptId, $date, $cid, $cardNo, $expiryDate, $expectedDate, $deliveredDate);
		
		if($count == 0){
			print("Sorry bud, can't find that receipt.");
			exit();
		} else {
			$stmt->fetch();
		}
		
		$r_date = new DateTime($date);
		$now = new DateTime();
		$diff = $now->diff($r_date);
		
		if($diff->days > 15) {
			$oldReceipt = true;
		    print 'Warning: This receipt was issued more than 15 days ago';
		}
	    
		print '<table>';
		print '<form method="post">';
		print '<input type="hidden" name="process_refund" value="true">';
		print '<input type="hidden" name="receipt_id" value="'.$receiptId.'">';
		print '<tr>';
		print '<td>Receipt ID:</td>';
		print '<td>'.$receiptId.'</td>';
		print '</tr>';
		print '<tr>';
		print '<td>Receipt Date:</td>';
		print '<td>'. date('Y-m-d' , $r_date->getTimestamp()) .'</td>';
		print '</tr>';
		print '<tr>';
		print '<td>Customer ID:</td>';
		print '<td>'.$cid.'</td>';
		print '</tr>';
		print '<tr>';
		print '<td>Delivery Date:</td>';
		print '<td>'.$deliveredDate.'</td>';		
		print '</tr>';
		if (!$oldReceipt) {
			print '<tr><th>UPC</th><th>Title</th><th>Order QTY</th><th>Return QTY</th></tr>';
		} else {
			print '<tr><th>UPC</th><th>Title</th><th>Order QTY</th></tr>';
		}
	    
	    getAllReceiptItems($receiptId, $oldReceipt);
	    
		if (!$oldReceipt) {
	    	print '<tr><td colspan=3></td><td><input type="submit" value="Process Return"></td></tr>';
		}
	    print '</table>';
	}  
     
	function getAllReceiptItems($receiptId, $oldReceipt){
		global $connection;
		$stmt = $connection->prepare("SELECT * FROM purchaseitem WHERE pi_receiptId =  ?");
	    $stmt->bind_param("s",  $receiptId);
	    $stmt->execute();
	    
		if($stmt->error) {       
	      printf("<b>Error: %s.</b>\n", $stmt->error);
	    } 
	    
	    $stmt->store_result();
	    $count = $stmt->num_rows;
		$stmt->bind_result($receiptId, $upc, $quantity);
		
		if($count == 0){
			print("This receipt don't have no items.");
			exit();
		}
	    $i=0;
	    while($stmt->fetch()){
			print '<tr><td>'.$upc.'</td>
				<input type="hidden" name="return['.$i.'][upc]" value='.$upc.'>
			<td>'.getItemInfo($upc).'</td>
			<td>'.$quantity.'</td>
			<input type="hidden" name="return['.$i.'][pqty]" value='.$quantity.'>';
			if (!$oldReceipt) {
				print '<td><input type="text" name="return['.$i.'][qty]"></td>';
			}	               
			print '</tr>';
			$i++;
	    }
	}
	
	function getItemInfo($upc){
		global $connection;
		$stmt = $connection->prepare("SELECT * FROM item WHERE it_upc =  ?");
	    $stmt->bind_param("s", $upc);
	    $stmt->execute();
	    
		if($stmt->error) {       
	      printf("<b>Error: %s.</b>\n", $stmt->error);
	    }
	     
	    $stmt->store_result();
		$count = $stmt->num_rows;

		$stmt->bind_result($it_upc, $item_name, $type, $category, $company,
			$year, $price, $stock);
		
		if($count == 0){
			print("Sorry bud, can't find that item.\n");
			exit();
		} 
		$stmt->fetch();

		//var_dump($item_name);
		return $item_name;
	}
	
	function renderReceiptCollector(){
		
		print' <h1>Return Items</h1>
			 <form method="post">
			 <input type="hidden" name="return_receiptno" value="true">
			 <p>Original Receipt Number: <input type="text" name="invoice_no"><input type=submit></p>
			 </form>';
	}
 
 ?>
 
