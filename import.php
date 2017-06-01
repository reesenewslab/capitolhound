
<?php

require_once("models/config.php");
if (!securePage($_SERVER['PHP_SELF'])){die();}

require_once('models/model.php');
require_once('models/db-settings.php');
require_once("models/header.php");


if ($_POST["step"] == 1) {

    // get input from the upload form
    $chamber = $_POST["chamber"];
    $year = $_POST["year"];
    $title = $_POST["title"];
    $location = $_POST["location"];
    $date = $_POST["date"];
    $audio_file = $_POST["audio_file"];
    $description = $_POST["description"];
    $transcript_file = $_POST["transcript_file"];

    //if ($chamber="" || $year="" || $title="" || $date="" || $audio_file="" || $transcript_file="") {
    //if (empty($chamber) || empty($year) || empty($title) || empty($date) || empty($audio_file) || empty($transcript_file)) {
   // if (! isset($chamber, $year, $title, $date, $audio_file, $transcript_file)) {

      if (! $chamber || ! $year || ! $title || ! $date || ! $audio_file || ! $_FILES["transcript_file"]["name"]) {

        print <<<__HTML__
<h1>ERROR Uploading Transcripts!</h1>
Please be sure to specify all required information (Chamber, Session Year, Title, Date of Transcript, Audio File URL and Transcript File) 
__HTML__;

        die();

    }

   print "$chamber - $year - $title - $date - $audio_file - $transcript_file<br/>";


    // the full path to the transcript we are uploading for processing
    $new_transcript_file = $transcript_directory . "/" . $_FILES["transcript_file"]["name"];

    if ($_FILES["transcript_file"]["error"] > 0) {
        echo "Return Code: " . $_FILES["transcript_file"]["error"] . "<br>";
    }
    else {
        //print "Upload: " . $_FILES["transcript_file"]["name"] . "<br>";
        ////print "Type: " . $_FILES["transcript_file"]["type"] . "<br>";
        //print "Size: " . ($_FILES["transcript_file"]["size"] / 1024) . " kB<br>";
        //print "Temp file: " . $_FILES["transcript_file"]["tmp_name"] . "<br>";

        // don't upload a file if it already exists.
        if (file_exists($new_transcript_file)) {
            echo $_FILES["transcript_file"]["name"] . " already exists. ";
            die();
        }
        else {
            move_uploaded_file($_FILES["transcript_file"]["tmp_name"], $new_transcript_file);
            echo "Stored Transcript: $new_transcript_file ... <br/>";
        }
    }

    //set up the connection to the Database
    //$mysqli = new mysqli($mysql_server, $mysql_username, $mysql_password, $mysql_database);

    /* check connection */
    if ($mysqli->connect_errno) {
        printf("Connect failed: %s\n", $mysqli->connect_error);
        exit();
    }

    $chamber = $mysqli->real_escape_string($chamber);
    $location = $mysqli->real_escape_string($location);
    $title = $mysqli->real_escape_string($title);
    $audio_file = $mysqli->real_escape_string($audio_file);
    $description = $mysqli->real_escape_string($description);

    // set up the MySQL query for the main transcript entry into the "transcript" table.
    $full_transcript_query = "INSERT INTO transcript (chamber, year, location, title, date, audio_file, description) VALUES('$chamber', $year, '$location', '$title', STR_TO_DATE('$date', '%Y-%m-%d'), '$audio_file', '$description')";
    //print "QUERY is $full_transcript_query <br/>";
    // insert record into the transcript table
    $mysqli->query($full_transcript_query);

    // get the AUTO INCREMENT ID of the previous query so we can use that id on transcript_segments inserts.
    $transcript_id = $mysqli->insert_id;

    print "Added Transcript to DB with ID $transcript_id ... <br/>";

    // Read in the Transcript FILE (HTML) and parse through it
    // Each segment is in a <p id="xx:xx"></p> where xx:xx is the timecode of the audio chunk
    $dom = new DOMDocument;
    $dom->loadHTMLFile($new_transcript_file);
    foreach($dom->getElementsByTagName('p') as $segment) {
        $timecode = $segment->getAttribute('id');
        $content = $segment->textContent;

        $timepieces = explode(":", $timecode);
        $seconds = (int)(($timepieces[0] * 60) + $timepieces[1]);

        print "Segment Added started at $seconds seconds of the audio file...<br/>";
        //print "TRANSCRIPT:<br/> $content<p>";

        $content = $mysqli->real_escape_string($content);

        $timestamp = strtotime($date);

        $segment_query = "INSERT INTO transcript_segment (transcript_id, timecode, content, date, timestamp) VALUES($transcript_id, $seconds, '$content', STR_TO_DATE('$date', '%Y-%m-%d'), $timestamp)";
        //print "$segment_query <p>";
        $mysqli->query($segment_query);
        print "Added Transcript Segment to the DB... <br/>";

    }

    // close out the database connection
    $mysqli->close();

    print "<p><b>Successfully added Transcript to Database</b></p>";

}

else {
    $yesterday = new DateTime();
    $yesterday->sub(new DateInterval('P1D'));
    $yesterday = $yesterday->format('Y-m-d');

//<form class="col-sm-offset-3 col-sm-6" role="form" action="import.php" enctype="multipart/form-data" method="post">

    print <<<__HTML__

<h1>Upload Transcripts</h1>

<form class="col-sm-offset-3 col-sm-6" role="form"  method="post" action="import.php" enctype="multipart/form-data">
<input type="hidden" name="step" value="1">

<div class="input-group input-group-lg">
	<label>Chamber <span class="required">*</span></label>
	<select class="form-control" name="chamber">
		<option value="Senate">Senate</option>
		<option value="House">House</option>
		<option value="Joint">Joint</option>
	</select>
</div>

<div class="input-group input-group-lg">
	<label>Session Year <span class="required">*</span></label>
	<select class="form-control"  name="year">
		<option value="2016">2016</option>
		<option value="2015">2015</option>
		<option value="2014">2014</option>
		<option value="2013">2013</option>
	</select>
</div>

<h3>Transcript</h3>

<div class="input-group input-group-lg">
	<label>Transcript Title <span class="required">*</span></label>
	<input class="form-control" type="text" name="title" size="50"/>
</div>

<div class="input-group input-group-lg">
	<label>Location</label>
	<select class="form-control" name="location">
		<option value="Chamber">Chamber</option>
		<option value="Committee Room">Committee Room</option>
		<option value="Press Room">Press Room</option>
	</select>
</div>

<div class="input-group input-group-lg">
         <label>Date of Transcript <span class="required">*</span> <em>YYYY-MM-DD</em></label>
	<input class="form-control" type="text" name="date" size="50" value="$yesterday" />
</div>

<div class="input-group input-group-lg">
	<label>Audio File Location <span class="required">*</span> (full path - http://abc.com/path_to_file/audio.mp3)</label>
	<input class="form-control" type="text" name="audio_file" size="50"/>
</div>

<div class="input-group input-group-lg">
	<label>Description</label>
	<textarea class="form-control" name="description" rows="5" cols="45"></textarea>
</div>

<div class="input-group input-group-lg">
	<label>Transcript File <span class="required">*</span></label>
	<input class="form-control" type="file" name="transcript_file">
</div>

<div class="input-group input-group-lg">
	<span class="required">*</span> Indicates a required field<br/>
	<button class="btn btn-lg btn-primary" type="submit" name="submit" value="Upload Transcript">Upload Transcript</button>
</div>


</form>



__HTML__;


}

require_once("models/footer.php");


?>
