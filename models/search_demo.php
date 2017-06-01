    
<?php if($_SERVER['PHP_SELF'] == '/demo.php'){ $method = 'POST'; } else { $method = 'GET'; } ?>

<?php
    $yesterday = new DateTime();
    $yesterday->sub(new DateInterval('P1D'));
    $yesterday = $yesterday->format('Y-m-d');

    $day_before_yesterday = new DateTime();
    $day_before_yesterday->sub(new DateInterval('P2D'));
    $day_before_yesterday = $day_before_yesterday->format('Y-m-d');

print "<hr/>$info_warning<hr/>";

?>

    
	<form class="col-sm-8 col-sm-offset-2 "  method="<?php echo $method;?>" action="<?php echo $_SERVER['PHP_SELF'];?>">
        <input type="hidden" name="step" value="1"/>

    <div class="row">		
        <div class="input-group input-group-lg searchbar">
			<input placeholder="Search keywords" id="id_q" name="search_term" type="text" value="<?php if($_SERVER['PHP_SELF'] == '/demo.php'){ echo 'healthcare'; }?>" class="form-control"i <?php //if($_SERVER['PHP_SELF'] == '/demo.php'){echo 'readonly=""';}?>/><br/>
			<span class="input-group-btn">
                <button class="btn btn-default" type="submit" value="Search">
                <i class="glyphicon glyphicon-search"></i>
			    </button>
            </span>
	</div>
    </div>
    <div class="row">
        
        <div class="col-sm-5 col-sm-offset-1 daterange">
            <label>Start Date</label>
            <div class="input-group input-group-lg date" id="start" data-date-format="YYYY-MM-DD">
                <input class="form-control" type="text" name="start_date" <?php if($_SERVER['PHP_SELF'] == '/demo.php'){ ?>value="2014-05-14"<?php } else{ ?> value="<?php print $day_before_yesterday; ?>" <?php } ?>>
                <span class="input-group-btn">
                    <button class="btn btn-default">
                        <i class="glyphicon glyphicon-calendar"></i>
                    </button>
                </span>
            </div>
        </div>
        <div class="col-sm-5 daterange">
            <label>End Date</label>
            <div class="input-group input-group-lg date" id="end" data-date-format="YYYY-MM-DD">
                <input class="form-control" type="text" name="end_date" value="2014-05-21" >
                <span class="input-group-btn">
                    <button class="btn btn-default">
                        <i class="glyphicon glyphicon-calendar"></i>
                    </button>
                </span>
            </div>
        </div>
        </form>
    </div>
    <div class="row">
	<div class="col-sm-10 col-sm-offset-1">
	    <br/></br/>
	</div>
    </div>
	
	
