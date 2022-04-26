
<tr>
<td>
    <?php echo $title; ?>
</td>
<td>
    <?php echo $icon; ?>
</td>
<td>
    <?php echo $isBillable; ?>
</td>
<td>
    <?php echo $isComplete; ?>
</td>
<td class="textRight">
    <a
        title="Delete"
        onclick="return confirm('This will delete the status - are you sure?')"
        href="<?php echo $deleteLink; ?>">
            <?php echo putIcon('fi-sr-trash'); ?>
    </a>
</td>
</tr>
