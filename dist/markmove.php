<?php
	$move_img = $_POST[f];
	$move_dir = $_POST[d];
	
	$dest = 'imgs/'.$move_dir.'/'.basename($move_img);
	
	rename($move_img, $dest);
?>