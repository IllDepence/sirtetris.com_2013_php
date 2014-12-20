<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8" />
</head>
<body>

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
    'tags: <input type="text" name="tags" value="'.(strlen($_GET['edit']) ? implode(',', $to_edit->tags) : '').'" /><br />'.
    'text: <textarea name="text" style="width: 1000px; height: 400px;">'.(strlen($_GET['edit']) ? htmlspecialchars($to_edit->text) : '').'</textarea><br />'.
    (strlen($_GET['edit']) ? '<input type="hidden" name="replace" value="'.$to_edit->id.'"/>' : '').
    '<input type="submit" />'.
    '</form><br />';

if(strlen($_POST['headline'])>0 && strlen($_POST['text'])>0) {
    $msg = '&gt;&gt; enrty ';
    $new_entry = new stdClass();
    $new_entry->text = $_POST['text'];
    $new_entry->tags = explode(',', $_POST['tags']);
    if(isset($_POST['replace']) && strlen($_POST['replace'])>0) {
        foreach($entries as $i => $e) {
            if($e->id == $_POST['replace']) {
                array_splice($entries, $i, 1);
                $new_entry->id = $e->id;
                $new_entry->date = $e->date;
                $msg .= 'edited';
                break;
                }
            }
        }
    else {
        $new_entry->id = hash('adler32', $new_entry->text, false);
        $new_entry->date = date('Y-m-d');
        $msg .= 'added';
        }
    $new_entry->headline = $_POST['headline'];
    $new_entry->image = $_POST['image'];
    array_push($entries, $new_entry);

    $entries_json = json_encode($entries);
    $fh = fopen('src/blog/entries.json', 'w');
    fwrite($fh, $entries_json);
    fclose($fh);
    echo $msg;
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
    echo '&gt;&gt; enrty deleted.';
    }
else {
    echo '&gt;&gt; fill in the form.';
    }

echo '<br /><br />';

$entries_json = file_get_contents('src/blog/entries.json');
$entries = json_decode($entries_json);
usort($entries, 'cmp_obj_by_date_attr');
foreach($entries as $e) echo $e->headline.' (<a href="?del='.$e->id.'">delete</a> / <a href="?edit='.$e->id.'">edit</a>)<br />';

?>

</body>
</html>
