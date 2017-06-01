<?php


require_once("models/config.php");
if (!securePage($_SERVER['PHP_SELF'])){die();}

require_once("models/header.php");

/* include the appropriate files*/
require_once('models/sphinxapi.php');
require_once('models/model.php');
require_once('models/db-settings.php');

/* get the current search page number and the term(s) searched for from the request */
/* a feeble attemp to sanitize the user input */

/* numbers on for the form step */
$step = preg_replace('/[^0-9]/', '', $_GET['step']);

/* if step paramter is not passed, then give them the default search form */
if (! $step) {
    require_once("models/search_main.php");
}
/* Do the actual searching */
else {

/* numbers only for page_number  */
$page_number = preg_replace('/[^0-9]/', '', $_GET['page_number']);

/* numbers and "-" only for dates */
$start_date = preg_replace('/[^0-9]-/', '', $_GET['start_date']);
$end_date = preg_replace('/[^0-9]-/', '', $_GET['end_date']);

/* a little more allowed for search term */
$search_term = preg_replace('/[^-a-zA-Z0-9\'  ]/', '', $_GET['search_term']);

/* if no date is specified then we want it all - set a wide date range */
if (! $start_date) {
    $start_date = "2000-01-01";
    $end_date = "2030-01-01";
}

/* 
We make sure to set the page number to 1 if it's anyone under 1 (like a negative number) 
Important because of how we use the page number to create pagination links on the fly
*/
if ($page_number < 1) {
    $page_number = 1;
}

/* convert the user's search_term to all caps for consistency */ 
$uppercase_search_term = strtoupper($search_term);

print "<h1>Searching for $uppercase_search_term</h1>";
//print "<h3>Searching between dates " . date("F n, Y ", strtotime($start_date)) . " and " . date("F n, Y", strtotime($end_date)) . "</h3>";
print "<h3>Searching between dates " . $start_date . " and " . $end_date . "</h3>";
print "<hr/>$info_warning<hr/>";


/*
Set up the new SphinxClient (by default it connects to the localhost
SPH_MATCH_PHRASE does an "EXACT MATCH" on terms entered (case insensitive)
If the user searches for "Affordable Care" It will only return results matching
that exact phrase (case insensitive).
We also have it set to return the results as an array for easier processing
The MaxQueryTime should be set as low as possible - but we may need to keep adjusting this
The query results are returned in the array $result
*/
$s = new SphinxClient;
$s->setMatchMode(SPH_MATCH_PHRASE);
$s->setArrayResult(TRUE);
$s->setMaxQueryTime($sphinx_query_time);
$s->setFilterRange(timestamp, strtotime($start_date), strtotime($end_date));
$result = $s->query($search_term);

/* 
Set up an empty array in which to store all the results. Use this array to access
the full transcript data from the actual storage database
*/
$matches = array();

/*
Loop through the results ($result) of the index search and add the "id" in teh array entry
to $matches. We do this becuase thes ids correlate to the segment_id in the database 
for the transcript segment. We are extracting the list of segment_id(s) from the 
search results array from the Sphinx index search and adding them to the $matches array
*/
foreach($result["matches"] as $value) {
    array_push($matches, "$value[id]");
}

/* get a count of how many total transcript_segment matches we found */
$result_count = count($matches);

/* convert the array $matches to a string of ids separated by commas in preparation for mysql query */
$ids_found = implode(",", $matches);


/* set up our mysql connetion */
/*$mysqli = new mysqli($mysql_server, $mysql_username, $mysql_password, $mysql_database);

/*
We have to create an offset here in order to get only the needed number of results from the DB query
(We don't need to return 1,000 results if we only show 5 results per page) So we create the offset
by subtracing 1 from the current page number (a 0 page_number gets set to 1 during the earlier check)
and then we multiply that by the total of results per page we want. 
Example: Page Number 3 would get us a starting offset of (3 - 1) * 5 = 10. Page 3 of results starts with
the 10th result found in the query. 
*/
$offset = (($page_number - 1) * $results_per_page);

/* 
Our DB queury which searches for any segment_id that equals any one of the ids_found (list of ids from Sphinx)
AND where the transcript_ids of both tables (segment and full transcrip) are equal to ensure we get it all 
lined up and matched appropriately. We limit by the number we need per page and, of course, start at the 
offset we just calculated.
*/
$query = "SELECT a.segment_id, a.timecode, a.content, a.transcript_id, b.title, b.date, b.audio_file, b.chamber, b.year, b.location, b.description
          FROM transcript_segment a, transcript b
          WHERE segment_id IN ($ids_found) AND a.transcript_id = b.transcript_id LIMIT $results_per_page OFFSET $offset";


$result = $mysqli->query($query);

/* stylize the search keyword so we can highlight in the search results */
$styled_keyword = stylize_search_text($uppercase_search_term);

/* loop through the query results and store everything in a variable to make things a bit easier */
    while($obj = $result->fetch_object()) {

        $transcript_id = $obj->transcript_id;
        $segment_id = $obj->segment_id;
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
        and store the number of times it was done (# of instances of the word) we can have a result count
        */
        //$transcript = str_ireplace($search_term, $styled_keyword, $transcript, $count);
        $count = substr_count($transcript, $search_term);

        /* Get the links for the segment and the full transcript */
        $segment_url  = create_segment_url($segment_id, $search_term, $start_date, $end_date);
        $transcript_url  = create_transcript_url($transcript_id, $search_term, $start_date, $end_date);


        /* create the audio player html by passing the file location and the timecode in seconds to the start of the clip */
        $audio_player_html = create_audio_player($audio_file, $timecode, $segment_id);

        /* 
        Find the first place in the transcript we found the keyword so we can create a smaller excerpt
        of the transcript segement. The start position will be X of characters subtracted from the position
        of the first instance and then Y of characters after it.
        */
        $excerpt_text = create_search_results_excerpt($transcript, $uppercase_search_term, $search_term, $styled_keyword);

        /* put it all together for the individual display results html */
        $search_result_html = create_search_result_html($title, $date, $chamber, $year, $location, $description, $count, 
                                                        $segment_url, $transcript_url, $audio_player_html, $excerpt_text,
                                                        $audio_file, $segment_timecode, $search_term, $styled_keyword);

        print $search_result_html;

    }

    /* free up the mysql results (although it's really unnecessary here) */
    $result->free();

/* close out the mysql server connection */
$mysqli->close();




/* Create Pagination Links */

/* divide the total results by the $results_per_page to get how many pages of results there should be */
$total_pages = (int)($result_count / $results_per_page);

/* default page number is 1 */
if (!$page_number) {
    $page_number = 1;
}

/* Find the next and previous page numbers */
$next_page_number = ($page_number + 1);
$previous_page_number = ($page_number - 1);

$pagination_links = create_pagination_links($page_number, $next_page_number, $previous_page_number, $total_pages, $search_term, $start_date, $end_date);

print <<<__HTML__
<h4><center>$pagination_links</center></h4>
__HTML__;


}

require_once("models/footer.php");

?>
