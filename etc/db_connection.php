<?php
// Open a connection to the database
$connection = new mysqli("localhost", "root", "", "amsstore");

// Check that the connection was successful, otherwise exit
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}
?>