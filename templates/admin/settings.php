
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
                                <input type="text" 
                            placeholder="fi-sr-briefcase" autocomplete="off" name="emoji" maxlength=10 style="width:200px;" />
                                <p class="fieldHelp">Select any icon from <a href="https://www.flaticon.com/uicons/?weight=solid&corner=rounded" target="_blank">here</a>.</p>
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
    <form action="/settings" method="post">
        <input type="hidden" name="action" value="updateStatuses" />

        <h4 class="bubble">Story Statuses</h4>
        <table>
        <thead>
        <tr>
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

        <!-- <table>
        <tbody>
            <tr>
                <td width="30%">
                    <input
                        type="text"
                        name="title"
                        placeholder="New Story Type"
                        style="width:100%;" />
                </td>
                <td width="10%">
                    <input
                        type="text"
                        name="icon"
                        placeholder="fi-sr-briefcase"
                        style="width:100%;" />
                    <p class="fieldHelp">Select any icon from <a href="https://www.flaticon.com/uicons/?weight=solid&corner=rounded" target="_blank">here</a>.</p>
                </td>
                <td width="10%">
                    <input
                        type="text"
                        name="icon"
                        placeholder="fi-sr-briefcase"
                        style="width:100%;" />
                    <p class="fieldHelp">Select any icon from <a href="https://www.flaticon.com/uicons/?weight=solid&corner=rounded" target="_blank">here</a>.</p>
                </td>
                <td width="42"></td>
                <td>
                    <select name="is_complete_state">
                        <option value="0">Open</option>
                        <option value="1">Complete</option>
                    </select>
                </td>
                <td>
                    <select name="is_billable_state">
                        <option value="0">Not Billable</option>
                        <option value="1">Billable</option>
                    </select>
                </td>
            </tr>
        </tbody>
        </table> -->

        <div class="underTableSubmit">
            <button type="submit">Save Statuses</button>
        </div>
    </form>
</div>

<div class="halfHalfColumns">
    <div class="collectionsTable">
        <form action="/settings" method="post">
            <input type="hidden" name="action" value="updateRates" />

            <h4 class="bubble">Rate Types</h4>
            <table>
            <thead>
            <tr>
            <th width="70%">Title</th>
            <th>Rate</th>
            <th width="42"></th>
            </tr>
            </thead>
            <tbody>
                <?php echo $renderedRateTypes; ?>
            </tbody>
            </table>
                
            <!-- <table>
            <tbody>
                <tr>
                    <td width="35%">
                        <input
                            type="text"
                            name="title"
                            placeholder="New Rate Type"
                            style="width:100%;" />
                    </td>
                    <td>
                        $<input
                            type="text"
                            min=0
                            name="rate"
                            placeholder="fi-sr-briefcase"
                            style="width:100px;" />
                        <p class="fieldHelp">Select any icon from <a href="https://www.flaticon.com/uicons/?weight=solid&corner=rounded" target="_blank">here</a>. Type the name (exp: "<u>fi-sr-briefcase</u>") found by clicking on the icon.</p>
                    </td>
                    <td width="42"></td>
                </tr>
            </tbody>
            </table>
-->
            <div class="underTableSubmit">
                <button type="submit">Save Rate Types</button>
            </div> 
        </form>
    </div>
    <div class="collectionsTable">
        <form action="/settings" method="post">
            <input type="hidden" name="action" value="updateTypes" />

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

            <!-- <table>
            <tbody>
                <tr>
                    <td>
                        <input
                            type="text"
                            name="title"
                            placeholder="New Story Type"
                            style="width:100%;" />
                    </td>
                    <td width="42"></td>
                </tr>
            </tbody>
            </table>
-->
            <div class="underTableSubmit">
                <button type="submit">Save Story Types</button>
            </div>
        </form>
    </div>
</div>