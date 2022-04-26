<?php if ($tripFlag) { ?>
<details>
    <summary>
        <h4 class="bubble"><?php echo $collection['title']; ?></h4>
    </summary>
<?php } else { ?>
<h4 class="bubble">
    <?php echo $collection['title']; ?>
</h4>
<?php } ?>
<?php if (!$isProjectDefault) { ?>
    <div class="clearFix"></div>
    <h4 class="bubble noMarginTop">Open Stories</h4>
<?php } ?>

<!-- Open stories table -->
<table>
<thead>
<tr>
<th width="140">ID</th>
<th width="140">Rate Type</th>
<th width="42"></th>
<th width="140">Type</th>
<th width=>Title</th>
<th width="240"></th>
</tr>
</thead>
<tbody>
    <?php echo $openStories; ?>
</tbody>
</table>

<?php if (!$isProjectDefault) { ?>
    <!-- Billable stories table -->
    <hr />

    <h4 class="bubble">Billable</h4>

    <form class="preventLeaving" action="/project" method="post">
    <input type="hidden" name="project_id" value="<?php echo $collection['project_id']; ?>" />
    <input type="hidden" name="action" value="updateStories" />

    <table>
    <thead>
    <tr>
    <th width="140">ID</th>
    <th width="180">Rate Type</th>
    <th width="150">Type</th>
    <th width="42"></th>
    <th width="120">Completed</th>
    <th width="75">Hours</th>
    <th width=>Title</th>
    <th width="150"></th>
    </tr>
    </thead>
    <tbody>
    <?php echo $otherStories; ?>

    <tr>
    <td colspan="5" class="textRight">
        <button type="button" onClick="window.open('invoice.php?collection=<?php echo $collection['id']; ?>')">Preview Invoice</button> <button type="button" onClick="window.location='invoice.php?collection=<?php echo $collection['id']; ?>&save=1'">Generate & Save Invoice</button>
    </td>
    <td><?php echo $hours; ?></td>
    <td colspan="2">
        <button type="submit">Update Stories</button>
    </td>
    </tr>
    </tbody>
    </table>

<?php } ?>

<?php if ($tripFlag) { ?>
</details>
<?php } ?>
