<?php
include('../include/config.php');
include('../include/functions.php');

/** Prevent unauthorized access to admin panel. */
if (!$admin_logged){
	header('HTTP/1.0 403 Forbidden');
	die('Nice tried, but your are not logged in.');
}

if (isset($_GET['action'])){
  $action = htmlspecialchars($_GET['action']);
  }
else {
  $action = "";
}

function page_header($tab, $sub = null, $message = null){?>
<!DOCTYPE html>
<html lang="en-En">
  <head>
    <title><?php echo _('Admin panel'); ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link rel="stylesheet" href="spc.css" type="text/css" />
		<link rel="icon" type="image/png" href="../favicon.png" />
	</head>
	<body>
		<div id="content">
		<div id="header">
			<a href="../admin.php" title="<?php echo _('Back'); ?>"><span class="fa fa-arrow-circle-o-left"></span></a>
			<a href="../admin.php"><?php echo _('Admin panel').' / '._($tab); ?></a>
			<?php
			if (!empty($sub)){
				?> / <span id="sub_title"><?php echo $sub;
			}
			?></span>
		</div>
    <?php echo $message; ?>
			<div id="admin_wrap">
				<!--<ul id="tab_list">
					<li><a href="?tab=contests"><?php echo _('Contests'); ?></a></li>
					<li><a href="?tab=settings"><?php echo _('Settings'); ?></a></li>
				</ul>-->
      <?php }

switch($action){
    case "":
      page_header('Theme');
      ?>
<style>
td{
	padding: 5px;
}
td input{
	width: 300px;
}
td input[type='color']{
	width: 100px;
}
</style>
<form action="theme.php?action=build" method="post">
<table>
	<tr><td>backgroundColor	</td><td>		<input type="color" name="backgroundColor"	value="#000000"></td></tr>
	<tr><td>boxBackgroundColor</td><td>		<input type="color" name="boxBackgroundColor"	value="#303030"></td></tr>
	<tr><td>textColor				</td><td>		<input type="color" name="textColor"				value="#666666"></td></tr>
	<tr><td>captionColor		</td><td>		<input type="color" name="captionColor"			value="#999999"></td></tr>
	<tr><td>linkColor				</td><td>		<input type="color" name="linkColor"				value="#FFFFFF"></td></tr>
	<tr><td>loveColor				</td><td>		<input type="color" name="loveColor"				value="#ff0056"></td></tr>
	<tr><td>errorColor			</td><td>		<input type="color" name="errorColor"				value="#333333"></td></tr>
	<tr><td>formInputColor	</td><td>		<input type="color" name="formInputColor"		value="#CCCCCC"></td></tr>
	<tr><td>wrapWidth				</td><td>		<input type="range" name="wrapWidth" min="0" max="100" value="95" onchange="updateTextInput(this.value);"></td><td id="textInput"></td></tr>
</table>
<input type="submit" value="Submit">
</form>
<script type="text/javascript">
function updateTextInput(val) {
  document.getElementById('textInput').innerHTML=val+"%";
}
updateTextInput(95);
</script>
<img src="example1.png" alt="Example image" style="border: 2px solid;">
      <?php
    break;

    case "build":
      page_header('Theme');
if (isset($_POST['backgroundColor']) && isset($_POST['boxBackgroundColor']) && isset($_POST['textColor']) && isset($_POST['captionColor']) && isset($_POST['linkColor']) && isset($_POST['loveColor']) && isset($_POST['errorColor']) && isset($_POST['formInputColor']) && isset($_POST['wrapWidth'])){
	$myfile = fopen("less/variables.less", "w") or die("Unable to open variable file!<br>less/variables.less");
	$txt = "// SPC LESS/CSS variables\n// You can modify values here, then compile `spc.less` to CSS file.\n\n// Colors\n\n// Background color\n@backgroundColor: ".$_POST["backgroundColor"].";\n// Background color of boxes\n@boxBackgroundColor: ".$_POST["boxBackgroundColor"].";\n// Default text color\n@textColor: ".$_POST["textColor"].";\n// Color of text in photos captions\n@captionColor: ".$_POST["captionColor"].";\n// Color of links\n@linkColor: ".$_POST["linkColor"].";\n@linkHoverColor:  darken(@linkColor, 10%);\n// Colored color\n@loveColor: ".$_POST["loveColor"].";\n@loveColorHover: @linkColor;\n\n@successColor: @linkColor;\n@successBackgroundColor: @loveColor;\n\n@errorColor: ".$_POST["errorColor"].";\n@errorBackgroundColor: @captionColor;\n\n@formInputColor: ".$_POST["formInputColor"].";\n\n// Font sizes\n\n// Global font size\n@fontSize: 1em;\n// Small font size\n@subFontSize: 0.8em;\n// Headers font size\n@headerFontSize: 3em;\n// Headers font weight (default : 300 - thin)\n@headerFontWeight: 300;\n\n// Font type\n@fontFamily: \"Segoe UI\",\"Helvetica\",\"Verdana\",\"Arial\",sans-serif;\n\n// Width of site container (default : 95% of screen width)\n@wrapWidth: ".$_POST["wrapWidth"]."%;\n\n// Alerts\n@alertFontSize: 1.4em;\n";
	fwrite($myfile, $txt);
fclose($myfile);
require_once '../include/less.php/Less.php';
$options = array( 'compress'=>true );
$parser = new Less_Parser($options);
$parser->parseFile( 'less/spc.less' );
$css = $parser->getCss();
$myfile = fopen("spc.css", "w") or die("Unable to open variable file!<br>spc.css");
fwrite($myfile, $css);
fclose($myfile);
echo "Theme file updated";
	}
    break;



}

 ?>
</div>
<div class="push"></div>
</div>
<div id="footer">
  <a href="https://github.com/Dric/simple-photos-contest"><span class="fa fa-github githubIcon"></span></a> <a href="../about.php" class=""><span class="colored">S</span>imple <span class="colored">P</span>hotos <span class="colored">C</span>ontest</a> <span class="colored"><?php echo SPC_VERSION; ?></span> by <a href="author.php"><span class="colored">Contributors</span></a>.
</div>
<script>
var noTiling = true;
</script>
<script type="text/javascript" src="../js/jquery-1.11.2.min.js"></script>
<script type="text/javascript" src="../js/lightbox.min.js"></script>
<script type="text/javascript" src="../js/jqBarGraph.1.1.min.js"></script>
<script type="text/javascript" src="../js/jquery.tinyscrollbar.min.js"></script>
<script type="text/javascript" src="../js/contest.js"></script>
<script type="text/javascript" src="../js/admin.js"></script>
</body>
</html>
