<?php

function block_shitheads() {
	if(preg_match('/<a\s+href="/i', $_SERVER['HTTP_USER_AGENT'])) {
		die('Dear visitor, the user agent string your browser sent this server contains an HTML &lt;a&gt; tag. This is a common practice to spam logs with bullshit links. Therefore your access to this page was revoked. If you\'re a normal human visitor not using some funny manipulated user agent string PLEASE try to access this page with another browser to get my contact information and inform me about this error.');
		}
	if(preg_match('/http:\/\/hand-made-soaps\.com/i', $_SERVER['HTTP_REFERER'])) {
		die('dear visitor, your the browser appears to have sent this server a fake HTTP referrer. This is a common practice to spam logs with bullshit links. Therefore your access to this page was revoked. If you\'re a normal human visitor not using some funny manipulated user agent string PLEASE try to access this page with another browser to get my contact information and inform me about this error.');
		}
	}

function cmp_obj_by_date_attr($a, $b) {
	$a = intval(str_replace(Array('-',' ',':'), '', $a->date));
	$b = intval(str_replace(Array('-',' ',':'), '', $b->date));
	if ($a == $b) return 0;
	return (($a > $b) ? -1 : 1);
	}

function urlsave($s) {
	return preg_replace('/\W/', '_', $s);
	}

function youtubedd($label, $id) {
	return 	"<span id=\"nojs_wrapper_".$id."\" style=\"display: inline;\"><p><a href=\"http://youtu.be/".$id."\">".$label."</a></p></span>".
		"<span id=\"js_wrapper_".$id."\" style=\"display: none;\"><p><span id=\"trigger_".$id."\">".
			"<span class=\"a\" onClick=\"dropdown('".$id."', '<iframe width=\'700\' height=\'436\' src=\'http://www.youtube.com/embed/".$id."\' frameborder=\'0\' allowfullscreen></iframe>', 'open', '".$label."')\"".
			" onMouseOver=\"this.style.cursor='pointer'\">".$label."</span>".
		"</span></p>".
		"<div id=\"cmnt_".$id."\"></div></span>".
		"<script type=\"text/javascript\">document.getElementById(\"nojs_wrapper_".$id."\").setAttribute(\"style\", \"display:none;\");".
		"document.getElementById(\"js_wrapper_".$id."\").setAttribute(\"style\", \"display:inline;\")</script>";
	}

function youtube($id) {
	return "<iframe width=\"700\" height=\"436\" src=\"http://www.youtube.com/embed/".$id."\" frameborder=\"0\" allowfullscreen></iframe>";
	}

function getimages($moreinfo=0, $givendir=0) {
	if($givendir) {
		$directory = $givendir;
		}
	else {
		if($_GET['c'] == "photography") { $add = "/fullsize"; }
		else { $add = ""; }
		$directory = "img/".$_GET['c'].$add;
		}

	$dir=opendir($directory);
	$x=0;

	while ($file = readdir ($dir)) {
		if ($file != "." && $file != "..") {
			$image[$x]=$file;
			if($moreinfo) {
				$isize=getimagesize($directory."/".$file);
				$iwidth[$file] = $isize[0];
				$iheight[$file] = $isize[1];
				$fsize[$file]=filesize($directory."/".$file);
			}
			$x++;
		}
	}

	sort($image);

	closedir($dir);
	return ($moreinfo ? array($image, $iwidth, $iheight, $fsize) : $image);
	}

function fixname($title) {
	$markup = array('-ae-', '-oe-', '-ue-', '-Ae-', '-Oe-', '-Ue-', '-ss-', '-fz-', '-az-', '-mdash-', '_', '-strn-', '-dots-', '-slash-');
	$replacement =	array('&auml;', '&ouml;', '&uuml;', '&Auml;', '&Ouml;', '&Uuml;', '&szlig', '?', '!', '&mdash;', ' ', '*', '...', '/');
	return str_replace($markup, $replacement, $title);
	}

function log_visit() {
	if ($_COOKIE['visits'] && is_numeric($_COOKIE['visits'])) {
		$hits = $_COOKIE['visits'];
		}
	else {
		$hits = 0;
	}

	$hits++;

	setcookie('visits', $hits, time()+60*60*24*365);

	$new_log_entry = new stdClass();
	$new_log_entry->timestamp = time();
	$new_log_entry->hits = $hits;
	$new_log_entry->http_referer = $_SERVER['HTTP_REFERER'];
	$new_log_entry->query_string = $_SERVER['QUERY_STRING'];
	$new_log_entry->remote_addr = $_SERVER['REMOTE_ADDR'];
	$new_log_entry->user_agent = $_SERVER['HTTP_USER_AGENT'];

	$entry_json = json_encode($new_log_entry)."\n";
	$fh = fopen('src/logs/logs.json', 'a');
	fwrite($fh, $entry_json);
	fclose($fh);
	}

?>
