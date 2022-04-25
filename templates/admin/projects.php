<div class="collectionsTable">

<div class="standardColumns border">
    <div>
        <div class="formBox padLess">
            <form action="project.php?id=<?php echo $project['id']; ?>" method="post">
                <div class="threeColumns">
                    <div>
                    <label><b>Story</b>&nbsp;&nbsp;Collection</label>
                    <select name="collection"><?php echo $collectionSelect; ?></select>
                    </div>

                    <div>
                    <label>Type</label>
                    <select name="type"><?php echo $storyTypeSelect; ?></select>
                    </div>

                    <div>
                    <label>Rate Type</label>
                    <select name="rate_type"><?php echo $hourTypeSelect; ?></select>
                    </div>
                </div>

                <div class="columns2575">
                    <div>
                        <label>Reference Number</label>
                        <input type="text" name="show_id" required="required" autocomplete="off" value="<?php echo $nextId; ?>" />
                    </div>

                    <div>
                    <label>Description</label>
                    <input type="text" name="title" required="required" autocomplete="off" style="width:80%;" /> <button type="submit">Create</button>
                    </div>
                </div>

                <input type="hidden" name="project_id" value="<?php echo $project['id']; ?>" />
                <input type="hidden" name="action" value="createStory" />
            </form>
        </div>
    </div>
    <div>
        <div class="formBox padLessRight padLessTop padLessBottom">
            <form action="project.php?id=<?php echo $project['id']; ?>" method="post">
                <div class="twoColumns">
                    <div>
                    <label><b>Collections</b>&nbsp;&nbsp;Title</label>
                    <input type="text" required="required" autocomplete="off" name="title" />
                    </div>

                    <div style="align-self: end;">
                        <input type="hidden" name="project_id" value="<?php echo $project['id']; ?>" />
                        <input type="hidden" name="action" value="createCollection" />
                        <button type="submit">Create</button>
                    </div>
                </div>
            </form>
        </div>

        <div id="collections" class="padLessBottom">
            <?php echo $collections; ?>
        </div>
    </div>
</div>

<div class="storyTable">
    <div>
        <h3 class="bubble blueBubble">Project <?php echo $project['title']; ?></h3>
        <?php echo $collectionsRendered; ?>
    </div>
</div>
<script type="text/javascript">
    /**
     * Prevents accidentally going back and losing unsaved changes.
     */
    $('.preventLeaving').data('serialize',$('#form').serialize());

    $(window).bind('keydown', function(event) {
    if (event.ctrlKey || event.metaKey) {
        switch (String.fromCharCode(event.which).toLowerCase()) {
        case 's':
            event.preventDefault();
            alert('ctrl-s');
            break;
        case 'f':
            event.preventDefault();
            alert('ctrl-f');
            break;
        case 'g':
            event.preventDefault();
            alert('ctrl-g');
            break;
        }
    }
}
</script>