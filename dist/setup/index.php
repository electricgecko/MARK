<?php
	$configPath = __DIR__.'/../config.php';
	$alreadyConfigured = file_exists($configPath);

	$errors = array();
	$success = false;
	$installdir = '';
	$username = '';

	if (!$alreadyConfigured) {
		$secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
		$path = rtrim(dirname(dirname($_SERVER['SCRIPT_NAME'])), '/');
		$installdir = ($secure ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST'].$path;

		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			$installdir = rtrim(trim($_POST['installdir'] ?? ''), '/');
			$username = trim($_POST['username'] ?? '');
			$password = $_POST['password'] ?? '';

			if ($installdir === '' || !filter_var($installdir, FILTER_VALIDATE_URL)) {
				$errors[] = 'Please enter a valid install URL.';
			}
			if (empty($username)) {
				$errors[] = 'Please enter a username.';
			}
			if ($password === '') {
				$errors[] = 'Please enter a password.';
			}

			if (!$errors) {
				$templatePath = __DIR__.'/../config_sample.php';
				$template = file_get_contents($templatePath);

				$installdirPlaceholder = "'https://yourserver.com/yourfolder'";
				$userinfoPlaceholder = "'Foo' => 'PASSWORD'";

				if ($template === false || strpos($template, $installdirPlaceholder) === false || strpos($template, $userinfoPlaceholder) === false) {
					$errors[] = 'config_sample.php could not be read or has already been modified. Please install M A R K manually, see readme.md.';
				} else {
					$config = str_replace(
						array($installdirPlaceholder, $userinfoPlaceholder),
						array(var_export($installdir, true), var_export($username, true).' => '.var_export($password, true)),
						$template
					);

					if (@file_put_contents($configPath, $config) === false) {
						$errors[] = 'Could not write config.php. Validate your directory is writable by the web server.';
					} else {
						$imgsPath = __DIR__.'/../imgs';
						if (is_dir($imgsPath) || @mkdir($imgsPath, 0755)) {
							$success = true;
						} else {
							$errors[] = 'config.php was created, but the imgs folder could not be created. Please create it manually. If you would like to start over, please delete config.php first.';
						}
					}
				}
			}
		}
	}
?>

<!DOCTYPE html>
<head>
	<meta charset="UTF-8"/>
	<meta name="robots" content="noindex, nofollow">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

	<title>M A R K — Setup</title>

	<link rel="stylesheet" href="../vendor/mark.css?04">
</head>

<body>
	<main class="login">
		<header>
			<h1>MARK</h1>
			<svg xmlns="http://www.w3.org/2000/svg" width="81.936" height="21.389" viewBox="0 0 81 21.3"><path d="M19.086 0l-8.461 19.123L2.021 0H0v21.348h1.658V3.242l8.124 18.106h1.666l8.002-18.08v18.08h1.658V0zM70.236 10.464L81.422 0h-2.394L68.765 9.695H66.92V0h-1.658v21.348h1.658V11.294h1.845l10.682 10.054h2.488zM57.278 12.003c3.029-.528 3.586-2.384 3.586-5.912C60.864.84 59.294 0 54.261 0h-8.4v21.348h1.658v-9.161h6.742c.453 0 .874-.007 1.265-.022l4.201 9.224h1.842l-4.29-9.386zm-1.307-1.463c-.49.032-1.08.048-1.83.048h-6.622v-8.99h6.622c4.54 0 5.061.462 5.065 4.496-.004 3.35-.435 4.14-2.648 4.393l-.587.053zM34.504.001h-1.766l-8.256 21.347h1.732l1.152-3.026h12.252l1.134 3.026h1.727L34.504 0zm-6.53 16.724l5.623-14.551 5.423 14.55H27.975z"/></svg>
		</header>

<?php if ($alreadyConfigured): ?>
		<p>M A R K is already set up on this server.</p>
		<p><a class="underline" href="../">View your images</a></p>
<?php elseif ($success): ?>
		<p>Setup complete<br/>Login saved to config.php<br/>Images folder created</p>
		<p><a class="underline" href="../">Start using M A R K</a></p>
<?php else: ?>
<?php foreach ($errors as $error): ?>
		<p><?php echo htmlspecialchars($error) ?></p>
<?php endforeach ?>
		<form name="setup" action="" method="post">
			<input type="text" name="installdir" value="<?php echo htmlspecialchars($installdir) ?>" />
			<label for="installdir">Installation URL</label>
			<input type="text" name="username" value="<?php echo htmlspecialchars($username) ?>" />
			<label for="username">Username</label>
			<input type="password" name="password" value="" />
			<label for="password">Password</label>
			<input type="submit" name="submit" value="Setup M A R K" />
		</form>
<?php endif ?>
	</main>
</body>
