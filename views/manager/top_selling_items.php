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

	global $error_stack;
	global $notice_stack;

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
		array_push($error_stack, $connection->error);
	}						
	
 	if($results->num_rows == 0){
	 	array_push($notice_stack, 'No Items Found');
 	} else {
		print('<table><tr><th>Top selling items on '.$cleanDate.'</th></tr></table>');

 		print '<table><tr>';
		    print '<th> Title </th>';
		    print '<th> Company </th>';
		    print '<th> Stock </th>';
		    print '<th> Quantity </th>';
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

		print('</table>');

		if ($i < $count){
			print ('<tr><td colspan=5>Only '.($i - 1).' item(s) to diplay</td></tr>');
		}
	}

	$results->free();
 }
 
 function renderTopSellingItemsPage(){
	 
	 renderFormInput();
	 checkInputRenderResults();
	 
 }
 
 function renderFormInput(){
	 ?>
	  <form method=post>
	 <h1>Top selling items</h1>
	 <table>
		 <tr>
			 <td>Enter Date:</td>
			 <td><input type=date name="report_date" class="dynamic_datepicker"></td>
		 </tr>
		 <tr>
			 <td>Enter Total items:</td>
			 <td><input type="text" name="count"></td>
		 </tr>
		 <tr><td></td>
			 <td><input type=submit value="Get top selling items"></td>
		 </tr>
	 </table>
	 </form>
 <?php
 }
 
 function checkInputRenderResults(){
	 global $error_stack;
	 
	 $displayOutput = false;
	 
	 if(isset($_POST['report_date'], $_POST['count'])){
		 $displayOutput = true;
		 
		 if(empty($_POST['report_date'])){
			 array_push($error_stack, 'Please Enter a Valid Date');
			 $displayOutput = false;
		 }
		 
		 if((!intval($_POST['count']) > 0) || empty($_POST['count'])){
			 array_push($error_stack, 'Please Enter a valid number');
			 $displayOutput = false;
		 } 
	 }
	 	
	if($displayOutput){
		topSellingItems($_POST['report_date'], intval($_POST['count']));
	 } 
 }
 
 renderTopSellingItemsPage();
 
?>
 
