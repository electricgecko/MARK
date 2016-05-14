<?php
	session_start();
	
	require_once('config.php');
	
	if(isset($_GET['logout'])) {
	    $_SESSION['user'] = '';
	    header('Location:  http://' . $_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']));
	}
	
	if (isset($_POST['user'])) {
	    if($userinfo[$_POST['user']] == $_POST['password']) {
	        $_SESSION['user'] = $_POST['user'];
	    }else {
	       echo 'invalid login';
	    }
	}
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
		
	<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
	<script src="vendor/isotope.min.js"></script>
	<script type="text/javascript">
		
		$(window).load(function(){	
			marked = $('main ul').isotope({
				itemSelector: 'main ul li',
				masonry: {
					gutter: 40
				}
			});	
		});

		$(document).ready(function(){
			
			imgdir = 'imgs/';
			images = $('main ul li');
			hidden = null;
			thumbBreakpoint = 600;
			activeFilter = '*';
		
			// hide sidebar
			$('aside').hide();
			$('aside #done').hide();
			
			// focus login form
			$('form input').first().focus();
			
			// get image size from local storage
			if (localStorage.getItem('MARKsz') === null) {
				// set image size to default value
				sz = parseInt(images.css('width'));
			} else {
				// set image size to stored value
				sz = localStorage.getItem('MARKsz');
					images.css('width',localStorage.getItem('MARKsz')+'px');
			}
			
			// dodgy, magic-number method to load full-sized images if thumbnail size is big
			if (sz >= thumbBreakpoint) {
				images.each(function(){
					$(this).find('figure a img').attr('src',$(this).data('url'));
				});
			}
			
			// keyboard controls to adjust image size
			$(window).keydown(function(evt) {	
			  
			  	var colwidth = parseInt(images.css('width'));
			  	var mult = .3; // image resize multiplier for each keypress
			  
			  	// plus
			    if (evt.keyCode === 187) {
					images.css('width', colwidth+colwidth*mult+'px');

					if (colwidth+colwidth*mult >= thumbBreakpoint) {
						images.each(function(){
							$(this).find('figure a img').attr('src',$(this).data('url'));
							console.log('big images');
						});						
					}

					marked.isotope('layout');
					localStorage.setItem('MARKsz',colwidth+colwidth*mult);
			
				// minus
			    } else if (evt.keyCode === 189) {
					images.css('width', colwidth-colwidth*mult+'px');

					if (colwidth-colwidth*mult < thumbBreakpoint) {
						images.each(function(){
							$(this).find('figure a img').attr('src',$(this).data('thumb'));
							console.log('small image');
						});						
					}
					
					marked.isotope('layout');
					localStorage.setItem('MARKsz', colwidth-colwidth*mult);
			    }
			});

			// remove selections by clicking in white space
			$(document).click(function(e){
				
				if (!$(e.target).closest('li').length) {
					
					// remove selected
					if ($('li.selected').length) {
						$('li.selected').removeClass('selected');
						$('main').css({
							'max-width': 'none',
							'margin': '60px auto',
						});
						
						$('header > nav').show();
						
						marked.isotope('layout');
						$('aside').hide();
					}

				} else {
					
					el = $(e.target).closest('li');

                    // shift-click images to mark them
					if (e.shiftKey) {
								
						e.preventDefault();
						
						// mark clicked image as selected
						el.toggleClass('selected');
						
						// if at least one image is selected, show sidebar
						if ($('li.selected').length) {
							
							// adjust main container width to sidebar width
							$('main').css({
								'max-width': $(window).width()-$('aside').outerWidth(),
								'margin': '60px 0 60px 0'
							});
							
							$('header > nav').hide();
							
							// add leading 0
							n = $('li.selected').length;
							if ((n < 10)) {
								n = '0'+n
							}
							
							$('aside p > span').text(n);
							
							marked.isotope('layout');
							$('aside').show();
							
						} else {
						
						// reset main container width
						$('main').css({
								'max-width': 'none',
								'margin': '60px auto',
							});
							
							$('header > nav').show();
							
							marked.isotope('layout');
							$('aside').hide();
							
						}
					}
				}
			});
			
			
			// move images to folders, remove images from folders
			$('aside ol li').each(function(){
				
				$(this).click(function(e){
					
					e.stopPropagation();
										
					// get destination folder name
					var folder = $(this).text();
					
					if (folder == $('aside ol li:first-child').text()) {
						folder = '';
					}
							
					// get image url
					var sel = $('main ul li.selected figure a img');
			
					// pass to move helper
					sel.each(function(){
						
						el = $(this);
						
						var thumb = el.attr('src');
						var file = el.parent().attr('href');;
						
						function updateFiles() {
							var findex = el.attr('src').lastIndexOf('/') + 1;
							
							// set correct urls for image and thumb
							el.attr('src', el.data('url'));
							el.parent().attr('href', el.data('thumb'));
							
							// keep selection
							if (el.closest('li').hasClass('selected')) { var sel = true; }
							el.closest('li').removeClass().addClass(folder);
							if (sel) {el.closest('li').addClass('selected')};
							
							marked.isotope({filter: activeFilter});
						}		
						
				
						$.post('mark.php', {a: 'move', f: file, t: thumb, d: folder}).done(function(){
							updateFiles(folder);
						});	
					});
					
					// fltr = '.'+activeFilter;
					
					$('aside #done').fadeIn().fadeOut();
				});
			})
			
	
			// filter images by folder
			$('nav ol li').click(function(){
						
				if (!$(this).is('nav ol li:first')) {
					activeFilter = '.'+$(this).text();			
				} else {
					activeFilter = '*';	
				}
				
				marked.isotope({filter: activeFilter});
				
			});
			
			// delete images
			$('a.del').each(function(){
				$(this).click(function(){

                    var thumb = $(this).next().find('img').attr('src');
					var url = $(this).next().find('a').attr('href');

					// pass to delete helper
					$.post('mark.php', {a: 'del', f: url, t: thumb});
										
					// remove image from view & rearrange layout
					marked.isotope('remove', $(this).parent()).isotope('layout');	
				})
			});
		});
	</script>
	
	<style>
		
		* {
			font-weight: normal;
			font-size: 100%;
		}
		
		body {
			font-family: 'Eurostile', 'EurostileTEE-Regu', 'Arial', sans-serif;
			font-weight: normal;
			font-size: 9pt;
			letter-spacing: 1px;
			-webkit-text-size-adjust: none
			
		}
		
		a {
			color: #000;
			text-decoration: none;
		}
		
		ul, ol {
			list-style: none;
			padding: 0;
			margin: 0;
		}
		
		header {
			position: fixed;
			top: 0;
			left: 0;
			
			line-height: 20px;
			padding: 5px 10px;
			
			z-index: 10;
		}

		nav {
			display: inline;
			margin-left: 100px;
		}
		
		nav ol {
			display: inline;
		}
		
		nav ol li {
			display: inline;
			margin-right: 20px;
			cursor: pointer;
		}
		
		nav ol li:hover {
			text-decoration: underline;
		}
		
		h1 {
			display: inline-block;
			letter-spacing: 3px;
			padding: 0;
			margin: 0;
			min-width: 40px;
			height: .7em;
			line-height: .8em;
			
			text-indent: -9999px;
			background: url('mark.svg') no-repeat;
			background-size: contain;
		}

		h1 a {
			display: block;
			height: .7em;
			min-width: 40px;
		}
				
		main {
			margin: 60px auto;
		}
		
		main ul li {
			width: 250px;
			margin-bottom: 40px;
			
			font-size: 9px;
			font-family: 'Arial', sans-serif;
			letter-spacing: 1px;
		}
		
		main ul li a.del {
			display: none;
			
			font-size: 160%;
			text-decoration: none;
			color: #000;
			line-height: 20px;
			text-align: center;
			
			position: absolute;
			width: 20px;
			height: 20px;
			z-index: 10;
			top: 0;
			right: 0;
		}
		
		main ul li a.del:hover {
			background: #fff;
		}
		
		main ul li:hover a.del {
			display: block;
		}
		
		figure {
			margin: 0;
			padding: 0;
			
		}
	
		main ul li:hover figure > a:after {
			content: ' ';
			display: block;
			position: absolute;
			width: 100%;
			height: 100%;
			top: 0;
			background: rgba(255, 220, 50, 0.5);
		}
		
		main ul li.selected figure > a:after {
			content: ' ';
			display: block;
			position: absolute;
			width: 100%;
			height: 100%;
			top: 0;
		
			background: rgba(0, 190, 255, 0.5);	
		}
		
		figure span {
			display: block;
		}
		
		img {
			display: block;
			width: 100%;
			height: auto;
		}	

		aside {
			position: fixed;
			
			top: 0;
			right: 0;
			min-height: 100%;
			width: 200px;
			
			line-height: 20px;
			padding: 5px 10px;
			
			background: #fff;
			
			z-index: 15;
		}
		
		aside p {
			padding: 0;
			margin: 0 0 1em 0;
			width: 100px;
		}
		
		aside p span {
			font-size: 90%;
		}

		aside ol li {
			cursor: pointer;
			margin-bottom: 1em;
		}
		
		aside ol li:hover {
			text-decoration: underline;
		}
		
		a.logout {
    		display: block;
    		position: fixed;
    		right: 10px;
    		bottom: 10px;
		}
		
		a.logout:hover {
    		text-decoration: underline;
		}
		
		main.login {
    		padding-top: 15%;
    		margin-top: 0;
		}
		
		form {
    		width: 40%;
    		margin: 0 auto;
		}
		
        form label {
            margin-bottom: 1em;
            display: block;

        }
		
		form input[type=text],
		form input[type=password] {
    		-webkit-appearance:none;
    		outline: none;
    		background: none;
    		
    		font-size: 400%;
    		font-weight: normal;
    		font-family: 'Eurostile', 'EurostileTEE-Regu', 'Arial', sans-serif;
    		
    		width: 100%;
    		
    		display: block;
    		    		
    		border: 0;
    		border-bottom: 1px solid;
		}
		
		form input[type=submit] {
    		-webkit-appearance:none;
    		outline: none;
    		background: none;
    		
    		padding: 0;
    		border: 0;
    		cursor: pointer;
    		
    		font-size: 100%;
    		            
            margin-top: 5em;
		}
		
		form input[type=submit]:hover {
    		text-decoration: underline;

		}
		
		.login > h1 {
           position: absolute;
           top: 10px;
           left: 10px;
		}

        @media only screen 
        and (min-device-width : 768px) 
        and (max-device-width : 1024px) {
            main ul li {
                width: 180px;
            }
        }
		
        @media only screen 
        and (min-device-width : 320px) 
        and (max-device-width : 568px)  {
            
            h1 {
                position: fixed;
                display: block;
                float: left;
                padding-top: 30px;
                background-position: center center;
                min-width: 50px;
                margin-right: 30px;
            }
            
            header {
                width: 100%;
            }
            
            nav {
                display: block;
                
                margin-left: 60px;
                padding: 10px;
                width: auto;

                overflow-x: scroll;
                overflow-y: hidden;
            }
            
            main {
                margin-top: 100px;
                width: 300px;  
            
            }

            main ul li {
                width: 300px;
            }
            
            .login form {
	            width: 100%;
            }
            
			form input[type=text],
			form input[type=password] {
	            border-radius: 0;
	            padding: 0;
	            width: 100%;
            }
            
            .login > h1 {
	            top: 0;
            }

            
        }
		
	</style>
</head>

<body>


    <?php if ($_SESSION['user']): ?>
        
	<?php
		$folders = array_filter(glob('imgs/*', GLOB_NOCHECK), 'is_dir'); // read folders in main image folder
		$images = array();	
		$c = 0;
		
		function remove_thumbs($arr) {
			global $thumb_indicator;
			$thumbs = array_filter($arr, function($var) use ($thumb_indicator) { return preg_match("/\b$thumb_indicator\b/i", $var); });
			$arr = array_diff($arr, $thumbs);
			return $arr;	
		}
		
		// go.
		
		// read main image folder	
		$main_content = glob($imgdir.'/*.{jpg,jpeg,gif,png}', GLOB_BRACE);
		
		// remove thumbnails
		$main_content = remove_thumbs($main_content);
		
		// add to image object
		foreach ($main_content as $image) {
			$images[$c]['name'] = $image;
			$images[$c]['folder'] = 'imgs';
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
		<h1><a href="<?php echo $installpath ?>">MARK</a></h1>
		<nav>
			<ol>
				<li>everything</li><?php
					if (count($folders) > 0) {				
						foreach ($folders as $folder) {
							echo '<li><span>'.basename($folder).'</span></li>';
						}
					}
				?>
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
						$img_info = explode($exp, basename($image['name']));
						$image_date = $img_info[0];
						$image_w = $img_info[1];
						$image_h = $img_info[2];	
						$image_title = $img_info[3];
						$image_thumbnail = substr_replace($image['name'], $exp.$thumb_indicator, strpos($image['name'],$exp), 0);
						
						// self-cleaning: if image is a duplicate, don't show it and delete it from server
						if (in_array($image_title, $displayed_images)) {
						unlink($image);
						
						} else {
							
							// show image
							array_push($displayed_images, $image_title);
							echo '<li id="'.$index.'" class="'.basename($image['folder']).'" data-thumb="'.$image_thumbnail.'" data-url="'.$image[name].'"><a class="del" href="javascript:void(0);">×</a><figure><a href="'.$image['name'].'"><img width="'.$image_w.'" height="'.$image_h.'" src="'.$image_thumbnail.'" /></a></figure></li>';
							$index++;
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
		<span id="done" class="done">✔</span>
	</aside>
	
	<a class="logout" id="logout" href="?logout=1">logout</a>
	
	
	<?php else: ?>
	    <main class="login">
	        <h1><a href="<?php echo $installpath ?>">MARK</a></h1>
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