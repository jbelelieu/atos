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
<hr />

<h4 class="bubble">Billable</h4>

<form class="preventLeaving" action="project.php?id=$_GET[id]" method="post" autocomplete="on">
<input type="hidden" name="project_id" value="$_GET[id]" />
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
</tbody>
</table>

<?php } ?>


<?php if ($tripFlag) { ?>
</details>
<?php } ?>
