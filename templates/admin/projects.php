
<div class="holder">

<h2>Project <?php echo $project['title']; ?></h2>
<button type="button" onclick="toggleDiv('createStory')" class="a">Create Task</button>

<div id="createStory" class="sunk hide">
    <h4>Create a Task</h4>
    <form action="/project?id=<?php echo $project['id']; ?>" method="post">
        <div class="threeColumns">
            <div>
            <label>Collection</label>
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

<div class="clearFix"></div>

<form action="/project?id=<?php echo $project['id']; ?>" method="post">
    <div id="createCollection" class="sunk less">
        <div id="collections" class="padLessBottom">
            <?php echo $collections; ?>
            <input type="text" style="width:150px;" placeholder="New Collection Title" required="required" autocomplete="off" name="title" /> <button type="submit">Create</button>
        </div>
    </div>
    <input type="hidden" name="project_id" value="<?php echo $project['id']; ?>" />
    <input type="hidden" name="action" value="createCollection" />
</form>

<?php echo $collectionsRendered; ?>

</div>