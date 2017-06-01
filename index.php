<?php
/*
UserCake Version: 2.0.2
http://usercake.com
*/

require_once("models/config.php");
if (!securePage($_SERVER['PHP_SELF'])){die();}

// IF USER IS LOGGED IN, SHOW SEARCHBOX
//if(isUserLoggedIn() == 1):
//	require_once("models/header.php");
//	require_once("models/topbanner.php");
//	require_once("models/searchbox.php");
//	require_once("models/footer.php");
	require_once("search.php");
//IF SEARCH_TERM
//else
if( isset( $_GET['search_term']  ) ):
	header('Location: /search.php');
// IF NOT, SHOW SEARCHBOX
else:
	require_once("search.php");
	//require_once('search.php');
endif; ?>
