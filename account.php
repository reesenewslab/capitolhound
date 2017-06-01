<?php
/*
UserCake Version: 2.0.2
http://usercake.com
*/

require_once("models/config.php");
if (!securePage($_SERVER['PHP_SELF'])){die();}
require_once("models/header.php");
//require_once("models/topbanner.php"); ?>

<h3><?php echo "Welcome, $loggedInUser->displayname";?></h3>
<a class="btn btn-lg btn-primary" href="user_settings.php">Update Your User Settings</a>

<?php 
require_once("models/footer.php");
?>
