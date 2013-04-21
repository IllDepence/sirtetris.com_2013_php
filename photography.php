<?php

$iinfo = getimages(1);
$image = $iinfo[0];
$iwidth = $iinfo[1];
$iheight = $iinfo[2];
$fsize = $iinfo[3];

$prevpic = (isset($_GET['pi']) ? $image[$_GET['pi']] : $image[count($image)-1]);
	$filesize = substr($fsize[$prevpic]/(1024*1024), 0, 4);
	$dimensions = $iwidth[$prevpic]."&times;".$iheight[$prevpic];
	$title = fixname(substr(str_replace(".jpg", "", $prevpic), 7));
	$date = "20".substr($prevpic, 0, 2)."-".substr($prevpic, 2, 2)."-".substr($prevpic, 4, 2);
	$max = count($image)-1;
	$curr = (isset($_GET['pi']) ? $_GET['pi'] : $max);
	$next = $curr-1;
	$prev = $curr+1;

?>

<div id="floatbox_r">
	<div class="innercontent_r">
	<?php

	echo "<div style=\"float: left;\"><h2>Grid</h2><div style=\"height: 3px;\"></div><span id=\"shift\" style=\"display: none;\">".(isset($_GET['shift']) ? $_GET['shift'] : "1")."</span></div>";
	$icnt = count($image)-1;
	rsort($image);
	$imgprintstr = "";
	$imgstr = "";
	foreach($image as $i) {
		$imgstr .= $i.($icnt ? "," : "");
		$imgprintstr .= "<div id=\"ipos".$icnt."\"><a href=\"?c=photography&amp;pi=".$icnt."\"><img src=\"img/photography/grid/".$i."\" alt=\"".$i."\" /></a></div>";
		$icnt--;
		}
	echo "<div id=\"jscontrols\" style=\"display: none;\"><span class=\"a\" onMouseOver=\"this.style.cursor='pointer'\" onclick=\"moveimages(".(count($image)).", 1, '".$imgstr."')\">&uarr;</span> <span class=\"a\" onMouseOver=\"this.style.cursor='pointer'\" onclick=\"moveimages(".(count($image)).", -1, '".$imgstr."')\">&darr;</span></div><div style=\"clear: both;\"></div>".$imgprintstr;
	echo '<script type="text/javascript">document.getElementById("jscontrols").setAttribute("style", "float: left; margin-left: 7px; position: relative; top: 1px;");</script>';
	?>
	</div>
</div>

<div id="floatbox_l">
	<div class="innercontent_l">
		<?php
		
		echo 	"<div style=\"float: left; margin: 0px 0px 1px 1px;\"><h2>".$title."</h2></div>".
			"<div style=\"float: right; margin-right: 2px;\">";

		if($curr < $max && $curr > 0) {
			echo "&nbsp;<span id=\"pr\"><a href=\"?c=photography&amp;pi=".$prev."\" title=\"previous\">&laquo;</a></span>&nbsp;".
			"<span id=\"nx\"><a href=\"?c=photography&amp;pi=".$next."\" title=\"next\">&raquo;</a></span>";
		}
		if($curr == 0) {
		echo "&nbsp;<span id=\"pr\"><a href=\"?c=photography&amp;pi=".$prev."\" title=\"previous\">&laquo;</a></span>&nbsp;&raquo;";
			}

		if($curr == $max) {
			echo "&nbsp;&laquo;&nbsp;<span id=\"nx\"><a href=\"?c=photography&amp;pi=".$next."\" title=\"next\">&raquo;</a></span>";
			}

		echo 	"</div>".
			"<div class=\"clear\"></div>".
	
		"<img src=\"img/photography/preview/".$prevpic."\" alt=\"".$prevpic."\" />".

		"<div style=\"float: right;\">".
				"<a href=\"img/photography/fullsize/".$prevpic."\" title=\"download full-size image\">".
				"&#10132; download (".$dimensions."px &middot; ".$filesize."mb)</a>".
			"</div>";
		?>
	</div>

	<?php
		if(isset($_GET['shift'])) echo "<script type=\"text/javascript\">document.getElementById('shift').innerHTML='1';moveimages(".count($image).", ".(($_GET['shift']-1)*-1).", \"".$imgstr."\");</script>";
	?>

</div>
