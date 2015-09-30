<?php
include("include/config.php");
$sql=mysqli_query($bd, "SELECT * FROM settings");
$settings = mysqli_fetch_object($sql);
unset($sql);

if(isset($_POST['id']) and !empty($_POST['id'])){
	$id= intval($_POST['id']);
	$contest= mysqli_real_escape_string($bd,htmlspecialchars($_POST['contest']));
	switch($settings->auth_method){
		case 0:
			$ip = mysqli_real_escape_string($bd,$_POST['fingerprint']);
		break;
		case 1:
			session_start();
			if(!isset($_SESSION['steamid'])) {
				die("User not logged ");
			}  else {
			include ('include/steamauth/userInfo.php');
			$ip = $steamprofile['steamid'];
		}

		break;
	}
  $ret = mysqli_query($bd, "select * from contests where contest = '$contest'");
  if ($ret !== null){
    $contest_settings = mysqli_fetch_object($ret);
		list($byear, $bmonth, $bday) = explode('-', $contest_settings->date_begin);
		list($eyear, $emonth, $eday) = explode('-', $contest_settings->date_end);
		/** @var bool $activeContest Is the contest active or not ? */
		$activeContest = (time() >= mktime(0,0,0,$bmonth,$bday,$byear) and time() <= mktime(0,0,0,$emonth,$eday,$eyear)) ? true : false;
		if($activeContest == false)
			die("The contest is closed!");
    if ($contest_settings->voting_type == "contest"){
      $ip_sql=mysqli_query($bd, "select ip_add from image_IP where contest = '$contest'");
    }else{
      $ip_sql=mysqli_query($bd, "select ip_add from image_IP where img_id_fk=$id and ip_add='$ip'");
    }
  	$count=mysqli_num_rows($ip_sql);
  	//var_dump($id);
  	if($count==0){
  		$sql = "UPDATE `images` SET love = love +1 WHERE img_id = ".$id;
  		//var_dump($sql);
  		mysqli_query($bd, $sql);
  		$sql_in = "insert into image_IP (ip_add,img_id_fk,contest) values ('$ip',$id,'$contest')";
  		mysqli_query($bd, $sql_in);
  		$result=mysqli_query($bd, "select love from images where img_id=$id");
  		//var_dump($result);
  		$row=mysqli_fetch_array($result);
  		$love=$row['love'];
  		?>
		  <span title="<?php echo _('I\'m in love !'); ?>"><span class="fa fa-heart"></span> <?php echo $love; ?> </span>
  		<?php
  	}else{
  		echo _('You have already voted !');
  	}
  }
}

if (isset($_POST['action'])){
	if ($_POST['action'] == 'login'){
		$pwd = $_POST['pwd'];
		if ($pwd == PASSWD){
			$ok = setcookie(COOKIE_NAME, sha1(PASSWD.HASH), 0, '/', '', FALSE, TRUE);
			if (!$ok){
        echo '<div class="alert error">cookie failed !</div>';
      }
		}else{
			echo '<div class="alert error"><a class="alert-close" href="#" title="'._('Close').'">×</a>'._('Wrong password !').'</div>';
		}
	}
	if ($_POST['action'] == 'logout'){
			$ok = setcookie(COOKIE_NAME, "", 0, '/', '', FALSE, TRUE);
			if (!$ok){
				echo '<div class="alert error">cookie failed !</div>';
			}
		else{
			echo 'logout';
		}
	}
}
?>
