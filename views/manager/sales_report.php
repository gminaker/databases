<?php

/**
 * Provides content and functionatilty for manager to 
 * view the daily sales report. 
 *
 * <long description>
 *
 * PHP version 5
 *
 * @author     Gordon, Thomas
 * @since    1.0
 *
 */
 
 	global $notice_stack;
 	
function generateDailySalesReport($raw_date){
	global $connection;
	global $notice_stack;
	global $error_stack;
	
	$date = date('Y-m-d', strtotime($raw_date));
	
	$results = $connection->query("	SELECT i.it_upc, i.category, i.price, sum(pi.pi_quantity), i.price*sum(pi.pi_quantity)
									FROM purchase p, purchaseItem pi, item i 
									WHERE p.p_receiptId = pi.pi_receiptId
									AND pi.pi_upc = i.it_upc
									AND p.p_date = '$date'
									GROUP BY i.it_upc, i.category, i.price;");
									
	if(!$results){
		array_push($error_stack, $connection->error);
	}						
 	
 	if($results->num_rows == 0){
	 	array_push($notice_stack,'No sales records found for '.$raw_date);
 	} else {
 		print '<table><tr><th colspan=5>Report for: '.$date.'</th></tr>';
	 	print '<tr>	
	 			<th>UPC</th>
	 			<th>Category</th>
 				<th>Unit Price</th>
 				<th>Units</th>
 				<th>Total Value</th>
 			   </tr>';
 			
 		$i = 0;
 		$category = "";
 		$totalunits = 0;
 		$totalvalue = 0.00;
 		$gtotalunits = 0;
 		$gtotalvalue = 0.00;

		while($row = $results->fetch_assoc()) {

		    print '<tr>';
			    print '<td>'.$row["it_upc"].'</td>';
			    print '<td>'.$row["category"].'</td>';
			    print '<td>'.$row["price"].'</td>';
			    print '<td>'.$row["sum(pi.pi_quantity)"].'</td>';
		    	print '<td>'.$row["i.price*sum(pi.pi_quantity)"].'</td>';
	    	print '</tr>';
	    
	    	$totalunits += floatval($row["sum(pi.pi_quantity)"]);
 			$totalvalue += floatval($row["i.price*sum(pi.pi_quantity)"]);
 			$gtotalunits += floatval($row["sum(pi.pi_quantity)"]);
 			$gtotalvalue += floatval($row["i.price*sum(pi.pi_quantity)"]);

 			if ($row["category"] != $category){
				print '<tr>';
		   			print '<td></td>';
		   			print '<td>Category Total</td>';
		    		print '<td></td>';
		    		print '<td>'.$totalunits.'</td>';
		    		print '<td>'.$totalvalue.'</td>';
	    		print '</tr>';

				$category = $row["category"];
				$totalunits = 0.00;
 				$totalvalue = 0.00;
	 		}
		    

	    	$i++;
		}

		if ($category != ""){
			print '<tr>';
				print '<td></td>';
				print '<td>Total Daily Sales</td>';
		    	print '<td></td>';
		    	print '<td>'.$gtotalunits.'</td>';
		    	print '<td>'.$gtotalvalue.'</td>';
	    	print '</tr></table>';
		} 
	}

	$results->free();
}
 
 ?>
 
 <form method=post>
 <h1>Daily Sales Report</h1>
 <table>
	 <tr>
		 <td>Enter Date:</td>
		 <td><input type=date name="report_date" class="dynamic_datepicker"></td>
		 <td><input type=submit value="Generate Report"></td>
	 </tr>
 </table>
 </form>
 
 <?php 
	 
 if(isset($_POST['report_date'])
 	and (!empty($_POST['report_date']))){
	 generateDailySalesReport($_POST['report_date']);
 } else if (isset($_POST['report_date'])){
	 array_push($notice_stack, "Please Enter a Date");
 }
 ?>
