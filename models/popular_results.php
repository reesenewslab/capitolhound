<?php
/*
 * This file determines, from the sphinx log, the most often searched for terms and provides links 
 */

// SET VARIABLES
$listlimit 	= 5;
$log_url 	= '../../../var/log/sphinx/query.log';

/*
 * Pre-process file
 */
	// GET FILE CONTENTS
	$log 		= file_get_contents($log_url);
	// REMOVE UNNECESSARY TEXT
	$search 	= array(' 0.000 sec 0.000 sec ',' [*] ');
	$log 		= str_replace($search,'',$log);

	// CREATE AN ARRAY FROM LINE BREAK
	$log_array 	= explode("\n", $log);

/*
 * Loop through & create array of terms by date, to distinguish alert
 */

	// Loop through 
	foreach($log_array as $key=>$array){
		// EXTRACT DATE
		$length 			=	strpos($array,']') + 1; // GET LENGTH OF STRING AT FIRST "]"
		$date 				=	substr($array,1,$length-2);

		// GET REMAINDER OF TEXT
		$sec_len 			=	strpos($array,']',$length);
		$content 			=	substr($array,$sec_len+1);

		$notice[$date][]	=	$content;
	}

/*
 * Separate alerts from queries
 */

	foreach($notice as $key=>$var){
		// GET COUNT IN ARRAY
		$count = count($var);

		// IF ARRAY HAS MORE THAN TWO RESULTS, ASSUME ITS AN ALERT
		if($count > 2){
			$alerts = $var;
		} 
		// ELSE, ASSUME IT IS JUST A SEARCH QUERY & MAKE UCWORDS CASE
		else{
			$searchterm[] = ucwords($var[0]);
		}
	}

/*
 * Count top search terms
 */

	// TOP SEARCH TERMS
	$get_counts = array_count_values( $searchterm );
	arsort($get_counts); // Sort highest results to lowest

/*
 * Display
 */
	echo '<ul class="topterms">';
	foreach( array_slice($get_counts,0,$listlimit) as $key=>$val){
		echo '<li><a href="/index.php?step=1&search_term='.$key.'">'.$key.' (' .$val.')</a></li>';
	}
	echo '</ul>';

?>
