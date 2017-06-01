<?php
require_once("models/header.php");
?>

<h1>Technical Support</h1>

<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
  <div class="panel panel-default">
    <div class="panel-heading" role="tab" id="headingOne">
      <h4 class="panel-title">
        <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
          I don't think I am getting all of my alerts.
        </a>
      </h4>
    </div>
    <div id="collapseOne" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">
      <div class="panel-body">
      <p>Alerts are sent each time the keywords you have selected are chosen.  The transcripts do not recognize that "schools," "education," and "teachers" might all be related to the same topic.  We suggest you select a few words that might be spoken about a particular topic so that you do not miss anything.</p>
      </div>
    </div>
  </div>
  <div class="panel panel-default">
    <div class="panel-heading" role="tab" id="headingTwo">
      <h4 class="panel-title">
        <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
          I am getting too many alerts to navigate.
        </a>
      </h4>
    </div>
    <div id="collapseTwo" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo">
      <div class="panel-body">
      <p>If a keywork is too generic, it might be spoken many times during a meeting and not be relevant to your purposes.  We recommend reviewing the keywords you have selected so that you do not receive so many false alarms.</p>
      </div>
    </div>
  </div>
  <div class="panel panel-default">
    <div class="panel-heading" role="tab" id="headingThree">
      <h4 class="panel-title">
        <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
          Other Questions
        </a>
      </h4>
    </div>
    <div id="collapseThree" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingThree">
      <div class="panel-body">
      <p>If you cannot find your answer online, please email Capitol Hound at capitolhound@reesenews.org and we will get in touch with you to answer your questions.</p>
      </div>
    </div>
  </div>
</div>

<?php require_once("models/footer.php"); ?>
