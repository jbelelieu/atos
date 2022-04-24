<?php

require "includes/db.php";

// -------------------------------------

if (isset($_GET['action'])) {
    switch ($_GET['action']) {
        case 'deleteCompany':
            deleteCompany($_GET);
            break;
        case 'deleteProject':
            deleteProject($_GET);
            break;
        default:
            redirect('project.php', $_GET['id'], '', 'Unknown action');
    }
}

if (isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'createProject':
            createProject($_POST);
            break;
        case 'createCompany':
            createCompany($_POST);
            break;
        default:
            echo "Unknown action.";
            exit;
    }
}

// -------------------------------------

$projects = getProjects();
$clients = getCompanies();

$clientSelect = '<option value=""></option>';
foreach ($clients as $aClient) {
    $clientSelect .= '<option value="' . $aClient['id'] . '">' . $aClient['title'] . '</option>';
}

include "includes/header.php";

echo <<<qq
<div class="border">
    <div class="halfHalfColumns">
        <div>
            <div class="formBox padLess">
            

                <form action="index.php" method="post">
                    <div class="halfHalfColumns">
                        <div>
                            <label><b>Companies &amp; Clients</b>&nbsp;&nbsp;Name</label>
                            <input type="text" name="title" />
                            
                            <label>Logo URL</label>
                            <input type="text" name="logo_url" />
                            
                            <label>Phone</label>
                            <input type="text" name="phone" />
                            
                            <label>Email</label>
                            <input type="text" name="email" />
                        </div>
                        <div>
                            <div>
                            <label>Address (html ok)</label>
                            <textarea name="address"></textarea>
                            </div>

                            <div>
                            <label>Instructions (html ok)</label>
                            <textarea name="address"></textarea>
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
qq;

if (sizeof($clients) < 2) {
    echo <<<qq
            <div class="bubble helpBubble">
                <label><b>Projects</b></label>
                <p class="help">You can't create a project until you've created at least two companies. We recommend adding your own company first, then your first client's information. For each project you start, you'll need (a) a company you are representing (the contracted party) and (b) a company you are working for (your client).<br /><br />👈&nbsp;&nbsp;Add companies over there</p>
            </div>
qq;
} else {
    echo <<<qq
                <form action="index.php" method="post">
                    <div class="halfHalfColumns">
                        <div>
                        <label><b>Projects</b>&nbsp;&nbsp;Contracted Party</label>
                        <select name="company_id">$clientSelect</select>
                        </div>

                        <div>
                        <label>Your Client</label>
                        <select name="client_id">$clientSelect</select>
                        </div>

                        <div>
                        <label>Project Code</label>
                        <input type="text" name="code" style="width:80px" maxlength=2  />
                        <p class="fieldHelp">Used to name stories for example, "<u>PA</u>-120".</p>
                        </div>

                        <div>
                        <label>Project Title</label>
                        <input type="text" name="title" />
                        </div>
                    </div>

                    <button type="submit">Create</button>

                    <input type="hidden" name="action" value="createProject" />
                </form>
qq;
}

echo <<<qq
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
qq;

    $totalProjectValue = 0;
    $totalProjectHours = 0;

    foreach ($projects as $aProject) {
        $value = getProjectTotals($aProject['id']);

        $totalProjectValue += $value['total'];
        $totalProjectHours += $value['hours'];

        echo "<tr>";
        echo "<td><a href=\"project.php?_success=Welcome+to+your+" . $aProject['title'] . "+project!&id=" . $aProject['id'] . "\">" . $aProject['title'] . "</a></td>";
        echo "<td>" . $aProject['company_name'] . "</td>";
        echo "<td>" . $value['hours'] . "</td>";
        echo "<td>" . formatMoney($value['total']) . "</td>";
        echo "<td class=\"textRight\"><a onclick=\"return confirm('This will delete the project and all associated data - are you sure?')\" href=\"index.php?action=deleteProject&id=" . $aProject['id'] . "\">❌</a></td>";
        echo "</tr>";
    }

    $totalProjectValue = formatMoney($totalProjectValue);

echo <<<qq
    <tr>
    <td colspan=2></td>
    <td class="summary">$totalProjectHours</td>
    <td class="summary">$totalProjectValue</td>
    <td></td>
    </tr>
    </table>
