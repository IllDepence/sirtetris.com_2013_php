<?php
ob_start();

	echo "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>";

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
<title>Generat0r</title>
</head>

<body>

<?php

include "functions.php";

$movieline = file("movies", FILE_IGNORE_NEW_LINES);

foreach ($movieline as $m) {

	if (strlen($m) == 6) {
		if(substr($m, 0, 3) == "---") $m_seen = substr($m, 3, 2);
		else $y_seen = substr($m, 1, 4);
		}

	if (strlen($m) > 6) {
			
		$chunk = explode(",,", $m);
		$imdbid = substr($chunk[2], 1);

		if(file_exists("cache/".$imdbid."/data")) continue;

		$imdbnfo = imdbinfo($imdbid);
		imdb_cache($imdbid, $imdbnfo[0], $imdbnfo[1], $imdbnfo[2], $imdbnfo[3], $imdbnfo[4]);
		}

	}

?>


</body>

</html>
