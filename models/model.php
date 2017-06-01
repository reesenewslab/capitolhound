<?php

date_default_timezone_set('America/New_York');

// MySQL Server Information
//$mysql_server = "rfdnwp.c2buzbgcpkgr.us-east-1.rds.amazonaws.com";
//$mysql_username = "reesenews";
//$mysql_password = "bLu3KZ8xSEc";
//$mysql_database = "capitolhound";

/* The default number of search results to display */
$results_per_page = 5;

/* How many seconds should the transcript be? We get a full transcript, but create individual segments into chunks fo this length */
$seconds_segment = 300;

/* the Sphinx Search Max Query Time */
$sphinx_query_time = 10000;
$sphinx_query_alert_time = 10000;

/* Length in characters of each excerpt on search results page */
$search_result_excerpt_length = 500;

/* Length in characters from the first character of the first instance where we found word_to_start_from */
$characters_from_first_position = 100;

/* What is the base URL for all links */
$base_url = "http://capitolhound.org";

/* What is full local path to transcript file uploads*/
$transcript_directory = "/transcripts";

/* Dislaimer for all pages */
$info_warning = <<<__HTML__
<em>
<b>Reliance on Information Posted</b> The information presented on or through the website is made available solely for general information purposes. We do not warrant the accuracy, completeness or usefulness of this information. Any reliance you place on such information is strictly at your own risk. We disclaim all liability and responsibility arising from any reliance placed on such materials by you or any other visitor to the Website, or by anyone who may be informed of any of its contents. Please see our <a href="http://capitolhound.org/terms_of_use.php">Terms of Use</a> for more information.
</em>
__HTML__;



/* Function to stylize/highlight the search term in the results */
function stylize_search_text($text_to_style) {
    //$stylized_text = '<span class="label label-info blueBG">' . $text_to_style . '</span>';
    $stylized_text = '<span class="highlight">' . $text_to_style . '</span>';
    return $stylized_text;
}


function create_segment_url($segment_id, $search_term) {

    global $base_url;

    if($segment_id && $search_term) {
        $segment_url = $base_url . '/segment.php?segment_id=' . $segment_id . '&search_term=' . $search_term;
    }
    else {
        $segment_url = "";
    }
    return $segment_url;
}


function create_transcript_url($transcript_id, $search_term) {

    global $base_url;

    if($transcript_id && $search_term) {
        $transcript_url = $base_url . '/transcript.php?transcript_id=' . $transcript_id . '&search_term=' . $search_term;
    }
    else {
        $transcript_url = "";
    }
    return $transcript_url;
}




/* Create the Audio Player HTML */
function create_audio_player($filename, $seconds, $audio_id) {

    global $base_url;

    $segment_time = gmdate("H:i:s", $seconds);
	//<source src="$filename" type="audio/mpeg">
	//<embed src="$filename" type="audio/mpeg" hidden="true"/>

    $audio_player_html = <<<__HTML__
<audio id="audio$audio_id" preload="auto" type="audio/mpeg" controls src="$filename#t=$seconds"></audio>
__HTML__;

//THIS IS THE GOOD STUFF
//<script>
//	myAudio=document.getElementById("audio$audio_id");
//	myAudio.addEventListener('loadedmetadata', 
//	function() {
//	this.currentTime = $seconds;
//	});
//</script>
// THIS IS THE END OF HTE GOOD STUFF

     //myAudio=document.getElementById('audio$audio_id');
     //myAudio.addEventListener('canplaythrough', function() {
     //this.currentTime = $seconds;
     //});
//<!--<audio id="audio$audio_id" preload="none" controls>
//	<source src="$filename" type="audio/mp3">
//</audio>-->

//<script src="$base_url/js/audioplayer.js"></script>


/*
        $audio_player_html = <<<__HTML__
<audio id="player" src="$filename" type="audio/mp3" preload="metadata"></audio>
<div>
    <button onclick="var mediaElement = document.getElementById('player'); mediaElement.currentTime = $seconds; document.getElementById('player').play()"> Play The Segment</button>
    <button onclick="document.getElementById('player').play()">Play Full Transcript</button>
    <button onclick="document.getElementById('player').pause()">Pause</button>
    <button onclick="document.getElementById('player').volume+=0.1">Volume Up</button>
</div>
__HTML__;
*/

    return $audio_player_html;

}

