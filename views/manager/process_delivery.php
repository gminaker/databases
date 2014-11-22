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
 
function getAllOrdersToProcess(){
	global $connection;
	$results = $connection->query("SELECT * FROM purchase WHERE deliveredDate IS NULL");
 	
 	if($results->num_rows == 0){
	 	print '<tr><td colspan=5>No Items Found</td></tr>';
 	}

 	$i = 0;
	while($row = $results->fetch_assoc()) {
	    print '<tr>';
		    print '<td>'.$row["p_receiptId"].'</td>';
		    print '<td>'.$row["p_date"].'</td>';
		    print '<td>'.$row["p_cid"].'</td>';
		    print '<td>'.$row["expectedDate"].'</td>';
		    print '<td><input type="date" name="date_'.$row["p_receiptId"].'" class="dynamic_datepicker"></td>';
	    print '</tr>';
	    
	    $i++;
	}  

	$results->free();
 }
 
 ?>
 
 <h1>Process Deliveries</h1>
 
  <table border="1">
	 <tr>
		 <th>Receipt ID</th>
		 <th>Date</th>
		 <th>Customer ID</th>
		 <th>Expected Delivery</th>
		 <th>Actual Delivery</th>
	 </tr>
	 
	 <?php getAllOrdersToProcess(); ?>
	 
	 <tr>
		 <td colspan=4></td>
		 <td><input type=submit value="Place Order"></td>
	 </tr>
 </table>
