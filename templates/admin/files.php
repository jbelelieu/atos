
<div class="holder">

    <hr />
    <h2 class="sectionHeader">Invoices</h2>
    <table>
    <thead>
    <tr class="noHighlight">
        <th width="120">Date</th>
        <th>Filename</th>
    </tr>
    </thead>
    <tbody>
        <?php foreach ($invoices as $aFile) { ?>
            <tr>
                <td>
                    <?php echo $aFile['date']; ?>
                </td>
                <td>
                    <a href="/documents/load?id=<?php echo $aFile['file']; ?>" target="_blank"><?php echo $aFile['file']; ?></a>
                </td>
            </tr>
        <?php } ?>
    </tbody>
    </table>

    <hr />
    <h2 class="sectionHeader">Reports</h2>
    <table>
    <thead>
    <tr class="noHighlight">
        <th width="120">Date</th>
        <th>Filename</th>
    </tr>
    </thead>
    <tbody>
        <?php foreach ($reports as $aFile) { ?>
            <tr>
                <td>
                    <?php echo $aFile['date']; ?>
                </td>
                <td>
                    <a href="/documents/load?id=<?php echo $aFile['file']; ?>" target="_blank"><?php echo $aFile['file']; ?></a>
                </td>
            </tr>
        <?php } ?>
    </tbody>
    </table>
    
    <hr />
    <h2 class="sectionHeader">Taxes</h2>
    <table>
    <thead>
    <tr class="noHighlight">
        <th width="120">Date</th>
        <th>Filename</th>
    </tr>
    </thead>
    <tbody>
        <?php foreach ($taxes as $aFile) { ?>
            <tr>
                <td>
                    <?php echo $aFile['date']; ?>
                </td>
                <td>
                    <a href="/documents/load?id=<?php echo $aFile['file']; ?>" target="_blank"><?php echo $aFile['file']; ?></a>
                </td>
            </tr>
        <?php } ?>
    </tbody>
    </table>
</div>