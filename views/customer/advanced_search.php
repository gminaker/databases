<?php
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
			if(isset($_POST["search_type"]) && $_POST['search_type'] == 'quick'){
				returnQuickSearchResults();
			}else if(isset($_POST["search_type"]) && $_POST['search_type'] == 'advanced'){
				returnAdvancedSearchResults();
			}
	} else { returnSearchPage();
	}

	function returnQuickSearchResults(){
		global $connection;
		global $notice_stack;
		
		$search_query = $_POST['quick_search'];

 	    $results = $connection->query("	SELECT * 
										FROM item, leadsinger
										WHERE ls_upc = it_upc AND 
										(MATCH (it_upc, it_title, type, category, company) AGAINST ('+$search_query*' IN BOOLEAN MODE)
										OR MATCH (ls_name) AGAINST('+$search_query*' IN BOOLEAN MODE)
										OR year like '%$search_query%');");
	 								 
		if($results->num_rows == 0) {       

	 		array_push($notice_stack, "Couldn't find any matching results. Please try again.");
	 		returnSearchPage();

    	} else {

    		renderTablePrefix();

			while($row = $results->fetch_assoc()) {
	   			renderRow($row);
			}  
			
    		renderTablePostfix();

			$results->free();

    	}	    
    	
	}
	
	function renderTablePostfix(){
		  print '</table>';
	}
	
	function returnAdvancedSearchResults(){
		global $notice_stack;
		global $error_stack;
		
		$upc = $_POST['upc'];
		$title = $_POST['title'];
		$item_type = $_POST['item_type'];
		$item_category = $_POST['item_category'];
		$ls = $_POST['ls'];
		$year = $_POST['year'];

		$like = "";

		if (is_numeric($upc) and !empty($upc)){
			$like .= " AND it_upc like '%$upc%'"; 
		}
		if (!empty($title) and $title != " "){
			$like .= " AND it_title like '%$title%'"; 
		}
		if (!empty($item_type)){
			$like .= " AND type like '%$item_type%'"; 
		}
		if (!empty($item_category)){
			$like .= " AND category like '%$item_category%'";
		}
		if (is_numeric($year) and (!empty($year) or $year == 0)){
			$like .= " AND year like '%$year%'";
		}
		if (!empty($ls) and $ls != " "){
			$like .= " AND ls_name like '%$ls%'";
		}
		if (!empty($like)){

			global $connection;
 	    	$results = $connection->query("	SELECT * 
											FROM item, leadsinger
											WHERE ls_upc = it_upc
											$like;");

 	    	if($results->num_rows == 0) {       

	 			array_push($notice_stack, "Couldn't find any matching results. Please try again.");
	 			returnSearchPage();

    		} else {

				renderTablePrefix();

				while($row = $results->fetch_assoc()) {
	   				renderRow($row);
				}  
    			
    			renderTablePostfix();

				$results->free();

    		}	    
		} else {
			array_push($error_stack, 'Error, please input something!');
			returnSearchPage();
		}
	}
	
function renderTablePrefix(){
	print '<h2>Search Results</h2>';
	print '<table>';
		print '<tr>';
			print '<th>UPC</th>';
			print '<th>Title</th>';
			print '<th>Type</th>';
			print '<th>Category</th>';
			print '<th>Company</th>';
			print '<th>Year</th>';
			print '<th>Price</th>';
			print '<th>Stock</th>';
			print '<th>QTY</th>';
			print '<th>Lead Singer</th>';
			print '<th>Add to cart</th>';
		print '</tr>';
}	

function renderRow($row){
	print '<tr>';
		print '<td>'.$row["it_upc"].'</td>';
		print '<td>'.$row["it_title"].'</td>';
		print '<td>'.$row["type"].'</td>';
		print '<td>'.$row["category"].'</td>';
		print '<td>'.$row["company"].'</td>';
		print '<td>'.$row["year"].'</td>';
		print '<td>'.$row["price"].'</td>';
		print '<td>'.$row["stock"].'</td>';
		print '<td>'.$row["ls_name"].'</td>';
		print '<form name="add_to_cart" method="post" action="?page=view_cart">';
		print '<input type="hidden" name="add_to_cart" value="true">';
		print '<input type="hidden" size="5" name="cart_upc" value="'.$row["it_upc"].'">';
		print '<td><input type="text" size="5" name="cart_qty"></td>';
		print '<td><input type="submit" value="Add to Cart"></td>';
		print '</form>';
	print '</tr>';
	    
}

function returnSearchPage(){
	?>
	<h2>Item Search</h2>
	
	<h3>Quick Search:</h3>
		<form name="item_search" method="post" action="?page=advanced_search">
		<input type="hidden" name="search_type" value="quick">
		<input name="quick_search" type="search" size="20">
		<input type="submit" value="Search">
		</form>
		
	<h3>Advanced Search:</h3>
		<form name="item_search" method="post" action="?page=advanced_search">
			<input type="hidden" name="search_type" value="advanced">
			<table>
				<tr>
					<th>UPC</th>
					<td><input type="text" name="upc"></td>
				</tr>
				<tr>
					<th>Title</th>
					<td><input type="text" name="title"></td>
				</tr>
				<tr>
					<th>Type</th>
					<td style="text-align: left">
						<select name="item_type">
						 	<option value="">Select...</option>
						 	<option value="cd">CD</option>
						 	<option value="dvd">DVD</option>
					 	</select>
					</td>
				</tr>
				<tr>
					<th>Category</th>
					<td style="text-align: left">
						<select name="item_category">
							 <option value="">Select...</option>
							 <option value="rock">Rock</option>
							 <option value="pop">Pop</option>
							 <option value="rap">Rap</option>
							 <option value="country">Country</option>
							 <option value="classical">Classical</option>
							 <option value="new age">New Age</option>
							 <option value="instrumental">Instrumental</option>
						</select>
					</td>
				</tr>
				<tr>
					<th>Year</th>
					<td><input type="text" name="year"></td>
				</tr>
				<tr>
				<tr>
					<th>Lead Singer</th>
					<td><input type="text" name="ls"></td>
				</tr>
				<tr>
					<td></td>
					<td style="text-align: right"><input type="submit" value="Search"></td>
				</tr>
			</table>
		</form>
	
	<?php 
}
?>