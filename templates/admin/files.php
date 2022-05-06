
<div id="holderFixed" class="border">

    <div class="highlightFixed">
        <p>Whenever you generate and save an invoice, report, or tax estimation, those files will appear in on this page.</p>
    </div>

    <div class="borderContainer pad">
        <h2>Invoices</h2>
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
    </div>

    <div class="borderContainer borderTop pad">
        <h2>Reports</h2>
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
    </div>
    
    <div class="borderContainer borderTop pad">
        <h2>Taxes</h2>
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
    
</div>