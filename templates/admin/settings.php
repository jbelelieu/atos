
<div class="holder">
    <hr />
    <h2 class="sectionHeader">Task Statuses</h2>
    <button type="button" onclick="toggleDiv('createStatus')" class="a">Create</button>

    <div id="createStatus" class="sunk hide">
        <h4>Create a Status</h4>
        <form action="/settings" method="post">
            <div>
                <label>Title</label>
                <input type="text" required="required" autocomplete="off" name="title" />
                
                <label>Complete State?</label>
                <input type="radio" name="is_complete_state" value="1" checked="checked" /> Yes, we can consider these tasks completed.<br />
                <input type="radio" name="is_complete_state" value="0" /> No, tasks with this status are not complete.

                <label>Billable State?</label>
                <input type="radio" name="is_billable_state" value="1" checked="checked" /> Yes, this status represents a billable state.<br />
                <input type="radio" name="is_billable_state" value="0" /> No, do not bill for tasks with this status.
                
                <div class="halfHalfColumns">
                    <div>
                        <label>Emoji</label>
                        <input type="text" 
                    placeholder="addons" placeholder="addons" autocomplete="off" name="emoji" maxlength=10 style="width:200px;" />
                        <p class="fieldHelp">Select any icon from <a href="https://icofont.com/icons" target="_blank">here</a>.</p>
                    </div>
                    <div>
                        <label>Color</label>
                        <input type="color" value="#111111" autocomplete="off" name="color" style="width:42px;" /> <button type="submit">Create</button>
                    </div>
                </div>
            </div>
            <input type="hidden" name="action" value="createStatus" />
        </form>
    </div>

    <form action="/settings" method="post">
        <input type="hidden" name="action" value="updateStatuses" />
        <table>
        <thead>
        <tr class="noHighlight">
        <th width="50%">Title</th>
        <th width="42"></th>
        <th>Is Complete?</th>
        <th>Is Billable?</th>
        <th width="42"></th>
        </tr>
        </thead>
        <tbody>
            <?php echo $renderedStatuses; ?>
        </tbody>
        </table>
        <div class="underTableSubmit textRight">
            <button type="submit">Save Statuses</button>
        </div>
    </form>

<hr />
<h2 class="sectionHeader">Rate Types</h2>
<button type="button" onclick="toggleDiv('createRate')" class="a">Create</button>

<div id="createRate" class="sunk hide">
    <h4>Create a Rate Type</h4>
    <form action="/settings" method="post">
    <input type="hidden" name="action" value="createRateType" />
        <div class="columns6535">
            <div>
                <label>Title</label>
                <input
                type="text"
                required="required"
                autocomplete="off"
                name="title" />
            </div>
            <div>
                <label>Rate</label>
                $<input
                    type="number"
                    min=0
                    required="required"
                    autocomplete="off"
                    name="rate"
                    style="width:100px;" /> <button type="submit">Create</button>
            </div>
        </div>
    </form>
</div>

<form action="/settings" method="post">
<input type="hidden" name="action" value="updateRates" />
<table>
<thead>
<tr class="noHighlight">
<th width="70%">Title</th>
<th>Rate</th>
<th width="42"></th>
</tr>
</thead>
<tbody>
    <?php echo $renderedRateTypes; ?>
</tbody>
</table>

<div class="underTableSubmit textRight">
    <button type="submit">Save Rate Types</button>
</div> 
</form>

<hr />
<h2 class="sectionHeader">Task Types</h2>
<button type="button" onclick="toggleDiv('createType')" class="a">Create</button>

<div id="createType" class="sunk hide">
    <h4>Create a Task Type</h4>
    <form action="/settings" method="post">
        <label>Title</label>
        <input
            type="text"
            required="required"
            autocomplete="off"
            name="title"
            style="width:70%;" /> <button type="submit">Create</button>

        <input type="hidden" name="action" value="createStoryType" />
    </form>
</div>

<form action="/settings" method="post">
    <input type="hidden" name="action" value="updateTypes" />

    <table>
    <thead>
    <tr class="noHighlight">
    <th>Title</th>
    <th width="42"></th>
    </tr>
    </thead>
    <tbody>
        <?php echo $renderedStoryTypes; ?>
    </tbody>
    </table>

    <div class="underTableSubmit textRight">
        <button type="submit">Save Task Types</button>
    </div>
</form>
