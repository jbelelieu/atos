
<div id="holderFixed">

    <h2>Invoices</h2>
    <div class="borderContainer border pad">
        <table>
        <thead>
        <tr>
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

    <h2>Reports</h2>
    <div class="borderContainer border pad">
        <table>
        <thead>
        <tr>
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
    
    <h2>Taxes</h2>
    <div class="borderContainer border pad">
        <table>
        <thead>
        <tr>
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