
<div class="border">
    <div class="halfHalfColumns">
        <div>
            <div class="formBox padLess">
                <form action="index.php" method="post">
                    <div class="halfHalfColumns">
                        <div>
                            <label><b>Companies &amp; Clients</b>&nbsp;&nbsp;Name</label>
                            <input
                                type="text"
                                required="required"
                                autocomplete="off"
                                name="title" />
                            
                            <label>Logo URL</label>
                            <input type="text" name="logo_url" autocomplete="off" />
                            
                            <label>Phone</label>
                            <input type="text" name="phone" autocomplete="off" />
                            
                            <label>Email</label>
                            <input type="text" name="email" autocomplete="off" />
                        </div>
                        <div>
                            <div>
                            <label>Address (html ok)</label>
                            <textarea name="address" autocomplete="off"></textarea>
                            </div>

                            <div>
                            <label>Instructions (html ok)</label>
                            <textarea
                                name="address"
                                autocomplete="off"></textarea>
                            </div>
                        </div>
                    </div>

                    <button type="submit">Create</button>
                    <input type="hidden" name="action" value="createCompany" />
                </form>
            </div>
        </div>
        <div>
            <div class="formBox padLess">

            <?php if ($totalClients < 2) { ?>
                <div class="bubble helpBubble">
                    <label><b>Projects</b></label>
                    <p class="help">You can't create a project until you've created at least two companies. We recommend adding your own company first, then your first client's information. For each project you start, you'll need (a) a company you are representing (the contracted party) and (b) a company you are working for (your client).<br /><br />👈&nbsp;&nbsp;Add companies over there</p>
                </div>
            <?php } else { ?>
                <form action="index.php" method="post">
                    <div class="halfHalfColumns">
                        <div>
                        <label><b>Projects</b>&nbsp;&nbsp;Contracted Party</label>
                        <select required="required" name="company_id"><?php echo $clientSelect; ?></select>
                        </div>

                        <div>
                        <label>Your Client</label>
                        <select required="required" name="client_id"><?php echo $clientSelect; ?></select>
                        </div>

                        <div>
                        <label>Project Code</label>
                        <input
                            required="required"
                            autocomplete="off"
                            type="text"
                            name="code"
                            style="width:80px"
                            maxlength=2 />
                        <p class="fieldHelp">Used to name stories for example, "<u>PA</u>-120".</p>
                        </div>

                        <div>
                        <label>Project Title</label>
                        <input
                            type="text"
                            name="title"
                            required="required"
                            autocomplete="off" />
                        </div>
                    </div>

                    <button type="submit">Create</button>

                    <input type="hidden" name="action" value="createProject" />
                </form>
            <?php } ?>
            </div>
        </div>
    </div>
</div>

<div class="collectionsTable">
    <h4 class="bubble">Projects</h4>
    <table>
    <thead>
    <tr>
    <th>Title</th>
    <th>Client</th>
    <th width=190>Hours Billed</th>
    <th width=190>Value Billed</th>
    <th width="42"></th>
    </tr>
    </thead>
    <?php echo $projects; ?>
    <tr>
    <td colspan=2></td>
    <td class="summary"><?php echo $totalProjectHours; ?></td>
    <td class="summary"><?php echo $totalProjectValue; ?></td>
    <td></td>
    </tr>
    </table>
</div>

<div class="collectionsTable">
    <h4 class="bubble">Companies &amp; Clients</h4>
    <table>
    <thead>
    <tr>
    <th>Logo</th>
    <th>Title</th>
    <th>Address</th>
    <th>Phone</th>
    <th>Email</th>
    <th>Website</th>
    <th>Value Billed</th>
    <th width="42"></th>
    </tr>
    </thead>
    <?php echo $clients; ?>
    <tr>
    <td colspan=6></td>
    <td class="summary"><?php echo $totalClientValue; ?></td>
    <td></td>
    </tr>
    </table>
</div>
