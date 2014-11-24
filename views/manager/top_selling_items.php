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


	$cleanDate = date('Y-m-d', strtotime($date));

 	global $connection;
 	$results = $connection->query(" SELECT i.it_title, i.company, i.stock, sum(pi.pi_quantity)
									FROM purchase p, purchaseItem pi, item i 
									WHERE p.p_receiptId = pi.pi_receiptId
									AND pi.pi_upc = i.it_upc
									AND p.p_date = '$cleanDate'
									GROUP BY i.it_upc, i.it_title, i.company, i.stock
									ORDER BY sum(pi.pi_quantity) DESC;");
									
	if(!$results){
		printf("Error: %s\n", $connection->error);
	}						
 	
 	if($results->num_rows == 0){
	 	print '<tr><td colspan=5>No Items Found</td></tr>';
 	} else {

 		print '<tr>';
		    print '<td> Title </td>';
		    print '<td> Company </td>';
		    print '<td> Stock </td>';
		    print '<td> Quantity </td>';
	    print '</tr>';

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
	 
 if(isset($_POST['report_date'], $_POST['count'])
 	and ($_POST['report_date'] != "")
 	and ($_POST['count'] != "")){
	
	topSellingItems($_POST['report_date'], intval($_POST['count']));
 }

 ?>