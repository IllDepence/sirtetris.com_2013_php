<?php
ob_start();

	echo "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>";

	if(!isset($_GET['c'])) $_GET['c'] = "l";

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
<title>Tarek's movielist</title>
<style>
* {
margin: 0px;
padding: 0px;
font-family: Helvetica, Arial, sans-serif;
}

p {
font-size: 12px;
}

strong {
font-weight: bold;
}

h1 {
font-size: 36px;
font-weight: normal;
}

body {
background-color: #f2f2f2;
}

a, a:active, a:focus, a:visited {
outline: none;
text-decoration: none;
color: #f20202;
border: none;
}

a:hover {
color: #ff5555;
}

a.chosen {
border-bottom: solid 1px #f20202;
}

div.stats:hover {
background-color: #ff9999 !important;
}

a.info, a.info:hover {
color: #000000;
}

em {
font-style: italic;
}

a.text, a.text:active, a.text:focus, a.text:visited, a.text:hover {
color: #000000;
}
</style>



</head>

<body>

<?php

include "functions.php";

$x = 0;
$numchar = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');
$num2mon = array('01' => 'January', '02' => 'February', '03' => 'March', '04' => 'April', '05' => 'May', '06' => 'June', '07' => 'July', '08' => 'August', '09' => 'September', '10' => 'October', '11' => 'November', '12' => 'December');
$y_seen = 0;
$m_seen = 0;
$min = 5000;
$max = 0;
$actorlist = array();
$directorlist = array();

// - - - categories - - -
// directors (pre)
$cat_dir = array();
// years
for($i=1900; $i<2100; $i++) {
	$cat_y[] = array($i);
	}
// duration
$cat_d[0][] = "runtime";
for($i=0; $i<600; $i++) {
	$cat_d[0][] = $i;
	}
// rating
$cat_r[0][] = "rating";
for($i=0; $i<=10; $i+=(1/10)) {
	$cat_r[0][] = number_format($i, 1);
	}
