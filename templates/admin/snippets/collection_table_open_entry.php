
<tr class="<?php echo $rowClass; ?> taskTopLine noHighlight">
    <td class="checkbox">
        <input type="checkbox" name="move[]" value="<?php echo $story['id']; ?>" />
    </td>
    <td class="label">
        <?php echo $label; ?>
    </td>
    <td>
        <?php echo $hourSelect; ?>
    </td>
    <td>
        <?php echo $typeSelect; ?>
    </td>
    <td>
        <input
            type="date"
            autocomplete="off"
            style="width:120px;"
            name="story[<?php echo $story['id']; ?>][ended_at]"
            value="<?php echo $endedAt; ?>" />
    </td>
    <td>
        <input
            type="number"
            step="0.25"
            autocomplete="off"
            style="width:80px;"
            name="story[<?php echo $story['id']; ?>][hours]"
            value="<?php echo $hours; ?>" />
    </td>
    <td class="textRight">
        <div class="emoji_bump">
            <?php echo $options; ?><a
            title="Delete"
            onclick="return confirm('This will delete the task - are you sure?')"
            href="<?php echo $deleteLink; ?>"><?php echo putIcon('icofont-delete'); ?></a>
        </div>
    </td>
</tr>
<tr class="noHighlight taskBottom">
    <td colspan="7" class="taskInput">
        <input
            type="text"
            autocomplete="off"
            style="width:100%;"
            name="story[<?php echo $story['id']; ?>][title]"
            value="<?php echo $story['title']; ?>" />
    </td>
</tr>