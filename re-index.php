<?php

/* include the appropriate files*/
require_once("models/config.php");
if (!securePage($_SERVER['PHP_SELF'])){die();}

require_once('models/model.php');
require_once('models/db-settings.php');


require_once("models/header.php");

$step = 0;
$step = $_GET['step'];

if ($step == 1) {

$result = shell_exec("/usr/bin/sudo /usr/bin/indexer --all --rotate");
//$result = exec("/usr/bin/sudo -u sphinx /usr/bin/indexer --all --rotate");
//$result = shell_exec("/usr/bin/sudo -u root /usr/bin/indexer --all --rotate");
//$result = shell_exec("/usr/bin/indexer --all --rotate");

print "Finished Indexing";
print "<pre>$result</pre><br/>";
print "if the box above is empty - something went wrong. Contact your administrator";

}

else {

    print <<<__HTML__
<h1>Reindex </h1>

        <form class="col-sm-8 col-sm-offset-2 "  method="get" action="re-index.php">
        <input type="hidden" name="step" value="1"/>
        <p><br/><center>
        <input type="submit" name="submit" value="Reindex" onClick="alert('BE ADVISED: It may take a few seconds for the indexer to complete and the page to load');" >
        </center>
        </p>
        </form>

__HTML__;

}

require_once("models/footer.php");

?>
