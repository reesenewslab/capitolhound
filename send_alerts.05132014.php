<?php

/* include the appropriate files*/
require_once("models/config.php");
if (!securePage($_SERVER['PHP_SELF'])){die();}

require_once('models/sphinxapi.php');
require_once('models/model.php');
require_once('models/db-settings.php');


require_once("models/header.php");

$step = 0;
$step = $_GET['step'];

if ($step == 1) {
      $start_date = $_GET['start_date'];
      $end_date = $_GET['end_date'];

      if (! $start_date || ! $end_date) {

        print <<<__HTML__
<h1>ERROR Sending Alerts!</h1>
PLEASE specify and Start and End Date
__HTML__;

        die();

      }

/* get the current search page number and the term(s) searched for from the request */
/* a feeble attemp to sanitize the user input */

/* numbers and "-" only for dates */
$start_date = preg_replace('/[^0-9]-/', '', $start_date);
$end_date = preg_replace('/[^0-9]-/', '', $end_date);



//$start_date = "2013-07-01";
//$end_date = "2013-10-01";

/* if not dates is specified then we set the date to yesterday */
//if (! $start_date || ! $end_date) {
//    $yesterday = new DateTime();
//    $yesterday->sub(new DateInterval('P1D'));
//    $start_date = $yesterday->format('Y-m-d');
//    $end_date = $yesterday->format('Y-m-d');
//}

print "<PRE>\n";

print "Start is $start_date\n";
print "End is $end_date\n";


//$mysqli = new mysqli($mysql_server, $mysql_username, $mysql_password, $mysql_database);


// First get all the keywords for active accounts that we need to look for
$query_keywords  = "SELECT keywords FROM alerts WHERE status=1";
$result = $mysqli->query($query_keywords);
$keywords = array();

while($obj = $result->fetch_object()) {
    foreach(explode(",", $obj->keywords) as $value) {
        // trim off the extra white space after we split the string at ","
        $value = ltrim($value);
        // convert the keywords to all upper so we can keep everything nice and tight. The search engine is case insensitve
        $value = strtoupper($value);
        //print "KEYWORD is $value \n";
        array_push($keywords, $value);
    }
}

$result->free();

// Remove duplicates from array
$keywords = array_unique($keywords);

// cycle through each keyword, perform the search in Sphinx to get the matches
// Then perform the search on our transcript database to produce the output
// Store the output in another array we can reference later when sending mail

$s = new SphinxClient;
$s->setMatchMode(SPH_MATCH_PHRASE);
//$s->setMatchMode(SPH_MATCH_EXTENDED2);
$s->setArrayResult(TRUE);
$s->setMaxQueryTime($sphinx_query_alert_time);
$s->setFilterRange(timestamp, strtotime($start_date), strtotime($end_date));

print count($keywords) . " keywords being searched \n";

foreach($keywords as $search_term) {

    //$search_term = "pat mccrory";
    //print "Searching for $search_term\n";
    //$hack_variable = "\"$search_term\"";
    $s->addQuery($search_term);
    
}

$search_results = $s->runQueries();

//print "hello\n\n";
//print_r($search_results);
//print "hello2\n";

$match_holder = array();

foreach($search_results as $value) {
    // Get the search Term from results
    //print_r($value);

    if ($value['total_found'] == 0) {
        //print "Skipping because we didn't find any results\n";
        continue;
    }


    $s_word = array_keys($value["words"]);

    if (count($s_word) > 1) {
        $s_word = implode(" ", $s_word);
    }
    else {
        $s_word = key($value["words"]);
    }

    $s_word = strtoupper($s_word);

   // $s_word = key($value["words"]);
   // $s_word = strtoupper($s_word);
    //$s_word = strtoupper($s_word);

    print "FOUND WORD: " . $value['total_found'] . " instances of " .  $s_word . "\n";
    //print "Instances: " . $value['total_found'] . "\n";

    $matches = array();

    // Get the Transcript Segement IDs in which the term is found
    //foreach($value["matches"] as $match_value) {
    foreach($value['matches'] as $match_value) {
        //print "VALUE is $match_value[id]\n";
        array_push($matches, $match_value["id"]);
    }

    //print "\n";
    $match_holder["$s_word"] = $matches;
    //print "\n\n\n";

}

//print_r($match_holder);
//JOHN

//print_r($match_holder);


$final_results = array();

foreach($match_holder as $key => $id_list) {
    //print "Search word is $key\n";

    $match_ids = implode(",", $id_list);
    //print "LIST is $match_ids\n";


    $query_transcript = "SELECT a.segment_id, a.timecode, a.content, a.transcript_id, 
                         b.title, b.date, b.audio_file, b.chamber, b.year, b.location, b.description
                         FROM transcript_segment a, transcript b
                         WHERE segment_id IN ($match_ids) AND a.transcript_id = b.transcript_id";

    $search_results = $mysqli->query($query_transcript);

    $total_matches = 0;
    $loop_count = 0;
    $excerpt = "";
    while($s_obj = $search_results->fetch_object()) {

        $loop_count++;
        $transcript_id = $s_obj->transcript_id;
        $segment_id = $s_obj->segment_id;
        $transcript  = $s_obj->content;
        $timecode = $s_obj->timecode;
        $title = $s_obj->title;
        $date = $s_obj->date;
        $audio_file = $s_obj->audio_file;
        $chamber = $s_obj->chamber;
        $year = $s_obj->year;
        $location = $s_obj->location;
        $description = $s_obj->location;

        $UpSearchTerm = strtoupper($key);

        $transcript = str_ireplace($key, $key, $transcript, $count);
        //$count = substr_count($transcript, $key);

        /* Increase total_matches with the number of times the term was found in this segment */
        $total_matches = ($total_matches + $count);


        $html_segment = <<<__HTML__
<h2>Results for $key</h2>
<p>
<b>$chamber : $date : $location : $title</b><br>
$total_matches match(s) found in this transcript<br/>
<!--a href="$base_url/search.php?step=1&search_term=$key&transcript_id=$transcript_id">See All $count Results</a><br/>-->
<a href="$base_url/search.php?step=1&search_term=$key&start_date=$start_date&end_date=$end_date">See All $total_matches Results</a><br/>
__HTML__;
        if ($loop_count == 1) {
            $transcript = str_ireplace("$key", '<b>' . $UpSearchTerm . '</b>', $transcript);
            $first_find = stripos($transcript, $UpSearchTerm);
            $start_position = ($first_find - 50);
            if ($start_position < 0) { $start_position = 0; }
            //$excerpt = substr($transcript, $start_position, 400);
            $excerpt = substr($transcript, $start_position, 150);
        }

    }

    $html_hold = "$html_segment<p>Excerpt:<br/>$excerpt</p>\n<hr/>";

    $search_results->free();

    // Store the results so we can email them later
    //print "Storing html results in $key\n";
    $final_results[$key] = $html_hold;

}

// Now we have to send the email!

$query_email = "SELECT email_address, keywords FROM alerts WHERE status=1";
$email_result = $mysqli->query($query_email);

while($e_obj = $email_result->fetch_object()) {

    $got_results = 0;
    $output_email = <<<__HTML__
<html>
<head>
<title>Capitol Hound</title>
</head>
<body>
$info_warning
<br/>
__HTML__;

    $email_address = $e_obj->email_address;

    foreach(explode(",", $e_obj->keywords) as $value) {
        // trim off the extra white space after we split the string at ","
        $value = ltrim($value);
        // convert the keywords to all upper so we can keep everything nice and tight. The search engine is case insensitve
        $value = strtoupper($value);

        if (! array_key_exists($value, $final_results)) {
            continue; 
        }

        $output_email .= $final_results[$value];
        $got_results = 1;
    }

    $output_email .= <<<__HTML__
</body>
</html>
__HTML__;

    if ($got_results > 0) {

        //send the mail
        //$to = $email_address;
        $to = "meghan.horton@unc.edu";
        $from = "capitolhound@reesenews.org";
        $bcc  = "capitolhound@reesenews.org";
        $subject = "Capitol Hound Email Alert";
        $headers = "MIME-Version: 1.0 \r\n";
        $headers .= "Content-type: text/html; charset=iso-8859-1 \r\n";
        $headers .= "From: $from\r\n";
        //$headers .= "Bcc: $bcc\r\n";

        mail($to, $subject, $output_email, $headers);
    
        print "sent mail to $to\n";
    }

}

$email_result->free();
$mysqli->close();


print "finished!\n";


print "</PRE>\n";

}

else {

    $yesterday = new DateTime();
    $yesterday->sub(new DateInterval('P1D'));
    $yesterday = $yesterday->format('Y-m-d');

    $day_before_yesterday = new DateTime();
    $day_before_yesterday->sub(new DateInterval('P2D'));
    $day_before_yesterday = $day_before_yesterday->format('Y-m-d');

    print <<<__HTML__
<h1>Send Alerts</h1>

        <form class="col-sm-8 col-sm-offset-2 "  method="get" action="send_alerts.php">
        <input type="hidden" name="step" value="1"/>

        <div class="col-sm-5 col-sm-offset-1 daterange">
            <label>Start Date</label>
            <div class="input-group input-group-lg date" id="start" data-date-format="YYYY-MM-DD">
                <input class="form-control" type="text" name="start_date" value="$yesterday" >
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
                <input class="form-control" type="text" name="end_date" value="$yesterday">
                <span class="input-group-btn">
                    <button class="btn btn-default">
                        <i class="glyphicon glyphicon-calendar"></i>
                    </button>
                </span>
            </div>
        </div>
        <p><br/><center>
        <input type="submit" name="submit" value="Send Alerts">
        </center>
        </p>
        </form>

__HTML__;

}



require_once("models/footer.php");

?>
