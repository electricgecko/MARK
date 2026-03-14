<?php 
    // full URL M A R K is installed to
    $installdir = 'https://yourserver.com/yourfolder';
        
    // user accounts
    // enter your passwords in plain text – they will be automatically hashed after first login
    $userinfo = array(
        'Foo' => 'PASSWORD'
    );
    
    // hash passwords upon first use
    $configFile = __FILE__;
    $config = file_get_contents($configFile);
    $updated = false;
    
    foreach ($userinfo as $user => $password) {
      // If the password is not already a bcrypt/argon hash
      if (!preg_match('/^\$2y\$|\$argon2/', $password)) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
            
        // Replace plaintext password in config file
        $config = str_replace("'$password'", "'$hash'", $config);
        $userinfo[$user] = $hash;
        $updated = true;
      }
    }
    
    if ($updated) {
        file_put_contents($configFile, $config);
    }
    
    // ----------------------------------------------------------------------
        
    // name of folder images are stored in
    $imgdir = 'imgs';
        
    // exploder character for image names
    $exp = '-';
        
    // character used to replace occurrences of $exp in original filename
    $rep_exp = '_';
        
    // thumbnail filename prefix
    $thumb_indicator = 'MARKthumb';
        
    // default thumbnail size
    $thumb_width = 400;

    // base name of download zip file, without file extension
    $zip_name = 'MARK';
?>