<?php if ($tripFlag) { ?>
<details>
    <summary>
        <h2><?php echo $collection['title']; ?></h2>
        <div class="clearFix"></div>
    </summary>
<?php } ?>

<?php if ($isProjectDefault) { ?>

    <a name="unorganized"></a>

    <hr />
    <h2 style="margin-bottom: 24px;">
        <?php echo $collection['title']; ?>
    </h2>
    <div class="clearFix"></div>

    <a class="unorgLink" href="#top">View Open</a> <a class="unorgLink" href="#completed">View Completed &amp; Billable</a>

<?php } else { ?>

    <a name="open"></a>

    <hr />
    <h2 style="margin-bottom: 24px;">
        <?php echo $collection['title']; ?>
    </h2>
    <div class="clearFix"></div>
    
    <h5 class="bubble noMarginTop">Open</h5> <a class="unorgLink" href="#unorganized">Unorganized</a> <a class="unorgLink" href="#completed">Completed &amp; Billable</a> <a class="unorgLink" href="/project?action=moveOpenToNextCollection&id=<?php echo $_GET['id']; ?>">Move All To Next Collection</a>

<?php } ?>

<!-- Open stories table -->
<form
    id="open-table"
    class="preventLeaving"
    action="/project?id=<?php echo $collection['project_id']; ?>"
    method="post">
    <input type="hidden" name="action" value="updateStories" />
    <input type="hidden" name="from" value="not_completed" />
    <input type="hidden" name="collection_id" value="<?php echo $collection['id']; ?>" />
    <input type="hidden" name="project_id" value="<?php echo $collection['project_id']; ?>" />

    <table id="table-all" class="allStoriesInCollection unorganized">
    <thead>
    <tr class="noHighlight">
        <th width="32"></th>
        <th width="90">ID</th>
        <th width="250">Rate Type</th>
        <th width="250">Type</th>
        <th width="80">Completed</th>
        <th width="90">Units</th>
        <th width=""></th>
    </tr>
    </thead>
    <tbody>
        <?php echo $openStories; ?>
        <tr class="noHighlight">
        <td colspan="5">
            <button type="submit">Move</button>
        </td>
        <td class="bold"><?php echo $openHours; ?></td>
        <td class="textRight">
            <button type="submit">Update</button>
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
    <h5>Completed &amp; Billable</h5><a class="unorgLink" href="#top">View Open</a> <a class="unorgLink" href="#unorganized">View Unorganzied</a>

    <form
        id="billable-table"
        class="preventLeaving"
        action="/project?id=<?php echo $collection['project_id']; ?>"
        method="post">
        <input type="hidden" name="action" value="updateStories" />
        <input type="hidden" name="from" value="completed" />
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
                <button type="submit">Update</button>
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
