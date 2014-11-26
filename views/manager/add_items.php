<?php

/**
 * Provides content and functionatilty for Managers to Add Items. 
 *
 * <long description>
 *
 * PHP version 5
 *
 * @author     Gordon
 * @since      1.0
 *
 */
 
 
if ($_SERVER["REQUEST_METHOD"] == "POST") {
 		
      if (isset($_POST["submit_new_item"]) && $_POST["submit_new_item"] == "SUBMIT") {
		checkValsThenInsertIntoDB(	$_POST['item_upc'],
		      						$_POST['item_title'],
		      						$_POST['item_type'],
		      						$_POST['item_category'],
		      						$_POST['item_company'],
		      						$_POST['item_year'],
		      						$_POST['item_price'],
		      						$_POST['item_stock'] );
      }
 }
 
 /**
 * sets up calls to check values and insert into DB 
 *
 * @param string $upc
 * @param string $title
 * @param string $type -- one of: cd dvd
 * @param string $category -- one of: rock, pop, rap, country, classical, new age and instrumental.
 * @param string $company
 * @param string $year
 * @param string $prices
 * @param string $stock
 *
 */
 function checkValsThenInsertIntoDB($upc, $title, $type, $category, $company, $year, $price, $stock){
	 
	$msg = checkValues($upc, $title, $type, $category, $company, $year, $price, $stock); 
	
	if(!$msg){	
		insertIntoDB($upc, $title, $type, $category, $company, $year, $price, $stock);
	}
}

/**
 * if we wanted to do a pre-check for NULL values, 
 * or check category values, etc.
 *
 * @param string $upc
 * @param string $title
 * @param string $type -- one of: cd dvd
 * @param string $category -- one of: rock, pop, rap, country, classical, new age and instrumental.
 * @param string $company
 * @param string $year
 * @param string $prices
 * @param string $stock
 *
 */
function checkValues($upc, $title, $type, $category, $company, $year, $price, $stock){
	
	global $error_stack;
	$errors = null;
	
	if (!is_numeric($upc) or (strlen($upc) != 12)){
		array_push($error_stack, "Please recheck UPC");
		$errors = true;
	}
	
	if (strlen($title) > 40){
		array_push($error_stack,"Title too long");
		$errors = true;
	}
	
	if (strlen($company) > 40){
		array_push($error_stack, "Company name too long");
		$errors = true;
	} 
	
	if (!is_numeric($year) or (strlen($year) != 4)){
		array_push($error_stack, "Please recheck year");
		$errors = true;
	} 
	
	if (!is_numeric($stock) or (intval($stock) < 0)){
		array_push($error_stack, "Please recheck stock");
		$errors = true;
	}
	
	if (empty($upc) or empty($title) or empty($type) or empty($category) or empty($company) or empty($year) or empty($price) or empty($stock)){
		array_push($error_stack, "Please fill in all fields");
		$errors = true;
	} 
	
	return $errors;
}


 /**
 * Insert the values received from the form into the items
 * table of our current DB connection. 
 *
 * @param string $upc
 * @param string $title
 * @param string $type -- one of: cd dvd
 * @param string $category -- one of: rock, pop, rap, country, classical, new age and instrumental.
 * @param string $company
 * @param string $year
 * @param string $prices
 * @param string $stock
 *
 */
function insertIntoDB($upc, $title, $type, $category, $company, $year, $price, $stock){
	//Since $connection was declared in another page 
	// within the site, we call global on it
	global $connection;
	global $error_stack;
	
 	$stmt = $connection->prepare("INSERT INTO item (it_upc, it_title, type, category, company, year, price, stock) 
 	VALUES (?,?,?,?,?,?,?,?)");
          
    // Bind the title and pub_id parameters, 'sss' indicates 3 strings
    $stmt->bind_param("ssssssss",$upc, $title, $type, $category, $company, $year, $price, $stock);
    
    // Execute the insert statement
    $stmt->execute();
    
    // Print any errors if they occured
    if($stmt->error) {       
      array_push($error_stack, "<b>Error: %s.</b>\n", $stmt->error);
    } else {
      array_push($notice_stack, "<b>Successfully added ".$title."</b>");
      unset($_POST);
    }      
    
 }
 
 
 function postValue($name){
	 if(isset($_POST[$name])){
		 print $_POST[$name];
	 }
 }

 
 ?>

 <h1>Add Items Page</h1>
 <form method="post" name="add_item">
 <input type="hidden" name="submit_new_item" value="SUBMIT">
 
 <table>
	 <tr>
		 <td colspan="2">New Item</td>
	 </tr>
	 <tr>
		 <td>UPC</td>
		 <td><input type="text" name="item_upc" value="<? postValue('item_upc'); ?>"></td>
	 </tr>
	 <tr>
		 <td>Type</td>
		 <td><select name="item_type">
			 	<option value="">Select...</option>
			 	<option value="cd">CD</option>
			 	<option value="dvd">DVD</option>
			 </select>
		 </td>
	 </tr>
	 <tr>
		 <td>Title</td>
		 <td><input type="text" name="item_title" value="<? postValue('item_title'); ?>"></td>
	 </tr>
	 <tr>
		 <td>Category</td>
		 <td>
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
		 <td>Company</td>
		 <td><input type="text" name="item_company" value="<? postValue('item_company'); ?>"></td>
	 </tr>
	 <tr>
		 <td>Year</td>
		 <td><input type="text" name="item_year" value="<? postValue('item_year'); ?>"></td>
	 </tr>
	 <tr>
		 <td>Price</td>
		 <td><input type="number" name="item_price" value="<? postValue('item_price'); ?>"></td>
	 </tr>
	 <tr>
		 <td>Stock</td>
		 <td><input type="text" name="item_stock" value="<? postValue('item_stock'); ?>"></td>
	 </tr>
	 <tr>
		 <td></td>
		 <td><input type="submit" name="item_submit" ></td>
	 </tr>
 </table>