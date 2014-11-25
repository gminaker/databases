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
		
		$search_query = $_POST['quick_search'];
		
		global $connection;
 	    $results = $connection->query("	SELECT * 
										FROM item 
										WHERE MATCH (it_upc, it_title, type, category, company) 
										AGAINST ('+$search_query*' IN BOOLEAN MODE)
										OR year like '%$search_query%';");
	 								 
		if(!$results) {       

	 		printf("<b>Error: please attempt again</b>");
	 		returnSearchPage();

    	} else {

    		print '<table>';

    			print'<tr><th>Results:</th></tr>';

    			print '<tr>';
			    	print '<th>Quantity</th>';
		    		print '<th>UPC</th>';
		    		print '<th>Title</th>';
		    		print '<th>Type</th>';
		    		print '<th>Category</th>';
		    		print '<th>Company</th>';
		    		print '<th>Year</th>';
		    		print '<th>Price</th>';
		    		print '<th>Stock</th>';
	    		print '</tr>';


 			$i = 0;
			while($row = $results->fetch_assoc()) {
	   			print '<tr>';
			    	print '<td><input type="text" size="5" name="purchase['.$i.'][qty]"></td>';
		    		print '<input type="hidden" size="5" name="purchase['.$i.'][upc]" value="'.$row["it_upc"].'">';
		    		print '<td>'.$row["it_upc"].'</td>';
		    		print '<td>'.$row["it_title"].'</td>';
		    		print '<td>'.$row["type"].'</td>';
		    		print '<td>'.$row["category"].'</td>';
		    		print '<td>'.$row["company"].'</td>';
		    		print '<td>'.$row["year"].'</td>';
		    		print '<td>'.$row["price"].'</td>';
		    		print '<td>'.$row["stock"].'</td>';
	    		print '</tr>';
	    
	    		$i++;
			}  
    		print '</table>';


			$results->free();

    	}	    
    	
	}
	
	function returnAdvancedSearchResults(){
		$upc = $_POST['upc'];
		$title = $_POST['title'];
		$item_type = $_POST['item_type'];
		$item_category = $_POST['item_category'];
		$year = $_POST['year'];

		$like = "";

		if (!empty($upc)){
			$like .= " AND it_upc like '%$upc%'"; 
		}
		if (!empty($title)){
			$like .= " AND it_title like '%$title%'"; 
		}
		if (!empty($item_type)){
			$like .= " AND type like '%$item_type%'"; 
		}
		if (!empty($item_category)){
			$like .= " AND category like '%$item_category%'";
		}
		if (!empty($year)){
			$like .= " AND year like '%$year%'";
		}
		if (!empty($like)){
			$prefix = ' AND ';
			$query = substr($like, strlen($prefix));

			global $connection;
 	    	$results = $connection->query("	SELECT * 
											FROM item 
											WHERE $query;");

 	    	if(!$results) {       

	 			printf("<b>Error: please attempt again</b>");
	 			returnSearchPage();

    		} else {

    			print '<table>';

    				print'<tr><th>Results:</th></tr>';

    				print '<tr>';
			    		print '<th>Quantity</th>';
		    			print '<th>UPC</th>';
		    			print '<th>Title</th>';
		    			print '<th>Type</th>';
		    			print '<th>Category</th>';
		    			print '<th>Company</th>';
		    			print '<th>Year</th>';
		    			print '<th>Price</th>';
		    			print '<th>Stock</th>';
	    			print '</tr>';


 				$i = 0;
				while($row = $results->fetch_assoc()) {
	   				print '<tr>';
				    	print '<td><input type="text" size="5" name="purchase['.$i.'][qty]"></td>';
		    			print '<input type="hidden" size="5" name="purchase['.$i.'][upc]" value="'.$row["it_upc"].'">';
		    			print '<td>'.$row["it_upc"].'</td>';
		    			print '<td>'.$row["it_title"].'</td>';
		    			print '<td>'.$row["type"].'</td>';
		    			print '<td>'.$row["category"].'</td>';
		    			print '<td>'.$row["company"].'</td>';
		    			print '<td>'.$row["year"].'</td>';
		    			print '<td>'.$row["price"].'</td>';
		    			print '<td>'.$row["stock"].'</td>';
	    			print '</tr>';
	    
	    			$i++;
				}  
    			print '</table>';


				$results->free();

    		}	    
		} else {
			print'Error, please input something!';
			returnSearchPage();
		}
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
				<td></td>
				<td style="text-align: right"><input type="submit" value="Search"></td>
			</tr>
		</table>
	</form>

<?php 
}
?>