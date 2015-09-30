<?php

if (!file_exists('./../include/config.php')){
	header('Location:install.php');
}
include('./../include/config.php');
include('./../include/functions.php');

if (isset($_POST['step']))
		$step = intval($_POST['step']);
else{
	$step = 0;
}

?>

<!DOCTYPE html>
<html>
  <head>
    <title>SPC - DB Upgrade</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link rel="stylesheet" href="./../css/spc.css" type="text/css" media="screen" />
		<link rel="icon" type="image/png" href="./../favicon.png" />
		<style type="text/css">
			body{
				font-size: 16px;
			}
			h1, h2{
				color: #ff0056;
				margin: 6px;
			}
			h1{
				font-size: 60px;
			}
			dt{
				color: #999;
			}
			dd, dl{
				margin-left: 10px;
			}
			p{
				margin-left: 10px;
				margin-bottom: 10px;
			}
			.code{
				border: 1px solid #ff0056;
				padding: 6px;
			}
		</style>
	</head>
	<body>
		<div id="wrap">
							<h1>Welcome to Simple Photos Contest DataBase Upgrader</h1>
<?php
switch($step){
	case 0: ?>
				<form class="large" method="POST" action="upgrade.php">
					<p><em>This installer will be displayed in english only</em>.</p>
					<p>It's <b>highly recommended</b> to do an SQL backup before upgrading.</p>
					<div class="form_buttons">
						<input type="submit" value="Upgrade" name="submit"/>
						<input type="hidden" name="step" value="1"/>
					</div>
<?php
	break;
	case 1 :?>
	<form class="large" method="POST" action="../index.php">
	<p>Upgrade from <a href="#"><?php
if(!isset($settings->spc_version) or $settings->spc_version=="")
	echo "Unknown(2.0?)";
else
	echo $settings->spc_version;
	?></a> to <a href="#" title="<?php echo SPC_VERSION; ?>"><?php echo SPC_VERSION_DB; ?></a></p>
<?php
if(!isset($settings->spc_version) or $settings->spc_version=="")
	$ver = 0;
else
	$ver = $settings->spc_version;

function	sqlalter($command){
	global $bd;
if (!mysqli_query($bd,"ALTER TABLE ".$command)) {
      die("Error : ". mysqli_error($bd));
  }
}

switch($ver)
{
	case SPC_VERSION_DB:
		echo "	<p>No upgraded needed</p>";
	break;
	case 0:
		sqlalter("`contests` ADD `icon` VARCHAR(200) NOT NULL");
		sqlalter("`settings` ADD `language_auto` BOOLEAN NOT NULL") ;
		sqlalter("`settings` ADD `homepage` BOOLEAN NOT NULL") ;
		sqlalter("`settings` ADD `auth_method` INT(2) NOT NULL , ADD `spc_version` VARCHAR(8) NOT NULL") ;
		sqlalter("`image_ip` CHANGE `ip_add` `ip_add` BIGINT NULL DEFAULT NULL;");
	//case "3.0 A2":
		if (!mysqli_query($bd, "UPDATE `settings` SET `spc_version`='".SPC_VERSION_DB."' WHERE 1")) {
					die("Error : ". mysqli_error($bd));
			}
		echo "	<p>Done!</a></p>";
	break;

	default:
		echo "	<p>Your version were not found.	</p>";
	break;


}

?>

	<div class="form_buttons">
		<input type="submit" value="Home" name="submit"/>
		<input type="hidden" name="step" value="1"/>
	</div>
</form>
	<?php
	break;
}
?>
				</form>
						</div>
		<script>
			var noTiling = true;
		</script>
		<script type="text/javascript" src="./../js/jquery-1.8.2.min.js"></script>
		<script type="text/javascript" src="./../js/jquery.freetile.min.js"></script>
		<script type="text/javascript" src="./../js/contest.js"></script>
	</body>
</html>
