<tr>
<td><?php echo $logo; ?></td>
<td><?php echo $client['title']; ?></td>
<td><?php echo $client['address']; ?></td>
<td><?php echo $client['phone']; ?></td>
<td><?php echo $client['email']; ?></td>
<td><?php echo $client['website']; ?></td>
<td><?php echo $totalClientValue; ?></td>
<td class="textRight">
    <a
        onclick="return confirm('This will delete the client - are you sure?')"
        href="/index.php?action=deleteCompany&id=<?php echo $client['id']; ?>">
        <?php echo putIcon('fi-sr-trash'); ?>
    </a>
</td>
</tr>
