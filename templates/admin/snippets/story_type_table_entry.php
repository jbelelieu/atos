
<tr class="noHighlight">
<td>
    <input
        type="text"
        name="item[<?php echo $id; ?>][title]"
        value="<?php echo $title; ?>"
        required="required"
        style="width:100%;" />
</td>
<td class="textRight">
    <div class="emoji_bump">
        <a
            title="Delete"
            onclick="return confirm('This will delete the task type - are you sure?')"
            href="<?php echo $deleteLink; ?>">
                <?php echo putIcon('icofont-delete'); ?>
        </a>
    </div>
</td>
</tr>
