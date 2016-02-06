<?php
	$move_img = $_POST[f];
	$move_thumb = $_POST[t];
	$move_dir = $_POST[d];
	
	$dest = 'imgs/'.$move_dir.'/'.basename($move_img);
	rename($move_img, $dest);
	
	$dest = 'imgs/'.$move_dir.'/'.basename($move_thumb);
	rename($move_thumb, $dest);
?>