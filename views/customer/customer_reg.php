<?php

/**
 * Provides content and functionatilty for Users to
 * register themselves on the site. 
 *
 * <long description>
 * Customer (cid, password, name, address, phone)
 *
 * PHP version 5
 *
 * @author     Gordon
 * @since      1.0
 *
 */
 
 // CHECK FOR AN INCOMING REQUEST BEFORE RENDERING THE PAGE. 
 // sorrry for yelling. 
 
 if ($_SERVER["REQUEST_METHOD"] == "POST") {

      if (isset($_POST["UserReg"]) && $_POST["UserReg"] == "SUBMIT") {
      		insertIntoDB(	$_POST['user_name'],
      						$_POST['user_address'],
      						$_POST['user_phone'],
      						$_POST['user_id'],
      						$_POST['user_password'] );
      }
 }
 
 function insertIntoDB(name, addr, tel, id, pass){
 	//TODO 
 }
 
 ?>
 
 <!-- PAGE RENDER BEGINS HERE -->
 <h1>Customer Registration Page</h1>
    
 <form method="post" name="register_user" action="">
 <input type="hidden" name="UserReg" value="SUBMIT">
	 <table>
		 <tr>
		 	<td>Name:</td>
		 	<td><input type="text" name="user_name" maxlength="20"></td>
		 </tr>
	 	 <tr>
		 	<td>Address:</td>
		 	<td><input type="text" name="user_address" maxlength="40"></td>
		 </tr>
	  	 <tr>
		 	<td>Phone:</td>
		 	<td><input type="tel" name="user_phone"></td>
		 </tr>
		 <tr>
		 	<td>User ID:</td>
		 	<td><input type="text" name="user_id" maxlength="20"></td>
		 </tr>
		 <tr>
		 	<td>Password:</td>
		 	<td><input type="password" name="user_password" maxlength="20"></td>
		 </tr>
		 <tr>
		 	<td></td>
		 	<td><input type="submit"></td>
		 </tr>
	 </table>
 </form>