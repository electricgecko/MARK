<?
  ob_start();
	session_start();
	
	require_once('config.php');

  // clean up download file
  if (file_exists($zip_name)) {
	  unlink ($zip_name);
  }

	if (isset($_GET['logout'])) {
	    $_SESSION['user'] = '';
	    setcookie('MARKsession','', time()-86400, '/');
	    header('Location:  https://' . $_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']));
	    exit;
	}
	
	if (isset($_COOKIE['MARKsession'])) {
    	$_SESSION['user'] = $_COOKIE['MARKsession'];
	} 
	
	else if (isset($_POST['user'])) {
    	
	    if ((array_key_exists($_POST['user'], $userinfo)) && $userinfo[$_POST['user']] == $_POST['password']) {
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
	
	<link rel="apple-touch-icon" sizes="114x114" href="/vendor/favicons/apple-icon-114x114.png">
	<link rel="apple-touch-icon" sizes="120x120" href="/vendor/favicons/apple-icon-120x120.png">
	<link rel="apple-touch-icon" sizes="144x144" href="/vendor/favicons/apple-icon-144x144.png">
	<link rel="apple-touch-icon" sizes="152x152" href="/vendor/favicons/apple-icon-152x152.png">
	<link rel="apple-touch-icon" sizes="180x180" href="/vendor/favicons/apple-icon-180x180.png">
	<link rel="icon" type="image/png" href="/vendor/favicons/favicon-256x256.png" sizes="256x256">
	<link rel="icon" type="image/png" href="/vendor/favicons/favicon-194x194.png" sizes="194x194">
	<link rel="icon" type="image/png" href="/vendor/favicons/favicon-96x96.png" sizes="96x96">
	<link rel="icon" type="image/png" href="/vendor/favicons/favicon-64x64.png" sizes="64x64">
	<link rel="icon" type="image/png" href="/vendor/favicons/favicon-32x32.png" sizes="32x32">
	<link rel="icon" type="image/png" href="/vendor/favicons/favicon-16x16.png" sizes="16x16">
	<link rel="mask-icon" href="vendor/favicons/safari-pinned-tab.svg" color="black">
	<link rel="shortcut icon" href="vendor/favicons/favicon.ico">
	
	<link rel="stylesheet" href="vendor/mark.css?028">
		
	<script src="https://code.jquery.com/jquery-2.2.4.min.js"></script>
</head>

<body data-imgdir="<? echo $imgdir ?>">

  <? if (array_key_exists('user', $_SESSION) && $_SESSION['user']): ?>
        
	<?
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
		$main_content = glob($imgdir.'/*.{jpg,jpeg,gif,png,webp,avif}', GLOB_BRACE);
		
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
			$folder_content = glob($folder.'/*.{jpg,jpeg,gif,png,webp,avif}', GLOB_BRACE);
			
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
		<h1>MARK</h1>
		<svg xmlns="http://www.w3.org/2000/svg" width="81.936" height="21.389" viewBox="0 0 81 21.3"><path d="M19.086 0l-8.461 19.123L2.021 0H0v21.348h1.658V3.242l8.124 18.106h1.666l8.002-18.08v18.08h1.658V0zM70.236 10.464L81.422 0h-2.394L68.765 9.695H66.92V0h-1.658v21.348h1.658V11.294h1.845l10.682 10.054h2.488zM57.278 12.003c3.029-.528 3.586-2.384 3.586-5.912C60.864.84 59.294 0 54.261 0h-8.4v21.348h1.658v-9.161h6.742c.453 0 .874-.007 1.265-.022l4.201 9.224h1.842l-4.29-9.386zm-1.307-1.463c-.49.032-1.08.048-1.83.048h-6.622v-8.99h6.622c4.54 0 5.061.462 5.065 4.496-.004 3.35-.435 4.14-2.648 4.393l-.587.053zM34.504.001h-1.766l-8.256 21.347h1.732l1.152-3.026h12.252l1.134 3.026h1.727L34.504 0zm-6.53 16.724l5.623-14.551 5.423 14.55H27.975z"/></svg>
		<nav>
			<ol>
				<li><span>everything</span></li><?
					if (count($folders) > 0) {				
						foreach ($folders as $folder) {
							echo '<li class="'.basename($folder).'"><span>'.basename($folder).'</span></li>';
						}
					}
				?><li><span>unsorted</span></li>
			</ol>
		</nav>
	</header>
	
	<main>
    <ul>
		  <?
        $index = 0;
				$displayed_images = array();
				
				foreach ($images as $image) {
				  // parse image info from filename
				  $img_info = explode($exp, basename($image['name']));
				  $image_date = $img_info[0];
				  $image_w = $img_info[1];
				  $image_h = $img_info[2];	
				  $image_title = $img_info[3];
				  $image_thumbnail = get_thumb($image['name']);
				  
				  // self-cleaning: if image is a duplicate, don't show it and delete it from server
				  if (in_array($image_title, $displayed_images)) {
            unlink($image['name']);
            unlink(get_thumb($image['name'])); 
                         
				  } else {
				    
				    // make sure the image isn't still being copied – and thumbnail is created
				    if (file_exists($image_thumbnail)) {
				    		// show image
				    		array_push($displayed_images, $image_title);
				    		echo '<li class="'.basename($image['folder']).'" data-thumb="'.$image_thumbnail.'" data-url="'.$image['name'].'"><a class="del" href="javascript:void(0);">×</a><figure><a href="'.$image['name'].'"><img loading="lazy" src="'.$image_thumbnail.'" width="'.$image_w.'" height="'.$image_h.'" /></a></figure></li>';
				    		echo "\n\t\t\t";
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
			<?
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
	
	<footer>
		<a class="zoom" id="zoomIn" href="javascript:void(0);">+</a>
		<a class="zoom" id="zoomOut" href="javascript:void(0);">-</a>
		<a class="mobile mobileInvert" id="mobileInvert" href="javascript:void(0);">invert</a>
		<div class="mobile mobileUploadWrap">
	  	<a href="javascript:void(0);">upload</a>
	    <input class="mobile mobileUpload" id="mobileUpload" type="file"/>
	  </div>
	  <a class="download" id="download" href="javascript:void(0);">download everything</a>
		<a class="logout" id="logout" href="?logout=1">logout</a>
	</footer>
	
	<? if ($_SESSION['user']): ?>
  <script src="vendor/ui.js"></script>
  <? endif ?>
	
	<? else: ?>
    <main class="login">
			<header>
        <h1>MARK</h1>
				<svg xmlns="http://www.w3.org/2000/svg" width="81.936" height="21.389" viewBox="0 0 81 21.3"><path d="M19.086 0l-8.461 19.123L2.021 0H0v21.348h1.658V3.242l8.124 18.106h1.666l8.002-18.08v18.08h1.658V0zM70.236 10.464L81.422 0h-2.394L68.765 9.695H66.92V0h-1.658v21.348h1.658V11.294h1.845l10.682 10.054h2.488zM57.278 12.003c3.029-.528 3.586-2.384 3.586-5.912C60.864.84 59.294 0 54.261 0h-8.4v21.348h1.658v-9.161h6.742c.453 0 .874-.007 1.265-.022l4.201 9.224h1.842l-4.29-9.386zm-1.307-1.463c-.49.032-1.08.048-1.83.048h-6.622v-8.99h6.622c4.54 0 5.061.462 5.065 4.496-.004 3.35-.435 4.14-2.648 4.393l-.587.053zM34.504.001h-1.766l-8.256 21.347h1.732l1.152-3.026h12.252l1.134 3.026h1.727L34.504 0zm-6.53 16.724l5.623-14.551 5.423 14.55H27.975z"/></svg>	
      </header>
			<form name="login" action="" method="post">
      	<input type="text" name="user" value="" />
        <label for="user">Username</label>
      	<input type="password" name="password" value="" />
        <label for="password">Password</label>
        <input type="submit" name="submit" value="Submit" />
       </form>	
    </main>
	<? endif ?>
</body>