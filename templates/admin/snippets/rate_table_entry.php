
<tr>
<td>
    <?php echo $title; ?>
</td>
<td>
    <?php echo $rate; ?>
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
