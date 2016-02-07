<?php	
	$del_img = $_POST[f];
	$del_thumb = $_POST[t];
	
	echo $del_thumb;
	echo ' ';
	echo $del_img;
	
	unlink($del_img);
	unlink($del_thumb);
?>