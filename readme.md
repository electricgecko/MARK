![alt tag](https://cdn.rawgit.com/electricgecko/mark/master/dist/mark.svg)

M A R K is a simple image bookmarking tool. I wrote it as an replacement for the now sadly dysfunctional [GimmeBar](http://gimmebar.com). It is tailored to my personal needs and preferences, so chances are it won’t fit your use case or make no sense to you at all.

## Rationale
- M A R K is a tool for personal bookmarking. There is no way to share your bookmarks with others, unless you grant them full access rights.
- M A R Ks general idea is to create a singular stream of your aesthetic sensibilities, their disparities, their evolution. Every image saved becomes part of this stream, called *everything*. GimmeBar worked in this way, and I enjoyed it greatly.
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
- For now, folders have to be created manually, on the server. For the beta version, I'd like to add an UI for this.
- The user account system is rudimentary and prehistoric, to say the least.
- JS & CSS remain uncompiled, no SCSS no nothing.

## Installation
TBC
