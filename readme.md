![alt tag](http://static.electricgecko.de/mark/mark.svg)

M A R K is a simple image bookmarking tool. I wrote it as an replacement for the now sadly dysfunctional [GimmeBar](http://gimmebar.com). It is tailored to my personal needs and preferences, so chances are it won’t fit your use case or make no sense to you at all.

Version 0.1.0 (Alpha)

## Rationale
- M A R K is a tool for personal bookmarking. There is no way to share your bookmarks with others, unless you grant them full access rights.
- M A R Ks general idea is to create a singular stream of your aesthetic sensibilities, their disparities, their evolution. Every image saved becomes part of this stream, called *everything*. GimmeBar worked in this way, and I enjoyed it greatly.
- Images are added via bookmarklet, which can be conveniently dragged to your bookmarks bar from */bookmarklet/*.
- The display size of images can be adjusted by using the **+** and **-** keys. I find this to be a crucial feature.
- Images can be organized into folders. An image can be in one folder. All images also remain part of the *everything* stream.
- The tool consciously does not use a database. Instead, it creates a server-side file structure that is understandable to humans.
	- Original images are copied to your server
	- M A R K folders are file system folders
	- Files are renamed by the date/time they were saved so they keep their order even when used outside of M A R K
	- Thumbnail images are easily identifiable by file name prefix

## Alpha version constraints
- You need to host M A R K on your own (LAMP-) server. For now, the installation process involves uploading the package and changing some php files (see *installation*).
- If your server does not have an SSL certificate, M A R K will not be able to save images via secure connections for cross-domain scripting reasons.
- M A R K does not save image sources in any way. In the rare cases this is of interest to me, I use a [reverse image search](https://gist.github.com/electricgecko/44152c19c83d7d1960a9).
- For now, folders have to be created manually, on the server. For the beta version, I'd like to add an UI for this.
- Adding images via bookmarklet works well on iOS devices. Moving images to folders or zooming the view does not.
- The user account system is rudimentary and prehistoric, to say the least. Passwords are stored as plain text, even.
- JS & CSS remain uncompiled, no task runner, no SCSS no nothing.

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
  		'Jack' => 'cYpeuzvKZ7Wxht4U'
	);
    ```

6. Save and close **config_sample.php**, then rename it to **config.php**.

### Setup & configuration
1. In your */imgs/* folder, create subfolders you want M A R K to use, such as **architecture**, **art** or **textures**. Folder names should not contain spaces.
2. Visit */bookmarklet/* subfolder within the path you installed M A R K to. On this page, drag the bookmarklet to your favourites bar.
3. In **config.php**, you will find some basic configuration options, such as the default thumbnail size and the name of your image folder. You may change these values if you like.
4. Good to go. Happy M A R King!

## Usage
- To add an image, click the M A R K bookmarklet. Saveable images are marked by a yellow frame. Click an image to save it to your collection. If an image cannot be added, try opening it in its own tab by selecting *open image in new tab* from your browser's context menu.
- Within M A R K, **shift click** one or more images to select them. With images selected, click on a folder on the right hand side to add them to this folder. Click in white space to remove selection.
- While hovering an image, click on the **×** in the upper right corner to delete the image from your collection.
- Press **+** and **-** to change thumbnail image size.
- Click on an image to display it in full resolution.
