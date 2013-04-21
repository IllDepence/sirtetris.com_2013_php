<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>
<a href="acp.php">reload</a>

<?php
include 'functions.php';

$entries_json = file_get_contents('src/blog/entries.json');
$entries = json_decode($entries_json);
usort($entries, 'cmp_obj_by_date_attr');

if(strlen($_GET['edit'])) {
	foreach($entries as $e) {
		if($e->id == $_GET['edit']) {
			$to_edit = $e;
			break;
			}
		}
	}

echo '<form method="POST" action="acp.php">'.
	'headline: <input type="text" name="headline" value="'.(strlen($_GET['edit']) ? htmlspecialchars($to_edit->headline) : '').'" /><br />'.
	'picture: <input type="text" name="image" value="'.(strlen($_GET['edit']) ? $to_edit->image : '').'" /><br />'.
	'text: <textarea name="text" style="width: 1000px; height: 400px;">'.(strlen($_GET['edit']) ? htmlspecialchars($to_edit->text) : '').'</textarea><br />'.
	(strlen($_GET['edit']) ? '<input type="hidden" name="replace" value="'.$to_edit->id.'"/>' : '').
	'<input type="submit" />'.
	'</form><br />';

foreach($entries as $e) echo $e->headline.' (<a href="?del='.$e->id.'">delete</a> / <a href="?edit='.$e->id.'">edit</a>)<br />';

echo '<br />';

if(strlen($_POST['headline'])>0 && strlen($_POST['text'])>0) {
	$new_entry = new stdClass();
	if(isset($_POST['replace']) && strlen($_POST['replace'])>0) {
		foreach($entries as $i => $e) {
			if($e->id == $_POST['replace']) {
				array_splice($entries, $i, 1);
				$new_entry->date = $e->date;
				break;
				}
			}
		}
	else {
		$new_entry->date = date('Y-m-d');
		}
	$new_entry->headline = $_POST['headline'];
	$new_entry->text = $_POST['text'];
	$new_entry->image = $_POST['image'];
	$new_entry->id = hash('adler32', $new_entry->text, false);
	array_push($entries, $new_entry);

	$entries_json = json_encode($entries);
	$fh = fopen('src/blog/entries.json', 'w');
	fwrite($fh, $entries_json);
	fclose($fh);
	echo 'Enrty added.';
	}
else if(isset($_GET['del'])) {
	foreach($entries as $i => $e) {
		if($e->id == $_GET['del']) {
			array_splice($entries, $i, 1);
			break;
			}
		}
	$entries_json = json_encode($entries);
	$fh = fopen('src/blog/entries.json', 'w');
	fwrite($fh, $entries_json);
	fclose($fh);
	echo 'Enrty deleted.';
	}
else {
	echo 'Fill in the form.';
	}

?>

</body>
</html>
