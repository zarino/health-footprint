<?php

require $_SERVER['DOCUMENT_ROOT'] . '/config.php';
require $_SERVER['DOCUMENT_ROOT'] . '/functions.php';

connect_to_database();
# setup_database();

print html_head(array('body_class'=>'home'));

$r = mysqli_query($GLOBALS['dbc'], "SELECT now()");
while($row=mysqli_fetch_array($r, MYSQLI_ASSOC)){
    prettyprint($row);
}

print html_foot();

?>