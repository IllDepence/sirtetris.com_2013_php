<?php

require_once('recaptchalib.php');
require_once('keys.php');
$privatekey = $recaptcha_privatekey;
$resp = recaptcha_check_answer ($privatekey,
	$_SERVER['REMOTE_ADDR'],
	$_POST['recaptcha_challenge_field'],
	$_POST['recaptcha_response_field']);

echo '<!DOCTYPE HTML><html><head><meta http-equiv="Refresh" content="1; URL='.$_SERVER['HTTP_REFERER'].'"></head><body><br /><br /><p style="text-align: center;">';

if (!$resp->is_valid) {
	echo 'The CAPTCHA wasn\'t entered correctly.';
	}
else if(strlen($_POST['text']) < 1) {
	echo 'No text.';
	}
else {
	$comments_json = file_get_contents('src/blog/comments.json');
	$comments = json_decode($comments_json);

	$new_comment = new stdClass();
	$new_comment->text = htmlspecialchars($_POST['text']);
	$new_comment->date = date('Y-m-d H:i:s');
	$tmp_arr = $comments->$_GET['a'];
	$tmp_arr[] = $new_comment;
	$comments->$_GET['a'] = $tmp_arr;

	$comments_json = json_encode($comments);
	$fh = fopen('src/blog/comments.json', 'w');
	fwrite($fh, $comments_json);
	fclose($fh);

	echo 'Thanks for your comment. :3';
	}

echo '</p></body></html>';

?>
