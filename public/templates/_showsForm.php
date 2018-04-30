<?php
use slelorrain\Aushowmatic\Core\Feed;

$availableShows = Feed::getAvailableShows();
?>
<div>Add a show:</div>
<form id="add_show" class="flex" method="post" action="?action=addShow">
    <?php if ($availableShows && !empty($availableShows)) { ?>
        <select name="show_name" class="flexAuto">
            <option disabled selected value>--- Select a show ---</option>
            <?php foreach ($availableShows as $name => $label) { ?>
                <option value="<?= $name ?>"><?= $label ?></option>
            <?php } ?>
        </select>
    <?php } else { ?>
        <input id="show_name" class="flexAuto" name="show_name" type="text" placeholder="Show name or ID"/>
        <input id="show_label" class="flexAuto" name="show_label" type="text" placeholder="Show label (optional)"/>
    <?php } ?>
    <input id="sumbit_add_show" class="yt-button" type="submit" value="Add"/>
</form>
