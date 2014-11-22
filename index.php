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

// Start a session to force the user to login
session_start();

$_SESSION['user_id'];
$_SESSION['cart'];

// Open a connection to the database
$connection = new mysqli("localhost", "root", "", "amsstore");

if(isset($_POST['login_id'])){
	$_SESSION['user_id'] = $_POST['login_id'];
}

if(isset($_GET['logout']) && $_GET['logout'] == true){
	$_SESSION['user_id'] = null;
	$_SESSION['cart'] = null;
}

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

	if(isset($_GET['page']) && isset($_SESSION['user_id'])){
	
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
		
	}else if(!isset($_SESSION['user_id']) && isset($_GET['page']) && $_GET['page'] == 'user_reg'){
		include('views/customer/customer_reg.php');
	}else if(isset($_SESSION['user_id'])){
		include('views/default.php');
	}else{
		include('views/login.php');
	}
	
}
?>

<!-- PAGE DISPLAY STARTS HERE -->
<html>
	<head>
		<meta content="text/html;charset=utf-8" http-equiv="Content-Type">
		<meta content="utf-8" http-equiv="encoding">
		
		<link rel="stylesheet" href="jquery-ui-1.11.2/jquery-ui.min.css"  type="text/css" >
		<script src="jquery-ui-1.11.2/external/jquery/jquery.js"></script>
		<script src="jquery-ui-1.11.2/jquery-ui.min.js"></script>
	
		<title>Online Store V1</title>
		
		<!--  A stylesheet to modify colours, fonts, etc. -->
	    <link href="css/default.css" rel="stylesheet" type="text/css">
	</head>
	
	<body>
		<table>
		<tr>
			<td colspan="5"> Online Store 1.0</td>
		</tr>
		<?php 
		if(isset($_SESSION['user_id'])){
			?>
			<tr>
				<td>Customers:</td>
				<td><a href="?page=user_reg">Registration</a></td>
				<td colspan="3"><a href="?page=purchase">Purchase</a></td>
				<td colspan="3"><a href="?logout=true">Logout</a></td>
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

			<?php
		}else{
			?>
			<tr>
				<td>Customers:</td>
				<td><a href="?page=user_reg">Registration</a></td>
				<td colspan="3"><a href="/">Login</a></td>
			</tr>
			<?php
		}
		?>
				<tr>
			<td colspan="5"><?php getContent(); ?></td>
		</tr>
		</table>
	</body>
	
</html>