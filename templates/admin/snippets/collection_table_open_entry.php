
<tr>
<td>
    <span class="bubble grayBubble">
        <?php echo $story['show_id']; ?>
    </span>
</td>
<td>
    <?php echo $story['hour_title']; ?>
</td>
<td>
    <div class="emoji_bump_sm">
        <?php echo putIcon($story['status_emoji'], $story['status_color']); ?>
    </div>
</td>
<td>
    <?php echo $story['type_title']; ?>
</td>
<td class="ellipsi">
    <?php echo $story['title']; ?>
</td>
<td class="textRight">
    <?php echo $options; ?><a
        title="Delete"
        onclick="return confirm('This will delete the story - are you sure?')"
        href="<?php echo $deleteLink; ?>">
        <?php echo putIcon('fi-sr-trash'); ?>
    </a>
</td>
</tr>
