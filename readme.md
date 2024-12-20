![MARK Logotype](https://static.electricgecko.de/mark/MARK-bg.svg)

M A R K is a simple image bookmarking tool. It is tailored to my personal needs and preferences, so chances are it won’t fit your use case or make no sense to you at all.

Version 0.2.8a

## Rationale
- M A R K is a tool for personal image bookmarking. There is no way to share your bookmarks with others, unless you grant them access to your instance. I have been pondering a feature that would enable sharing of selected content, though.
- M A R Ks general idea is to create a singular stream of your aesthetic sensibilities, their disparities, their evolution. Every image saved becomes part of this stream, called *everything*. The now defunctGimmeBar worked in this way, and I enjoyed it greatly.
- Images are added via bookmarklet, which can be conveniently dragged to your bookmark bar from */bookmarklet/*.
- Alternatively, images can be uploaded via drag and drop or via the *upload* button on touch-based devices.
- Images are always sorted by date saved.
- The display size of images can be adjusted by using the **+** and **-** keys. I find this to be a crucial feature.
- Images can be organized into folders. An image can be in one folder. All images also remain part of the *everything* stream.
- The tool consciously does not use a database. Instead, it creates a server-side file structure that is understandable to humans.
	- Original images are copied to your server
	- M A R K folders are file system folders
	- Files are renamed by the date/time they were saved so they keep their order even when used outside of M A R K
	- Thumbnail images are easily identifiable by file name prefix
- There is an <a href="https://www.are.na/malte-muller/m-a-r-k">Are.na channel</a> documenting pleasing visual moments from M A R K instances.

## Alpha version constraints
- You need to host M A R K on your own (LAMP-) server. For now, the installation process involves uploading the package and changing some php files (see *installation*).
- If your server does not have an SSL certificate, M A R K will not be able to save images via secure connections for cross-domain scripting reasons.
- M A R K does not save image sources in any way. In the rare cases this is of interest to me, I use a [reverse image search](https://gist.github.com/electricgecko/44152c19c83d7d1960a9).
- For now, folders have to be created manually, on the server. For the beta version, I'd like to add an UI for this.
- Adding images works well on iOS devices. ~~Moving images requires [Force Touch](https://developer.apple.com/library/content/documentation/AppleApplications/Conceptual/SafariJSProgTopics/RespondingtoForceTouchEventsfromJavaScript.html), which basically means iPhone 6S and iOS 10 or newer.~~ Moving images on mobile devices has been removed and is due for a rewrite.
- The user account system is rudimentary and prehistoric, to say the least. Passwords are stored as plain text, even.
- JS & CSS remain uncompiled, no task runner, no SCSS no nothing. I'd like to support View Source.

## Installation

### Installation to server
1. [Download](https://github.com/electricgecko/MARK/archive/master.zip) this repo.
2. Copy all files within the */dist/* folder to your server. Most likely, you will want to place it in some subfolder of your */htdocs/* directory.
3. Create an empty folder named */imgs/* in the same directory you installed M A R K to.
4. On your server, edit **config_sample.php** and change the value of the very first variable (**$installpath**) to match the sever folder you installed M A R K to.
5. In **config_sample.php**, also change the user account info to your desired login name and password. Yes, in plain text. I am sorry. Feel free to add multiple users like so:

    ```
	$userinfo = array(
  		'Jill' => 'Hveywhb9yAGbVuBu',
  		'John' => 'cYpeuzvKZ7Wxht4U'
	);
    ```

6. Save and close **config_sample.php**, then rename it to **config.php**.

### Setup & configuration
1. In your */imgs/* folder, create subfolders you want M A R K to use, such as **architecture**, **art** or **textures**. Folder names should not contain spaces.
2. Open the */bookmarklet/* subfolder within the path you installed M A R K to in your browser. On this page, drag the bookmarklet to your favourites bar.
3. In **config.php**, you will find some basic configuration options, such as the default thumbnail size and the name of your image folder. You may change these values if you like.
4. Good to go. Happy M A R King!

## Usage
- To add an image, click the M A R K bookmarklet. Saveable images are marked by a yellow frame. Click an image to save it to your collection. If an image cannot be added, try opening it in its own tab by selecting *open image in new tab* from your browser's context menu.
- Within M A R K, **shift click** one or more images to select them. With images selected, click on a folder on the right hand side to add them to this folder. Click in white space to remove selection.
- To sort images into folders on touch-based devices, force touch an image. Select the desired folder from the list. Tap **×** to cancel the operation.
- While hovering an image, click on the **×** in the upper right corner to delete the image from your collection.
- Drag an image file from your desktop into the M A R K browser window to upload it.
- Press **+** and **-** to change thumbnail image size.
- Press **i** to invert the color scheme.
- Click on an image to display it in full resolution.
- Click **download _folder name_** to download a zip of your collection, sorted into folders. The download respects the currently active folder filter.

## Updating
1. To update your M A R K installation, [re-download](https://github.com/electricgecko/MARK/archive/master.zip) this repo.
2. Replace the following files and folders on your server:

    ```
    index.php
    mark.php
    bookmarklet.js
    /vendor
    /bookmarklet
    ```

3. If you prefer individual files, please refer to the commit notes to identify updated components.

## Screenshot

<img src="https://d2w9rnfcy7mm78.cloudfront.net/23278587/original_96cd13ec3ca67cd6f3c766515914f17e.png" alt="M A R K running on a desktop computer" width="900"/>
