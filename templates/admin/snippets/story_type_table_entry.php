
<tr>
<td>
    <?php echo $title; ?>
</td>
<td class="textRight">
    <a
        onclick="return confirm('This will delete the story type - are you sure?')"
        href="settings.php?action=deleteStoryType&id=<?php echo $id; ?>">
            <?php echo putIcon('fi-sr-trash'); ?>
    </a>
</td>
</tr>
