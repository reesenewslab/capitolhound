<?php

require_once("models/model.php");

print <<<__HTML__
	</div>
	<footer class="navbar-fixed-bottom" style="background-color:#fff">
                <div class="container small center">
                Capitol Hound is a product of the <a target="_blank" href="http://mj.unc.edu/">UNC School of Media and Journalism</a>'s <a target="_blank" href="http://reesenewslab.org">Reese News Lab</a>. &copy; 2016.
<br/>
<a href="/terms_of_use.php">Terms of Use</a> | <a href="/privacy_policy.php">Privacy Policy</a> | <a href="/help.php">Help</a>
                </div>
        </footer>
        <script src="$base_url/assets/js/funcs.js" type='text/javascript'></script>
        <script src="http://code.jquery.com/jquery-latest.min.js" type="text/javascript"></script>
	<script src="$base_url/assets/js/bootstrap.min.js"></script>
        <script src="$base_url/assets/js/app.js"></script>
        <script src="$base_url/assets/js/moment.min.js" type="text/javascript"></script>
        <script src="$base_url/assets/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
        <script src="$base_url/assets/js/custom.js" type="text/javascript"></script>

<!-- GOOGLE ANALYTICS -->
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-65314362-1', 'capitolhound.org');
  ga('send', 'pageview');

</script>


</body>
</html>
__HTML__;

?>
