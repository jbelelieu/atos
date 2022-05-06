
<tr class="noBorder taskTopLine noHighlight">
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
    <td></td>
    <td colspan="4" class="taskInput">
        <input
            type="text"
            autocomplete="off"
            name="story[<?php echo $story['id']; ?>][title]"
            value="<?php echo $story['title']; ?>" />
    </td>
</tr>
