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
    <h5 class="bubble noMarginTop">Open</h5> <a class="unorgLink" href="#unorganized">Unorganized</a> <a class="unorgLink" href="#completed">Completed &amp; Billable</a>

<?php } ?>

<!-- Open stories table -->
<form
    id="open-table"
    class="preventLeaving"
    action="/project?id=<?php echo $collection['project_id']; ?>"
    method="post">
    <input type="hidden" name="action" value="updateStories" />
    <input type="hidden" name="collection_id" value="<?php echo $collection['id']; ?>" />
    <input type="hidden" name="project_id" value="<?php echo $collection['project_id']; ?>" />

    <table id="table-all" class="allStoriesInCollection unorganized">
    <thead>
    <tr class="noHighlight">
        <th width="32"></th>
        <th width="90">ID</th>
        <th width="250">Rate Type</th>
        <th width="250">Type</th>
        <th width=""></th>
    </tr>
    </thead>
    <tbody>
        <?php echo $openStories; ?>
        <tr class="noHighlight">
        <td colspan="3">
            <button type="submit">Move Selected</button>
        </td>
        <td class="textRight"></td>
        <td colspan="2" class="textRight">
            <button type="submit">Update Tasks</button>
        </td>
        </tr>
    </tbody>
    </table>
</form>

<?php if (!$isProjectDefault) { ?>
<div class="" style="margin-top: 12px;">
    <!-- Billable stories table -->

    <a name="completed"></a>
    <hr />
    <h5>Completed &amp; Billable</h5>

    <form
        id="billable-table"
        class="preventLeaving"
        action="/project?id=<?php echo $collection['project_id']; ?>"
        method="post">
        <input type="hidden" name="action" value="updateStories" />
        <input type="hidden" name="collection_id" value="<?php echo $collection['id']; ?>" />
        <input type="hidden" name="project_id" value="<?php echo $collection['project_id']; ?>" />

        <table class="allStoriesInCollection">
        <thead>
            <tr class="noHighlight">
            <th width="90">ID</th>
            <th width="180">Rate Type</th>
            <th width="180">Type</th>
            <th width="80">Completed</th>
            <th width="90">Units</th>
            <th></th>
            </tr>
        </thead>
        <tbody>
            <?php echo $otherStories; ?>
            <tr class="noHighlight">
            <td colspan="4" class="textRight">
                <button type="button" onClick="window.location='/invoice?collection=<?php echo $collection['id']; ?>&save=1'">Generate &amp; Save Invoice</button> <button type="button" onClick="window.open('/invoice?collection=<?php echo $collection['id']; ?>')">Preview Invoice</button>
            </td>
            <td class="bold"><?php echo $hours; ?></td>
            <td colspan="2" class="textRight">
                <button type="submit">Update Tasks</button>
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
