<?php 

require 'vendor/autoload.php';

$USERNAME = "*******";
$PASSWORD = "*******";

$aa = new Buki\AnadoluAgency($USERNAME, $PASSWORD);

$content = $aa->discover();

var_dump($content); // format: json
