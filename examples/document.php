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

$content = json_decode($content);

$type = "";
foreach($content->data->result as $news)
{
	if($news->type == "text")
		$type = "newsml29";
	elseif($news->type == "picture")
		$type = "web";
	elseif($news->type == "video")
		$type = "sd";
		
	$data = $aa->document($news->id, $type);
	var_dump( $aa->save() );
}



