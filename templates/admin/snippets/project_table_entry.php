
<tr>
    <td>
        <a href="/project?_success=Welcome+to+your+<?php echo $project['title']; ?>+project&id=<?php echo $project['id']; ?>">
            <?php echo $project['title']; ?>
        </a>
    </td>
    <td>
        <?php echo $project['code']; ?>
    </td>
    <td>
        <?php echo $project['client_name']; ?>
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
            title="Delete"
            onclick="return confirm('This will delete the project and all associated data - are you sure?')"
            href="<?php echo $deleteLink; ?>">
                <?php echo putIcon('icofont-delete'); ?>
        </a>
    </td>
</tr>
