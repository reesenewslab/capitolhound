<?php
header('Cache-Control: no cache'); //no cache
require_once("models/model.php");
require_once("models/config.php");
if (!securePage($_SERVER['PHP_SELF'])){die();}



print <<<__HTML__
<!DOCTYPE HTML>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Capitol Hound</title>
    <link href="$base_url/assets/css/bootstrap.css" rel="stylesheet">
    <link href="$base_url/assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="$base_url/assets/css/style.css" rel="stylesheet">
    <link href="$base_url/assets/css/app.css" rel="stylesheet">
    <link href="$base_url/assets/css/audioplayer.css" rel="stylesheet">
    <link rel="shortcut icon" href="$base_url/assets/images/favicon.ico" />
    <link href="$base_url/assets/css/bootstrap-datetimepicker.css" rel="stylesheet">

    
</head>

<body>
	<nav class="navbar green-nav navbar-default navbar-fixed-top" role="navigation">
		<div class="container relative-container">
		<!-- Brand and toggle get grouped for better mobile display -->
		<div class="navbar-header">
		  <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navcollapse">
		    <span class="sr-only">Toggle navigation</span>
		    <span class="glyphicon glyphicon-align-justify"></span>
		  </button>
		  <div class="header-logo">
		    <a href="$base_url"><img src="$base_url/assets/images/CapitolHoundLogo.png"></a>  
		  </div>
		</div>
	      
		<!-- Collect the nav links, forms, and other content for toggling -->
		<div class="collapse navbar-collapse" id="navcollapse">

		  <ul class="nav navbar-nav navbar-right">
		    <li><a href="$base_url"><i class="glyphicon glyphicon-search"></i> Home</a></li>
		    <li class="dropdown">
			<a href="$base_url/search.php" data-toggle="dropdown"><i class="glyphicon glyphicon-list"></i> Search</a>
			<ul class="dropdown-menu">
				<li><a href="$base_url/search.php">Basic Search</a></li>
				<li><a href="$base_url/view_transcripts.php">Browse by date</a></li>
				<li><a href="$base_url/list_of_members.php">Browse by legislator</a></li>
			</ul>
		    </li>
		   <!-- <li>
			<a href="$base_url/alerts.php"><i class="glyphicon glyphicon-warning-sign"></i> My Alerts</a>
		    </li> -->
		    <li class="dropdown">
			<a href="$base_url/about.php" data-toggle="dropdown"><i class="glyphicon glyphicon-question-sign"></i> About</a>
		    	<ul class="dropdown-menu">
				<li><a href="$base_url/about.php">About</a></li>
				<li><a href="$base_url/terms_of_use.php">Terms of use</a></li>
				<li><a href="$base_url/privacy_policy.php">Privacy Policy</a></li>
			</ul>
  		    </li>
		    <li><a href="$base_url/press.php"><i class="glyphicon glyphicon-bullhorn"></i> Press</a></li>
		

__HTML__;
        if(isUserLoggedIn())  { 
                print <<<__HTML__
                <li class="dropdown">
		      <a href="#" class="dropdown-toggle" data-toggle="dropdown" id="accountButton"><i class="glyphicon glyphicon-user"></i> Account</a>
		      <ul class="dropdown-menu">
			    <li class="dropdown-heading">$loggedInUser->displayname</li>
			    <li><a href="$base_url/account.php">Account</a></li>
			    <li><a href="$base_url/user_settings.php">User Settings</a></li>
			    <li><a href="$base_url/alerts.php">Alerts</a></li>
			    <li><a href="$base_url/help.php">Help</a></li>
			    <li><a href="$base_url/logout.php">Logout</a></li>
__HTML__;

                //Links for permission level 2 (default admin)
                if ($loggedInUser->checkPermission(array(2))){
                print <<<__HTML__
                             <li class="dropdown-heading">Admin</li>
                             <li><a href="$base_url/admin_configuration.php">Admin Configuration</a></li>
                             <li><a href="$base_url/admin_users.php">Admin Users</a></li>
                             <li><a href="$base_url//admin_permissions.php">Admin Permissions</a></li>
                             <li><a href="$base_url/admin_pages.php">Admin Pages</a></li>
                             <li><a href="$base_url//import.php">TURK: Upload new transcripts</a></li>
                             <li><a href="$base_url/upload.php">CLOUDFACTORY: Upload new transcripts</a></li>
                             <li><a href="$base_url/re-index.php">Re-index Database</a></li>
                             <li><a href="$base_url/send_alerts.php">Send Alerts</a></li>
__HTML__;
               }

        }
        else {
                print <<<__HTML__
			   <!-- <li><a href="$base_url/login.php">Login</a></li> -->
__HTML__;

        }



print <<<__HTML__
		      </ul>
		    </li>
		  </ul>
		</div><!-- /.navbar-collapse -->
	    </div><!-- /.container -->
	</nav>
	<div class="blurb">
	
	<div class="container">
	<div class="row">
		<!-- <div class="subscriber-nav col-sm-12">
		<div class="right">
			<a href="$base_url/news-outlets.php">News Outlets</a>
			<a href="$base_url/new-subscribers.php">New Subscribers</a>
			<a href="$base_url/support.php">Support</a>
		</div>
		</div> -->
	</div>
	</div>
	
		<div class="row">
			<div class="col-sm-6 col-sm-offset-3 center">
		    <h3>A searchable audio archive from the 2013-2016 legislative sessions of the North Carolina General Assembly.</h3>
		    </div>
		</div>
	</div>
	<div class="container main-container">
__HTML__;

?>
