
<div class="collectionsTable">

    <div class="border pad">
    <h1 style="margin: 0;">
        <?php echo $displayType; ?>
    </h1>
    <p style="margin-bottom: 0;">
        <?php echo $attentionMessage; ?>
    </p>
    </div>
    
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
                        <td class="listLeft">Billed</td>
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

    <form action="/tax/render" method="post">
    <input type="hidden" name="action" value="createEstimatedPayments" />
    <input type="hidden" name="year" value="<?php echo $year; ?>" />
    <input type="hidden" name="income" value="<?php echo empty($_GET['income']) ? '' : $_GET['income']; ?>" />
    <input
        type="hidden"
        name="estimate"
        value="<?php echo empty($_GET['estimate']) ? '' : $_GET['estimate']; ?>" />
        
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
                            <th>Estimated</th>
                            <th>Actual Paid</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="listLeft"></td>
                            <td class="bold"><?php echo $details['results']['_tax']; ?></td>
                            <td class="bold"><?php echo $regionTotals[$region]; ?></td>
                        </tr>
                        <?php
                        $up = 0;
                        foreach ($details['recommendations']['schedule'] as $date => $details) {
                            $thisRegion = $estimatedTaxes[$region];
                            if (array_key_exists($up, $thisRegion)) {
                                $useDate =  $estimatedTaxes[$region][$up]['created_at'];
                                $useAmount = $estimatedTaxes[$region][$up]['amount'];
                            } else {
                                $useDate =  $date;
                                $useAmount =  0;
                            }
                            $addClass = $useAmount > 0 ? 'paid' : ''; ?>
                        <tr>
                            <td class="listLeft">
                                <input
                                    type="date"
                                    class="<?php echo $addClass; ?>"
                                    name="dates[<?php echo $region; ?>][<?php echo $up; ?>]"
                                    value="<?php echo $useDate; ?>"
                                    style="width:120px;" />


                                <!-- <?php echo $details['date']; ?> -->
                                <p class="fieldHelp">Days until due: <?php echo $details['daysUntil']; ?></p>
                            </td>
                            <td><?php echo $details['amount']; ?></td>
                            <td>
                                $<input
                                    type="number"
                                    name="region[<?php echo $region; ?>][<?php echo $up; ?>]"
                                    value="<?php echo $estimatedTaxes[$region][$up]['amount'] ?>"
                                    style="width:120px;" />
                            </td>
                        </tr>
                        <?php
                        $up++;
                        } ?>
                    </tbody>
                </table>
            </div>

            <?php
} ?>
        </div>

        <div class="textCenter">
            <button type="submit">Update Estimated Payments</button>
        </div>
    </form>
    

    <div class="halfHalfColumns">
        <div class="">
            <form action="/tax/render" method="post">
            <input type="hidden" name="action" value="createDeduction" />
            <input type="hidden" name="year" value="<?php echo $year; ?>" />
            <input type="hidden" name="income" value="<?php echo empty($_GET['income']) ? '' : $_GET['income']; ?>" />
            <input type="hidden" name="estimate" value="<?php echo empty($_GET['estimate']) ? '' : $_GET['estimate']; ?>" />

            <h4>Deductions</h4>
            <table>
                <thead>
                    <tr>
                        <th width="230"></th>
                        <th></th>
                        <th></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="textRight weak">Total</td>
                        <td class="bold"><?php echo $income['deductions']; ?></td>
                        <td></td>
                    </tr>
                    <?php foreach ($_raw['deductions'] as $aDeduction) { ?>
                    <tr>
                        <td class="textRight"><?php echo $aDeduction['title']; ?></td>
                        <td><?php echo $aDeduction['adjustment']; ?></td>
                        <td>
                            <a
                                title="Delete"
                                onclick="return confirm('This will delete the deduction - are you sure?')"
                                href="/tax/render?action=deleteDeduction&id=<?php echo $aDeduction['id'] . $queryString; ?>">
                                <?php echo putIcon('fi-sr-trash'); ?>
                            </a>
                        </td>
                    </tr>
                    <?php } ?>
                    <tr>
                        <td class="listLeft">
                            <input type="text" name="title" autocomplete="off" placeholder="Standard Deduction" required="required" style="" />
                            <p class="fieldHelp">Name of the deduction</p>
                        </td>
                        <td>
                            $<input type="number" name="amount" autocomplete="off" placeholder="12950" required="required" style="width:100px" /> <button type="submit">Add</button>
                            <p class="fieldHelp">Amount of the deduction. This will be removed from your base income.</p>
                        </td>
                        <td></td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        </form>
        </div>
        <div class="">
            <form action="/tax/render" method="post">
            <input type="hidden" name="action" value="createAdjustment" />
            <input type="hidden" name="year" value="<?php echo $year; ?>" />
            <input type="hidden" name="income" value="<?php echo empty($_GET['income']) ? '' : $_GET['income']; ?>" />
            <input type="hidden" name="estimate" value="<?php echo empty($_GET['estimate']) ? '' : $_GET['estimate']; ?>" />

            <h4>Adjustments</h4>
            <table>
                <thead>
                    <tr>
                        <th width="230"></th>
                        <th></th>
                        <th></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="textRight weak">Total</td>
                        <td class="bold"><?php echo $income['additionalTaxBurdens']; ?></td>
                    </tr>
                    <?php foreach ($_raw['taxBurdens'] as $aBurden) { ?>
                    <tr>
                        <td class="textRight">
                            <?php echo $aBurden['title']; ?>
                        </td>
                        <td>
                            <?php echo $aBurden['adjustment']; ?>
                        </td>
                        <td class="weak">
                            <?php echo $aBurden['taxable_percent']; ?>% on <?php echo $aBurden['_amount']; ?>
                        </td>
                        <td>
                            <a
                                title="Delete"
                                onclick="return confirm('This will delete the adjustment - are you sure?')"
                                href="/tax/render?action=deleteAdjustment&id=<?php echo $aBurden['id'] . $queryString; ?>">
                                <?php echo putIcon('fi-sr-trash'); ?>
                            </a>
                        </td>
                    </tr>
                    <?php } ?>
                    <tr>
                        <td class="listLeft">
                            <input
                                type="text"
                                name="title"
                                placeholder="Capital Gains"
                                autocomplete="off"
                                required="required" />
                            <p class="fieldHelp">Name of the adjustment</p>
                        </td>
                        <td>
                            $<input
                                type="number"
                                name="taxable_amount"
                                autocomplete="off"
                                required="required"
                                style="width:100px" />
                            <p class="fieldHelp">Enter the full amount being taxed.</p>
                        </td>
                        <td>
                            <input
                                type="number"
                                max=100
                                min=0
                                autocomplete="off"
                                name="taxable_percent"
                                required="required"
                                style="width:70px;" />% <button type="submit">Add</button>
                            <p class="fieldHelp">Percent taxable of the full amount entered to the left.</p>
                        </td>
                    </tr>
                </tbody>
            </table>
            </form>
        </div>
    </div>
</div>
