<?php
require_once("models/header.php");

// CONNECTION VARIABLES
$db_host = "rfdnwp.c2buzbgcpkgr.us-east-1.rds.amazonaws.com"; //Host address (most likely localhost)
$db_name = "capitolhound"; //Name of Database
$db_user = "reesenews"; //Name of database user
$db_pass = "bLu3KZ8xSEc"; //Password for database user

/*
 * mysql server connect
*/

$link = mysql_connect($db_host, $db_user , $db_pass);
    if (!$link) {
        die('Could not connect: ' . mysql_error());
}

/* connect to db */

$db_selected = mysql_select_db( $db_name, $link);
    if (!$db_selected) {
        die ('Can\'t use foo : ' . mysql_error());
}

/* my first query */

$first_query = "select * from transcript order by date";

   //echo '<h2>my name is '. $_GET['name'].'</h2>';

/* run that query */
$result = mysql_query($first_query);
    if (!$result) {
        die('Invalid query: ' . mysql_error());
}

while($row = mysql_fetch_array($result, MYSQL_ASSOC))
{
    echo $row['chamber '];
    echo $row['date'];
    echo '<br>';
    
    
 

}







require_once("models/footer.php");
?>