<?php
	require_once('functions.php');
	log_visit();

	$navitems = array('blog', 'person', 'interests', 'stuff', 'projects', 'random', 'photography', 'links', 'contact');
	$validcontent = array_merge($navitems, array('el', 'imprint'));
	$valid = 0;
	if(isset($_GET['c'])) {
		foreach($validcontent as $v) {
			if($_GET['c'] == urlsave($v)) { $valid = 1; break; }
			}
		$content = ($valid ? $_GET['c'] : 'blog');
		}
	else {
		$content = 'blog';
		}

	$title = str_replace('_', ' ', $content);

?>

<!DOCTYPE HTML>

<!-- - - - - - - - - - - - - - - - - <html> - - - - - - - - - - - - - - - - -->
<html>

<!-- - - - - - - - - - - - - - - - - <head> - - - - - - - - - - - - - - - - -->
<head>

<link rel="SHORTCUT ICON" href="favicon.ico" />
<link rel="stylesheet" type="text/css" href="style.css" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<?php echo '<title>sirtetris/'.$title.'</title>'; ?>

<meta name="description" content="Personal website of Tarek Saier" />
<meta name="keywords" content="Tarek, Saier, IllDepence, sirtetris, blog, philosophy, photography, open source" />
<meta name="author" content="Tarek Saier" />
<meta name="medium" content="mult" />
<meta name="robots" content="index,follow" />

<script type="text/javascript" src="functions.js"></script>

<!-- - - - - - - - - - - - - - - - - <style> - - - - - - - - - - - - - - - - -->
<style type="text/css">

<?php
	if (preg_match('/Firefox/i', $_SERVER['HTTP_USER_AGENT'])) {
		echo 'body { overflow: -moz-scrollbars-vertical !important; }';
		}
?>

</style>
<!-- - - - - - - - - - - - - - - - - </style> - - - - - - - - - - - - - - - - -->

</head>
<!-- - - - - - - - - - - - - - - - - </head> - - - - - - - - - - - - - - - - -->


<!-- - - - - - - - - - - - - - - - - <body> - - - - - - - - - - - - - - - - -->
<body>

<div id="headbox">
	<div id="innerheadbox">
	<?php
		$label_prop = array('USER AGENT' => 'HTTP_USER_AGENT', 'IP ADDRESS' => 'REMOTE_ADDR', 'REFERER' => 'HTTP_REFERER', 'QUERY' => 'QUERY_STRING');
		foreach($label_prop as $l => $p) echo '&gt; '.$l.': '.((isset($_SERVER[$p]) && $_SERVER[$p] != '') ? '<span>'.$_SERVER[$p].'</span>' : '<span><em>empty</em></span>').'<br />';
	?>
	</div>
	<div id="navbox">
	<?php
		for($i=0; $i<14; $i++) {
			if($i<count($navitems)) echo '<a href="?c='.urlsave($navitems[$i]).'"><div class="navitem">'.$navitems[$i].'</div></a>';
			else echo '<div class="navitem"></div>';
			}
	?>
	</div>
</div>
<div id="ribbon"></div>
<div id="contentbox">
	<?php include $content.'.php';?>
</div>

<div id="footer">
	<a href="http://creativecommons.org/licenses/by-sa/3.0"><img src="img/site/ccbysa.png" alt="CC BY-SA 3.0" title="CC BY-SA 3.0" /></a>
</div>

</body>
<!-- - - - - - - - - - - - - - - - - </body> - - - - - - - - - - - - - - - - -->

</html>
<!-- - - - - - - - - - - - - - - - - </html> - - - - - - - - - - - - - - - - -->