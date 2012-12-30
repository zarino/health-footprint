<?php

require $_SERVER['DOCUMENT_ROOT'] . '/config.php';
require $_SERVER['DOCUMENT_ROOT'] . '/functions.php';

connect_to_database();
setup_database();

if(!empty($_POST)){

	$defaults = array('gmc_number'=>Null, 'first_name'=>Null, 'last_name'=>Null, 'hospital'=>Null, 'department'=>Null, 'subspecialty'=>Null, 'location'=>Null, 'funder'=>Null);
    $doctor = array();
    foreach($_POST as $k=>$v){
        if(array_key_exists($k, $defaults)){
            $doctor[$k] = $v;
        }
    }
	$doctor['created'] = date('Y-m-d H:i:s');
    $doctor = array_map('db_safe', $doctor);
	$q = "REPLACE INTO `doctor` (" . implode(',', array_keys($doctor)) . ") VALUES (" . implode(',', $doctor) . ")";
    prettyprint(array($q));
	$r = mysqli_query($GLOBALS['dbc'], $q);
	if (mysqli_error($GLOBALS['dbc']) || mysqli_affected_rows($GLOBALS['dbc']) == 0) {
        $error = 'There was an error while saving your details: ' . mysqli_error($GLOBALS['dbc']);
	} else {
        $message = 'Your details have been saved!';
	}

    if(isset($_POST['destination']) && isset($_POST['month']) && isset($_POST['year']) && isset($_POST['days'])){
        $rows = array();
        $len = count($_POST['destination']);
        for($i=0;$i<$len;$i++){
            $rows[$i] = implode(',', array(
                db_safe($_POST['gmc_number']),
                db_safe($_POST['destination'][$i]),
                db_safe($_POST['month'][$i]),
                db_safe($_POST['year'][$i]),
                db_safe($_POST['days'][$i]),
                db_safe(date('Y-m-d H:i:s'))
            ));
        }
        $q = "INSERT INTO `trip` (gmc_number, destination, month, year, days, created) VALUES (" . implode('),(', $rows) . ")";
        prettyprint(array($q));
    	$r = mysqli_query($GLOBALS['dbc'], $q);
    	if (mysqli_error($GLOBALS['dbc']) || mysqli_affected_rows($GLOBALS['dbc']) == 0) {
            print 'Could not add trips: ' . mysqli_error($GLOBALS['dbc']);
    	} else {
            print 'Trips recorded! (id: ' . mysqli_insert_id($GLOBALS['dbc']) . ')';
    	}
    }

}

print html_head(array('body_class'=>'trip'));

?><div class="container" style="margin: 0 auto; max-width: 700px">

<form method="post" action="." id="add_trip">
    <div class="row-fluid">
        <h3 class="span12">About you</h3>
    </div>
    <div class="row-fluid">
        <p class="span4">
            <label for="first_name">First name:</label>
            <input type="text" id="first_name" name="first_name" class="span12" required />
        </p>
        <p class="span4">
            <label for="last_name">Last name:</label>
            <input type="text" id="last_name" name="last_name" class="span12" required />
        </p>
        <p class="span4">
            <label for="gmc_number">GMC Number:</label>
            <input type="text" id="gmc_number" name="gmc_number" class="span12" required />
        </p>
    </div>
    <div class="row-fluid">
        <p class="span4">
            <label for="hospital">Hospital:</label>
            <input type="text" id="hospital" name="hospital" class="span12" />
        </p>
        <p class="span4">
            <label for="department">Department:</label>
            <input type="text" id="department" name="department" class="span12" />
        </p>
        <p class="span4">
            <label for="subspecialty">Sub-specialty:</label>
            <input type="text" id="subspecialty" name="subspecialty" class="span12" />
        </p>
    </div>
    <div class="row-fluid">
        <h3 class="span12">About your trips</h3>
    </div>
    <div class="row-fluid trip">
        <p class="span6">
            <label for="destination_0">Where did you volunteer?</label>
            <input type="text" id="destination_0" name="destination[]" class="span12" />
        </p>
        <p class="span3">
            <label for="month_0">When?</label>
            <select id="month_0" name="month[]" class="span8"><?php
for($i=0;$i<12;$i++){
    if($i==gmdate('n')-1){ $s = 'selected="selected" '; } else { $s = ''; }
    echo '
                <option value="' . $i . '"' . $s . '>' . gmdate('F', mktime(0,0,0,$i+1,1,2011)) . '</option>';
}
                ?>
            </select> <input type="text" id="year_0" name="year[]" class="span4" placeholder="<?php echo gmdate('Y'); ?>" />
        </p>
        <p class="span3">
            <label for="days_0">For how long?</label>
            <span class="input-append">
                <input type="text" id="days_0" name="days[]" style="width: 66%" />
                <span class="add-on">days</span>
            </span>
        </p>
    </div>
    <hr/>
    <div class="row-fluid">
        <p class="span12">
            <button type="submit" class="btn btn-primary pull-right"><i class="icon-ok icon-white"></i> Save!</button>
            <button class="btn" id="add_another"><i class="icon-plus"></i> Add Another Trip</button>
        </p>
    </div>
</form>

</div><?php

print html_foot();

?>