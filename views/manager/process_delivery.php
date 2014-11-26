<?php

/**
 * Provides content and functionatilty for Managers to Process Deliveries. 
 *
 * 'Process delivery' means to set the date value for a delivery. 
 *
 * PHP version 5
 *
 * @author     Gordon
 * @since     1.0
 *
 */
 

 
   if ($_SERVER["REQUEST_METHOD"] == "POST") {
 		
      if (isset($_POST["updateDeliveryDates"]) && $_POST["updateDeliveryDates"] == "SUBMIT") {
		checkValsThenInsertIntoDB($_POST);
      }
   }
   
   function checkValsThenInsertIntoDB($all){
	    global $error_stack;
		global $notice_stack;
 
	   if(isset($_POST['updates'])){
		   foreach($_POST['updates'] as $key => $value) {
			   
			    $receiptId = $value['receipt'];
			    $date = $value['date'];
			    
				if (isset($date) && $date != ""){
				  	global $connection;
					$stmt = $connection->prepare("UPDATE purchase SET deliveredDate = ? WHERE p_receiptId = ?");
	
					$stmt->bind_param("ss", date('Y-m-d h:i:s', strtotime($date)), $receiptId);
					 
					$stmt->execute();
					
					if($stmt->error){       
					  array_push($error_stack, $stmt->error);
					}    
			
			    }
			    
			    unset($date);
			    unset($receiptId);
			}
			
		}
		
		if(count($error_stack) == 0){
			array_push($notice_stack, "Delivery dates were submitted successfully!");
		}
   }
 
 
function getAllOrdersToProcess(){
	global $connection;
	global $notice_stack;
	
	$results = $connection->query("SELECT * FROM purchase WHERE deliveredDate IS NULL");
 	
 	if($results->num_rows == 0){
	 	array_push($notice_stack, 'No orders needing delivery dates were found.');
 	}else{

	 	$i = 0;
		while($row = $results->fetch_assoc()) {
			print '<input type="hidden" name="updates['.$i.'][receipt]" value='.$row["p_receiptId"].'>';
		    print '<tr>';
			    print '<td>'.$row["p_receiptId"].'</td>';
			    print '<td>'.date('Y-m-d', strtotime($row["p_date"])).'</td>';
			    print '<td>'.$row["p_cid"].'</td>';
			    print '<td>'.date('Y-m-d', strtotime($row["expectedDate"])).'</td>';
			    print '<td><input type="date" name="updates['.$i.'][date]" class="dynamic_datepicker"></td>';
		    print '</tr>';
		    
		    $i++;
		}  
		print'<tr><td colspan=4></td>
		 		  <td><input type=submit value="Update Delivery Dates"></td>
		      </tr>';
	}
	$results->free();
 }
 
 
 function renderProcessDeliveries(){
	 
	 $output = getAllOrdersToProcess();
	 
	 if($output){
	 renderProcessDeliveryPrefix();
	 $output;
	 renderProcessDeliveryPostfix();
	 }
	 
 }
 
 function renderProcessDeliveryPostfix(){
	 print '</form>
	 		</table>';
 }
 
 function renderProcessDeliveryPrefix(){
	 print '<h1>Process Deliveries</h1>
 
			  <table border="1">
				 <tr>
					 <th>Receipt ID</th>
					 <th>Date</th>
					 <th>Customer ID</th>
					 <th>Expected Delivery</th>
					 <th>Actual Delivery</th>
				 </tr>
				 <form method="post">
				 <input type=hidden name="updateDeliveryDates" value="SUBMIT">';
 }
 
 
  renderProcessDeliveries();
  
 ?>
 
