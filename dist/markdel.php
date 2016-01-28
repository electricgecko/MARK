<?php
	
	// get file to delete
	$del_img = $_POST[f];
	
	// delete file
	unlink($del_img);
?>