qq;

// --------------------------------

echo <<<qq
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
qq;

    $totalValue = 0;
    foreach ($clients as $aClient) {
        $value = getClientTotals($aClient['id']);

        $totalValue += $value['total'];

        $logo = $aClient['logo_url']
            ? '<img src="' . $aClient['logo_url'] . '" class="clientLogo" />'
            : '';

        echo "<tr>";
        echo "<td>" . $logo . "</td>";
        echo "<td>" . $aClient['title'] . "</td>";
        echo "<td>" . $aClient['address'] . "</td>";
        echo "<td>" . $aClient['phone'] . "</td>";
        echo "<td>" . $aClient['email'] . "</td>";
        echo "<td>" . $aClient['website'] . "</td>";
        echo "<td>" . formatMoney($value['total']) . "</td>";
        echo "<td class=\"textRight\"><a onclick=\"return confirm('This will delete the client - are you sure?')\" href=\"index.php?action=deleteCompany&id=" . $aClient['id'] . "\">❌</a></td>";
        echo "</tr>";
    }

echo "<tr>";
echo "<td colspan=6 class=\"summary\">" . $logo . "</td>";
echo "<td class=\"summary\">" . formatMoney($totalValue) . "</td>";
echo "<td></td>";
echo "</tr>";
echo "</table>";

// --------------------------------

echo "</div>";

include "includes/footer.php";
exit;


/**
 *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 *
 *   Module functions
 *
 */

/**
 * @param array $data
 * @return void
 */
function createCompany(array $data): void
{
    global $db;

    $statement = $db->prepare('
        INSERT INTO company (
            title,
            logo_url,
            address,
            phone,
            email,
            instructions,
            website
        )
        VALUES (
            :title,
            :logo_url,
            :address,
            :phone,
            :email,
            :instructions,
            :website
        )
    ');

    $statement->bindParam(':title', $data['title']);
    $statement->bindParam(':logo_url', $data['logo_url']);
    $statement->bindParam(':address', $data['address']);
    $statement->bindParam(':phone', $data['phone']);
    $statement->bindParam(':email', $data['email']);
    $statement->bindParam(':instructions', $data['instructions']);
    $statement->bindParam(':website', $data['website']);

    $statement->execute();

    redirect('index.php', null, 'The "' . $data['title'] . '" company has been created.');
}

/**
 * @param array $data
 * @return void
 */
function createProject(array $data): void
{
    global $db;

    try {
        $db->beginTransaction();

        $statement = $db->prepare('
        INSERT INTO project (
            company_id,
            client_id,
            title,
            code
        )
        VALUES (
            :company_id,
            :client_id,
            :title,
            :code
        )
    ');

        $statement->bindParam(':company_id', $data['company_id']);
        $statement->bindParam(':client_id', $data['client_id']);
        $statement->bindParam(':title', $data['title']);
        $statement->bindParam(':code', $data['code']);
        $statement->execute();

        $lastProjectId = $db->lastInsertId();

        $statement = $db->prepare('
            INSERT INTO story_collection (
                project_id,
                title,
                is_project_default
            )
            VALUES (
                :project_id,
                :title,
                true
            )
        ');

        $statement->bindParam(':project_id', $lastProjectId);
        $statement->bindParam(':title', getSetting(AsosSettings::UNORGANIZED_NAME, 'Unorganized'));
        $statement->execute();

        $db->commit();
    } catch (\PDOException $e) {
        dd($e);

        $db->rollback();
    }

    redirect('index.php', null, 'Your project has been created; now go got get that bread.');
}

/**
 * @param array $data
 * @return void
 */
function deleteCompany(array $data): void
{
    global $db;

    $statement = $db->prepare('
        DELETE FROM company
        WHERE id = :id
    ');

    $statement->bindParam(':id', $data['id']);

    $statement->execute();

    redirect('index.php', null, 'That company has been deleted. Bye forever, I guess.');
}

/**
 * @param array $data
 * @return void
 */
function deleteProject(array $data): void
{
    global $db;

    $statement = $db->prepare('
        DELETE FROM project
        WHERE id = :id
    ');

    $statement->bindParam(':id', $data['id']);

    $statement->execute();

    redirect('index.php', null, 'That company has been deleted. Bye forever, I guess.');
}
