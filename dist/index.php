<?php
    ob_start();
	session_start();
	
	require_once('config.php');
	
	if(isset($_GET['logout'])) {
	    $_SESSION['user'] = '';
	    setcookie('MARKsession','', time()-86400, '/');
	    header('Location:  http://' . $_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']));
	    exit;
	}
	
	if (isset($_COOKIE['MARKsession'])) {
    	$_SESSION['user'] = $_COOKIE['MARKsession'];
	} 
	else if (isset($_POST['user'])) {
    	
	    if($userinfo[$_POST['user']] == $_POST['password']) {
	        $_SESSION['user'] = $_POST['user'];
	        setcookie('MARKsession',$_POST['user'], time()+86400*30, '/');
	   
	    } else {
	       echo 'invalid login';
	    }
	}
	ob_end_flush();
?>

<!DOCTYPE html>
<head>
	<meta charset="UTF-8"/>
	<meta name="author" content="Malte Müller"/>
	<meta name="description" content="Private collection of images, collected silently by M A R K.">
	<meta name="robots" content="noindex, nofollow">

    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    
	<title>M A R K</title>
	
	<link rel="apple-touch-icon" sizes="114x114" href="vendor/favicons/apple-touch-icon-114x114.png">
	<link rel="apple-touch-icon" sizes="120x120" href="vendor/favicons/apple-touch-icon-120x120.png">
	<link rel="apple-touch-icon" sizes="144x144" href="vendor/favicons/apple-touch-icon-144x144.png">
	<link rel="apple-touch-icon" sizes="152x152" href="vendor/favicons/apple-touch-icon-152x152.png">
	<link rel="apple-touch-icon" sizes="180x180" href="vendor/favicons/apple-touch-icon-180x180.png">
	<link rel="icon" type="image/png" href="vendor/favicons/favicon-32x32.png" sizes="32x32">
	<link rel="icon" type="image/png" href="vendor/favicons/favicon-16x16.png" sizes="16x16">
	<link rel="manifest" href="vendor/favicons/manifest.json">
	<link rel="mask-icon" href="vendor/favicons/safari-pinned-tab.svg" color="#5bbad5">
	<link rel="shortcut icon" href="vendor/favicons/favicon.ico">
	
	<link rel="stylesheet" href="vendor/mark.css">
		
	<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
	<script src="vendor/isotope.min.js"></script>
	<script src="vendor/pressure.min.js"></script>
	<script src="vendor/ui.js"></script>
</head>

<body data-imgdir="<?php echo $imgdir ?>">

    <?php if ($_SESSION['user']): ?>
        
	<?php
		$folders = array_filter(glob($imgdir.'/*', GLOB_NOCHECK), 'is_dir'); // read folders in main image folder
		$images = array();	
		$c = 0;
		
		function remove_thumbs($arr) {
			global $thumb_indicator;
			$thumbs = array_filter($arr, function($var) use ($thumb_indicator) { return preg_match("/\b$thumb_indicator\b/i", $var); });
			$arr = array_diff($arr, $thumbs);
			return $arr;	
		}
		
        function get_thumb($img_name) {
    	    global $thumb_indicator;
    	    global $exp;
            return substr_replace($img_name, $exp.$thumb_indicator, strpos($img_name,$exp), 0);
	    }
		
		// go.
		
		// read main image folder	
		$main_content = glob($imgdir.'/*.{jpg,jpeg,gif,png}', GLOB_BRACE);
		
		// remove thumbnails
		$main_content = remove_thumbs($main_content);
		
		// add to image object
		foreach ($main_content as $image) {
			$images[$c]['name'] = $image;
			$images[$c]['folder'] = $imgdir;
			$c++;
		}
		
		// read subfolders
		foreach ($folders as $folder) {
			
			// read folder content
			$folder_content = glob($folder.'/*.{jpg,jpeg,gif,png}', GLOB_BRACE);
			
			// remove thumbnails
			$folder_content = remove_thumbs($folder_content);
			
			// add to image object
			foreach ($folder_content as $image) {
				$images[$c]['name'] = $image;
				$images[$c]['folder'] = $folder;
				$c++;
			}			
		}
	
		// sort image object
		uasort($images, function($a, $b) {
			return basename($b['name']) - basename($a['name']);
		});
	?>
		
	<header>
		<h1><a href="<?php echo $installdir ?>">MARK</a></h1>
		<nav>
			<ol>
				<li>everything</li><?php
					if (count($folders) > 0) {				
						foreach ($folders as $folder) {
							echo '<li><span>'.basename($folder).'</span></li>';
						}
					}
				?><li>unsorted</li>
			</ol>
		</nav>
	</header>
	
	<main>
		<ul>
			<?php
	
				$index = 0;
				$displayed_images = array();
				
				foreach ($images as $image) {
		
						// parse image info from filename
						$img_info = explode($exp, basename($image[name]));
						$image_date = $img_info[0];
						$image_w = $img_info[1];
						$image_h = $img_info[2];	
						$image_title = $img_info[3];
						$image_thumbnail = get_thumb($image[name]);
						
						// self-cleaning: if image is a duplicate, don't show it and delete it from server
						if (in_array($image_title, $displayed_images)) {
                            unlink($image[name]);
                            unlink(get_thumb($image[name]));
                            
						} else {
							
							// make sure the image isn't still being copied – and thumbnail is created
							if (file_exists($image_thumbnail)) {
							
				    			// show image
				    			array_push($displayed_images, $image_title);
				    			echo '<li class="'.basename($image['folder']).'" data-thumb="'.$image_thumbnail.'" data-url="'.$image[name].'"><a class="del" href="javascript:void(0);">×</a><figure><a href="'.$image['name'].'"><img width="'.$image_w.'" height="'.$image_h.'" src="'.$image_thumbnail.'" /></a></figure></li>';
				    			$index++;
				    			
                            }
						}
				}
			?>
		</ul>

	</main>
	
	<aside>
		<p>add <span>0</span> to</p>
		<ol>
			<li><span>everything</span></li>
			<?php
				// list folders 
				if (count($folders) > 0) {				
					foreach ($folders as $folder) {
						echo '<li><span>'.basename($folder).'</span></li>';
					}
				}
			?>
		</ol>
		<span id="close" class="close">✕</span>
		<span id="done" class="done">✔</span>
	</aside>
	

	<a class="mobileInvert" id="mobileInvert" hred="javascript;">invert</a>
	<div class="mobileUploadWrap">
        <a href="javascript:;">upload</a>
    	<input class="mobileUpload" id="mobileUpload" type="file"/>
    </div>
	<a class="logout" id="logout" href="?logout=1">logout</a>
	
	
	<?php else: ?>
	    <main class="login">
	        <h1><a href="<?php echo $installdir ?>">MARK</a></h1>
            <form name="login" action="" method="post">

    	        <input type="text" name="user" value="" />
    	        <label for="user">Username</label>
    	            	        
                <input type="password" name="password" value="" />
                <label for="password">Password</label>
    	        
    	        <input type="submit" name="submit" value="Submit" />
    	   </form>	
	    </main>
    	
	<?php endif; ?>
</body>