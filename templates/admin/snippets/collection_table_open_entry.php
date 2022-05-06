
<tr>
<td class="checkbox">
    <input type="checkbox" name="move[]" value="<?php echo $story['id']; ?>" />
</td>
<td>
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
        type="text"
        autocomplete="off"
        name="story[<?php echo $story['id']; ?>][title]"
        value="<?php echo $story['title']; ?>" />
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
