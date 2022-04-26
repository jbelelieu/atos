
<div class="border">
    <div class="halfHalfColumns">
        <div>
            <div class="formBox padLess">
                <form action="/settings" method="post">
                    <div>
                        <label><b>Statuses</b>&nbsp;&nbsp;Title</label>
                        <input type="text" required="required" autocomplete="off" name="title" />
                        
                        <label>Complete State?</label>
                        <input type="radio" name="is_complete_state" value="1" checked="checked" /> Yes, we can consider these stories completed.<br />
                        <input type="radio" name="is_complete_state" value="0" /> No, stories with this status are not complete.

                        <label>Billable State?</label>
                        <input type="radio" name="is_billable_state" value="1" checked="checked" /> Yes, this status represents a billable state.<br />
                        <input type="radio" name="is_billable_state" value="0" /> No, do not bill for stories with this status.
                        
                        <div class="halfHalfColumns">
                            <div>
                                <label>Emoji</label>
                                <input type="text" autocomplete="off" name="emoji" maxlength=10 style="width:200px;" />
                                <p class="fieldHelp">Select any icon from <a href="https://www.flaticon.com/uicons/?weight=solid&corner=rounded" target="_blank">here</a>. Type the name (exp: "<u>fi-sr-briefcase</u>") found by clicking on the icon.</p>
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
        </div>
        <div>
            <div class="formBox padLess">
                <form action="/settings" method="post">
                    <div>
                        <label><b>Rate Types</b>&nbsp;&nbsp;Title</label>
                        <input type="text" required="required" autocomplete="off" name="title" />
                        
                        <label>Rate</label>
                        $<input
                            type="number"
                            min=1
                            required="required"
                            autocomplete="off"
                            name="rate"
                            style="width:100px;" /> <button type="submit">Create</button>
                    </div>
                    <input type="hidden" name="action" value="createRateType" />
                </form>

                <hr />

                <form action="/settings" method="post">
                    <div>
                        <label><b>Story Types</b>&nbsp;&nbsp;Title</label>
                        <input type="text" required="required" autocomplete="off" name="title" style="width:60%;" /> <button type="submit">Create</button>
                    </div>
                    <input type="hidden" name="action" value="createStoryType" />
                </form>
            </div>
        </div>
    </div>
</div>

<div class="collectionsTable">
    <h4 class="bubble">Story Statuses</h4>
    <table>
    <thead>
    <tr>
    <th width="42"></th>
    <th width="50%">Title</th>
    <th>Is Complete?</th>
    <th>Is Billable?</th>
    <th width="42"></th>
    </tr>
    </thead>
    <tbody>
        <?php echo $renderedStatuses; ?>
    </tbody>
    </table>
</div>

<div class="collectionsTable">
    <h4 class="bubble">Rate Types</h4>
    <table>
    <thead>
    <tr>
    <th width="50%">Title</th>
    <th>Rate</th>
    <th width="42"></th>
    </tr>
    </thead>
    <tbody>
        <?php echo $renderedRateTypes; ?>
    </tbody>
    </table>
</div>

<div class="collectionsTable">
    <h4 class="bubble">Story Types</h4>
    <table>
    <thead>
    <tr>
    <th>Title</th>
    <th width="42"></th>
    </tr>
    </thead>
    <tbody>
        <?php echo $renderedStoryTypes; ?>
    </tbody>
    </table>
</div>
