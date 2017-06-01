    
<?php //if($_SERVER['PHP_SELF'] == '/demo.php'){ $method = 'POST'; } else { $method = 'GET'; } ?>

<?php
    $yesterday = new DateTime();
    $yesterday->sub(new DateInterval('P1D'));
    $yesterday = $yesterday->format('Y-m-d');
	
	

    $day_before_yesterday = new DateTime();
    $day_before_yesterday->sub(new DateInterval('P2D'));
    $day_before_yesterday = $day_before_yesterday->format('Y-m-d');



?>


	<div class="row">
		<div class = "col-sm-8 col-sm-offset-2" style = "padding-bottom:15px">
			<p>Capitol Hound is a searchable audio archive from the 2013, 2014, 2015 and 2016 legislative sessions of the North Carolina General Assembly. Its searchable archives contain audio and transcripts from the House and Senate floor, as well as key committee meetings. It is not being updated and no new alerts are being sent.</p>
		</div>
	</div>
	
	<form class="col-sm-8 col-sm-offset-2 "  method="post" action="<?php  echo $_SERVER['PHP_SELF'];?>">
        <input type="hidden" name="step" value="1"/>
    <div class="row">		
        <div class="input-group input-group-lg searchbar">
			<input placeholder="Search keywords" id="id_q" name="search_term" type="text" value="<?php //if($_SERVER['PHP_SELF'] == '/demo.php'){ echo 'healthcare'; }?>" class="form-control"i <?php //if($_SERVER['PHP_SELF'] == '/demo.php'){echo 'readonly=""';}?>/><br/>
			<span class="input-group-btn">
                <button class="btn btn-default" type="submit" value="Search">
                <i class="glyphicon glyphicon-search"></i>
			    </button>
            </span>
	</div>
    </div>
    <div class="row">
        
        <div class="col-sm-5 col-sm-offset-1 daterange">
            <label>Start Date</label>
            <div class="input-group input-group-lg date" id="start" data-date-format="YYYY-MM-DD">
                <input class="form-control" type="text" name="start_date" value="2015-01-14">
                <span class="input-group-btn">
                    <button class="btn btn-default">
                        <i class="glyphicon glyphicon-calendar"></i>
                    </button>
                </span>
            </div>
        </div>
        <div class="col-sm-5 daterange">
            <label>End Date</label>
            <div class="input-group input-group-lg date" id="end" data-date-format="YYYY-MM-DD">
                <input class="form-control" type="text" name="end_date" value="<?php print $yesterday; ?>" >
                <span class="input-group-btn">
                    <button class="btn btn-default">
                        <i class="glyphicon glyphicon-calendar"></i>
                    </button>
                </span>
            </div>
        </div>
        </form>
    </div>
    <div class="row">
	<div class="col-sm-10 col-sm-offset-1">
	    <br/></br/>
	    <center>
	    <p><a href="/view_transcripts.php">Browse by date</a> | <a href="/list_of_members.php">Browse by legislator</a></p>
	    </center>
	</div>
    </div>
	
<?php 
if (!isset($_POST['search_term'])=='') {
?>
<div class="row">
    <div class="col-sm-12">
        <h2>Results</h2>
		<?php
//load database connection
    $host = "rfdnwp.c2buzbgcpkgr.us-east-1.rds.amazonaws.com";
    $user = "reesenews";
    $password = "bLu3KZ8xSEc";
    $database_name = "capitolhound";
    $pdo = new PDO("mysql:host=$host;dbname=$database_name", $user, $password, array(
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ));
// Search from MySQL database table
$search=$_POST['search_term'];
$date=$_POST['end_date'];
//$query = $pdo->prepare("select * from transcript where title LIKE '%$search%' OR date LIKE '%$date%'  LIMIT 0 , 10");
$query = $pdo->prepare("select * from transcript,transcript_segment WHERE transcript.title LIKE '%$search%' OR transcript.date LIKE '%$date%' OR transcript_segment.content LIKE '%$search%' LIMIT 0 , 10");
//SELECT * FROM TABLE a JOIN TABLE b ON b.column LIKE CONCAT('%', a.column ,'%')





$query->bindValue(1, "%$search%", PDO::PARAM_STR);
$query->execute();
// Display search result
         if (!$query->rowCount() == 0) {
		 		
				
               			
            while ($results = $query->fetch()) {
			$date =  strtotime($results['date']);
    $date1 = date('F d, Y',$date);
			
			 echo '<h4><b>'. $results['chamber'] . '</b>' . ' '. '|' . '  '. $date1 . ' '. '|' . '  '. $results['location'] . ' '. '|' . '  '. $results['title'] . '<br> </h4>' ;
			echo '<a class="btn btn-primary" href="/transcript.php?transcript_id=' . $results['transcript_id'] . '">Full Transcript</a>';			
            }
					
        } else {
            echo 'Nothing found';
        }
	?>
	</div>
</div>
<?php
		}
?>
	 
	<?php
	print "<hr/>$info_warning<hr/>";
	?>
