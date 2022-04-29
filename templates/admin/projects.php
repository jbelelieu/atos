
<div class="holder">

    <?php if ($totalCollections === 1) { ?>
        <p class="highlight">
            Start here by creating your first collection of stories!
        </p>
    <?php } ?>

    <form action="/project?id=<?php echo $project['id']; ?>" method="post">
        <div id="createCollection" class="sunk less">
            <div id="collections" class="padLessBottom">
                <?php echo $collections; ?>
                <input type="text" style="width:150px;" placeholder="Sprint May 1 - 15" required="required" autocomplete="off" name="title" /> <button type="submit">Create</button>
            </div>
        </div>
        <input type="hidden" name="project_id" value="<?php echo $project['id']; ?>" />
        <input type="hidden" name="action" value="createCollection" />
    </form>

    <hr />

    <h2>Project <?php echo $project['title']; ?></h2>
    <button type="button" onclick="toggleDiv('createHandOFf')" class="a">Generate Report</button>

    <div id="createHandOFf" class="sunk border pad hide bg">
        <form action="/project/report" method="get">
        <input type="hidden" name="project_id" value="<?php echo $project['id']; ?>" />

        <div class="freeColumns">
            <div>
            <label>Title</label>
            <input type="text" name="title" autocomplete="off" placeholder="Project Hand Off Checklist" />
            </div>

            <div>
            <label>Message (html ok)</label>
            <textarea name="message"></textarea>
            </div>

            <div>
            <label>Template</label>
            <select name="template" required="required">
                <?php foreach ($templates as $aTemplate => $cleanName) { ?>
                    <option value="<?php echo $aTemplate; ?>"><?php echo $cleanName; ?></option>
                <?php } ?>
            </select>
            </div>

            <div>
            <label>Include Statuses</label>
            <?php foreach ($storyStatuses as $anItem) { ?>
                <input type="checkbox" name="status[<?php echo $anItem['id']; ?>]" value="1" /> <?php echo $anItem['title']; ?><br />
            <?php } ?>
            </div>

            <div>
            <label>Include Types</label>
            <?php foreach ($storyTypes as $anItem) { ?>
                <input type="checkbox" name="type[<?php echo $anItem['id']; ?>]" value="1" /> <?php echo $anItem['title']; ?><br />
            <?php } ?>
            </div>

            <div class="emoji_bump">
                <button type="submit">Generate</button>
            </div>
        </div>

        </form>
    </div>

    <?php if ($totalCollections > 1 && $totalTasks <= 0) { ?>
        <p class="highlight">
            Congrats on creating your first collection; now add some tasks to it to start billing!
        </p>
    <?php } ?>

    <div id="createStory" class="sunk">
        <h4>Create a Task</h4>
        <form action="/project?id=<?php echo $project['id']; ?>" method="post">
            <div class="fourColumns">
                <div>
                    <label>Collection</label>
                    <select name="collection">
                        <?php foreach ($allCollections as $anItem) { ?>
                            <option value="<?php echo $anItem['id']; ?>"><?php echo $anItem['title']; ?></option>
                        <?php } ?>
                    </select>
                </div>

                <div>
                    <label>Type</label>
                    <select name="type">
                        <?php foreach ($storyTypes as $anItem) { ?>
                            <option value="<?php echo $anItem['id']; ?>"><?php echo $anItem['title']; ?></option>
                        <?php } ?>
                    </select>
                </div>

                <div>
                    <label>Rate Type</label>
                    <select name="rate_type">
                        <?php foreach ($hourTypes as $anItem) { ?>
                            <option value="<?php echo $anItem['id']; ?>"><?php echo $anItem['title']; ?></option>
                        <?php } ?>
                    </select>
                </div>

                <div>
                    <label>Status</label>
                    <select name="status">
                        <?php foreach ($storyStatuses as $anItem) { ?>
                            <option value="<?php echo $anItem['id']; ?>"><?php echo $anItem['title']; ?></option>
                        <?php } ?>
                    </select>
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


    <?php echo $collectionsRendered; ?>
</div>

<script type="text/javascript">
    preventUnloadBasedOnFormChanges('handoff-table');
</script>
