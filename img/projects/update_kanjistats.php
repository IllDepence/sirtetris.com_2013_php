<?php

require_once('/homez.151/sirtetri/www/keys.php');

if (($_FILES['statsimg']['name'] == 'output.png') && ($_POST['token'] == $stats_token)) {
	if ($_FILES['statsimg']['error'] > 0) {
		echo 'Return Code: '.$_FILES['statsimg']['error'];
		}
	else {
		move_uploaded_file($_FILES['statsimg']['tmp_name'], getcwd().'/kanjistats.png');
		echo 'Done';
		}
	}
else {
	echo 'Invalid file or key';
	}

?> 
