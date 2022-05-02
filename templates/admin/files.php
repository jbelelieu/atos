
<div id="holderFixed">

    <h2>Invoices</h2>
    <div class="borderContainer border pad">
        <table>
        <thead>
        <tr>
            <th>Filename</th>
        </tr>
        </thead>
        <tbody>
            <?php foreach ($invoices as $aFile) { ?>
                <tr>
                    <td>
                        <a href="/documents/load?id=<?php echo $aFile; ?>" target="_blank"><?php echo $aFile; ?></a>
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
            <th>Filename</th>
        </tr>
        </thead>
        <tbody>
            <?php foreach ($reports as $aFile) { ?>
                <tr>
                    <td>
                        <a href="/documents/load?id=<?php echo $aFile; ?>" target="_blank"><?php echo $aFile; ?></a>
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
            <th>Filename</th>
        </tr>
        </thead>
        <tbody>
            <?php foreach ($taxes as $aFile) { ?>
                <tr>
                    <td>
                        <a href="/documents/load?id=<?php echo $aFile; ?>" target="_blank"><?php echo $aFile; ?></a>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
        </table>
    </div>
    
</div>