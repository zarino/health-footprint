<?php

/* HTML TEMPLATE FUNCTIONS */

function html_head($args){
	$defaults = array(
        'title' => 'Health Footprint',
        'description'=> 'Helping Doctors make an impact with medical volunteering.'
    );
    $vars = array_merge($defaults, $args);
    $html = '<!doctype html>
<!--[if lt IE 7]><html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if (IE 7)&!(IEMobile)]><html class="no-js lt-ie9 lt-ie8" lang="en"><![endif]-->
<!--[if (IE 8)&!(IEMobile)]><html class="no-js lt-ie9" lang="en"><![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"><!--<![endif]-->
<head>
    <meta charset="utf-8">
    <title>' . $vars['title'] . '</title>
    <meta name="description" content="' . $vars['description'] . '">
    <meta name="author" content="Zarino Zappia and Alex Spratt">
    <meta name="HandheldFriendly" content="True">
    <meta name="MobileOptimized" content="320">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="/vendor/css/bootstrap.min.css" rel="stylesheet">
    <link href="/vendor/css/bootstrap-responsive.min.css" rel="stylesheet">
    <meta http-equiv="cleartype" content="on">
</head>
<body>
';
    return $html;
}

function html_foot(){
    return '
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8/jquery.min.js"></script>
<script>window.jQuery || document.write(\'<script src="vendor/js/jquery-1.8.3.min.js"><\/script>\')</script>
<script src="/vendor/js/bootstrap.min.js"></script>
</body>
</html>';
}

/* DATABASE FUNCTIONS */

function connect_to_database(){
	$GLOBALS['dbc'] = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) OR die ('Could not connect to MySQL: ' . mysqli_connect_error() );
	mysqli_set_charset($GLOBALS['dbc'], 'utf8');
	mysqli_query($GLOBALS['dbc'], "SET time_zone='" + get_mysql_timezone_offset() + "';");
}

function db_safe($v, $s="'"){
	if(is_null($v)){
		return 'NULL';
	} else if(is_string($v) || is_int($v) || is_float($v) || is_numeric($v)) {
		return $s . mysqli_real_escape_string($GLOBALS['dbc'], (string) $v) . $s;
	} else {
		throw new Exception("db_safe() can't handle arguments of type " . gettype($v));
	}
}

function get_mysql_timezone_offset(){
    $tz = new DateTimeZone("Europe/London");
    $dt = new DateTime("now", $tz);
    $mins = $dt->getOffset() / 60;
    $sgn = ($mins < 0 ? -1 : 1);
    $mins = abs($mins);
    $hrs = floor($mins / 60);
    $mins -= $hrs * 60;
    return sprintf('%+d:%02d', $hrs*$sgn, $mins);
}

function setup_database(){
    mysqli_query($GLOBALS['dbc'], 'CREATE TABLE IF NOT EXISTS doctor (gmc_number INT PRIMARY KEY, first_name VARCHAR(64) NULL, last_name VARCHAR(64) NULL, hospital TINYTEXT NULL, department TINYTEXT NULL, subspecialty TINYTEXT NULL, location VARCHAR(64) NULL, funder VARCHAR(64) NULL, created DATETIME NULL)');
    mysqli_query($GLOBALS['dbc'], 'CREATE TABLE IF NOT EXISTS trip (id INT PRIMARY KEY AUTO_INCREMENT, gmc_number INT NULL, destination VARCHAR(64) NULL, year INT NULL, month TINYINT NULL, days INT NULL, created DATETIME NULL)');
}

/* UTILITY FUNCTIONS */

# Turns an alphanumeric hash into an integer (eg: '2n9c' -> 123456)
function hash_to_integer($string, $base = HASH_CHARS){
    $length = strlen($base);
    $size = strlen($string) - 1;
    $string = str_split($string);
    $out = strpos($base, array_pop($string));
    foreach($string as $i => $char){
        $out += strpos($base, $char) * pow($length, $size - $i);
    }
    return $out;
}

# Turns an integer into an alphanumeric hash (eg: 123456 -> '2n9c')
function integer_to_hash($integer, $base = HASH_CHARS){
    $length = strlen($base);
    $out = '';
    while($integer > $length - 1){
        $out = $base[fmod($integer, $length)] . $out;
        $integer = floor( $integer / $length );
    }
    return $base[$integer] . $out;
}

function uri_part($index){
	$parts = array_values(array_filter(explode('/', preg_replace('#(\.json|\.html)$#', '', $_SERVER['REQUEST_URI']))));
	if($index < count($parts)){
		return $parts[$index];
	} else {
		throw new Exception("Cannot return part #" . $index . ': REQUEST_URI "' . $_SERVER['REQUEST_URI'] . '" only has ' . count($parts) . ' part' . pluralise(count($parts)));
	}
}

/* STRING FUNCTIONS */

function begins_with($haystack, $needle){
    return (strpos($haystack, $needle) === 0 ? True : False);
}

function contains($haystack, $needle){
    return (strpos($haystack, $needle) ? True : False);
}

function ends_with($haystack, $needle){
    return (strpos($haystack, $needle) === strlen($haystack)-strlen($needle) ? True : False);
}

# Turns an array into a list like: "First, second and third"
function human_list($items, $separator='and'){
	$last = array_pop($items);
	return implode(', ', $items) . ' $separator ' . $last;
}

function pluralise($number, $plural_suffix='s', $singular_suffix=''){
	if(is_int($number) || is_float($number)){
		if($number == 1){
			return $singular_suffix;
		} else {
			return $plural_suffix;
		}
	} else {
		throw new Exception("pluralise() was passed a non-integer, non-float argument");
	}
}

function pretty_print_r($arg1, $arg2='', $suffix=''){
	if(is_array($arg1) || is_object($arg1)){
		$array = $arg1;
	} else if(is_array($arg2) || is_object($arg2)) {
		$array = $arg2;
	}
	if(is_string($arg1)){
		$prefix = $arg1;
	} else if(is_string($arg2)) {
		$prefix = $arg2;
	}
	if(isset($prefix) && isset($array)){
		print $prefix . '<pre>';
		print_r($array);
		print '</pre>' . $suffix;
	} else {
	    throw new Exception("Incorrect parameters supplied to pretty_print_r() - function takes an optional string prefix, a required array, and an optional string suffix");
	}
}

?>