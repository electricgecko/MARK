<?php
	header("Access-Control-Allow-Origin: *");
	date_default_timezone_set('Europe/Berlin');
	$exp = '-';
	
	$img = $_POST[f];
	$img_w = $_POST[w];
	$img_h = $_POST[h];
	
	$img_date = date(ymdhi);

	$img_folder = '';
	$img_name = 'imgs/'.$img_date.$exp.$img_w.$exp.$img_h.$exp.basename($img);
	
	copy($img, $img_name);
?>