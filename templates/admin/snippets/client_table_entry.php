<tr>
<td><?php echo $logo; ?></td>
<td><?php echo $client['title']; ?></td>
<td><?php echo $client['address']; ?></td>
<td><?php echo $client['phone']; ?></td>
<td><?php echo $client['email']; ?></td>
<td>
    <?php if ($url) { ?>
    <a href="<?php echo $url; ?>" target="_blank"><?php echo putIcon('icofont-link'); ?></a>
    <?php } ?>
</td>
<td><?php echo $totalClientValue; ?></td>
<td class="textRight">
    <a
        title="Delete"
        onclick="return confirm('This will delete the client and all associated data, including projects, collections, and tasks. Are you sure?')"
        href="<?php echo $deleteLink; ?>">
        <?php echo putIcon('icofont-delete'); ?>
    </a>
</td>
</tr>
