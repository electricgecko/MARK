<?php
	header('Access-Control-Allow-Origin: *');
	
	date_default_timezone_set('Europe/Berlin');
	
    require_once('config.php');
 	
	$a = $_POST[a];
    
	if (isset($_POST[f]))  { $file = $_POST[f];     }
	if (isset($_POST[t]))  { $thumb = $_POST[t];    }
	if (isset($_POST[d]))  { $dir = $_POST[d];      }
	if (isset($_FILES[u])) { $upload = true;        }
	
	
	
	switch ($a) {
    case 'del':
        markdel($file, $thumb);
        break;
    case 'move':
    	markmove($file, $thumb, $dir);
		break;
	case 'load':
        markload($file, $upload);
		break;
	}
	
	function markdel($del_img, $del_thumb) {
    	
		if (!unlink($del_img)) {

		}
		if (!unlink($del_thumb)) {

		}				
	}	
	
	function markmove($move_img, $move_thumb, $move_dir) {
    	global $imgdir;
    	
		$dest = $imgdir.'/'.$move_dir.'/'.basename($move_img);
		rename($move_img, $dest);
		
		$dest = $imgdir.'/'.$move_dir.'/'.basename($move_thumb);
		rename($move_thumb, $dest);		
	}

	
	function markload($img, $upload) {
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
		
		global $exp, $rep_exp, $thumb_indicator, $thumb_width, $imgdir;
    
        if ($upload) {
           $img = $_FILES['u']['tmp_name'];
        }    
          
        if (exif_imagetype($img) == IMAGETYPE_JPEG) {
            $img_el = imagecreatefromjpeg($img);
            $ext = '.jpg';
        } elseif (exif_imagetype($img) == IMAGETYPE_PNG) {
            $img_el = imagecreatefrompng($img);
            $ext = '.png';
        } elseif (exif_imagetype($img) == IMAGETYPE_GIF) {
            $img_el = imagecreatefromgif($img);
            $ext = '.gif';
        }
        
		$img_w = imagesx($img_el);
		$img_h = imagesy($img_el);
		
		// get time/date string
		$img_date = date(ymdHis);
		
		// get original filename in case file was uploaded
		if ($upload) {
    		// construct file name, removing extension
    		$img_file = sanitizeFilename(preg_replace('/\\.[^.\\s]{3,4}$/', '', $_FILES['u']['name']));
		} else {
            $img_file = sanitizeFilename(basename($img));    		
		}
		
		$img_file = str_replace($exp, $rep_exp, $img_file);
		
		// define image name
		$img_name = $imgdir.'/'.$img_date.$exp.$img_w.$exp.$img_h.$exp.$img_file;
		if ($upload) { $img_name .= $ext; }
		
		// copy actual image
		copy($img, $img_name);
		
		// create thumbnail	
		$thumb_height = floor($img_h * ($thumb_width / $img_w));	
		$thumb_name = $imgdir.'/'.$img_date.$exp.$thumb_indicator.$exp.$img_w.$exp.$img_h.$exp.$img_file;   
		if ($upload) { $thumb_name .= $ext; }
		$thumb_image = imagecreatetruecolor($thumb_width, $thumb_height);
		imagecopyresampled($thumb_image, $img_el, 0, 0, 0, 0, $thumb_width, $thumb_height, $img_w, $img_h);
	
		imagejpeg($thumb_image, $thumb_name);
		
		echo json_encode(array('img_name' => $img_name, 'thumb_name' => $thumb_name);
	}
	
	exit();
?>