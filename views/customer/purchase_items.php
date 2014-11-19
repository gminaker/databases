<?php

/**
 * Provides content and functionatilty for Customers to Purchase Items. 
 *
 * <long description>
 *
 * PHP version 5
 *
 * @author     Gordon
 * @since     1.0
 *
 */
 
 function getAllItemRows(){
	global $connection;
 	$results = $connection->query("SELECT * FROM item");
 	
 	if($results->num_rows == 0){
	 	print '<tr><td colspan=9>No Items Found</td</tr>';
 	}

	while($row = $results->fetch_assoc()) {
	    print '<tr>';
	    print '<td><input type="text" size="5"></td>';
	    print '<td>'.$row["it_upc"].'</td>';
	    print '<td>'.$row["it_title"].'</td>';
	    print '<td>'.$row["type"].'</td>';
	    print '<td>'.$row["category"].'</td>';
	    print '<td>'.$row["company"].'</td>';
	    print '<td>'.$row["year"].'</td>';
	    print '<td>'.$row["price"].'</td>';
	    print '<td>'.$row["stock"].'</td>';
	    print '</tr>';
	}  

	$results->free();
 }
 
 ?>
 
 <table border="1">
	 <tr>
		 <th>QTY</th>
		 <th>UPC</th>
		 <th>Title</th>
		 <th>Type</th>
		 <th>Category</th>
		 <th>Company</th>
		 <th>Year</th>
		 <th>Price</th>
		 <th>Stock</th>
	 </tr>
	 
	 <?php getAllItemRows(); ?>
	 
	 <tr>
		 <td colspan=8></td>
		 <td><input type=submit></td>
	 </tr>
 </table>
	 