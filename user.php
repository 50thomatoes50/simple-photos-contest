<?php
if (!file_exists('include/config.php')){
	header('Location:install/install.php');
}
/*if(file_exists('include/config.php') and file_exists('install/install.php'))
	die("<h1>Remove 'install/install.php'</h1>")*/
include('include/config.php');
include('include/functions.php');
require 'include/steamauth/steamauth.php';

?>
<!DOCTYPE html>
<html lang="<?php echo getenv ( "LC_ALL" ) ?>">
  <head>
    <title>User</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link rel="stylesheet" href="css/spc.css" type="text/css" />
		<link rel="icon" type="image/png" href="favicon.png" />
	  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
	  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	  <!--[if lt IE 9]>
	  <script src="js/html5shiv.js"></script>
	  <script src="js/respond.min.js"></script>
	  <![endif]-->
	</head>
	<body>
		<div id="settings-button">
			<?php
			if ($admin_logged){
				?>
			<a href="admin.php" title="<?php echo _('settings'); ?>"><span class="fa fa-cog" title="<?php echo _('settings'); ?>"></span></a>
				<?php
			}
			if(!isset($_SESSION['steamid'])) {
        header('Location:index.php');
        die("User not logged");
					//echo "<div class='steamlogin'>".steamlogin()."</div>"; //login button
			}  else {
					include ('include/steamauth/userInfo.php');
					//Protected content
					echo "<a class='steamlogin' href='user.php' title='Steam user'><i class='fa fa-steam'></i><img src='".$steamprofile['avatar']."' title='' alt='' />" . $steamprofile['personaname'] ."</a><a href='logout.php' title='Logout'><i class='fa fa-sign-out'> </i></a>";
			}
      ?>
    </div>
<div id="header"><a tiptitle="Back" href="index.php"><span class="fa fa-arrow-circle-o-left"></span></a><a href="">User profile of <span class="header-contest"><?php echo "<img src='".$steamprofile['avatarmedium']."' title='' alt='' />" .$steamprofile['personaname']; ?></span></a></div>
<div id="contest_table" class="table">					<ul class="item_wrap active">
						<li class="item_title"><a tiptitle="Account Info" href=""><i class="fa fa-info-circle"></i> Account Info</a><br></li>
            <br>
              <table border='1'>
                <tr><td>Steam ID</td><td><?php echo $steamprofile['steamid']; ?></td></tr>
                <tr><td>Steam Profile</td><td><?php
if($steamprofile['profilestate']==1)
{
  if($steamprofile['communityvisibilitystate']==3)
  {
    echo "<a href='".$steamprofile['profileurl']."'>".$steamprofile['profileurl']."</a>" ;
  }
  else{echo "Account not visible";}
}
else{echo "No Steam Community profile";}
?></td></tr>
<tr><td>Status</td><td><?php
switch($steamprofile['personastate']){
  case 0:
    echo "Offline";
  break;
  case 1:
    echo "Online";
  break;
  case 2:
    echo "Busy";
  break;
  case 3:
    echo "Away";
  break;
  case 4:
    echo "Snooze";
  break;
  case 5:
    echo "looking to trade";
  break;
  case 6:
    echo "looking to play";
  break;
  default:
    echo "Status error";
  break;
}
?></td></tr>
              </table>
					</ul>
					<ul class="item_wrap active">
						<li class="item_title"><a tiptitle="Vote" href=".?contest=test"><i class="fa fa-heart"></i> Vote</a></li>
<table border='1'>
<?php
$contests = array();
$imgs = array();
$vote= array();
$ip = array();
$sql=mysqli_query($bd, "SELECT `contest`,`contest_name` FROM contests");
while($row=mysqli_fetch_array($sql)){
	$contests[$row['contest']] = $row['contest_name'];
}
$sql=mysqli_query($bd, "SELECT `img_id`,`img_name` FROM images");
while($row=mysqli_fetch_array($sql)){
	$imgs[$row['img_id']] = $row['img_name'];
}
  //$ip_sql=mysqli_query($bd, "select ip_add from image_IP where img_id_fk=$id and ip_add='$ip'");
$ip_sql=mysqli_query($bd, "SELECT img_id_fk,contest FROM image_IP WHERE ip_add='".$steamprofile['steamid']."'");
while($row=mysqli_fetch_array($ip_sql)){
	$ip[$row['contest']] = $row['img_id_fk'];
}
foreach($contests as $key=> $contest){
	if(!isset($ip[$key]))
		echo "<tr><td><a href='index.php?contest=".$key."'>".$contest."</a></td><td>no vote</td></tr>";
	else {
		echo "<tr><td><a href='index.php?contest=".$key."'>".$contest."</a></td><td>voted : ".$imgs[$ip[$key]]."</td></tr>";
	}
}

?>
</table>
					</ul>
				</div>
<?php
include "html/footer.html";
exit();?>