/* Function to create search page result excerpt */
function create_search_results_excerpt ($text_to_shorten, $word_to_start_from, $search_term, $styled_keyword) {
        global $search_result_excerpt_length, $characters_from_first_position;

        /* find starting and ending positions and create the excerpt */
        $first_find = stripos($text_to_shorten, $word_to_start_from);
        $start_position = ($first_find - $characters_from_first_position);
        if ($start_position < 0) { $start_position = 0; }
        $new_excerpt = substr($text_to_shorten, $start_position, $search_result_excerpt_length);

        //$new_excerpt = str_ireplace($search_term, $styled_keyword, $new_excerpt);
        return $new_excerpt;
}


function create_search_result_html($title, $date, $chamber, $year, $location, $description,
                                   $count, $segment_url, $transcript_url, $audio_player_html, 
                                   $text, $audio_file, $seg_timecode, $search_term, $styled_keyword) {

    $date = date('F j, Y',strtotime($date));
    $title_html = "";
    if ($chamber ) { $title_html .= "<b>$chamber</b> | "; }
    if ($date)     { $title_html .= "$date | "; }
    if ($location) { $title_html .= "$location | "; }
    if ($title)    { $title_html .= "$title | "; }
    $title_html = substr($title_html, 0, -3);

// TITLE
    $result_html = <<<__HTML__
<div class="result">
<h4>$title_html</h4>
__HTML__;

// NUMBER OF MATCHES
    //$count_html = "";
    if ($count) {
        $result_html .= <<<__HTML__
<h5><b>$count match(s)</b> found in this 5-minute segment (timecode $seg_timecode)</h5>
__HTML__;
    }


// DISPLAY TEXT
    if ($text) {
        $text = str_ireplace($search_term, $styled_keyword, $text);

        $result_html .= <<<__HTML__
<p>... $text ...</p> 
__HTML__;
    }

// AUDIOPLAYER
    if ($audio_player_html) {
        $result_html .= <<<__HTML__
<div class="audioplayer">$audio_player_html</div>
__HTML__;
    }
// SEGMENT AND FULL TRANSCRIPT URLS
    if ($segment_url && $transcript_url) {
        $result_html .= <<<__HTML__
<a class="btn btn-primary" href="$segment_url">Segment</a>
<a class="btn btn-primary" href="$transcript_url">Full Transcript</a>
<a class="btn btn-primary" href="$audio_file">Full MP3 Audio File</a>
__HTML__;
    }
    elseif ($transcript_url) {
        $result_html .= <<<__HTML__
<a class="btn btn-primary" href="$transcript_url">Full Transcript</a>
<a class="btn btn-primary" href="$audio_file">Full MP3 Audio File</a>
__HTML__;
    }
    else {
        $result_html .= <<<__HTML__
<a class="btn btn-primary" href="$audio_file">Full MP3 Audio File</a>
__HTML__;
    }

    $result_html .= <<<__HTML__
</div>
__HTML__;


    return $result_html;
}




function create_pagination_links($page_number, $next_page_number, $previous_page_number, $total_pages, $search_term, $start_date, $end_date) {

    global $base_url;

    $pagination_html = <<<__HTML__
<ul class="pagination">
__HTML__;

    $page_number_links = "";
    $p = 0;
    while ($p <= $total_pages) {
        $p++;
        $page_number_links .= '<li><a href="' . $base_url . '/search.php?search_term=' . $search_term . 
                              '&page_number='. $p . '&start_date=' . $start_date . '&end_date=' . $end_date . '&step=1">'. $p . '</a></li>';
    }

    if ($page_number == $p) {
        $pagination_html .=  <<<__HTML__
<li><a href="$base_url/search.php?search_term=$search_term&page_number=$previous_page_number&start_date=$start_date&end_date=$end_date&step=1">Previous</a></li>
$page_number_links
__HTML__;
    }
    elseif ($page_number > 1) {
        $pagination_html .= <<<__HTML__
<li><a href="$base_url/search.php?search_term=$search_term&page_number=$previous_page_number&start_date=$start_date&end_date=$end_date&step=1">Previous</a></li>
$page_number_links
<li><a href="$base_url/search.php?search_term=$search_term&page_number=$next_page_number&start_date=$start_date&end_date=$end_date&step=1">Next</a></li>
__HTML__;

    }

    else {
        $pagination_html .= <<<__HTML__
$page_number_links <li><a href="$base_url/search.php?search_term=$search_term&page_number=$next_page_number&start_date=$start_date&end_date=$end_date&step=1">Next</a></li>
__HTML__;
    }

    $pagination_html .= "</ul>";

    return $pagination_html;

}


?>
