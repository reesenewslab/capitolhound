
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

    // Clean up the audio ULRs - NO parameters left in a URL in case someone copied/pasted wrong.
    $audio_url =  parse_url($audio_file);
    $audio_file = $audio_url["scheme"] . "://" . $audio_url["host"] . $audio_url["path"];

    // the full path to the transcript we are uploading for processing
    // Get the Session Year from drop downs so we can put it in the right year's directory
    $new_transcript_file = $transcript_directory . "/$year/" . $_FILES["transcript_file"]["name"];


    // ERROR CHECKING!

    // Did we input everything?
    if (! $chamber || ! $year || ! $title || ! $date || ! $audio_file || ! $_FILES["transcript_file"]["name"]) {
        print <<<__HTML__
<h1>ERROR Uploading Transcripts!</h1>
Please be sure to specify all required information (Chamber, Session Year, Title, Date of Transcript, Audio File URL and Transcript File) 
__HTML__;

        die();
    }
    // Is the Date formatted properlly
    else if (! preg_match("/^(\d\d\d\d)-(\d\d)-(\d\d)$/", $date)) {
        print <<<__HTML__
<h1>ERROR Uploading Transcripts!</h1>
Your Date of Transcript <b>$date</b> doesn't appear to be the correct format of YYYY-MM-DD
__HTML__;

        die();
    }
    // If the the link to the audio file appear to be an actual link
    // We probably could so something better here to test the link for a 200....
    else if (($audio_url["scheme"] != "http") && ($audio_url["scheme"] != "https")) {
        print <<<__HTML__
<h1>ERROR Uploading Transcripts!</h1>
Your Audio File URL <b>$audio_file</b> doesn't appear to be a valid link in the form of http://domain.com/path_to_audio/file.mpg
__HTML__;

        die();
    }
    // Did we get an erro from the file uplaod
    else if ($_FILES["transcript_file"]["error"] > 0) {
        echo "Return Code: " . $_FILES["transcript_file"]["error"] . "<br>";
        die();
    }
    // don't upload a file if the target directory on the server doesn't exist
    else if (! file_exists($transcript_directory . "/" . $year)) {
        print "<h1>ERROR Uploading Transcripts!</h1><br/>";
        print "The directory on the server <b>$transcript_directory/$year/</b> doesn't exist. Check with the server admin";
        die();
    }
    // don't upload a file if it already exists.
    else if (file_exists($new_transcript_file)) {
        print "<h1>ERROR Uploading Transcripts!</h1><br/>";
        print "Your XML File <b> " . $_FILES["transcript_file"]["name"] . "</b> already exists.";
        die();
    }
    // is the transcript an XML file?
    else if ($_FILES["transcript_file"]["type"] != "text/xml") {
        print "<h1>ERROR Uploading Transcripts!</h1><br/>";
        print "Your XML File <b> " . $_FILES["transcript_file"]["name"] . "</b> doesn't appear to be an XML file.";
        die();
    }
    // If we get here, save the transcript and keep on going
    else {
        //print "Upload: " . $_FILES["transcript_file"]["name"] . "<br>";
        //print "Type: " . $_FILES["transcript_file"]["type"] . "<br>";
        //print "Size: " . ($_FILES["transcript_file"]["size"] / 1024) . " kB<br>";
        //print "Temp file: " . $_FILES["transcript_file"]["tmp_name"] . "<br>";

        move_uploaded_file($_FILES["transcript_file"]["tmp_name"], $new_transcript_file);
        echo "Stored Transcript: $new_transcript_file ... <br/>";

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
     print "Full query is $full_transcript_query<br/>";

    // get the AUTO INCREMENT ID of the previous query so we can use that id on transcript_segments inserts.
    $transcript_id = $mysqli->insert_id;

    print "Added Transcript to DB with ID $transcript_id ... <br/>";

    // Read in the Transcript FILE (XML) and parse through it
    // Each segment is in a <p id="11" begin="00:00:23.97" end="00:00:25.43">Text here </p> 
    $dom = new DOMDocument;
    $dom->load($new_transcript_file);

    
    // This is a holder for the segment start time. We have to put this holder in to "hold" the start time of
    // of the segment being created. We set this holder with the currnet seconds of the previous individual segments of the XML file.
    // We start with 0.00 seconds becuase the first segment starts at the beginning of the file, obviouslly.
    // This holder is increased at the end of each newly created segment in order to keep our sequence correct.
    $seconds_id_holder = 0.00;


    // This is the holder for the actual timecode that gets put into the databse for the segment.
    // We start with 0 seconds becuase the first segment starts at the beginning of the file, obviously.
    $timecode_holder = 0;

    // $seconds_id_holer and $timecode_holder are basically the same except that one is more precise because the XML
    // file produces a more exact time. We round up/down when sticking into the database (don't care about milliseconds)
    // but we separate them to avoid having a snippet from the HTML with the same seconds and confusing everything.


    // We have a default text holder that concatenate the transcripts segments to as we loop through the XML
    $text_holder = "";

    // This keeps up with how many lines we run through in the XML file so we know when we've parsed the full transcript
    $line_count = 0;


    // Get the number of transcript segments we need to parse through
    $lines = $dom->getElementsByTagName('content')->length;
    print "Running through $lines Lines of transcription<br/>";

    // Loop through ALL the p tags in the XML for the small segments
    foreach($dom->getElementsByTagName('content') as $segment) {

        // Increase line count so we can compare later
        $line_count++;

        // Get the time timecode of the snippted
        $timecode = $segment->getAttribute('timestamp');

        // Get the actual transcript segment
        $content = $segment->textContent;

        // the timestamp ist total seconds and milliseconds - so we insert a decimal before the last three digits (milisecods)
        $seconds = substr_replace($timecode, ".", -3, 0);

        // Make seconds only version of the timeconde (make it an integer so it doesn't have milliseconds).
        $seconds_int = (int)($seconds);
        //print "Now I have a Integer of $seconds_int<br/>";

        //print "Segment Added started at $seconds seconds of the audio file...<br/>";
   
        // Add the content to the segment text holder
        // NOTE: we are include an extra space after our content so the next segment doesn't run into this one as we concatenate
        $text_holder .= "$content ";

        // Check for two things here. If either criteria are met, we add a completed segment of lenght $seconds_segment) to the database.
        // If this snippets timecode in integer seconds is GREATER than the previous full segments timecode + the desired lenght of segment
        // If the nubmer of lines in the XML file is equal to the number of lines we parse (we get to the end without a full seconds_segment time.
        if (($seconds > ($seconds_id_holder + $seconds_segment)) || ($lines == $line_count )) { 
            // It's time to insert the segment

            $text_holder = $mysqli->real_escape_string($text_holder);
            $timestamp = strtotime($date);

            $segment_query = "INSERT INTO transcript_segment (transcript_id, timecode, content, date, timestamp) VALUES($transcript_id, $timecode_holder, '$text_holder', STR_TO_DATE('$date', '%Y-%m-%d'), $timestamp)";
            //print "$segment_query <br/>";
            $mysqli->query($segment_query);
            print "Added Transcript Segment to the DB... <br/>";
            print "At Audio Seconds of $timecode_holder... <br/>";
            print "$text_holder <br/><br/>";

            // Update the segment count stuff
            $seconds_id_holder = $seconds;
            $timecode_holder = $seconds_int;
            $text_holder = "";
        }

    }

    // close out the database connection
    $mysqli->close();

    print "<p><b>Successfully added Transcript to Database</b></p>";

}

else {
    $yesterday = new DateTime();
    $yesterday->sub(new DateInterval('P1D'));
    $yesterday = $yesterday->format('Y-m-d');

//<form class="col-sm-offset-3 col-sm-6" role="form" action="upload.php" enctype="multipart/form-data" method="post">

    print <<<__HTML__

<h1>Upload Transcripts</h1>

<form class="col-sm-offset-3 col-sm-6" role="form"  method="post" action="upload.php" enctype="multipart/form-data">
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
