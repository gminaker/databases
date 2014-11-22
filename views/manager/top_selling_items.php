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
									ORDER BY sum(pi.pi_quantity) DESC");
									
	if(!$results){
		printf("Error: %s\n", $connection->error);
	}						
 	
 	if($results->num_rows == 0){
	 	print '<tr><td colspan=5>No Items Found</td></tr>';
 	}

 	$i = 1;
	while($row = $results->fetch_assoc() and $i =< n) {
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