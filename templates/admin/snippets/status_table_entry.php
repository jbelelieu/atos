
<tr>
<td>
    <?php echo $icon; ?>
</td>
<td>
    <?php echo $title; ?>
</td>
<td>
    <?php echo $isBillable; ?>
</td>
<td>
    <?php echo $isComplete; ?>
</td>
<td class="textRight">
    <a
        onclick="return confirm('This will delete the status - are you sure?')"
        href="/settings.php?action=deleteStatus&id=<?php echo $id; ?>">
            <?php echo putIcon('fi-sr-trash'); ?>
    </a>
</td>
</tr>
