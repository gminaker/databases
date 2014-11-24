 <?php
	 
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

		  case "view_cart":
		    include('views/customer/view_cart.php');
		    break;
		    
		  case "advanced_search":
		    include('views/customer/advanced_search.php');
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