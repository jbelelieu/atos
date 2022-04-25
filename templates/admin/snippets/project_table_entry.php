
<tr>
    <td>
        <a href="/project.php?_success=Welcome+to+your+<?php echo $title; ?>+project&id=<?php echo $id; ?>">
            <?php echo $project['title']; ?>
        </a>
    </td>
    <td>
        <?php echo $project['company_name']; ?>
    </td>
    <td>
        <?php echo $hours; ?>
    </td>
    <td>
        <?php echo $total; ?>
    </td>
    <td class="textRight">
        <a
            onclick="return confirm('This will delete the project and all associated data - are you sure?')"
            href="index.php?action=deleteProject&id=<?php echo $project['id']; ?>">
            <?php echo putIcon('fi-sr-trash'); ?>
        </a>
    </td>
</tr>
