<?php

/**
 * Provides content and functionatilty for manager to 
 * view the daily sales report. 
 *
 * <long description>
 *
 * PHP version 5
 *
 * @author     Gordon
 * @since    1.0
 *
 */
 
function generateDailySalesReport($date){
	global $connection;
	$results = $connection->query("	SELECT i.it_upc, sum(pi.pi_quantity)
									FROM purchase p, purchaseItem pi, item i 
									WHERE p.p_receiptId = pi.pi_receiptId
									AND pi.pi_upc = i.it_upc
									AND p.p_date = $date
									GROUP BY i.it_upc");
									
	if(!$results){
		printf("Error: %s\n", $connection->error);
	}						
 	
 	if($results->num_rows == 0){
	 	print '<tr><td colspan=5>No Items Found</td></tr>';
 	}

 	$i = 0;
	while($row = $results->fetch_assoc()) {
	    print '<tr>';
		    print '<td>'.$row["it_upc"].'</td>';
		    print '<td>'.$row["pi_quantity"].'</td>';
	    print '</tr>';
	    
	    $i++;
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
	 
 if(isset($_POST['report_date'])){
	 generateDailySalesReport($_POST['report_date']);
 }