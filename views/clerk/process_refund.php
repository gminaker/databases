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
		refundItems();
      }else {
	    renderReceiptCollector();
      }
	}else{
		renderReceiptCollector();
	}
	
	function refundItems(){
		$receiptId = $_POST['receipt_id'];
		
		global $connection;
	 	$stmt = $connection->prepare("INSERT INTO returnrecord (ret_date, ret_receiptId) VALUES (?,?)");
	    
	    // Bind the title and pub_id parameters, 'sss' indicates 3 strings
	    $stmt->bind_param("ss", date('Y-m-d h:i:s', time()),$receiptId);
	    
	    // Execute the insert statement
	    $stmt->execute();
	    
	    $error_stack = array();
	    
	    // Print any errors if they occured
	    if($stmt->error) {       
	      array_push($error_stack, $stmt->error);
	    }     
	    
	    $returnReceiptId = $stmt->insert_id;
	    
	    if(isset($_POST['return'])){
		    foreach($_POST['return'] as $key => $value) {
			    
				    $upc = $value['upc'];
				    $qty = $value['qty'];
				    
					if ($qty > 0){
					  	
					$stmt = $connection->prepare("INSERT INTO returnitem (ri_retid, ri_upc, ri_quantity) VALUES (?,?,?)");
	
					$stmt->bind_param("sss",$returnReceiptId, $upc, $qty);
			
					$stmt->execute();
	
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
			print("Return was processed successfully!");
		}	
	}
	
	function checkReceiptDisplayContents(){
		$receiptId = $_POST["invoice_no"];
		
		global $connection;
		$stmt = $connection->prepare("SELECT * FROM purchase WHERE p_receiptId =  ?");
	    $stmt->bind_param("s",  $receiptId);
	    $stmt->execute();
	    
		if($results->error) {       
	      printf("<b>Error: %s.</b>\n", $stmt->error);
	    }
	     
	    $stmt->store_result();
		$count = $stmt->num_rows;

		$stmt->bind_result($receiptId, $date, $cid, $cardNo, $expiryDate, $expectedDate, $deliveredDate);
		
		if($count == 0){
			print("Sorry bud, can't find that receipt");
			exit();
		}

		$date = new DateTime($row["p_date"]);
		$now = new DateTime();
		$diff = $now->diff($date);
		
		if($diff->days > 15) {
		    echo 'Warning: This receipt was issued more than 15 days ago';
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
		    print '<td>'. date('Y-m-d' , $date->getTimestamp()) .'</td>';
		print '</tr>';
		print '<tr>';
			print '<td>Customer ID:</td>';
		    print '<td>'.$cid.'</td>';
		print '</tr>';
		print '<tr>';
			print '<td>Delivery Date:</td>';
		    print '<td>'.$deliveredDate.'</td>';
	    print '</tr>
	    	   <tr><th>UPC</th><th>Title</th><th>Order QTY</th><th>Return QTY</th></tr>';
	    
	    getAllReceiptItems($receiptId);
	    
	    print '<tr><td colspan=3></td><td><input type="submit" value="Process Return"></td></tr>';
	    print '</table>';
	    
	}  
     
	function getAllReceiptItems($receiptId){
		global $connection;
		$stmt = $connection->prepare("SELECT * FROM purchaseitem WHERE pi_receiptId =  ?");
	    $stmt->bind_param("s",  $receiptId);
	    $stmt->execute();
	    
		if($results->error) {       
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
		    		   <input type="hidden" name="return['.$i.'][upc]">
		    		   <td>'.getItemInfo($upc, 'it_title').'</td>
		               <td>'.$quantity.'</td>
		               <td><input type="text" name="return['.$i.'][qty]"></td>
		           </tr>';
		           $i++;
	    }
	}
	
	function getItemInfo($upc){
		return $upc;
	}
	
	function renderReceiptCollector(){
		
		print' <h1>Return Items</h1>
			 <form method="post">
			 <input type="hidden" name="return_receiptno" value="true">
			 <p>Original Receipt Number: <input type="text" name="invoice_no"><input type=submit></p>
			 </form>';
	}
 
 ?>
 
