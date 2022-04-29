
<tr class="<?php if ($is_hidden) {
    echo "notLive";
} ?>">
<td>
    <input
        type="text"
        name="item[<?php echo $id; ?>][title]"
        value="<?php echo $title; ?>"
        required="required"
        style="width:100%;" />
</td>
<td>
    $<input
        type="number"
        min=0
        name="item[<?php echo $id; ?>][rate]"
        value="<?php echo $rate; ?>"
        required="required"
        style="width:100px;" />
</td>
<td class="textRight">
    <div class="emoji_bump">
        <a
            title="Delete"
            onclick="return confirm('This will delete the status - are you sure?')"
            href="<?php echo $deleteLink; ?>">
                <?php echo putIcon('icofont-delete'); ?>
        </a>
    </div>
</td>
</tr>
