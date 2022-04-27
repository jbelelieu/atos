
<div class="collectionsTable">
    
    <div class="halfHalfColumns">
        <div>
            <h4>Tax Breakdown</h4>

            <table>
                <thead>
                    <tr>
                        <th width="230"></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="listLeft">Base Income</td>
                        <td><?php echo $income['baseIncome']; ?></td>
                    </tr>
                    <tr>
                        <td class="listLeft">Deductions</td>
                        <td>(<?php echo $income['deductions']; ?>)</td>
                    </tr>
                    <tr>
                        <td class="listLeft">Taxable Income</td>
                        <td class="bold"><?php echo $income['taxableIncome']; ?></td>
                    </tr>
                    <tr>
                        <td class="listLeft">Additional Tax Burdens</td>
                        <td><?php echo $income['additionalTaxBurdens']; ?></td>
                    </tr>
                    <tr>
                        <td class="listLeft">Total Taxes Due</td>
                        <td class="bold"><?php echo $taxes['totalTax']; ?> (<?php echo $taxes['effectiveRate']; ?>% effective rate)</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div>
            <h4>Averages</h4>
            <table>
                <thead>
                    <tr>
                        <th width="230"></th>
                        <th>Daily</th>
                        <th>Monthly</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="listLeft">Pre-tax Income</td>
                        <td><?php echo $averages['daily']['preTax']; ?></td>
                        <td><?php echo $averages['monthly']['preTax']; ?></td>
                    </tr>
                    <tr>
                        <td class="listLeft">Tax</td>
                        <td>(<?php echo $averages['daily']['tax']; ?>)</td>
                        <td>(<?php echo $averages['monthly']['tax']; ?>)</td>
                    </tr>
                    <tr>
                        <td class="listLeft">Post-tax Income</td>
                        <td><?php echo $averages['daily']['postTaxIncome']; ?></td>
                        <td><?php echo $averages['monthly']['postTaxIncome']; ?></td>
                    </tr>
                    <tr class="weak">
                        <td class="listLeft">Actual (as of day #<?php echo $dayNumber; ?>)</td>
                        <td><?php echo $averages['actual']['daily']['preTax']; ?></td>
                        <td><?php echo $averages['actual']['monthly']['preTax']; ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="halfHalfColumns">
        <?php foreach ($taxes['regions'] as $region => $details) { ?>

        <div class="redTables">
            <h4>Tax Burden: <?php echo $details['_class']::REGION; ?></h4>
            <span class="fillingStatus">
                <?php echo $details['filingStrategy'] ?>
            </span>
            <span class="bold fillingStatus">
                <?php echo $details['recommendations']['percentOfTotalTaxBurden'] ?>% of total
            </span>
            <table>
                <thead>
                    <tr>
                        <th width="230">Date Due</th>
                        <th>Est. Amount Owed</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="listLeft"></td>
                        <td class="bold"><?php echo $details['results']['_tax']; ?></td>
                    </tr>
                    <?php foreach ($details['recommendations']['schedule'] as $date => $details) { ?>
                    <tr>
                        <td class="listLeft"><?php echo $date; ?></td>
                        <td><?php echo $details['amount']; ?></td>
                        <td class="weak">Days until due: <?php echo $details['daysUntil']; ?></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <?php } ?>
    </div>

    <div class="halfHalfColumns">
        <div class="">
            <h4>Deductions</h4>
            <table>
                <thead>
                    <tr>
                        <th width="230"></th>
                        <th></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="listLeft"></td>
                        <td class="bold"><?php echo $income['deductions']; ?></td>
                    </tr>
                    <?php foreach ($_raw['deductions'] as $aDeduction) { ?>
                    <tr>
                        <td class="listLeft"><?php echo $aDeduction['title']; ?></td>
                        <td><?php echo $aDeduction['adjustment']; ?></td>
                        <td></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <div class="">
            <h4>Adjustments</h4>
            <table>
                <thead>
                    <tr>
                        <th width="230"></th>
                        <th></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="listLeft"></td>
                        <td class="bold"><?php echo $income['additionalTaxBurdens']; ?></td>
                    </tr>
                    <?php foreach ($_raw['taxBurdens'] as $aBurden) { ?>
                    <tr>
                        <td class="listLeft">
                            <?php echo $aBurden['title']; ?>
                        </td>
                        <td>
                            <?php echo $aBurden['adjustment']; ?>
                        </td>
                        <td class="weak">
                            <?php echo $aBurden['percent']; ?>% on <?php echo $aBurden['_amount']; ?>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
