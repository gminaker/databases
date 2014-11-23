<?php

/**
 * Provides content and functionatilty for Managers to 
 * view top selling items. 
 *
 * <long description>
 *
 * PHP version 5
 *
 * @author     Thomas
 * @since    1.0
 *
 */
function topSellingItems($date,$count){
 	global $connection;
 	$results = $connection->query(" SELECT i.it_title, i.company, i.stock, sum(pi.pi_quantity)
									FROM purchase p, purchaseItem pi, item i 
									WHERE p.p_receiptId = pi.pi_receiptId
									AND pi.pi_upc = i.it_upc
									AND p.p_date = $date
									GROUP BY i.it_upc, i.it_title, i.company, i.stock
									ORDER BY sum(pi.pi_quantity) DESC;");
									
	if(!$results){
		printf("Error: %s\n", $connection->error);
	}						
 	
 	if($results->num_rows == 0){
	 	print '<tr><td colspan=5>No Items Found</td></tr>';
 	}

 	$i = 1;
	while($row = $results->fetch_assoc() and $i <= $count) {
	    print '<tr>';
		    print '<td>'.$row["it_title"].'</td>';
		    print '<td>'.$row["company"].'</td>';
		    print '<td>'.$row["stock"].'</td>';
		    print '<td>'.$row["sum(pi.pi_quantity)"].'</td>';
	    print '</tr>';
	    
	    $i++;
	}  

	$results->free();
 }
 
?>
 
 <form method=post>
 <h1>Top selling items</h1>
 <table>
	 <tr>
		 <td>Enter Date:</td>
		 <td><input type=date name="report_date" class="dynamic_datepicker"></td>
		 <td>Enter Total items:</td>
		 <td><input type="text" name="count"></td>
		 <td><input type=submit value="Get top selling items"></td>
	 </tr>
 </table>
 </form>
 
 <?php 
	 
 if(isset($_POST['report_date'], $_POST['count'])){
 	 print '<tr>';
 	 print '<td>'.$_POST["report_date"].'</td>';
	 print '<td>'.$_POST["count"].'</td>';
	 print '</tr>';

	 
	 topSellingItems($_POST['report_date'], intval($_POST['count']));
 }

 ?>