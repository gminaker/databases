<?php 

/**
 * Provides framework for appearance of site
 *
 * This file sets up the overall appearance and template
 * of the site. Content is dynamically collected and 
 * displayed from files within the 'view' folder. This 
 * allows us to modify the appearance of the site from one
 * point of control, and to divide the functionality into 
 * separate files. 
 *
 * PHP version 5
 *
 * @author     Gordon, Thomas, Mike, Annie
 * @version    1.0
 *
 */


// Open a connection to the database
// TODO: update connection credentials 
$connection = new mysqli("dbserver.ugrad.cs.ubc.ca", "cs304_student", "goStudentFindData", "bookbiz");

// Check that the connection was successful, otherwise exit
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}
    
 /**
 *
 * Gets the content for the current page. The current page
 * is specified in the URL 'page' variable. 
 *
 */
function getContent(){

	if(isset($_GET['page'])){
	
		  switch ($_GET['page']) {
		  
		  case "user_reg":
		    include('views/customer/customer_reg.php');
		    break;
		    
		  case "purchase":
		    include('views/customer/purchase_items.php');
		    break;
		    
		  case "return":
		    include('views/clerk/process_refund.php');
		    break;
		    
		  case "add_items":
		    include('views/manager/add_items.php');
		    break;
		    
		  case "process_delivery":
		    include('views/manager/process_delivery.php');
		    break;
		    
		  case "sales_report":
		    include('views/manager/sales_report.php');
		    break;
		    
		   case "top_selling_items":
		    include('views/manager/top_selling_items.php');
		    break;
		    
		  default:
		    include('views/default.php');
		}
		
	}else{
		include('views/default.php');
	}
	
}
?>

<!-- PAGE DISPLAY STARTS HERE -->
<html>
	<head>
		<meta content="text/html;charset=utf-8" http-equiv="Content-Type">
		<meta content="utf-8" http-equiv="encoding">
		
		<title>Online Store V1</title>
		
		<!--  A stylesheet to modify colours, fonts, etc. -->
	    <link href="css/default.css" rel="stylesheet" type="text/css">
	</head>
	
	<body>
		<table>
		<tr>
			<td colspan="5"> Online Store 1.0</td>
		</tr>
		<tr>
			<td>Customers:</td>
			<td><a href="?page=user_reg">Registration</a></td>
			<td colspan="3"><a href="?page=purchase">Purchase</a></td>
		</tr>
		<tr>
			<td>Clerks:</td>
			<td><a href="?page=return">Return Item</a></td>
		</tr>
		<tr>
			<td>Managers:</td>
			<td><a href="?page=add_items">Add Items</a></td>
			<td><a href="?page=process_delivery">Process Delivery</a></td>
			<td><a href="?page=sales_report">Daily Sales Report</a></td>
			<td><a href="?page=top_selling_items">Top Selling Items</a></td>
		</tr>
		<tr>
			<td colspan="5"><?php getContent(); ?></td>
		</tr>
		</table>
	</body>
	
</html>