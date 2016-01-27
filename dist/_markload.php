<?php
	header("Access-Control-Allow-Origin: *");
	date_default_timezone_set('Europe/Berlin');
	$exp = '-';
	
	// get vars
	$img = $_POST[f];
	$img_w = $_POST[w];
	$img_h = $_POST[h];
	
	// get time/date string
	$img_date = date(ymdHis);
	
	$img_file = basename($img).replace(/[^a-z0-9]/gi, '_').toLowerCase();

	$img_name = 'imgs/'.$img_date.$exp.$img_w.$exp.$img_h.$exp.$img_file;

	
	copy($img, $img_name);
?>