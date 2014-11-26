<?php

/**
 * Provides content and functionatilty for Managers to Update stock and/or prices. 
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
		      						$_POST['item_price'],
		      						$_POST['item_stock'] );
      }
 }
 
 /**
 * sets up calls to check values and insert into DB 
 *
 * @param string $upc
 * @param string $prices
 * @param string $stock
 *
 */
 function checkValsThenInsertIntoDB($upc, $price, $stock){
	global $error_stack;
	$msg = checkValues($upc, $price, $stock); 

	
	if(!$msg){
		$msg = checkExists($upc);
		if(!$msg){
		insertIntoDB($upc, $price, $stock);
		}
	}
}

/**
 * if we wanted to do a pre-check for NULL values, 
 * or check category values, etc.
 *
 * @param string $upc
 * @param string $prices
 * @param string $stock
 *
 */
function checkValues($upc, $price, $stock){
	
	global $error_stack;
	$errors = null;
	
	if (empty($upc)){
		array_push($error_stack, "Please fill in UPC");
		$errors = true;
	} else if (!is_numeric($upc) or (strlen($upc) != 12)){
		array_push($error_stack, "Please recheck UPC");
		$errors = true;	
	}
	
	if (!empty($stock) and (!is_numeric($stock) or (intval($stock) < 0))){
		array_push($error_stack, "Please recheck stock");
		$errors = true;
	}

	if (!empty($price) and (!is_numeric($price) or (floatval($price) < 0.0))){
		array_push($error_stack, "Please recheck price");
		$errors = true;
	}

	if (empty($price) and empty($stock)){
		array_push($error_stack, "Please fill in one of: stock, price");
		$errors = true;
	} 
	
	return $errors;
}

/**
* Check if UPC exists in DB
*
*@param string $upc
*
*/

function checkExists($upc){
	global $error_stack;
	global $connection;

	$errors = null;

	$results = $connection->query("SELECT * FROM item WHERE it_upc = '$upc';");

	if(!$results){
		array_push($error_stack, $connection->error);
	}else if($results->num_rows == 0){
		array_push($error_stack, 'Not a valid upc.');
		$errors = true;
		$results->free();
	}
	return $errors;
}


 /**
 * Insert the values received from the form into the items
 * table of our current DB connection. 
 *
 * @param string $upc
 * @param string $prices
 * @param string $stock
 *
 */
function insertIntoDB($upc, $price, $stock){
	//Since $connection was declared in another page 
	// within the site, we call global on it
	global $connection;
	global $error_stack;
	global $notice_stack;
	
 	if (empty($price)){
 		$query = '	UPDATE item
					SET stock = stock + '.$stock.'
					WHERE it_upc = '.$upc.';';
 	} else if (empty($stock)){
 		$query = '	UPDATE item
					SET price = '.$price.'
					WHERE it_upc = '.$upc.';';

 	} else{
 		$query = '	UPDATE item
					SET stock = stock +'.$stock.', price = '.$price.'
					WHERE it_upc = '.$upc.';';
 	}

 	$results = $connection->query($query);

    // Print any errors if they occured
    if($connection->error) {       
      array_push($error_stack, "<b>Error: %s.</b>\n", $results->error);
    } else {
      array_push($notice_stack, "<b>Successfully updated ".$upc."</b>");
      unset($_POST);
    }      
    
 }
 
 
 function postValue($name){
	 if(isset($_POST[$name])){
		 print $_POST[$name];
	 }
 }

 
 ?>

 <h1>Update Items Page</h1>
 <form method="post" name="add_item">
 <input type="hidden" name="submit_new_item" value="SUBMIT">
 
 <table>
	 <tr>
		 <td>UPC</td>
		 <td><input type="text" name="item_upc" value="<?php postValue('item_upc'); ?>"></td>
	 </tr>
	 <tr>
		 <td>Price</td>
		 <td><input type="number" step ='any' name="item_price" value="<?php postValue('item_price'); ?>"></td>
	 </tr>
	 <tr>
		 <td>Stock</td>
		 <td><input type="text" name="item_stock" value="<?php postValue('item_stock'); ?>"></td>
	 </tr>
	 <tr>
		 <td></td>
		 <td><input type="submit" name="item_submit" ></td>
	 </tr>
 </table>