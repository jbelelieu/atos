
<div class="holder">

    <h2>Displaying <?php echo $totalResults; ?> for "<?php echo $query; ?>"</h2>

    <table id="table-all" class="allStoriesInCollection unorganized">
    <thead>
    <tr class="noHighlight">
        <th width="80">ID</th>
        <th width="">Title</th>
        <th width="">Project</th>
        <th width="">Collection</th>
        <th width="">Type</th>
        <th width="">Rate</th>
        <th width="">Status</th>
    </tr>
    </thead>
    <tbody>
        <?php foreach ($results as $aTask) { ?>
            <tr>
                <td>
                    <?php echo $aTask['show_id']; ?>
                </td>
                <td>
                    <?php echo $aTask['title']; ?>
                </td>
                <td>
                    <a href="/project?id=<?php echo $projectId; ?>"><?php echo $aTask['projectTitle']; ?></a>
                </td>
                <td>
                    <?php echo $aTask['collectionTitle']; ?>
                </td>
                <td>
                    <?php echo $aTask['typeTitle']; ?>
                </td>
                <td>
                    <?php echo $aTask['rateTypeTitle']; ?>
                </td>
                <td>
                    <?php echo $aTask['statusTitle']; ?>
                </td>
            </tr>
        <?php } ?>
    </tbody>
    </table>
</div>

<script type="text/javascript">
    preventUnloadBasedOnFormChanges('handoff-table');
</script>
