
<tr>
<td>
    <span class="ticketId">
        <?php echo $story['show_id']; ?>
    </span>
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
<td class="">
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
            href="<?php echo $deleteLink; ?>">
            <?php echo putIcon('icofont-delete'); ?>
        </a>
    </div>
</td>
</tr>
