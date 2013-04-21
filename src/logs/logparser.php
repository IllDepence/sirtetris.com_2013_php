<?php

if ($_POST['li']) {
	$limit = $_POST['li'];
	setcookie('limit', $limit);
	}
else {
	$limit = 25;
	}

if ($_POST['rr']) {
	$rate = $_POST['rr'];
	setcookie('rate', $rate);
	}
else {
	$rate = 1;
	}

if (!isset($_POST['nb']) && isset($_POST['li'])) {
	$nobots = 'off';
	}
else {
	$nobots = 'on';
	}

if ($_COOKIE['limit']) {
	$limit = $_COOKIE['limit'];
	}

if ($_COOKIE['rate']) {
	$rate = $_COOKIE['rate'];
	}

$logs_json_lines = array_reverse(file('logs.json'));
foreach($logs_json_lines as $key => $l) {
	if($key > 10000) break;
	$logs[] = json_decode($l);
	}

$blocked = array();
if($nobots == 'on' ) {
	$templogs = $logs;
	$logs = array();
	foreach($templogs as $l) {
		if(!preg_match('/bot|clr|crawler|spider/i', $l->user_agent)) {
			$logs[] = $l;
			}
		else {
			if(!in_array($l->user_agent, $blocked)) $blocked[] = $l->user_agent;
			}
		}
	}
?>

<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<style>
* {
margin: 0px;
padding: 0px;
}
body {
background-color: #f9f9f9;
font-family: sans-serif;
font-size: 10px;
color: #111111;
}
a, a:visited, a:active, a:focus {
color: #111188;
text-decoration: none;
}
a:hover {
color: #000000;
text-decoration: none;
}
div#container {
}
div#head {
font-weight: bold;
margin-bottom: 10px;
}
div#setup {
font-weight: bold;
margin: 10px 0px 30px 0px;
}
td {
font-size: 10px;
padding: 1px 3px 1px 3px;
min-height: 12px;
white-space: nowrap;
overflow: hidden;
display: inline-block;
}
#tablehead td {
font-weight: bold; 
}
td.daychanged {
background-color: #e5e5e5;
}
table {
border-collapse: collapse;
table-layout: fixed;
}
tr {
border-style: solid;
border-width: 0px 0px 1px 0px;
border-color: #000000;
}
tr:hover {
background-color: #e5e5e5;
}
</style>
<?php

if ($rate > 1) {
	echo '<meta http-equiv="Refresh" content="'.$rate.'; URL=#">';
	}

?>
<link rel="SHORTCUT ICON" href="favicon.ico" />
</head>
<body>

<div id="head">
<a href="http://cqcounter.com/whois/">whois</a> &middot; <a href="http://www.useragentstring.com/pages/All/">useragents</a> &middot; <?php echo count($logs).' entries'; ?>
</div>

<?php

echo '<table id="table">'.
	'<tr id="tablehead">'.
		'<td class="hits">#</td>'.
		'<td class="time">time</td>'.
		'<td class="ip">ip</td>'.
		'<td class="part">referrer</td>'.
		'<td class="part">query</td>'.
		'<td class="last">user agent</td>'.
	'</tr>';

$path_to_index = $_SERVER['SERVER_NAME'].substr($_SERVER['REQUEST_URI'], 0, -22).'index.php';
$prevdate = '';
$count = 0;
foreach ($logs as $l) {
	$currdate = date('Y-m-d', $l->timestamp);
	if($currdate != $prevdate) {
		echo 	'<tr>'.
				'<td class="daychanged" colspan="6">'.$currdate.'</td>'.
			'</tr>';
		}
	//$ip_parts = explode('.', $l->remote_addr);
	echo 	'<tr onClick="expand_cell(this);">'.
			'<td class="hits">'.$l->hits.'</td>'.
			'<td class="time">'.date('H:i:s', $l->timestamp).'</td>'.
			//'<td class="ip">'.$ip_parts[0].'.'.$ip_parts[1].'.*.*</td>'.
			'<td class="ip">'.$l->remote_addr.'</td>'.
			'<td class="part"><a href="'.$l->http_referer.'">'.$l->http_referer.'</a></td>'.
			'<td class="part"><a href="http://'.$path_to_index.'?'.$l->query_string.'">'.$l->query_string.'</a></td>'.
			'<td class="last">'.$l->user_agent.'</td>'.
		'</tr>';
	$count++;
	$prevdate = $currdate;
	if($count >= $limit) break;
	}
?>
</table>

<div id="setup">
<?php

echo '<form action="" method="post">'.
	'refreshrate>1 <input type="text" name="rr" value="'.$rate.'" size="3" /> limit <input type="text" name="li" value="'.$limit.'" size="5" /> <input type="submit" value="go" />&emsp;<a href="logparser.php">refresh</a>'.
	'&emsp;<input type="checkbox" name="nb"'.($nobots=='on'  ? ' checked="checked"' : '').' onChange="this.form.submit()" style="position: relative; top: 3px;"/> hide bots'.
	'</form>';

?>
</div>


<script type="text/javascript">
function expand_cell(elem) {
	var tds = elem.childNodes;
	for(var i=0; i<tds.length; i++) {
		tds[i].setAttribute('style', tds[i].getAttribute('style') + '; white-space: normal;');
		}
	elem.setAttribute('onClick', 'collapse_cell(this)');
}
function collapse_cell(elem) {
	var tds = elem.childNodes;
	for(var i=0; i<tds.length; i++) {
		tds[i].setAttribute('style', tds[i].getAttribute('style') + '; white-space: nowrap;');
		}
	elem.setAttribute('onClick', 'expand_cell(this)');
}

var scrollbar = 16;
var full_width = window.window.innerWidth - scrollbar;
var hits_width = 30;
var time_width = 57;
var ip_width = 100;
var rest_width = full_width - (hits_width + time_width + ip_width + 6*6);
var part_width = (rest_width/3)-((rest_width/3)%1);
var last_width = rest_width - part_width*2;

var hits_cells = document.getElementsByClassName('hits');
for(var i=0; i<hits_cells.length; i++) hits_cells[i].setAttribute('style', 'width: ' + hits_width + 'px');
var time_cells = document.getElementsByClassName('time');
for(var i=0; i<time_cells.length; i++) time_cells[i].setAttribute('style', 'width: ' + time_width + 'px');
var ip_cells = document.getElementsByClassName('ip');
for(var i=0; i<ip_cells.length; i++) ip_cells[i].setAttribute('style', 'width: ' + ip_width + 'px');
var part_cells = document.getElementsByClassName('part');
for(var i=0; i<part_cells.length; i++) part_cells[i].setAttribute('style', 'width: ' + part_width + 'px');
var last_cells = document.getElementsByClassName('last');
for(var i=0; i<last_cells.length; i++) last_cells[i].setAttribute('style', 'width: ' + last_width + 'px');
var dc_cells = document.getElementsByClassName('daychanged');
for(var i=0; i<dc_cells.length; i++) dc_cells[i].setAttribute('style', 'width: ' + (full_width-6) + 'px');
</script>

</body>
</html>
