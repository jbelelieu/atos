<!doctype html>
<html class="no-js" lang="">

<head>
    <meta charset="utf-8">
    <title>Tax Estimate: <?php echo $displayType; ?> (ATOS)</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style type="text/css">
        <?php echo $css; ?>
    </style>
</head>

<body>
    <div id="holderFixed">
        <?php echo $logo; ?>

        <div class="textCenter" style="padding-bottom:12px;">
            <nav class="marginTop">
                <a href="#d0">Tax Breakdown</a>
                <a href="#regions">Regional Tax Burdens</a>
                <a href="#deductions">Deducations</a>
                <a href="#adjustments">Adjustments</a>
                <a href="#d5">Averages</a>
            </nav>
        </div>

        <div class="border">
            <!-- start -->
            <div class="borderSection">
                <div class="columns2575">
                    <div class="textRight pad">
                        <h4>
                            <span class="larger">
                                <?php echo $displayType; ?>
                            </span>
                        </h4>
                    </div>
                    <div class="attentionTop pad borderLeft">
                        <div>
                        <p>
                            <?php echo $attentionMessage; ?>
                        </p>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end -->
            
            <a name="d0"></a>
            <div class="borderSection">
                <div class="columns2575">
                    <div class="textRight pad">
                        <h4>
                            <span class="larger">
                                Tax Breakdown
                            </span>
                        </h4>
                    </div>
                    <div class="pad borderLeft">
                        <div>
                            <table>
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
                                        <td class="bold"><?php echo $taxes['totalTax']; ?></td>
                                    </tr>
                                    <tr>
                                        <td class="listLeft">Effective rate</td>
                                        <td><?php echo $taxes['effectiveRate']; ?>%</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end -->
            

            <a name="regions"></a>
            <form action="/tax/render" method="post">
                <input type="hidden" name="action" value="createEstimatedPayments" />
                <input type="hidden" name="year" value="<?php echo $year; ?>" />
                <input type="hidden" name="income" value="<?php echo empty($_GET['income']) ? '' : $_GET['income']; ?>" />
                <input
                    type="hidden"
                    name="estimate"
                value="<?php echo empty($_GET['estimate']) ? '' : $_GET['estimate']; ?>" />
                
                <?php foreach ($taxes['regions'] as $region => $details) { ?>

                <div class="borderSection">
                    <div class="columns2575 sunk">
                        <div class="textRight pad">
                            <h4>
                                <span class="larger red">
                                    TAX BURDEN
                                </span><br /><br />
                                <?php echo $details['_class']::REGION; ?>
                                <br />
                                <?php echo $details['filingStrategy'] ?>
                                <br />
                                <?php echo $details['recommendations']['percentOfTotalTaxBurden'] ?>% of total
                            </h4>
                        </div>
                        <div class="pad borderLeft">
                            <table>
                                <thead>
                                    <tr>
                                        <th width="230">Date Due</th>
                                        <th>Estimated</th>
                                        <th>Actual Paid</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $up = 0;
                                    foreach ($details['recommendations']['schedule'] as $date => $sDetails) {
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


                                            <!-- <?php echo $sDetails['date']; ?> -->
                                            <p class="weak">Days until due: <?php echo $sDetails['daysUntil']; ?></p>
                                        </td>
                                        <td><?php echo $sDetails['amount']; ?></td>
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
                                <tr>
                                    <td class="listLeft"></td>
                                    <td class="bold"><?php echo $details['results']['_tax']; ?></td>
                                    <td class="bold"><?php echo $regionTotals[$region]; ?></td>
                                </tr>
                             </table>
                        </div>
                        
                    </div>
                </div>
                <?php } ?>
                <div class="textCenter sunk pad">
                    <button type="submit">Update Estimated Payments</button>
                </div>
                </form>
            <!-- end -->

            <a name="deductions"></a>
            <div class="borderSection">
                <div class="columns2575">
                    <div class="textRight pad">
                        <h4>
                            <span class="larger blue">
                                Deductions
                            </span>
                            <br /><br />
                            <?php echo $income['deductions']; ?>
                        </h4>
                    </div>
                    <div class="pad borderLeft">
                        
                        <form action="/tax/render" method="post">
                            <input type="hidden" name="action" value="createDeduction" />
                            <input type="hidden" name="year" value="<?php echo $year; ?>" />
                            <input type="hidden" name="income" value="<?php echo empty($_GET['income']) ? '' : $_GET['income']; ?>" />
                            <input type="hidden" name="estimate" value="<?php echo empty($_GET['estimate']) ? '' : $_GET['estimate']; ?>" />

                            <table>
                                <thead>
                                    <tr>
                                        <th width="50%">Deduction</th>
                                        <th>Amount</th>
                                        <th width="50"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($_raw['deductions'] as $aDeduction) { ?>
                                    <tr>
                                        <td class="textRight">
                                            <?php echo $aDeduction['title']; ?>
                                        </td>
                                        <td>
                                            <?php echo $aDeduction['adjustment']; ?>
                                        </td>
                                        <td>
                                            <a
                                                title="Delete"
                                                onclick="return confirm('This will delete the deduction - are you sure?')"
                                                href="/tax/render?action=deleteDeduction&id=<?php echo $aDeduction['id'] . $queryString; ?>">
                                                Delete
                                            </a>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                    <tr>
                                        <td class="listLeft">
                                            <div class="marginTop">
                                                <input type="text" name="title" autocomplete="off" placeholder="Standard Deduction" required="required" style="width:100%;" />
                                                <p class="weak">Name of the deduction</p>
                                            </div>
                                        </td>
                                        <td colspan="2">
                                            <div class="marginTop">
                                                $<input type="number" name="amount" autocomplete="off" placeholder="12950" required="required" style="width:100px" /> <button type="submit">Add</button>
                                                <p class="weak">Amount of the deduction. This will be removed from your base income.</p>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </form>
                    </div>
                </div>
            </div>
            <!-- end -->
            
            <a name="adjustments"></a>
            <div class="borderSection">
                <div class="columns2575">
                    <div class="textRight pad">
                        <h4>
                            <span class="larger red">
                                Adjustments
                            </span>
                            <br /><br />
                            <?php echo $income['additionalTaxBurdens']; ?>
                        </h4>
                    </div>
                    <div class="pad borderLeft">
                        
                        <form action="/tax/render" method="post">
                        <input type="hidden" name="action" value="createAdjustment" />
                        <input type="hidden" name="year" value="<?php echo $year; ?>" />
                        <input type="hidden" name="income" value="<?php echo empty($_GET['income']) ? '' : $_GET['income']; ?>" />
                        <input type="hidden" name="estimate" value="<?php echo empty($_GET['estimate']) ? '' : $_GET['estimate']; ?>" />

                        <table>
                            <thead>
                                <tr>
                                    <th width="">Adjustment</th>
                                    <th width="190">Taxed Amount</th>
                                    <th width="80"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($_raw['taxBurdens'] as $aBurden) { ?>
                                <tr>
                                    <td class="textRight">
                                        <p class="deAdHeading">
                                            <?php echo $aBurden['title']; ?>
                                        </p>
                                        <p class="weak">
                                            <?php echo $aBurden['taxable_percent']; ?>% on <?php echo $aBurden['_amount']; ?>
                                        </p>
                                    </td>
                                    <td>
                                        <?php echo $aBurden['adjustment']; ?>
                                    </td>
                                    <td>
                                        <a
                                            title="Delete"
                                            onclick="return confirm('This will delete the adjustment - are you sure?')"
                                            href="/tax/render?action=deleteAdjustment&id=<?php echo $aBurden['id'] . $queryString; ?>">
                                            Delete
                                        </a>
                                    </td>
                                </tr>
                                <?php } ?>
                                <tr>
                                    <td class="listLeft">
                                        <div class="marginTop">
                                        <input
                                            type="text"
                                            name="title"
                                            placeholder="Capital Gains"
                                            autocomplete="off"
                                            style="width:100%;"
                                            required="required" />
                                        <p class="weak">Name of the adjustment</p>
                                        </div>
                                    </td>
                                    <td colspan="2">
                                        <div class="marginTop">
                                        $<input
                                            type="number"
                                            placeholder="5000"
                                            name="taxable_amount"
                                            autocomplete="off"
                                            required="required"
                                            style="width:90%" />
                                        <p class="weak">Enter the full amount being taxed.</p>
                                        </div>
                                    
                                        <hr />

                                        <input
                                            type="number"
                                            max=100
                                            min=0
                                            placeholder="15"
                                            autocomplete="off"
                                            name="taxable_percent"
                                            required="required"
                                            style="width:80px;" />%&nbsp;&nbsp;<button type="submit">Add</button>
                                        <p class="weak">Percent taxable of the full amount entered to the left.</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        </form>
                    </div>
                </div>
            </div>
            <!-- end -->

            <div class="borderSection">
                <div class="columns2575">
                    <div class="textRight pad">
                        <h4>
                            <span class="larger">
                                Averages
                            </span>
                        </h4>
                    </div>
                    <div class="pad borderLeft">
                        <table>
                            <thead>
                                <tr>
                                    <th width="">Average</th>
                                    <th width="120">Daily</th>
                                    <th width="120">Monthly</th>
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
                                <!-- <tr class="">
                                    <td class="listLeft">
                                        <p class="deAdHeading">Actual</p>
                                        <p class="weak">This is how much you have actually earned this year on average per day through <?php echo $dayNumber; ?> days.</p>
                                    </td>
                                    <td><?php echo $averages['actual']['daily']['preTax']; ?></td>
                                    <td><?php echo $averages['actual']['monthly']['preTax']; ?></td>
                                </tr> -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- end -->
        </div>

        <div class="invoiceFooter">This tax estimate was generated by <a
            href="https://github.com/jbelelieu/atos" target="_blank">ATOS
            software</a>.</div>
    </div>
</body>
</html>
