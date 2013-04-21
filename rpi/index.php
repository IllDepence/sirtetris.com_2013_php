<?php

if(isset($_POST['token'])) {
	if($_POST['token'] == 'not on github, sorry ;)') {
		file_put_contents('curr_rpi_ip', $_SERVER['REMOTE_ADDR']);
		}
	}
else {
	$ip = file_get_contents('curr_rpi_ip');
	header('Location: https://'.$ip.':3739/');
	}

?>
