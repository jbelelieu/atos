
<div class="holder">

    <h2 class="sectionHeader">Projects</h2>
<?php if ($totalClients < 2) { ?>
    <p class="highlight">
        Before you can create a project, you'll need to first create a contracted party (you or your company) and a client (the company you are working for).
    </p>
<?php } else { ?>
    <table>
    <thead>
    <tr>
    <th>Title</th>
    <th>Code</th>
    <th>Contractor</th>
    <th>Client</th>
    <th width=190>Hours</th>
    <th width=190>Billed</th>
    <th width="42"></th>
    </tr>
    </thead>
    <?php echo $projects; ?>
    <tr>
    <td colspan=4>
        <button
        type="button"
        id="createProject-button"
        onClick="toggleDiv('createProject', 'Cancel', 'Create New')"
        class="createNew">Create New</button>
    </td>
    <td class="summary"><?php echo $totalProjectHours; ?></td>
    <td class="summary"><?php echo $totalProjectValue; ?></td>
    <td></td>
    </tr>
    </table>
<?php } ?>

<div class="borderSection pad sunk hide" id="createProject">
    <form action="/" method="post">
        <div class="halfHalfColumns pullUp">
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

        <div class="marginTopLess textRight">
            <button type="submit">Create</button>
        </div>

        <input type="hidden" name="action" value="createProject" />
    </form>
</div>

    <h2 class="sectionHeader">Companies &amp; Clients</h2>
<?php if ($totalClients < 1) { ?>
    <p class="highlight">
        You currently don't have any companies in the system. Please start by adding your own company in below, and then add a client company to begin creating projects.
    </p>
<?php } else { ?>
    <table>
    <thead>
    <tr>
    <th>Logo</th>
    <th>Title</th>
    <th>Address</th>
    <th><?php echo putIcon('fi-sr-smartphone'); ?></th>
    <th><?php echo putIcon('fi-sr-envelope'); ?></th>
    <th><?php echo putIcon('fi-sr-link'); ?></th>
    <th>Billed</th>
    <th width="42"></th>
    </tr>
    </thead>
    <?php echo $clients; ?>
    <tr>
    <td colspan=6>
        <button
        type="button"
        id="createClient-button"
        onClick="toggleDiv('createClient', 'Cancel', 'Create New')"
        class="createNew">Create New</button>
    </td>
    <td class="summary"><?php echo $totalClientValue; ?></td>
    <td></td>
    </tr>
    </table>
<?php } ?>

<div class="pad sunk <?php if ($totalClients < 2) {
    echo "show";
} else {
    echo "hide";
} ?>" id="createClient">
    <form action="/" method="post">
        <div class="halfHalfColumns">
            <div>
                <label><b>Companies &amp; Clients</b>&nbsp;&nbsp;Name</label>
                <input
                    type="text"
                    required="required"
                    autocomplete="off"
                    name="title" />
                
                <label>Phone</label>
                <input type="text" name="phone" autocomplete="off" />
                
                <label>Email</label>
                <input type="text" name="email" autocomplete="off" />

                <label>Website</label>
                <input
                    type="url"
                    name="url"
                    autocomplete="off"
                    placeholder="https://www.companysite.com" />

                <label>Logo URL</label>
                <input
                    type="url"
                    name="logo_url"
                    autocomplete="off"
                    placeholder="https://www.companysite.com/logo.png" />
            </div>
            <div>
                <div>
                <label>Address (html ok; line breaks auto-added)</label>
                <textarea
                    placeholder="123 Sesame St&#10;Unit 911&#10;New York, NY, 11111"
                    name="address"
                    autocomplete="off"></textarea>
                </div>

                <div>
                <label>Instructions (html ok; line breaks auto-added)</label>
                <textarea
                    placeholder="Please send payments to Chase Bank.&#10;&#10;<ul>&#10;<li>Account number: 9188273647</li>&#10;<li>Routing number: 123456789</li>&#10;</ul>"
                    name="address"
                    autocomplete="off"
                    style="height:165px;"></textarea>
                </div>
            </div>
        </div>

        <div class="marginTopLess textRight">
            <button type="submit">Create</button>
            <input type="hidden" name="action" value="createCompany" />
        </div>
    </form>

</div>
            
</div>
