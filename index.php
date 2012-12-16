<?php

require $_SERVER['DOCUMENT_ROOT'] . '/config.php';
require $_SERVER['DOCUMENT_ROOT'] . '/functions.php';

connect_to_database();
# setup_database();

$r = mysqli_query($GLOBALS['dbc'], "SELECT now()");
while($row=mysqli_fetch_array($r)){
    pretty_print_r($row);
}

?>