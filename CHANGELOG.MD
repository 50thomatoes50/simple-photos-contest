Simple Photos Contest
==========

ChangeLog
--------

* 3.0 Alpha 2
  - Theme (Change color of variable.less and recompile it in the admin panel)
  - Move included file to a secure folder
  - updated AwesomeFont to 4.4
  - LogOut bouton for admin page
  - SteamAuthentication added
  - fixed an exploit (can vote whenever a contest is expired)
  - Installer can upgrade the db when upgrading SPC
  - Automatic language selector (not tested)

* 3.0 Alpha 1
  - You can add icon to a gallery
  - Added a proper main page
  - Added some button to go back to main page

* 2.0
	- Major rewrite : frontend now use HTML5 and CSS3
	- Used of LESS files to build CSS file
	- Use fingerprinting method instead of IP address to detect if a visitor has already voted.
	- ZebraDatePicker replaced by glDatePicker
* 1.3
	- `mysql_*` obsolete php functions replaced by `mysqli_*` functions.
	- When `config.php` file is not found, user will be redirected to install script.
	- Link to admin panel in breadcrumb
	- When contests are saved in Db but no default contest is set, SPC will display the first contest saved in Db
	- Better SQL error check
	- A few bugs fixed
	- Updated jQuery to v1.11.2
	- Updated freeTile to v0.3.1
	- Custom Slimbox2 replaced by LightBox
* 1.2
  - Use of SimpleImage class instead of TimThumb
  - Add a setting to allow voting once in a contest or unlimited votes (you are still not allowed to vote more than once for a photo)
