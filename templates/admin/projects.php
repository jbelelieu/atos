
<div class="holder">

    <?php if ($totalCollections === 1) { ?>
        <p class="highlight">
            Start here by creating your first collection of stories. A collection is a grouping of stories that will be billed to a client. One common use case is to map bi-weekly sprints to invoiced work.
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
    <button type="button" onclick="toggleDiv('createLink')" class="a">Links</button>
    <button type="button" onclick="toggleDiv('createFile')" class="a">Files</button>

    <div
        id="createLink"
        class="<?php echo(!empty($_GET['_showLink'])) && parseBool($_GET['_showLink']) ? '' : 'hide'; ?>">
        <h4 class="marginTopLess">Links</h4>
        <div
            class="sunk border pad bg">

            <form action="/project?id=<?php echo $project['id']; ?>" method="post">
            <input type="hidden" name="action" value="createLink" />
            <input type="hidden" name="project_id" value="<?php echo $project['id']; ?>" />
                <table>
                    <thead>
                        <tr>
                            <th width="250">Title</th>
                            <th>Link</th>
                            <th width="42"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($links as $aLink) { ?>
                            <tr>
                                <td><?php echo $aLink['title']; ?></td>
                                <td class="ellipsis">
                                    <a href="<?php echo $aLink['data']; ?>" target="_blank">
                                        <?php echo $aLink['data']; ?>
                                    </a>
                                </td>
                                <td>
                                    <a
                                        title="Delete"
                                        onclick="return confirm('Are you sure?')"
                                        href="/project?id=<?php echo $project['id']; ?>&action=deleteFileLink&file_id=<?php echo $aLink['id']; ?>"><?php echo putIcon('icofont-delete'); ?></a>
                                </td>
                            </tr>
                        <?php } ?>
                        <tr>
                            <td>
                                <input
                                    type="text"
                                    name="title"
                                    required="required"
                                    autocomplete="off"
                                    required="required"
                                    placeholder="Github Repo" />
                            </td>
                            <td>
                                <input
                                    type="url"
                                    name="data"
                                    required="required"
                                    autocomplete="off"
                                    style="width:80%;"
                                    placeholder="https://www.github.com/myrepo" /> <button type="submit">Create</button>
                            </td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </form>
        </div>
    </div>

    <div
        id="createFile"
        class="<?php echo(!empty($_GET['_showFile'])) && parseBool($_GET['_showFile']) ? '' : 'hide'; ?>">
        <h4>Files</h4>
        <div
            class="sunk border pad bg">

            <form enctype="multipart/form-data" action="/project?id=<?php echo $project['id']; ?>" method="post">
            <input type="hidden" name="action" value="uploadFile" />
            <input type="hidden" name="project_id" value="<?php echo $project['id']; ?>" />
                <table>
                    <thead>
                        <tr>
                            <th width="250">Title</th>
                            <th>Location</th>
                            <th width="42"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($files as $aFile) { ?>
                            <tr>
                                <td><?php echo $aFile['title']; ?></td>
                                <td class="ellipsis">
                                    <?php echo $aFile['data']; ?>
                                </td>
                                <td>
                                    <a
                                        title="Delete"
                                        onclick="return confirm('Are you sure?')"
                                        href="/project?id=<?php echo $project['id']; ?>&action=deleteFileLink&file_id=<?php echo $aFile['id']; ?>"><?php echo putIcon('icofont-delete'); ?></a>
                                </td>
                            </tr>
                        <?php } ?>
                        <tr>
                            <td>
                                <input
                                    type="text"
                                    name="title"
                                    required="required"
                                    autocomplete="off"
                                    required="required"
                                    placeholder="Github Repo" />
                            </td>
                            <td>
                                <input
                                    type="file"
                                    name="data"
                                    autocomplete="off"
                                    required="required"
                                    style="width:80%;"
                                    placeholder="https://www.github.com/myrepo" /> <button type="submit">Create</button>
                            </td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </form>
        </div>
    </div>

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
                <br /><br />
                <input type="checkbox" name="save" value="1" /> Save as file
            </div>
        </div>

        </form>
    </div>

    <?php if ($totalCollections > 1 && $totalTasks <= 0) { ?>
        <p class="highlight">
            Congrats on creating your first collection! Now add some tasks to it. Once you've added a task, you can assign it a status. If a status sets a story to "complete" and "billable" (see "Settings"), it will be added to generate invoices.
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
