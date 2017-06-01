<?php
require_once("models/header.php");
?>

<style>
.yes{color:#060;text-align: center; vertical-align: center;}.no{color:#900; text-align: center; vertical-align: center;}
.keywords td{ padding: 10px; }
th{font-weight: 600; vertical-align: top;}
.keywords {margin: 20px;}
.keywords td{border-bottom: thin solid #eee;}
.issues li,.issues ul{padding: 0; margin: 0;}

</style>


<h1>Welcome to Capitol Hound Support</h1>
<p>Learn about the features of Capitol Hound and receive professional advice from Capitol Hound.</p>
<p>For best results, we recommend using <b>Google Chrome</b> to prevent audio playback problems. <a href="https://www.google.com/intl/en_us/chrome/browser/" target="_blank">Download the recommended browser.</a></p>

<h2>Browse common issues</h2>
<!--<table class="issues" width="100%" style="font-size: 150%">
<tr>
	<th width="33%">Alerts</th> 
    <th width="33%">Search</th>
   <th width="33%">User Settings</th> 
</tr> -->
<tr>
	<!--<th>
    <ul>
    	<li><a href="#alert_setup">Set up alert</a></li>
        <li><a href="#alert_remove">Remove alerts</a></li>
        <li><a href="#alert_limits">Alert limitations</a></li>
    </ul>
    </th> -->
        
    <th>
    <ul>
    	<li><a href="#search_keyword">Empty results</a></li>
    	<li><a href="#good_search">Effective keywords</a></li>
    	<li><a href="#search_sessions">Previous sessions</a></li>
        <li><a href="#search_time">New updates</a></li>
    </ul>
    </th>
   <!-- <th>
    <ul>
    	<li><a href="#user_password">Change Password</a></li>
    </ul>
    </th> -->
</tr>

</table>
<br/>
<p>Can't find the answer to your question? Email <a href="mailto:capitolhound@reesenewslab.org?subject=Capitol Hound Customer Service Request">capitolhound@reesenewslab.org</a>.</p>

<hr/>

<!--<h2>Alerts</h2>
<h3 id="alert_setup">How do I set up an alert?</h3>
<ol>
  <li>To set an alert, visit <strong><a href="/alerts.php">My Alerts</a>.</strong> </li>
  <li>Enter a keyword or keywords into the <strong>Add Alerts</strong> box.</li>
  <li>Select the green <span class="yes"><strong>+</strong></span> button to add.</li>
</ol>
<h3 id="alert_remove">How do I remove an alert?</h3>
<ol>
  <li>To remove an alert, visit <strong><a href="/alerts.php">My Alerts</a>.</strong></li>
  <li>Select the red <span class="no">&#10005;</span> to the right of the term.</li>
</ol>
<h3 id="alert_limits">What are the limitations of an alert?</h3>
<p>Alerts search for <strong>exact keyword terms</strong> within a transcript. </p>

<table id="example" class="keywords">
<tr>
	<th width="60%">Example terms for Pat McCrory</td>
    <th width="10%"><div align="center">Pat McCrory</div></td>
    <th width="10%"><div align="center">McCrory</div></td>
    <th width="10%"><div align="center">Gov. McCrory</div></td>
    <th width="10%"><div align="center">Governor</div></td>
</tr>
<tr>
	<td>I continue to recommend further study on this issue, <strong>Pat McCrory</strong>, governor of the state of North Carolina. Calendar, governors objections and veto message, House bill 786, an act to require the department of public safety to study measures for addressing the problem of illegal immigration in this state and to clarify which employers are subject to the state's E-verify laws.</td>
    <td class="yes">&#10003;</td>
    <td class="yes">&#10003;</td>
    <td class="no">&#10005;</td>
    <td class="yes">&#10003;</td>
</tr>
<tr>
  <td>In last fall's gubernatorial debate, <strong>Governor McCrory</strong> committed that he wouldn't sign any further restrictions on abortion. Governor, the women of North Carolina are counting on you to keep your commitment.</td>
  <td class="yes"><span class="no">&#10005;</span></td>
  <td class="yes">&#10003;</td>
  <td class="no">&#10005;</td>
  <td class="yes">&#10003;</td>
</tr>
</table> -->

<!-- <h2>Search</h2> -->

<h3  id="search_keyword">My keyword isn't returning anything. What should I do?</h3>
<p>Keywords search for <strong>exact keyword terms</strong> within a transcript, with a specified <strong>date range</strong>.</p>
<ol>
  <li>View an example of <a href="#example">good search terms</a>.</li>
  <li>Verify the date range. Try changing the start or end date.</li>
  <li>Try spelling variations: &quot;healthcare&quot; and &quot;health care&quot; may return different results.</li>
</ol>

<h3 id="good_search"> What does a good search term look like?</h3>
<table id="example" class="keywords">
<tr>
	<th width="60%">Example terms for Pat McCrory</td>
    <th width="10%"><div align="center">Pat McCrory</div></td>
    <th width="10%"><div align="center">McCrory</div></td>
    <th width="10%"><div align="center">Gov. McCrory</div></td>
    <th width="10%"><div align="center">Governor</div></td>
</tr>
<tr>
	<td>I continue to recommend further study on this issue, <strong>Pat McCrory</strong>, governor of the state of North Carolina. Calendar, governors objections and veto message, House bill 786, an act to require the department of public safety to study measures for addressing the problem of illegal immigration in this state and to clarify which employers are subject to the state's E-verify laws.</td>
    <td class="yes">&#10003;</td>
    <td class="yes">&#10003;</td>
    <td class="no">&#10005;</td>
    <td class="yes">&#10003;</td>
</tr>
<tr>
  <td>In last fall's gubernatorial debate, <strong>Governor McCrory</strong> committed that he wouldn't sign any further restrictions on abortion. Governor, the women of North Carolina are counting on you to keep your commitment.</td>
  <td class="yes"><span class="no">&#10005;</span></td>
  <td class="yes">&#10003;</td>
  <td class="no">&#10005;</td>
  <td class="yes">&#10003;</td>
</tr>
</table>

<h3 id="search_sessions">Can I search previous sessions of the N.C. General Assembly?</h3>
<p>Capitol Hound currently provides searchable results and alerts for the 2013-2016 legislative sessions.</p>

<h3 id="search_time">How often is Capitol Hound updated?</h3>
<p>Capitol Hound is not currently being updated with new sessions.</p>



<!--<h2>User Settings</h2>
<h3 id="user_password">How do I change my password?</h3>
<ul>
<li>Visit the <a href="/user_settings.php">User Settings</a> page by visiting <strong>Account > User Settings</strong>.</li>
</ul>-->



<?php require_once("models/footer.php"); ?>
