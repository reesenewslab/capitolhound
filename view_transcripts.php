<?php
require_once("models/header.php");

error_reporting(E_ALL); 
ini_set("display_errors", 1); 

 

// CONNECTION VARIABLES
$db_host = "rfdnwp.c2buzbgcpkgr.us-east-1.rds.amazonaws.com"; //Host address (most likely localhost)
$db_name = "capitolhound"; //Name of Database
$db_user = "reesenews"; //Name of database user
$db_pass = "bLu3KZ8xSEc"; //Password for database user

/*
 * mysql server connect
*/
$con = mysqli_connect("rfdnwp.c2buzbgcpkgr.us-east-1.rds.amazonaws.com","reesenews","bLu3KZ8xSEc","capitolhound");

/*$link = mysqli_connect($db_host, $db_user , $db_pass);
    if (!$link) {
        die('Could not connect: ' . mysql_error());
}*/

/* connect to db */

/*$db_selected = mysqli_select_db( $db_name, $link);
    if (!$db_selected) {
        die ('Can\'t use foo : ' . mysql_error());
}*/

/* my first query */

$first_query = "select * from transcript ";

if (isset($_GET['date'])){
$first_query .= "where date = '".$_GET['date']." ' ";

}

$first_query .= " order by date desc";

   //echo '<h2>my name is '. $_GET['name'].'</h2>';

/* run that query */
$result = mysqli_query($con, $first_query);
    if (!$result) {
        die('Invalid query: ' . mysql_error());
}
?>

<div class="row">
    <div class="col-xs-8">
        <h2>Search Transcripts by Date</h2>
    <form class="form-inline">

        <div class="daterange"> 
            <div class="input-group input-group-lg date" id="start" data-date-format="YYYY-MM-DD">
                <input class="form-control" type="text" name="date" value="" >
                <span class="input-group-btn">
                    <button class="btn btn-default">
                        <i class="glyphicon glyphicon-calendar"></i>
                    </button>
                </span>
            </div>
        </div>
        <br/>
        <button type='submit' class="btn btn-primary">Search</button>
        <hr/>
    </form>
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <h2>Results</h2>
<?php

while($row = mysqli_fetch_array($result))
{
    $date =  strtotime($row['date']);
    $date = date('F d, Y',$date);    
    
    
    echo '<h4><b>'. $row['chamber'] . '</b>' . ' '. '|' . '  '. $date . ' '. '|' . '  '. $row['location'] . ' '. '|' . '  '. $row['title'] . '<br> </h4>' ;
    echo '<a class="btn btn-primary" href="/transcript.php?transcript_id=' . $row['transcript_id'] . '">Full Transcript</a>'; 
}

?>
    </div>
</div>
<?php







require_once("models/footer.php");
?>