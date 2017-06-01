<?php
require_once("models/config.php");
if(!securePage($_SERVER['PHP_SELF'])){die();}

require_once("models/header.php");

/* include the appropriate files*/
require_once('models/model.php');
require_once('models/db-settings.php');

/* get the current search page number and the term(s) searched for from the request */
/* a feeble attemp to sanitize the user input */

/* numbers only for dates */
$transcript_id = preg_replace('/[^0-9]/', '', $_GET['transcript_id']);

/* a little more allowed for search term */
$search_term = preg_replace('/[^-a-zA-Z0-9\'  ]/', '', $_GET['search_term']);

/* convert the user's search_term to all caps for consistency */
$uppercase_search_term = strtoupper($search_term);

print "<h3>searching for $uppercase_search_term</h3>";
print "<hr/>$info_warning<hr/>";

/* set up our mysql connetion */
//$mysqli = new mysqli($mysql_server, $mysql_username, $mysql_password, $mysql_database);

/* Our DB queury which returns the transcript information */
$query = "SELECT a.timecode, a.content, b.title, b.date, b.audio_file, b.chamber, b.year, b.location, b.description
          FROM transcript_segment a, transcript b
          WHERE a.transcript_id = $transcript_id AND a.transcript_id = b.transcript_id";

$result = $mysqli->query($query);

/* Start a counter */
$i = 1;

/* stylize the search keyword so we can highlight in the body */
$styled_keyword = stylize_search_text($uppercase_search_term);

/* loop through the query results and store everything in a variable to make things a bit easier */
    while($obj = $result->fetch_object()) {

        $transcript  = $obj->content;
        $timecode = $obj->timecode;
        $UpSearchTerm = strtoupper($search_term);


        /*
        do a case insensitive find/replace and replace the search_term with the upper_case_search_term
        Replace the search term in the transcript text with the stylized version to make it stand out
        */
        //$transcript = str_ireplace($search_term, $styled_keyword, $transcript);
        $count = substr_count($transcript, $search_term);

        if ($i == 1) {
            $title = $obj->title;
            $date = $obj->date;
            $audio_file = $obj->audio_file;
            $chamber = $obj->chamber;
            $year = $obj->year;
            $location = $obj->location;
            $description = $obj->location;
            $i++;

            /* create the audio player html by passing the file location and the timecode in seconds to the start of the clip */
            $audio_player_html = create_audio_player($audio_file, $timecode, $transcript_id);

            /* put it all together for the individual display results html */
            //$search_result_html = create_search_result_html($title, $date, $chamber, $year, $location, $description, $count,
             //                                               $segment_url, $transcript_url, $audio_player_html, $transcript);

            /* put it all together for the individual display results html */
            $search_result_html = create_search_result_html($title, $date, $chamber, $year, $location, $description, "",
                                                        $segment_url, $transcript_url, $audio_player_html, "",
                                                        $audio_file, $segment_timecode, $search_term, $styled_keyword);
            print $search_result_html;

            $transcript = str_ireplace($search_term, $styled_keyword, $transcript);

            print "<p>$transcript</p>";
        }
        else {
            $transcript = str_ireplace($search_term, $styled_keyword, $transcript);
            print <<<__HTML__
<p>$transcript</p>
__HTML__;

        }

    }
    /* free up the mysql results (although it's really unnecessary here) */
    $result->free();

/* close out the mysql server connection */
$mysqli->close();

require_once("models/footer.php");

?>

