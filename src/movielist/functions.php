<?php

function getIMDbSite($id) {
	return getSite("www.imdb.com", "title/tt".$id."/");
	}

function getSite($HOST, $PATH="") {
	// perform request
	$status = array();
	$answer = "";
	$fp = fsockopen($HOST, 80, $errorno, $errstr, 30);
	if(!$fp) echo $errstr." (".$errno.") <br />";
	else {
		$out = "GET /".$PATH." HTTP/1.1\r\n";
		$out .= "Host: ".$HOST."\r\n";
		$out .= "Connection: Close\r\n\r\n";
		fwrite($fp, $out);
		for($i = 0; !feof($fp); $i++) {
			if($i<2) $status[$i] = fgets($fp, 128);
			else $answer .= fgets($fp, 128);
			}
		fclose($fp);
		}
	// follow redirect if occured
	if(strstr($status[0], "HTTP/1.1 302 Found")) {
		preg_match("/http:\/\/[^\/]*/", $status[1], $hostmatch);
		preg_match("/http:\/\/.*/", $status[1], $urlmatch);
		$newhost = substr($hostmatch[0], 7);
		$newpath = substr($urlmatch[0], strlen($hostmatch[0])+1);
		$answer = getSite($newhost, $newpath);
		}
	// return answer
	return $answer;
	}

function getRating($page) {
	$a = preg_match("/<span itemprop=\"ratingValue\">[0-9]{1}\.[0-9]{1}/", $page, $result);
	if($a) return substr($result[0], 29);
	else return "not found";
	}

function getRuntime($page) {
	$a = preg_match("/[0-9]+\smin/", $page, $result);
	if($a) return substr($result[0], 0, -4);
	else return "not found";
	}

function getGenres($page) {
	$a = preg_match_all("/href=\"\/genre\/[0-9a-zA-Z\-]+\"/", $page, $result);
	if($a) {
		$genres = array();
		foreach($result[0] as $r) { 
			if(!in_array(substr($r, 13, -1), $genres)) $genres[] = substr($r, 13, -1); 
			}
		return $genres;
		}
	else return array("not found");
	}

function getImage($page) {
	$a = preg_match("/<img src=\"http:\/\/ia\.media\-imdb\.com\/images\/M\/[0-9a-zA-Z@\._,]+\"/", $page, $result);
	if($a) { return substr($result[0], 44, -1); } //10
	else return "not found";
	}

function getDescription($page) {
	$a = preg_match("/<meta name=\"description\" content=\"[^<>]+?\s\/>/", $page, $result);
	$breaks = array("\n\r", "\r\n", "\n", "\r");
	if($a) {
		$fulldescr = str_replace("...", ".", str_replace($breaks, "", substr($result[0], 34, -4)));
		$d_chunk_pre = explode(". ", $fulldescr);
		$cnt = 0; 	// prechunks in temp
		$cnt2 = 0; 	// position in d_chunk
		$temp = "";
		foreach($d_chunk_pre as $dcp) {
			if($cnt) $temp .= ". "; 	// add dot if not first prechunk to save in temp
			$temp .= $dcp;			// add prechunk in temp
			$cnt++; 			// increase counter for prechunks in temp
			if(!preg_match("/[ \.]{1}[a-zA-Z]{1}/", substr($dcp, -2)) && !(strlen($dcp) == 1)) {	// if end of prechunk is not a single letter
				$d_chunk[$cnt2] = $temp;							// save content of temp in d_chunk
				$cnt = 0;									// reset counter for prechunks in temp
				$temp = "";									// empty temp
				$cnt2++;									// increase counter for position in d_chunk
				}
			}
		$director = substr($d_chunk[0], 12);
		$stars = substr($d_chunk[1], 5);
		foreach($d_chunk as $k => $dc) {
			if($k<2) continue;
			if($k>2) $descr .= ". ";
			$descr .= $dc;
			}
		return array($director, $stars, $descr);
		}
	else return array("not found", "not found", "not found");
	}

function imdbinfo($id) {
	$site = getIMDbSite($id);
	$runtime = getRuntime($site);
	$rating = getRating($site);
	$genres = getGenres($site);
	$image = getImage($site);
	$description = getDescription($site);
	return array($rating, $runtime, $image, $genres, $description);
	}

function imdb_cache($id, $rating, $length, $imgfile, $genres, $description) {
	mkdir("cache/".$id);

	$dataline = $rating.",, ".$length.",, ";
	foreach($genres as $g) {
		$dataline .= $g.", ";
		}
	$dataline = substr($dataline, 0, -2);

	$dataline .= ",, ".$description[0];
	$dataline .= ",, ".$description[1];
	$dataline .= ",, ".$description[2];

	$datafile = fopen("cache/".$id."/data", "w");
	fwrite($datafile, $dataline);
	fclose($datafile);

	imdb_dl_img($id, $imgfile);
	}

