<?php

/**
 * Provides content and functionatilty for Viewing the Shopping Cart
 *
 * <long description>
 *
 * PHP version 5
 *
 * @author     Gordon
 * @since    2.0
 *
 */
 global $connection;
 
  if ($_SERVER["REQUEST_METHOD"] == "POST") {
 		
      if (isset($_POST["delete_upc"])){
	      $key = in_array($_POST["delete_upc"],$_SESSION['cart']);
	      $index = $_POST["delete_upc"];
	      unset($_SESSION['cart'][$index]);
	      
		}
		
		if(isset($_POST['add_to_cart']) && $_POST['add_to_cart'] == true){
			if($_POST['cart_qty'] == 0){
				print 'Error: don\'t add zero to cart';
			}else{
				$key = $_POST['cart_upc'];
				$qty = $_POST['cart_qty'];
				
				if(isset($_SESSION['cart'][$key])){
					$current_cart_qty = $_SESSION['cart'][$key];
				}else{
					$current_cart_qty = 0;
				}
				
				$result = $connection->query("SELECT * FROM item WHERE it_upc = $key");
				$row = $result->fetch_assoc();
				$max = $row['stock'];
				
				$proposed_cart_qty = $current_cart_qty + $qty;
				
				if($proposed_cart_qty > $max){
					$new_qty = $qty - ($proposed_cart_qty - $max);
					print 'Sorry, we don\'t have '.$qty.' of those in stock. We\'ve added '.$new_qty.' instead.';
					$qty = $new_qty;
				}
				
				$_SESSION['cart'][$key] += $qty;

			}
		}
    }
    

 
 function generateCartDisplay(){
 	 
	 foreach($_SESSION['cart'] as $key => $value){
		 getItemRow($key, $value);
	 }
 }
 
 function getItemRow($upc, $qty){
	global $connection;
 	$result = $connection->query("SELECT * FROM item WHERE it_upc = $upc");
 	
 	if($result->num_rows == 0){
	 	print '<tr><td colspan=9>No Items Found</td</tr>';
 	}

 	$i = 0;
	while($row = $result->fetch_assoc()) {
	    print '<tr>';
		    print '<td>'.$qty.'</td>';
		    print '<input type="hidden" size="5" name="cart['.$i.'][upc]" value="'.$row["it_upc"].'">';
		    print '<input type="hidden" size="5" name="cart['.$i.'][qty]" value="'.$qty.'">';
		    print '<td>'.$row["it_upc"].'</td>';
		    print '<td>'.$row["it_title"].'</td>';
		    print '<td>'.$row["type"].'</td>';
		    print '<td>'.$row["category"].'</td>';
		    print '<td>'.$row["company"].'</td>';
		    print '<td style="text-align:right">'.$row["year"].'</td>';
		    print '<td style="text-align:right">'.$row["price"].'</td>';
		    print '<form name="delete_item" method="post" action="">';
		    print '<input type="hidden" name="delete_upc" value="'.$upc.'">';
		    print '<td style="text-align:right"><input type=submit value="Delete"></td>';
	    print '</form></tr>';
	    
	    $i++;
	}  

	$result->free();
 }
 
 ?>
<h2>Shopping Cart</h2>
 <table border="1" width="500px";>
	 <tr>
		 <th>QTY</th>
		 <th>UPC</th>
		 <th>Title</th>
		 <th>Type</th>
		 <th>Category</th>
		 <th>Company</th>
		 <th>Year</th>
		 <th>Price</th>
		 <th>Delete</th>
	 </tr>
	 
	 <?php  if(isset($_SESSION['cart']) && is_array($_SESSION['cart'])){
				 generateCartDisplay();
 			}
 	 ?>
	 
  </table><br /><br />
  <h2>Ready to Checkout?</h2>
  <p>Just enter your credit card number below, and we will steal your money and ship <br />your order to the address that we don't have on file. Thanks for shopping with us!</p>
   <table>
	   <form name="purchase_submit" method="post" action="?page=purchase">
	   <input type="hidden" name="purchase_items" value="SUBMIT">
	 <tr>
		 <td>Credit Card Number</td>
		 <td><input type="text" name="user_ccno"></td>
	 </tr>
	 <tr>
		 <td>Credit Card Expiry</td>
		 <td><input type=date name="user_ccex"></td>
	 </tr>
	 <tr>
		 <td></td>
		 <td><input type=submit value="Purchase Items"></td>
	 </tr>
	   </form>
 </table>