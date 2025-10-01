<?php
define('DB_SERVER','localhost');
define('DB_USER','lewvitec_admin');
define('DB_PASS' ,'Ladasha2024');
define('DB_NAME', 'lewvitec_shopping');
$con = mysqli_connect(DB_SERVER,DB_USER,DB_PASS,DB_NAME);
// Check connection
if (mysqli_connect_errno())
{
 echo "Failed to connect to MySQL: " . mysqli_connect_error();
}
?>