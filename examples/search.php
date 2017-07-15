<?php 

require 'vendor/autoload.php';

$USERNAME = "*******";
$PASSWORD = "*******";

$aa = new Buki\AnadoluAgency($USERNAME, $PASSWORD);

$content = $aa->time($start = date("Y-m-d", time()), $end = "NOW")
			  ->limit(0, 10)
			  ->filter("type", [1])
			  ->filter("language", [1])
			  ->filter("category", [1])
			  ->search();

var_dump($content); // format: json
