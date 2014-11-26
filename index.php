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
 
 $error_stack = array();
 $notice_stack = array();


include_once('etc/db_connection.php');
include_once('etc/login_functionality.php');
include_once('etc/dynamic_content_display.php');

ob_start();
getContent(); 
$content = ob_get_contents();
ob_end_clean();


function displayNotices(){
	global $notice_stack;
	if(is_array($notice_stack) && count($notice_stack) > 0){
		print '<div id="notices">';
		foreach($notice_stack as $notice){
			print '<b>Notice:</b> ' . $notice . '<br />';
			unset($notice);
		}
		print '</div>';
	}
}

function displayErrors(){
	global $error_stack;
	if(is_array($error_stack) && count($error_stack) > 0){
		print '<div id="errors">';
		foreach($error_stack as $error){
			print '<b>Error:</b> ' . $error . '<br />';;
			unset($error);
		}
		print '</div>';
	}
}
    
?>

<!-- PAGE DISPLAY STARTS HERE -->
<html>
	<head>
		<meta content="text/html;charset=utf-8" http-equiv="Content-Type">
		<meta content="utf-8" http-equiv="encoding">
		
		<link rel="stylesheet" href="js/jquery-ui-1.11.2/jquery-ui.min.css"  type="text/css" >
		<script src="js/jquery-ui-1.11.2/external/jquery/jquery.js"></script>
		<script src="js/jquery-ui-1.11.2/jquery-ui.min.js"></script>
		<script src="js/datepicker.js"></script>
	
		<title>Online Store V1</title>
		
		<!--  A stylesheet to modify colours, fonts, etc. -->
	    <link href="css/default.css" rel="stylesheet" type="text/css">
	</head>
	
	<body>
		<div id="container">
		<div id="header">
		<h1>Online Store V2</h1>
		</div>
		<div id="nav">
			<h2>Item Search</h2>
			<form name="item_search" method="post" action="?page=advanced_search">
		    <input type="hidden" name="search_type" value="quick">
			<input name="quick_search" type="search" size="12">
			<input type="submit" value="Go">
			</form>
			<a href="?page=advanced_search">advanced search</a>
		<?php 
		if(isset($_SESSION['user_id'])){
			?>
			
				<h2>Customers</h2>
				<ul>
				<li><a href="?page=view_cart">View Cart</a></li>
				<li><a href="?logout=true">Logout</a></li>
				</ul>

				<h2>Clerks</h2>
				<ul>
				<li><a href="?page=return">Return Item</a></li>
				</ul>
			
				<h2>Managers:</h2>
				<ul>
				<li><a href="?page=add_items">Add Items</a></li>
				<li><a href="?page=update_stock">Update Item</a></li>
				<li><a href="?page=process_delivery">Process Delivery</a></li>
				<li><a href="?page=sales_report">Daily Sales Report</a></li>
				<li><a href="?page=top_selling_items">Top Selling Items</a></li>
				</ul>

			<?php
		}else{
			?>
			
				<h2>Customers:</h2>
				<ul>
				<li><a href="?page=user_reg">Registration</a></li>
				<li><a href="/">Login</a></li>
				</ul>
			<?php
		}
		
		?>
		</div>
		<div id="content">
					<?php 
						displayErrors(); 
						displayNotices();
						echo $content; ?>
			
		</div>
		<div id="footer">
			Footer Message
		</div>
		</div>
	</body>
	
</html>