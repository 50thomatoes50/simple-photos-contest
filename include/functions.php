<?php
DEFINE('SPC_VERSION', '3.0 Alpha 2');
DEFINE('SPC_VERSION_DB', '3.0 A2');

$sql=mysqli_query($bd, "SELECT * FROM settings");
$settings = mysqli_fetch_object($sql);
unset($sql);

function parseLanguageList($languageList) {
    if (is_null($languageList)) {
        if (!isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            return array();
        }
        $languageList = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
    }
    $languages = array();
    $languageRanges = explode(',', trim($languageList));
    foreach ($languageRanges as $languageRange) {
        if (preg_match('/(\*|[a-zA-Z0-9]{1,8}(?:-[a-zA-Z0-9]{1,8})*)(?:\s*;\s*q\s*=\s*(0(?:\.\d{0,3})|1(?:\.0{0,3})))?/', trim($languageRange), $match)) {
            if (!isset($match[2])) {
                $match[2] = '1.0';
            } else {
                $match[2] = (string) floatval($match[2]);
            }
            if (!isset($languages[$match[2]])) {
                $languages[$match[2]] = array();
            }
            $languages[$match[2]][] = strtolower($match[1]);
        }
    }
    krsort($languages);
    return $languages;
}

// compare two parsed arrays of language tags and find the matches
function findMatches($accepted, $available) {
    $matches = array();
    $any = false;
    foreach ($accepted as $acceptedQuality => $acceptedValues) {
        $acceptedQuality = floatval($acceptedQuality);
        if ($acceptedQuality === 0.0) continue;
        foreach ($available as $availableQuality => $availableValues) {
            $availableQuality = floatval($availableQuality);
            if ($availableQuality === 0.0) continue;
            foreach ($acceptedValues as $acceptedValue) {
                if ($acceptedValue === '*') {
                    $any = true;
                }
                foreach ($availableValues as $availableValue) {
                    $matchingGrade = matchLanguage($acceptedValue, $availableValue);
                    if ($matchingGrade > 0) {
                        $q = (string) ($acceptedQuality * $availableQuality * $matchingGrade);
                        if (!isset($matches[$q])) {
                            $matches[$q] = array();
                        }
                        if (!in_array($availableValue, $matches[$q])) {
                            $matches[$q][] = $availableValue;
                        }
                    }
                }
            }
        }
    }
    if (count($matches) === 0 && $any) {
        $matches = $available;
    }
    krsort($matches);
    return $matches;
}

function selectLanguage($avail,$client){
	if(count($client)==0)
		return $avail[0];
	$lang="";$val=0;
	foreach($client as $key => $lc){
		foreach($avail as $a){
			if($a[0]==$lc[0][0] and $a[1]==$lc[0][1])
				if(floatval($key)>$val){
	        $val=$key;$lang=$a;
	      }
			}
	}
	return $lang;
}

if (!empty($settings)){
	/** Translations ! */

	/** Define language used
	*
	* To see the locales installed in your ubuntu server, type locale -a in shell.
	*/
	if($settings->language_auto)
{
		$langs = array(
        'en-US',// default
        'fr-FR',);
$accepted = parseLanguageList($_SERVER['HTTP_ACCEPT_LANGUAGE']);
$lang_selected = selectLanguage($langs, $accepted);
		putenv("LC_ALL=".$lang_selected);
		setlocale(LC_ALL, $lang_selected);
	}
else {
	putenv("LC_ALL=".$settings->language);
	setlocale(LC_ALL, $settings->language);
}

	bindtextdomain("messages", "lang");
	bind_textdomain_codeset('messages', 'UTF-8');
	textdomain("messages");

}

/** Are we logged in ? */
$admin_logged = admin_logged();


/**
* Transcrit une date au format sql en format français.
*
* @param string $mysql_date DATE ou DATETIME SQL
* @param string $time Si valeur égale à 'notime' (valeur par défaut), on ne retourne pas les heures:minutes:secondes.
*/
function date_formatting($mysql_date, $to_sql = false){
	global $settings;
  if (!$to_sql){
    //return date_format(date_create($mysql_date), $settings->date_format);
		return changeDateFormat($mysql_date, 'Y-m-d', $settings->date_format);
  }else{
    //return date_format(date_create_from_format($settings->date_format, $mysql_date), 'Y-m-d');
		return changeDateFormat($mysql_date, $settings->date_format, 'Y-m-d');
  }
}
/** For php version < 5.3
* http://php.net/manual/en/function.date.php#90423
*/
function dateParseFromFormat($stFormat, $stData)
 {
     $aDataRet = array('day'=>0, 'month'=>0, 'year'=>0, 'hour'=>0, 'minute'=>0, 'second'=>0);
     $aPieces = preg_split('[:/.\ \-]', $stFormat);
     $aDatePart = preg_split('[:/.\ \-]', $stData);
     foreach($aPieces as $key=>$chPiece)
     {
         switch ($chPiece)
         {
             case 'd':
             case 'j':
                 $aDataRet['day'] = $aDatePart[$key];
                 break;

             case 'F':
             case 'M':
             case 'm':
             case 'n':
                 $aDataRet['month'] = $aDatePart[$key];
                 break;

             case 'o':
             case 'Y':
             case 'y':
                 $aDataRet['year'] = $aDatePart[$key];
                 break;

             case 'g':
             case 'G':
             case 'h':
             case 'H':
                 $aDataRet['hour'] = $aDatePart[$key];
                 break;

             case 'i':
                 $aDataRet['minute'] = $aDatePart[$key];
                 break;

             case 's':
                 $aDataRet['second'] = $aDatePart[$key];
                 break;
         }

     }
     return $aDataRet;
 }

 function changeDateFormat($stDate,$stFormatFrom,$stFormatTo)
 {
   // When PHP 5.3.0 becomes available to me
   $date = date_parse_from_format($stFormatFrom,$stDate);
   //For now I use the function above
   //$date = dateParseFromFormat($stFormatFrom,$stDate);
   return date($stFormatTo,mktime($date['hour'],
                                     $date['minute'],
                                     $date['second'],
                                     $date['month'],
                                     $date['day'],
                                     $date['year']));
 }

