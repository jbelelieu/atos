<?php if ($tripFlag) { ?>

<details>
    <summary>
        <h4 class="bubble"><?php echo $collection['title']; ?></h4>
        <div class="clearFix"></div>
    </summary>

<?php } ?>

    <h4 class="bubble">
        <?php echo $collection['title']; ?>
    </h4>

<?php if ($isProjectDefault) { ?>

    <a name="unorganized"></a>
    <a class="unorgLink" href="#top">View Open</a>

<?php } else { ?>

    <div class="clearFix"></div>
    <h4 class="bubble noMarginTop">Open</h4> <a class="unorgLink" href="#unorganized">View Unorganized</a>

<?php } ?>

<!-- Open stories table -->
<table class="allStoriesInCollection">
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
    <h4 class="bubble">Billable</h4>

    <form
        class="preventLeaving"
        action="/project?id=<?php echo $collection['project_id']; ?>"
        method="post">
        <input type="hidden" name="action" value="updateStories" />
        <input type="hidden" name="project_id" value="<?php echo $collection['project_id']; ?>" />

        <table class="allStoriesInCollection">
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
                <button type="button" onClick="window.open('/invoice?collection=<?php echo $collection['id']; ?>')">Preview Invoice</button> <button type="button" onClick="window.location='/invoice?collection=<?php echo $collection['id']; ?>&save=1'">Generate & Save Invoice</button>
            </td>
            <td><?php echo $hours; ?></td>
            <td colspan="2">
                <button type="submit">Update Stories</button>
            </td>
            </tr>
        </tbody>
        </table>
    </form>

<?php } ?>

<?php if ($tripFlag) { ?>
</details>
<?php } ?>
