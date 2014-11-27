<?php
// Open a connection to the database
$connection = new mysqli("localhost", "root", "", "test");
global $error_stack;

// Check that the connection was successful, otherwise exit
if (mysqli_connect_errno()) {
	array_push($error_stack, mysqli_connect_error());
}
?>