<?php
	header("Access-Control-Allow-Origin: *");
	date_default_timezone_set('Europe/Berlin');
	
	function sanitizeFilename($f) {
	$replace_chars = array(
	    'Š'=>'S', 'š'=>'s', 'Ð'=>'Dj','Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A',
	    'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E', 'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I',
	    'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U', 'Ú'=>'U',
	    'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss','à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a',
	    'å'=>'a', 'æ'=>'a', 'ç'=>'c', 'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i',
	    'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 'ù'=>'u',
	    'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y', 'ƒ'=>'f'
	);
	$f = strtr($f, $replace_chars);
	// convert & to "and", @ to "at", and # to "number"
	$f = preg_replace(array('/[\&]/', '/[\@]/', '/[\#]/'), array('-and-', '-at-', '-number-'), $f);
	$f = preg_replace('/[^(\x20-\x7F)]*/','', $f); // removes any special chars we missed
	$f = str_replace(' ', '-', $f); // convert space to hyphen 
	$f = str_replace('\'', '', $f); // removes apostrophes
	$f = preg_replace('/[^\w\-\.]+/', '', $f); // remove non-word chars (leaving hyphens and periods)
	$f = preg_replace('/[\-]+/', '-', $f); // converts groups of hyphens into one
	return strtolower($f);
	}
	
	$exp = '-';
	
	// get vars
	$img = $_POST[f];
	$img_w = $_POST[w];
	$img_h = $_POST[h];
	
	// get time/date string
	$img_date = date(ymdHis);
	
	// $img_file = basename($img);
	$img_file =sanitizeFilename(basename($img));

	$img_name = 'imgs/'.$img_date.$exp.$img_w.$exp.$img_h.$exp.$img_file;
	
	
	copy($img, $img_name);
?>