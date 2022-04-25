
<tr>
<td>
    <?php echo $title; ?>
</td>
<td>
    <?php echo $rate; ?>
</td>
<td class="textRight">
    <a
        onclick="return confirm('This will delete the status - are you sure?')"
        href="settings.php?action=deleteRate&id=<?php echo $id; ?>">
            <?php echo putIcon('fi-sr-trash'); ?>
    </a>
</td>
</tr>