function imdb_dl_img($id, $filename) {
	// - - - - - - - - - - - - - - - - - - - - - - - - - - -
	// - - - - - - - download image from imdb - - - - - - - -
	// - - - - - - - - - - - - - - - - - - - - - - - - - - -
	// set host (ia.media-imdb.com/images/M)
	$host = "ia.media-imdb.com/images/M";
	// set file (z.B.: MV5BMTQ4MDI2MzkwMl5BMl5BanBnXkFtZTYwMjk0NTA5._V1._SY317_.jpg)
	$file = $filename;
	// construct header for request
	$hdrs = array(
		'http' => array(
			'method' => "POST",
			'header'=> "accept-language: en\r\n" . 
			"Host: $host\r\n" .
			"Referer: http://$host\r\n" .  // set http-referer
			"Content-Type: application/x-www-form-urlencoded\r\n" .
			"Content-Length: 33\r\n\r\n" .
			"username=mustap&comment=NOCOMMENT\r\n"
	    		)
		);
	// get requested file from the server with created header
	$context = stream_context_create($hdrs);
	$fp = fopen("http://" . $host . "/" . $file, 'r', false, $context);
	// create invisible div to hide image bitstream
	echo "<div style=\"display: none;\">";
	// flush output buffer
	ob_flush();
	// steam image to output buffer
	fpassthru($fp);
	echo "</div>";
	// create file to write image bitsteam into
	$filetowrite = fopen("cache/".$id."/thumb.jpg", "c");
	// catch image bitsteam in var
	$imagesteam = ob_get_contents();
	// write image bitsteam into file
	fwrite($filetowrite, $imagesteam);
	// flush output buffer
	//ob_flush();
	// close file
	fclose($filetowrite);
	// - - - - - - - - - - - - - - - - - - - - - - - - - - -
	}

function aliaschars($string) {
	$bad = array('Æ', 'á', 'à', 'â', 'Á', 'À', 'Â', 'é', 'è', 'ê', 'É', 'È', 'Ê', 'í', 'ì', 'î', 'Í', 'Ì', 'Î', 'ó', 'ò', 'ô', 'Ó', 'Ò', 'Ô', 'ú', 'ù', 'û', 'Ú', 'Ù', 'Û');	
	$good = array('A', 'a', 'a', 'a', 'A', 'A', 'A', 'e', 'e', 'e', 'E', 'E', 'E', 'i', 'i', 'i', 'I', 'I', 'I', 'o', 'o', 'o', 'O', 'O', 'O', 'u', 'u', 'u', 'U', 'U', 'U');
	return str_replace($bad, $good, $string);
	}

function fixchars($string) {
	$bad = array('ä', 'Ä', 'ö', 'Ö', 'ü', 'Ü', 'ß', 'á', 'à', 'â', 'é', 'è', 'ê', '·', '—', 'Æ', '³');	
	$good = array('&auml;', '&Auml;', '&ouml;', '&Ouml;', '&uuml;', '&Uuml;', '&szlig;', '&aacute;', '&agrave;', '&acirc;', '&eacute;', '&egrave;', '&ecirc;', '&middot;', '&mdash;', '&AElig;', '&sup3;');
	return str_replace($bad, $good, $string);
	}

function cleanstring($string) {
	return preg_replace('/&[0-9a-zA-Z#]+?;/', "_", $string);
	}

function menuitem($title, $letter) {
	return "&nbsp;&thinsp;&thinsp;<a ".($_GET['c']==$letter ? "class=\"chosen\"" : "")."href=\"?c=".$letter.addrestr()."\">".$title."</a>";
	}

function addrestr() {
	$add = "";
	if(isset($_GET['actrestr'])) $add .= "&amp;actrestr=".$_GET['actrestr'];
	if(isset($_GET['dirrestr'])) $add .= "&amp;dirrestr=".$_GET['dirrestr'];
	return $add;
	}

function ratingbar($r) {
	$r *= 10;
	$string .= "<div style=\"width: 209px; height: 3px; border: solid 1px #202020; background-color: #202020;\">";
	for($t=0; $t<100; $t+=10) {
		if(($r-($r%10)) > $t) {
			$string .= "<div style=\"float: left; width: 20px; height: 3px; background-color: #ff4444; ".($t ? "margin-left: 1px;" : "")."\"></div>";
			}
		else if(($r-($r%10)) == $t) {
			$part = ((($r%10)*20)/10);
			$string .= "<div style=\"float: left; width: ".$part."px; height: 3px; background-color: #ff4444; ".($t ? "margin-left: 1px;" : "")."\"></div>";
			$string .= "<div style=\"float: left; width: ".(20-$part)."px; height: 3px; background-color: #ffbbbb;\"></div>";
			}
		else {
			$string .= "<div style=\"float: left; width: 20px; height: 3px; background-color: #ffbbbb; ".($t ? "margin-left: 1px;" : "")."\"></div>";
			}
		}
	$string .= "<div style=\"clear: both;\"></div></div>";

	return $string;
	}

?>
