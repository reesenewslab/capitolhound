#!/usr/bin/php -q
<?php

        //send the mail
        $to = "johnclark@unc.edu";
        $from = "john.clark@reesenews.org";
        $subject = "Capitol Hound Daily Email Alert";
        $headers = "MIME-Version: 1.0 \r\n";
//        $headers .= "Content-type: text/html; charset=iso-8859-1 \r\n";
        $headers .= "From: $from\r\n";
        mail($to, $subject, "hello world", $headers);
    
        print "sent mail to $to\n";

exit;

?>
