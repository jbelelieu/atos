<tr class="noHighlight">
<td>
    <input
        type="text"
        name="item[<?php echo $id; ?>][title]"
        value="<?php echo $title; ?>"
        required="required"
        style="width:100%;" />
</td>
<td>
    <div class="emoji_bump textCenter">
        <?php echo $icon; ?>
    </div>
</td>
<td>
    <select name="item[<?php echo $id; ?>][is_complete_state]">
        <option value="0" <?php if (!$isComplete) {
    echo 'selected=selected';
} ?>>No</option>
        <option value="1" <?php if ($isComplete) {
    echo 'selected=selected';
} ?>>Yes</option>
    </select>
</td>
<td>
    <select name="item[<?php echo $id; ?>][is_billable_state]">
        <option value="0" <?php if (!$isBillable) {
    echo 'selected=selected';
} ?>>No</option>
        <option value="1" <?php if ($isBillable) {
    echo 'selected=selected';
} ?>>Yes</option>
    </select>
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
