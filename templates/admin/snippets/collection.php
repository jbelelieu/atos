<?php if ($tripFlag) { ?>
<details>
    <summary>
        <h3><?php echo $collection['title']; ?></h3>
        <div class="clearFix"></div>
    </summary>
<?php } ?>


<hr />

<h2 style="margin-bottom: 24px;">
    <?php echo $collection['title']; ?>
</h2>

<?php if ($isProjectDefault) { ?>

    <a name="unorganized"></a>
    <a class="unorgLink" href="#top">View Open</a>

<?php } else { ?>

    <div class="clearFix"></div>
    <h5 class="bubble noMarginTop">Open</h5> <a class="unorgLink" href="#unorganized">View Unorganized</a>

<?php } ?>

<!-- Open stories table -->
<form
    id="open-table"
    class="preventLeaving"
    action="/project?id=<?php echo $collection['project_id']; ?>"
    method="post">
    <input type="hidden" name="action" value="updateStories" />
    <input type="hidden" name="project_id" value="<?php echo $collection['project_id']; ?>" />

    <table id="table-all" class="allStoriesInCollection unorganized">
    <thead>
    <tr>
    <th width="80">ID</th>
    <th width="140">Rate Type</th>
    <th width="140">Type</th>
    <th width="32"></th>
    <th width=>Title</th>
    <th width="160"></th>
    </tr>
    </thead>
    <tbody>
        <?php echo $openStories; ?>
        <tr>
        <td colspan="4" class="textRight"></td>
        <td colspan="2">
            <button type="submit">Update Tasks</button>
        </td>
        </tr>
    </tbody>
    </table>
</form>

<?php if (!$isProjectDefault) { ?>
<div class="" style="margin-top: 12px;">
    <!-- Billable stories table -->

    <hr class="lighter" />
    <h5>Completed &amp; Billable</h5>

    <form
        id="billable-table"
        class="preventLeaving"
        action="/project?id=<?php echo $collection['project_id']; ?>"
        method="post">
        <input type="hidden" name="action" value="updateStories" />
        <input type="hidden" name="project_id" value="<?php echo $collection['project_id']; ?>" />

        <table class="allStoriesInCollection">
        <thead>
            <tr>
            <th width="80">ID</th>
            <th width="180">Rate Type</th>
            <th width="150">Type</th>
            <th width="32"></th>
            <th width="120">Completed</th>
            <th width="75">Hours</th>
            <th width=>Title</th>
            <th width="160"></th>
            </tr>
        </thead>
        <tbody>
            <?php echo $otherStories; ?>
            <tr>
            <td colspan="5" class="textRight">
                <button type="button" onClick="window.location='/invoice?collection=<?php echo $collection['id']; ?>&save=1'">Generate & Save Invoice</button> <button type="button" onClick="window.open('/invoice?collection=<?php echo $collection['id']; ?>')">Preview Invoice</button>
            </td>
            <td class="bold"><?php echo $hours; ?></td>
            <td colspan="2">
                <button type="submit">Update Stories</button>
            </td>
            </tr>
        </tbody>
        </table>
    </form>
</div>
<?php } ?>

<?php if ($tripFlag) { ?>
</details>
<?php } ?>

<script type="text/javascript">
    preventUnloadBasedOnFormChanges('open-table');
    preventUnloadBasedOnFormChanges('billable-table');
</script>