/**
* Valide une date suivant un format.
*
* @param string $date Date à valider
* @param string $format Format que doit avoir la date à valider
*/
function date_valid($date, $format = null) {
	global $settings;
	if (empty($format)){
		$format = $settings->date_format;
	}
   if (date($format, strtotime($date)) == $date) {
       return true;
   } else {
       return false;
   }
 }

/**
 * Permet de savoir si l'admin est connecté.
 *
 * @return bool
 */
function admin_logged(){
  if (isset($_COOKIE[COOKIE_NAME])){
    $cookie = $_COOKIE[COOKIE_NAME];
    if ($cookie == sha1(PASSWD.HASH)){
			return true;
		}
  }
	return false;
}
/**
 * Affiche un message de notification.
 *
 * @param object $message
 */
function disp_message($message){
	if (isset($message->type)){
		return '<div class="alert '.$message->type.'"><a class="alert-close" href="#" title="Fermer">×</a>'.$message->text.'</div>';
	}
}

function info_disp($message){
	return '<span class="fa fa-info imgInfo" title="'.$message.'"></span>';
	//<img alt="'.$message.'" class="img_info" src="img/info.png" />';
}

/**
* Display a vertical bar graph with contest votes.
* @param string $contest Contest ID
*
*/
function contest_stats($contest){
	global $settings, $c_path, $bd;
	// Some text to explain why the voting system is not 100% reliable
	?><div class="width60Percent"><?php echo _('Warning : SPC does not provide voters authentication, and as a result the voting limit is not 100% reliable. People could vote multiple times with other devices or browsers and SPC would not be able to detect that this is the same voter.'); ?></div><?php
	/** Get contest data. */
	$sql=mysqli_query($bd, 'SELECT * FROM contests WHERE contest = "'.$contest.'"');
	$cont = mysqli_fetch_object($sql);
	/** Get images and votes. */
	$sql=mysqli_query($bd, 'SELECT * FROM images WHERE contest = "'.$contest.'" ORDER BY img_name');
	$nbphotos = mysqli_num_rows($sql);
	/** Get number of voters (format : array with only first value populated) */
	$nbvoters = mysqli_fetch_row(mysqli_query($bd, 'SELECT COUNT(DISTINCT ip_add) FROM image_IP WHERE contest = "'.$contest.'"'));
	$nbvoters = $nbvoters[0];
	/** Let's build the graph source js array ! */
	?>
	<script>
		if (typeof arrayOfData == "undefined") {
			arrayOfData = new Array();
		}
		arrayOfData["<?php echo $contest; ?>"] = new Array(
	<?php
	$disp = '';
	$nbvotes = 0;
	/** @param array Photo name as first value, img url as second and number of votes as third. */
	$mostvoted = array(0,0,0);
	while($row=mysqli_fetch_array($sql)){
		$disp .= "\r\n".'['.$row['love'].', "<a title=\"'.$row['img_name'].'\" href=\"'.$c_path.$contest.'/'.$row['img_url'].'\" class=\"\" data-lightbox=\"'.$row['img_id'].'\">'.$row['img_name'].'</a>"],';
		$nbvotes += $row['love'];
		if ($mostvoted[2] < $row['love']){
			$mostvoted[0] = $row['img_name'];
			$mostvoted[1] = $row['img_url'];
			$mostvoted[2] = $row['love'];
		}
	}
	$disp = trim($disp, ',');
	echo $disp;
	?>
		);
	</script>
	<div class="graph" data-contest="<?php echo $contest; ?>" data-title="<h2><?php echo sprintf(_('%s contest'), $cont->contest_name); ?></h2>" id="contest_graph_<?php echo $contest; ?>"></div>
	<ul class="stats_data">
		<li><?php echo _('Number of votes'); ?> : <span class="stats_numbers"><?php echo $nbvotes; ?></span></li>
		<li><?php echo _('Number of voters'); ?> : <span class="stats_numbers"><?php echo $nbvoters; ?></span></li>
		<li><?php echo _('Number of photos'); ?> : <span class="stats_numbers"><?php echo $nbphotos; ?></span></li>
		<li><?php echo _('Favorite'); ?> : <span class="stats_numbers"><a class="" data-lightbox="favorite" href="<?php echo $c_path.$contest.'/'.$mostvoted[1]; ?>" title="<?php echo $mostvoted[0]; ?>"><?php echo $mostvoted[0]; ?></a></span> <?php echo _('with'); ?> <span class="stats_numbers"><?php echo $mostvoted[2]; ?></span> <?php echo ngettext('vote', 'votes', $mostvoted[2]); ?></li>
	</ul>
	<?php
}
?>
