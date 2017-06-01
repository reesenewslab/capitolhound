<?php

require_once("models/config.php");
if (!securePage($_SERVER['PHP_SELF'])){die();}


require_once("models/header.php");

/* include the appropriate files*/
require_once('models/model.php');
require_once('models/db-settings.php');


/* get the current search page number and the term(s) searched for from the request */
/* a feeble attemp to sanitize the user input */

/* numbers only for dates */
$segment_id = preg_replace('/[^0-9]/', '', $_GET['segment_id']);

/* a little more allowed for search term */
$search_term = preg_replace('/[^-a-zA-Z0-9\'  ]/', '', $_GET['search_term']);

/* convert the user's search_term to all caps for consistency */
$uppercase_search_term = strtoupper($search_term);

print "<h3>searching for $uppercase_search_term</h3>";

print "<hr/>$info_warning<hr/>";

/* set up our mysql connetion */
//$mysqli = new mysqli($mysql_server, $mysql_username, $mysql_password, $mysql_database);


/* Our DB queury which returns the segment information */

$query = "SELECT a.transcript_id, a.timecode, a.content, b.title, b.date, b.audio_file, b.chamber, b.year, b.location, b.description
          FROM transcript_segment a, transcript b
          WHERE segment_id = $segment_id AND a.transcript_id = b.transcript_id LIMIT 1";

$result = $mysqli->query($query);

/* stylize the search keyword so we can highlight in the body */
$styled_keyword = stylize_search_text($uppercase_search_term);

/* loop through the query results and store everything in a variable to make things a bit easier */
    while($obj = $result->fetch_object()) {

        $transcript_id  = $obj->transcript_id;
        $transcript  = $obj->content;
        $timecode = $obj->timecode;
        $title = $obj->title;
        $date = $obj->date;
        $audio_file = $obj->audio_file;
        $chamber = $obj->chamber;
        $year = $obj->year;
        $location = $obj->location;
        $description = $obj->location;

        /* convert timecode to to human readable HH:MM:SS */
        $segment_timecode = gmdate("H:i:s", $timecode);

        /*
        do a case insensitive find/replace and replace the search_term with the upper_case_search_term
        Replace the search term in the transcript text with the stylized version to make it stand out
        */
       // $transcript = str_ireplace($search_term, $styled_keyword, $transcript);
        $count = substr_count($transcript, $search_term);

        
        /* Get the link for the full transcript */
        $transcript_url  = create_transcript_url($transcript_id, $search_term);

        /* create the audio player html by passing the file location and the timecode in seconds to the start of the clip */
        $audio_player_html = create_audio_player($audio_file, $timecode, $segment_id);

        /* put it all together for the individual display results html */
        $search_result_html = create_search_result_html($title, $date, $chamber, $year, $location, $description, $count,
                                                        $segment_url, $transcript_url, $audio_player_html, $transcript, 
                                                        $audio_file, $segment_timecode, $search_term, $styled_keyword);

        print $search_result_html;
        
    }

    /* free up the mysql results (although it's really unnecessary here) */
    $result->free();

/* close out the mysql server connection */
$mysqli->close();

require_once("models/footer.php");

?>
