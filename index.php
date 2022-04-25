<?php

/**
 * ATOS: "Built by freelancer 🙋‍♂️, for freelancers 🕺 🤷 💃🏾 "
 *
 * This file controls projects and companies.
 *
 * @author @jbelelieu
 * @copyright Humanity, any year.
 * @license AGPL-3.0 License
 * @link https://github.com/jbelelieu/atos
 */

require "includes/db.php";

/**
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 *
 *   Actions
 *
 */

if (isset($_GET['action'])) {
    switch ($_GET['action']) {
        case 'deleteCompany':
            deleteCompany($_GET);
            break;
        case 'deleteProject':
            deleteProject($_GET);
            break;
        default:
            redirect('index.php', null, null, 'Unknown action');
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
            redirect('index.php', null, null, 'Unknown action');
    }
}

/**
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 *
 *   Down and Dirty
 *
 */

$clients = getCompanies();

// The client select dropdown
$clientSelect = '<option value=""></option>';
foreach ($clients as $aClient) {
    $clientSelect .= '<option value="' . $aClient['id'] . '">' . $aClient['title'] . '</option>';
}

// Client table
$totalValue = 0;
$renderedClients = '';

foreach ($clients as $aClient) {
    $value = getCompanyTotals($aClient['id']);

    $totalValue += $value['total'];

    $logo = $aClient['logo_url']
        ? '<img src="' . $aClient['logo_url'] . '" class="clientLogo" />'
        : '';

    $renderedClients .= template(
        'snippets/client_table_entry',
        [
            'client' => $aClient,
            'logo' => $logo,
            'totalClientValue' => formatMoney($totalValue),
        ],
        true
    );
}

// Build the projects table.
$projects = getProjects();

$totalProjectValue = 0;
$totalProjectHours = 0;
$renderedProjects = '';

// Project table
foreach ($projects as $aProject) {
    $value = getProjectTotals($aProject['id']);

    $totalProjectValue += $value['total'];
    $totalProjectHours += $value['hours'];

    $renderedProjects .= template(
        'snippets/project_table_entry',
        [
            'project' => $aProject,
            'hours' => $value['hours'],
            'total' => formatMoney($value['total']),
        ],
        true
    );
}

echo template(
    'index',
    [
        'clientSelect' => $clientSelect,
        'clients' => $renderedClients,
        'projects' => $renderedProjects,
        'totalClients' => sizeof($clients),
        'totalProjectHours' => $totalProjectHours,
        'totalProjectValue' => formatMoney($totalProjectValue),
        'totalClientValue' => formatMoney($totalValue),
    ]
);
exit;

/**
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 *
 *   Functions
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
        $db->rollback();

        systemError($e->getMessage());
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
