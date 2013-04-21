<?php

// load blogentries
$entries_json = file_get_contents('src/blog/entries.json');
$blogentries = json_decode($entries_json);
usort($blogentries, 'cmp_obj_by_date_attr');

// load comments
$comments_json = file_get_contents('src/blog/comments.json');
$comments = json_decode($comments_json);

// handle permalinks
$permalink = false;
if(isset($_GET['a'])) {
	foreach($blogentries as $b) {
		if($b->id == $_GET['a']) {
			$blogentries = Array($b);
			$permalink = true;
			}
		}
	}

// handle pages
if(!$permalink) {
	$entries_per_page = 3;
	
	$num_entries = count($blogentries);
	$num_pages = ceil($num_entries/$entries_per_page);
	if(isset($_GET['p']) && is_numeric($_GET['p']) && $_GET['p']>0 && $_GET['p']<=$num_pages) {
		$currpage=$_GET['p'];
		}
	else {
		$currpage=1;
		}
	$prevpage = $currpage+1;
	$nextpage = $currpage-1;
	
	$shift = $currpage-1;
	$start = $entries_per_page*$shift;
	$end = ($start+$entries_per_page<($num_entries) ? $start+$entries_per_page : $num_entries);
	
	$all_entries = $blogentries;
	$blogentries = Array();
	for($i=$start; $i<$end; $i++) {
		$blogentries[] = $all_entries[$i];
		}
	}

//print blogentries
echo '<div id="floatbox_b">';

foreach($blogentries as $i => $b) {

	// headline
	$id = $b->id;
	echo '<div class="innercontent_b">'.
		'<div class="headline">'.
			'<h1><a href="?a='.$id.'">'.$b->headline.'</a></h1>'.
		'</div>'.
		'<div class="textbody">';

	// image if set
	if(strlen($b->image) > 4) {
		echo '<div class="imgfloat'.($i%2==0 ? 'left' : 'right').'">'.
			'<img src="img/blog/'.$b->image.'" />';
		preg_match('/.+(?=\.[a-zA-Z0-9]{1,5}$)/', $b->image, $matches);
		$source_file = $matches[0].'.src';
		$source = preg_replace('/\s/', '', file_get_contents('img/blog/'.$source_file));
		if(strlen($source) > 1) echo '<div class="imgsrc"><p><a href="'.$source.'">source</a></p></div>';
		echo '</div>';
		}

	// text
	eval('echo "'.$b->text.'";');

	// foot
	echo	'</div>'.
		'<div class="footline">'.
			'<div class="comments"><p><strong><a href="?a='.$id.'">Comments: '.count($comments->$id).'</a></strong></p></div>'.
			'<div class="date"><p>'.$b->date.'</p></div>'.
		'</div>';

	// comments if permalink is set
	if($permalink) {
		echo '<div style="height: 5px;"></div>';
		$comments = $comments->$_GET['a'] ? $comments->$_GET['a'] : Array();
		usort($comments, 'cmp_obj_by_date_attr');
		$comments = array_reverse($comments);
		foreach($comments as $i => $c) {
				$date_time_arr = explode(' ', $c->date);
				echo '<div class="comment">'.
					'<p><strong>#'.$i.'</strong>&emsp;('.$date_time_arr[0].')</p>'.
					'<p>'.str_replace(Array("\r\n", "\n\r", "\r", "\n"), '<br />', $c->text).'</p>'.
				'</div>';
			}
		echo '<p style="margin: 12px 0px 0px 10px;"><strong>Write a comment:</strong></p>'.
			'<form class="commentform" action="postcomment.php?a='.$b->id.'" method="POST">'.
				'<textarea class="commenttext" name="text"></textarea>';

		echo '<script type="text/javascript">var RecaptchaOptions={theme:\'clean\'};</script>';
		require_once('recaptchalib.php');
		$publickey = '6Leqr9cSAAAAABLmn-z98ENYqXpHXVSMUIMOfcEZ';
		echo recaptcha_get_html($publickey).

				'<input class="commentpost" type="submit" value="Post Comment">'.
				'<input class="commentreset" type="reset" value="Clear">'.
				'</form>';
		}
	echo '</div>';
	}

// page controls if permalink is not set
if(!$permalink) {
	echo '<div class="innercontent_b" style="text-align: center; padding: 0px;">'.
			($currpage < $num_pages ? '<a href="?content=blog&amp;p='.$prevpage.'" title="older enties"><div class="blog_nav blog_nav_hover"><p>&laquo;</p></div></a>' : '<div class="blog_nav"><p>&laquo;</p></div>').
			'<div class="blog_nav" style="width: 50%;"><p>';
	for($i=$num_pages; $i>0; $i--) echo ($i!=$currpage ? '<a href="?content=blog&amp;p='.$i.'" title="jump to page '.$i.'">'.$i.($i>1 ? ' ' : '').'</a>' : $i.($i>1 ? ' ' : ''));
	echo		'</p></div>'.
			($currpage > 1 ? '<a href="?c=blog&amp;p='.$nextpage.'" title="newer enties"><div class="blog_nav blog_nav_hover"><p>&raquo;</p></div></a>' : '<div class="blog_nav"><p>&raquo;</p></div>').
		'</div>';
	}

echo '</div>';

?>
