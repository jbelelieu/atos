
<tr class="<?php echo $rowClass; ?>">
    <td>
        <?php echo $label; ?>
    </td>
    <td>
        <?php echo $hourSelect; ?>
    </td>
    <td>
        <?php echo $typeSelect; ?>
    </td>
    <td class="textCenter">
        <div class="emoji_bump">
            <?php echo putIcon($story['status_emoji'], $story['status_color']); ?>
        </div>
    </td>
    <td>
        <input
            type="date"
            autocomplete="off"
            name="story[<?php echo $story['id']; ?>][ended_at]"
            value="<?php echo $endedAt; ?>" />
    </td>
    <td>
        <input
            type="number"
            step="0.25"
            autocomplete="off"
            name="story[<?php echo $story['id']; ?>][hours]"
            value="<?php echo $hours; ?>" />
    </td>
    <td>
        <input
            type="text"
            autocomplete="off"
            style="width:100%;"
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
