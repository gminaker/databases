<?php
	
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		
		if($_POST['search_type'] == 'quick'){
			returnQuickSearchResults();
		}else if($_POST['search_type'] == 'advanced'){
			returnAdvancedSearchResults();
		}
	}
	
	function returnQuickSearchResults(){
		
		$search_query = $_POST['quick_search'];
		
		global $connection;
 	    $results = $connection->query("SELECT * 
	 								  FROM item 
	 								  WHERE MATCH (it_upc, it_title, type, category, company, year) 
	 								  AGAINST ('$search_query')");
	 								 
		if(!$results) {       
	 		printf("<b>Error</b>");
    	} else {
			echo "<b>Successfully Executed</b>";
			print_r($results);
    	} 
    	
		while($row = $results->fetch_assoc()) {
			
		}
		
 	
 		    
    	
	}
	
	function returnAdvancedSearchResults(){
		
	}
	
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
				<td style="text-align: right">
					<select name="item_type">
					 	<option value="">Select...</option>
					 	<option value="cd">CD</option>
					 	<option value="dvd">DVD</option>
				 	</select>
				</td>
			</tr>
			<tr>
				<th>Category</th>
				<td style="text-align: right">
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