// letters
$cat_l = array(
	array('0-9', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9'), 
	array('A', 'a'),
	array('B', 'b'),
	array('C', 'c'),
	array('D', 'd'),
	array('E', 'e'),
	array('F', 'f'),
	array('G', 'g'),
	array('H', 'h'),
	array('I', 'i'),
	array('J', 'j'),
	array('K', 'k'),
	array('L', 'l'),
	array('M', 'm'),
	array('N', 'n'),
	array('O', 'o'),
	array('P', 'p'),
	array('Q', 'q'),
	array('R', 'r'),
	array('S', 's'),
	array('T', 't'),
	array('U', 'u'),
	array('V', 'v'),
	array('W', 'w'),
	array('X', 'x'),
	array('Y', 'y'),
	array('Z', 'z')
	);
// genre
$cat_g = array(
	array("Action"),
	array("Adventure"),
	array("Animation"),
	array("Biography"),
	array("Comedy"),
	array("Crime"),
	array("Documentary"),
	array("Drama"),
	array("Family"),
	array("Fantasy"),
	array("Film-Noir"),
	array("Game-Show"),
	array("History"),
	array("Horror"),
	array("Music"),
	array("Musical"),
	array("Mystery"),
	array("News"),
	array("Reality-TV"),
	array("Romance"),
	array("Sci-Fi"),
	array("Sport"),
	array("Talk-Show"),
	array("Thriller"),
	array("War"),
	array("Western")
	);

$movieline = file("movies", FILE_IGNORE_NEW_LINES);

foreach ($movieline as $m) {

	if (strlen($m) == 6) {
		if(substr($m, 0, 3) == "---") $m_seen = substr($m, 3, 2);
		else $y_seen = substr($m, 1, 4);
		}

	if (strlen($m) > 6) {

		$chunk = explode(",,", $m);

		$title = $chunk[0];
		$year = substr($chunk[1], 1);
		$imdbid = substr($chunk[2], 1);

		$lcheck = aliaschars($title);
		while(!in_array(substr($lcheck, 0, 1), $numchar)) { $lcheck = substr($lcheck, 1); }
		$letter = aliaschars(substr($lcheck, 0, 1));

		$cachedata = file("cache/".$imdbid."/data", FILE_IGNORE_NEW_LINES);
		$cachechunk = explode(",, ", $cachedata[0]);
		$rating = $cachechunk[0];
		$runtime = $cachechunk[1];
		$genreline = $cachechunk[2];
			$genres = explode(", ", $genreline);
			$maingenre = $genres[0];
		$director = $cachechunk[3];
		$actorline = $cachechunk[4];
			$actors = explode(", ", $actorline);
		$descr = $cachechunk[5];
		

		$info = array($title, $year, $imdbid, $letter, $x, $y_seen, $m_seen, $rating, $runtime, $genres, $maingenre, $director, $actors, $descr);

		$movie[$x] = $info;

		$x++;
		$tlen += $runtime;
		$trate += $rating;

		$seen[$y_seen]['all']++;
		$seen[$y_seen][$m_seen]++;

		$cleandir = cleanstring($director);
		if(!isset($diract[$cleandir])) $diract[$cleandir] = array();
		foreach($actors as $a) {
			$cleanact = cleanstring($a);
			if(!in_array($a, $actorlist)) $actorlist[] = $a;
			if(!in_array($cleanact, $diract[$cleandir])) $diract[$cleandir][] = $cleanact;
			if(!isset($actdir[$cleanact])) $actdir[$cleanact] = array();
			if(!in_array($cleandir, $actdir[$cleanact])) $actdir[$cleanact][] = $cleandir;
			}
		if(!in_array($director, $directorlist)) $directorlist[] = $director;

		// director category
		if(!isset($cat_dir[$info[11]])) $cat_dir[$info[11]] = array($info[11], 0);
		$cat_dir[$info[11]][1]++;

		// first/last year
		$min = ($info[1] < $min ? $info[1] : $min);
		$max = ($info[1] > $max ? $info[1] : $max);

		}

	}

$last = end($movie);
$avg = $x/($max-$min);

sort($actorlist);
sort($directorlist);

// sort director category
function dircmp($a, $b) {
	if ($a[1] == $b[1]) return 0;
	return (($a[1] > $b[1]) ? -1 : 1);
	}
usort($cat_dir, "dircmp");




$am = $tlen%60;
$ah = ($tlen-$am)/60;
$ahr = $ah%24;
$ad = ($ah-$ahr)/24;
$adr = $ad%7;
$aw = ($ad-$adr)/7;
$avrl = $tlen/$x;
$avrls = $avrl*60;
$avrls = number_format($avrls, 0, '', '');
$avrlsr = $avrls%60;
$avrlm = ($avrls-$avrlsr)/60;
$avrlmr = $avrlm%60;
$avrlh = ($avrlm-$avrlmr)/60;

foreach($actorlist as $a) {
	if(strlen($_GET['dirrestr'])>1 && !in_array($a, $diract[$_GET['dirrestr']])) continue;
	$optionstring_a .= "<option value=\"".cleanstring($a)."\" ".($_GET['actrestr'] == cleanstring($a) ?  "selected=\"selected\"" : "").">".$a."</option>";
	$optioncount_a++;
	}

foreach($directorlist as $d) {
	if(strlen($_GET['actrestr'])>1 && !in_array($d, $actdir[$_GET['actrestr']])) continue;
	$optionstring_d .= "<option value=\"".cleanstring($d)."\" ".($_GET['dirrestr'] == cleanstring($d) ?  "selected=\"selected\"" : "").">".$d."</option>";
	$optioncount_d++;
	}

echo 	"<div style=\"margin: 10px 10px 10px 10px; width: 1500px;\">".
	"<h1>Tarek's movielist</h1>".
	"<div style=\"float: left; padding: 15px; width: 500px; border: dashed 1px #d0d0d0;\">".

	'<div style="background-image:url(\'ellen.jpg\'); background-repeat: no-repeat; background-position: right bottom; height: 260px;">'.

		"<p style=\"font-size: 18px; margin-bottom: 12px;\">".$x." movies</p>".
		"<p>".number_format($tlen, 0, '', '.')." minutes</p>".
		"<p>".number_format($ah, 0, '', '.')." hours ".$am." minute".($am!=1 ? "s" : "")."</p>".
		"<p>".$ad." days ".$ahr." hour".($ahr!=1 ? "s" : "")." ".$am." minute".($am!=1 ? "s" : "").
		"<p style=\"font-size: 16px;\">".$aw." weeks ".$adr." day".($adr!=1 ? "s" : "")." ".$ahr." hour".($ahr!=1 ? "s" : "")." ".$am." minute".($ahr!=1 ? "s" : "")."</p>".

		"<div style=\"margin: 12px 0px 0px 0px;\">".
			"<p><strong>Sort by".
			menuitem("title", "l").
			menuitem("year", "y").
			menuitem("runtime", "d").
			menuitem("genre", "g").
			menuitem("rating", "r").
			menuitem("director", "dir").
		"</strong></div>".
		"<div style=\"margin-top: 12px; padding-bottom: 12px;\">".
			"<form action=\"#\" method=\"get\">".
				"<select style=\"display: none;\" name=\"c\"><option value=\"".$_GET['c']."\" selected=\"selected\"></option></select>".
				"<p>Show only movies with</p>".
				"<select style=\"width: 275px;\" name=\"actrestr\" onchange=\"this.form.submit()\"><option value=\"0\">".
				(strlen($_GET['actrestr'])>1 ? "- reset -" : "- choose from ".$optioncount_a." -").
				"</option>".
				$optionstring_a.
				"</select>".
				"<p>Show only movies by</p>".
				"<select style=\"width: 275px;\" name=\"dirrestr\" onchange=\"this.form.submit()\"><option value=\"0\">".
				(strlen($_GET['dirrestr'])>1 ? "- reset -" : "- choose from ".$optioncount_d." -").
				"</option>".
				$optionstring_d.
				"</select>".
			"</form>".
		"</div>".
	"</div>";

$cats = array("l", "c", "y", "d", "r", "g", "dir");

if(!in_array($_GET['c'], $cats)) {
	$cat = $cat_l;
	$check = 3;
	}
else {
	if($_GET['c'] == "l") {
		$cat = $cat_l;
		$check = 3;
		}
	if($_GET['c'] == "y") {
		$cat = $cat_y;
		$check = 1;
		}
	if($_GET['c'] == "d") {
		$cat = $cat_d;
		$check = 8;
		}
	if($_GET['c'] == "r") {
		$cat = $cat_r;
		$check = 7;
		}
	if($_GET['c'] == "g") {
		$cat = $cat_g;
		$check = 10;
		}
	if($_GET['c'] == "dir") {
		$cat = $cat_dir;
		$check = 11;
		}

	}

function lcmp($a, $b) {
	if ($a[8] == $b[8]) return 0;
	return ($a[8] > $b[8] ? -1 : 1);
	}

function rcmp($a, $b) {
	if ($a[7] == $b[7]) return 0;
	return ($a[7] > $b[7] ? -1 : 1);
	}

function yrcmp($a, $b) {
	if ($a[1] == $b[1]) return 0;
	return (($a[1] > $b[1]) ? 1 : -1);
	}

foreach ($cat as $c) {
	foreach ($movie as $m) {
		$adont = 0;
		$ddont = 0;
		if (in_array($m[$check], $c)) {

			if(strlen($_GET['actrestr']) > 1) {
			$adont = 1;
				foreach($m[12] as $a) {
					if(cleanstring($_GET['actrestr']) == cleanstring($a)) $adont = 0;
					}
				}
			if(strlen($_GET['dirrestr']) > 1) {
			$ddont = 1;
				if(cleanstring($_GET['dirrestr']) == cleanstring($m[11])) $ddont = 0;
				}
			if($adont || $ddont) continue;

			$lcount[$c[0]] += 1;
			$lmvs[$c[0]][] = $m;
			}
		}
	}

$mrgn = 0;
foreach ($cat as $c) {

	if(!$lcount[$c[0]]) continue;

	echo "<div style=\"background-color: #afafaf; padding: 3px 5px 3px 5px; ".($mrgn ? "margin-top: 10px;" : "")." border-style: solid; border-color: #555555; border-width: 5px 5px 0px 5px\">
			<div style=\"float: left;\"><p style=\"font-size: 34px; color: #555555;\">".$c[0]."</p></div>
			<div style=\"float: right;\"><p style=\"font-size: 30px; color: #888888; line-height: 43px;\">(".$lcount[$c[0]].")</p></div>
			<div style=\"clear: both;\"></div>
		</div>
		<div style=\"background-color: #555555; padding: 5px;\">";

	if($_GET['c'] == "d") {
		usort($lmvs[$c[0]], "lcmp");
		}
	else if($_GET['c'] == "r") {
		usort($lmvs[$c[0]], "rcmp");
		}
	else if($_GET['c'] == "dir") {
		usort($lmvs[$c[0]], "yrcmp");
		}
	else {
		sort($lmvs[$c[0]]);
		}

	foreach ($lmvs[$c[0]] as $k => $m) {

		$validimg = 0;
		$isize=getimagesize("cache/".$m[2]."/thumb.jpg");
		if(($isize[0]>90) || ($isize[1]>90)) $validimg = 1;

		$mins = $m[8]%60;
		$h = ($m[8]-$mins)/60;
		
		if($k) echo "<div style=\"height: 5px;\"></div>";

		echo 	"<div style=\"background-color: #f2f2f2; padding: 3px;\">".
				"<p style=\"font-size: 20px;\"><strong>".fixchars($m[0])."</strong> <span style=\"color: #777777;\">(".$m[1].")</span></p>".
				($validimg ? "<div style=\"float: left; margin: 0px 4px -3px 0px;\"><img src=\"cache/".$m[2]."/thumb.jpg\" /></div>".
						"<div style=\"float: left; width: ".(480-$isize[0])."px;\">" : "").
					"<p>Seen: ".($num2mon[$m[6]] ? $num2mon[$m[6]] : "")." ".($m[5] ? $m[5] : "unknown")."</p>".
					"<p>Rating: ".$m[7]."</p>".
					ratingbar($m[7]).
					"<p>Runtime: ".$h."h ".$mins."min</p>".
					"<p>Genre: ";
		foreach($m[9] as $k => $g) { if($k) { echo ", "; } echo $g; }
		echo			"</p>".
					"<br /><p><a class=\"text\" href=\"?c=".$_GET['c']."&amp;dirrestr=".cleanstring($m[11])."\" title=\"Show only movies by ".$m[11]."\">Director: ".$m[11]."</a></p>".
					"<p>Actors: ";
		foreach($m[12] as $k => $g) { if($k) { echo ", "; } echo "<a class=\"text\" href=\"?c=".$_GET['c']."&amp;actrestr=".cleanstring($g)."\" title=\"Show only movies with ".$g."\">".$g."</a>"; }
		echo			"</p>".
					(strlen($m[13])>2 ? "<br /><p>Plot: ".$m[13]."</p>" : "").
					"<br /><p>- <a href=\"http://www.imdb.com/title/tt".$m[2]."/\">IMDb</a></p>".
				($validimg ? "</div><div style=\"clear: both;\"></div>" : "").
			"</div>";

		}

	echo "</div>";
	$mrgn = 1;

	}

ob_flush();

echo "</div><div style=\"margin-left: 10px; padding: 15px; float: left;\">";

$maxcount = 0;
$maxavglen = 0;
$maxavgrate = 0;
foreach ($cat_y as $c) {
	foreach ($movie as $m) {
		if (in_array($m[1], $c)) {
				$scount[$c[0]] += 1;
				$scntlen[$c[0]] += $m[8];
				$scntrate[$c[0]] += $m[7];
			}
		if($scount[$c[0]]) {
			$avglen[$c[0]] = ($scntlen[$c[0]]/$scount[$c[0]]);
			$avgrate[$c[0]] = ($scntrate[$c[0]]/$scount[$c[0]]);
			$maxcount = ($scount[$c[0]] > $maxcount ? $scount[$c[0]] : $maxcount);
			$maxavglen = ($avglen[$c[0]] > $maxavglen ? $avglen[$c[0]] : $maxavglen);
			$maxavgrate = ($avgrate[$c[0]] > $maxavgrate ? $avgrate[$c[0]] : $maxavgrate);
			}
		}
	}
$avgrt = $trate/$x;

$seen_max = 0;
$sfirst = 0;
$steps = 0;
$cntd = 0;
foreach($seen as $y => $s) {
	foreach($s as $m => $c) {
		if($m && $m != 'all') {
			if(!$sfirst) $sfirst = $num2mon[$m]." ".$y;
			$seen_max = ($c > $seen_max ? $c : $seen_max);
			$steps++;
			$cntd += $c;
			}
		}
	$slast = $num2mon[$m]." ".$y;
	}
$seen_avg = $cntd/$steps;

echo "<p style=\"font-size: 14px;\"># Movies watched</p>".
	"<div style=\"width: ".((($steps)*20)+300)."px; height: ".(20+($seen_max*5))."px; border: solid 1px #333333; background-color: #ffffff;\">";

echo "<div style=\"float: left; width: 299px; height: ".(20+($seen_max*5))."px; border-right: dashed 1px #aaaaaa;\">".
		"<div style=\"height: ".(20+(($seen_max*5)*(11/6))-($seen_max*5))."px;\"></div>".
		"<a style=\"info\" title=\"unknown: ".($x-$cntd)."\">".
			"<div style=\"background-color: #ffffff; height: ".(($seen_max*5)*(1/6))."px; text-align: left;\">".
				"<p style=\"position: relative; left: 60px; bottom: 16px; color: #38acec; width: 200px;\">A long time ago in a galaxy far,</p>".
				"<p style=\"position: relative; left: 60px; bottom: 16px; color: #38acec; width: 200px;\">far away ...</p>".
			"</div>".
		"</a>".
	"</div>";

$j=0;
foreach($seen as $y => $s) {
	foreach($s as $m => $c) {
		if($m && $m != 'all') {
			echo "<div style=\"float: left; width: 20px; height: ".(20+($seen_max*5))."px;\">".
				"<div style=\"height: ".((20+($seen_max*5))-($c*5))."px;\">".
					"<div style=\"height: ".(($seen_max*5)-($c*5)+9)."px;\"></div>".
					"<div style=\"font-size: 8px; text-align: center; color: #333333;\">".$c."</div>".
				"</div>".
				"<a title=\"".$num2mon[$m]." ".$y.": ".$c."\">".
				"<div class=\"stats\" style=\"background-color: ".($j ? "#cccccc" : "#aaaaaa")."; height: ".($c*5)."px;\"></div>".
				"</a>".
				"</div>";
			$j = 1-$j;
			}
		}
	}

echo "<div style=\"clear: both;\"></div>";

echo "<div style=\"height: 15px; width: ".((($steps)*20))."px; border: solid; border-color: #333333; border-width: 0px 1px 0px 1px; margin-left: 299px;\">".
		"<div style=\"float: left; padding-left: 1px;\">".
			"<p style=\"font-size: 10px; line-height: 27px;\">".$sfirst."</p>". // position: relative; left: 299px; border-left: solid 1px #000000;
		"</div>".
		"<div style=\"float: right; padding-right: 1px;\">".
			"<p style=\"font-size: 10px; line-height: 27px;\">".$slast."</p>".
		"</div>".
		"<div style=\"height: 1px; width: ".((($steps)*20)+300)."px; background-color: #ff8888; position: relative; top: -".($seen_avg*5)."px; right: 300px;\"></div>".
		"<div style=\"position: relative; top: -".(($seen_avg*5)+11)."px; right: 325px; font-size: 9px; width: 300px;\"><span style=\"color: #ff3333;\">average: ".$seen_avg."</span> <span style=\"color: #ff8888;\">(displayed at ".number_format((((number_format($seen_avg, 1)*10)-((number_format($seen_avg, 1)*10)%2))/10), 1).")</span></div>".
	"</div>".

	"<div style=\"clear: both;\"></div></div>";

// ------------------------

ob_flush();

echo "<br /><br />";

echo "<p style=\"font-size: 14px;\"># Movies in timeline</p>".
	"<div style=\"width: ".((1+$max-$min)*10)."px; height: ".(20+($maxcount*5))."px; border: solid 1px #333333; background-color: #ffffff;\">";

$j=0;
for($i=$min; $i<=$max; $i++) {
	echo "<div style=\"float: left; width: 10px; height: ".(20+($maxcount*5))."px;\">".
		"<div style=\"height: ".((20+($maxcount*5))-($scount[$i]*5))."px;\">".
			"<div style=\"height: ".(($maxcount*5)-($scount[$i]*5)+9)."px;\"></div>".
			"<div style=\"font-size: 8px; text-align: center; color: #333333;\">".$scount[$i]."</div>".
		"</div>".
		"<a title=\"".$i.": ".$scount[$i]."\">".
		"<div class=\"stats\" style=\"background-color: ".($j ? "#cccccc" : "#aaaaaa")."; height: ".($scount[$i]*5)."px;\"></div>".
		"</a>".
		"</div>";
	$j = 1-$j;
	}

echo "<div style=\"clear: both;\"></div>";

echo "<div style=\"height: 15px; width: ".((1+$max-$min)*10)."px; border: solid; border-color: #333333; border-width: 0px 1px 0px 1px; margin-left: -1px;\">".
		"<div style=\"float: left; padding-left: 1px;\">".
			"<p style=\"font-size: 10px; line-height: 27px;\">".$min."</p>".
		"</div>".
		"<div style=\"float: right; padding-right: 1px;\">".
			"<p style=\"font-size: 10px; line-height: 27px;\">".$max."</p>".
		"</div>".
		"<div style=\"height: 1px; width: ".((1+$max-$min)*10)."px; background-color: #ff8888; position: relative; top: -".($avg*5)."px;\"></div>".
		"<div style=\"position: relative; top: -".(($avg*5)+11)."px; font-size: 9px; width: 300px;\"><span style=\"color: #ff3333;\">average: ".$avg."</span> <span style=\"color: #ff8888;\">(displayed at ".number_format((((number_format($avg, 1)*10)-((number_format($avg, 1)*10)%2))/10), 1).")</span></div>".
	"</div>".

	"<div style=\"clear: both;\"></div></div>";

// ------------------------

ob_flush();

echo "<br /><br />";

echo "<p style=\"font-size: 14px;\"># Average runtime</p>".
	"<div style=\"width: ".((1+$max-$min)*10)."px; height: ".(20+($maxavglen))."px; border: solid 1px #333333; background-color: #ffffff;\">";

$j=0;
for($i=$min; $i<=$max; $i++) {
	echo "<div style=\"float: left; width: 10px; height: ".(20+($maxavglen))."px;\">".
		"<div style=\"height: ".((20+($maxavglen))-(round($avglen[$i])))."px;\">".
			"<div style=\"height: ".(($maxavglen)-($avglen[$i])+12)."px;\"></div>".
			"<div style=\"font-size: 5px; text-align: center; color: #111111;\">".($avglen[$i] ? round($avglen[$i]) : "")."</div>".
		"</div>".
		"<a title=\"".$i.": ".$avglen[$i]."\">".
		"<div class=\"stats\" style=\"background-color: ".($j ? "#cccccc" : "#aaaaaa")."; height: ".round(($avglen[$i]))."px;\"></div>".
		"</a>".
		"</div>";
	$j = 1-$j;
	}

echo "<div style=\"clear: both;\"></div>";

echo "<div style=\"height: 15px; width: ".((1+$max-$min)*10)."px; border: solid; border-color: #333333; border-width: 0px 1px 0px 1px; margin-left: -1px;\">".
		"<div style=\"float: left; padding-left: 1px;\">".
			"<p style=\"font-size: 10px; line-height: 27px;\">".$min."</p>".
		"</div>".
		"<div style=\"float: right; padding-right: 1px;\">".
			"<p style=\"font-size: 10px; line-height: 27px;\">".$max."</p>".
		"</div>".
		"<div style=\"height: 1px; width: ".((1+$max-$min)*10)."px; background-color: #ff8888; position: relative; top: -".($avrl)."px;\"></div>".
		"<div style=\"position: relative; top: -".(($avrl)+11)."px; font-size: 9px; width: 300px;\"><span style=\"color: #ff3333;\">average: ".$avrl."</span> <span style=\"color: #ff8888;\">(displayed at ".number_format($avrl, 0).")</span></div>".
	"</div>".

	"<div style=\"clear: both;\"></div></div>";

// ------------------------

ob_flush();

echo "<br /><br />";

echo "<p style=\"font-size: 14px;\"># Average rating</p>".
	"<div style=\"width: ".((1+$max-$min)*10)."px; height: ".(20+($maxavgrate*25))."px; border: solid 1px #333333; background-color: #ffffff;\">";

$j=0;
for($i=$min; $i<=$max; $i++) {
	echo "<div style=\"float: left; width: 10px; height: ".(20+($maxavgrate*25))."px;\">".
		"<div style=\"height: ".((20+($maxavgrate*25))-($avgrate[$i]*25))."px;\">".
			"<div style=\"height: ".(($maxavgrate*25)-($avgrate[$i]*25)+9)."px;\"></div>".
			"<div style=\"font-size: 8px; text-align: center; color: #333333;\">".($avgrate[$i] ? number_format($avgrate[$i], 1) : "")."</div>".
		"</div>".
		"<a title=\"".$i.": ".$avgrate[$i]."\">".
		"<div class=\"stats\" style=\"background-color: ".($j ? "#cccccc" : "#aaaaaa")."; height: ".($avgrate[$i]*25)."px;\"></div>".
		"</a>".
		"</div>";
	$j = 1-$j;
	}

echo "<div style=\"clear: both;\"></div>";

echo "<div style=\"height: 15px; width: ".((1+$max-$min)*10)."px; border: solid; border-color: #333333; border-width: 0px 1px 0px 1px; margin-left: -1px;\">".
		"<div style=\"float: left; padding-left: 1px;\">".
			"<p style=\"font-size: 10px; line-height: 27px;\">".$min."</p>".
		"</div>".
		"<div style=\"float: right; padding-right: 1px;\">".
			"<p style=\"font-size: 10px; line-height: 27px;\">".$max."</p>".
		"</div>".
		"<div style=\"height: 1px; width: ".((1+$max-$min)*10)."px; background-color: #ff8888; position: relative; top: -".($avgrt*25)."px;\"></div>".
		"<div style=\"position: relative; top: -".(($avgrt*25)+11)."px; font-size: 9px; width: 300px;\"><span style=\"color: #ff3333;\">average: ".$avgrt."</span> <span style=\"color: #ff8888;\">(displayed at ".number_format((((number_format($avgrt, 1)*10)-((number_format($avgrt, 1)*10)%2))/10), 1).")</span></div>".
	"</div>".

	"<div style=\"clear: both;\"></div></div>";

// ------------------------





echo "</div></div>";


?>

</div>
</body>

</html>
