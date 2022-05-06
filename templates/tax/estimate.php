<!doctype html>
<html class="no-js" lang="">

<head>
    <meta charset="utf-8">
    <title>Tax Estimate: <?php echo $displayType; ?> (ATOS)</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style type="text/css">
        <?php echo $css; ?>
    </style>
    <script src="/assets/main.js"></script>
</head>

<body>
    <div id="holderFixed">
        <a href="/tax"><?php echo $logo; ?></a>

        <div class="textCenter noprint" style="padding-bottom:12px;">
            <nav class="marginTop">
                <a href="/tax">Back to Taxes</a>
                <a class="blue bold" href="/tax/render?<?php echo $queryString; ?>&save=1">Save Estimate</a>
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
                                <br /><br />
                                Generated on<br />
                                <?php echo formatDate(date('Y-m-d H:i:s')); ?>
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
                                        <td class="listLeft">Estimated Taxes Due</td>
                                        <td class="bold"><?php echo $taxes['totalTax']; ?></td>
                                    </tr>
                                    <tr>
                                        <td class="gray listLeft">Currently Set Aside</td>
                                        <td class="gray">
                                            <?php echo $taxes['asideTotal']; ?> (<?php echo $taxes['asideDifference']; ?>)
                                        </td>
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
                                    TAX BURDEN</br >
                                    <?php echo $details['results']['_tax']; ?> (<button type="button" class="a" onClick="toggleDiv('region-<?php echo $region; ?>');">?</button>)
                                </span><br /><br />
                                <?php echo $details['_class']::REGION; ?>
                                <br />
                                <?php echo $details['filingStrategy'] ?>
                                <br />
                                <?php echo $details['recommendations']['percentOfTotalTaxBurden'] ?>% of total
                                <br /><br />
                            </h4>
                        </div>
                        <div class="pad borderLeft">
                            <table>
                                <thead>
                                    <tr class="noHighlight">
                                        <th width="170">Date Due or Paid</th>
                                        <th>Rec. Payment</th>
                                        <th>Actual Paid</th>
                                        <th>Difference</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $up = 0;
                                    $left = 4;
                                    $totalDifference = 0;
                                    $difference = 0;
                                    $addPerPayment = 0;

                                    foreach ($details['recommendations']['schedule'] as $date => $sDetails) {
                                        $thisRegion = $estimatedTaxes[$region];

                                        $split = ($difference != 0)
                                            ? ceil($difference / $left)
                                            : 0;

                                        $sDetails['_amount'] -= ($split + $addPerPayment);
                                        $sDetails['amount'] = formatMoney($sDetails['_amount'] * 100);

                                        $addPerPayment += $split;

                                        if (array_key_exists($up, $thisRegion)) {
                                            $useDate =  $estimatedTaxes[$region][$up]['created_at'];
                                            $useAmount = $estimatedTaxes[$region][$up]['amount'];

                                            $difference = ($useAmount > 0)
                                                ? $useAmount - $sDetails['_amount']
                                                : 0;
                                        } else {
                                            $useDate =  $date;
                                            $useAmount =  0;
                                            $difference = 0;
                                        }

                                        $totalDifference += $difference;
                                        
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
                                        <td class="<?php echo ($difference === 0) ? '' : 'gray'; ?>">
                                            <?php echo $sDetails['amount']; ?>
                                        </td>
                                        <td>
                                            $<input
                                                type="number"
                                                name="region[<?php echo $region; ?>][<?php echo $up; ?>]"
                                                value="<?php echo $estimatedTaxes[$region][$up]['amount'] ?>"
                                                style="width:120px;" />
                                        </td>
                                        <td class="<?php echo ($difference < 0) ? 'red' : ''; ?>">
                                            <?php
                                                echo ($difference !== 0)
                                                    ? formatMoney($difference * 100)
                                                    :  '-'; ?>
                                        </td>
                                    </tr>
                                    <?php
                                        $left--;
                                        $up++;
                                    } ?>
                                </tbody>
                                <tr class="noHighlight">
                                    <td class="listLeft">
                                    </td>
                                    <td class="bold"></td>
                                    <td class="bold"><?php echo $regionTotals[$region]; ?></td>
                                    <td class="bold <?php echo $totalDifference < 0 ? 'red' : ''; ?>"><?php echo formatMoney($totalDifference * 100); ?></td>
                                </tr>
                             </table>

                             <div class="hide marginTop" id="region-<?php echo $region; ?>">
                                <table class="weak">
                                    <thead>
                                    <tr class="noHighlight">
                                        <th>Bracket</th>
                                        <th>Taxable</th>
                                        <th>Bracket Tax</th>
                                        <th>Total Tax</th>
                                        <th>Remaining</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $totalTax = 0;
                                    foreach ($details['results']['taxedAt']  as $item) {
                                        $totalTax += $item['tax']; ?>
                                        <tr>
                                            <td><?php echo $item['rate']; ?>%</td>
                                            <td><?php echo formatMoney($item['taxableAmountInBracket'] * 100); ?></td>
                                            <td>
                                                <?php echo formatMoney($item['tax'] * 100); ?>
                                            </td>
                                            <td>
                                                <?php echo formatMoney($totalTax * 100); ?>
                                            </td>
                                            <td>
                                                <?php echo formatMoney($item['remainining'] * 100); ?>
                                            </td>
                                        </tr>
                                    <?php
                                    } ?>
                                    </tbody>
                                </table>
                             </div>
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
                                    <tr class="noHighlight">
                                        <th width="50%">Deduction</th>
                                        <th>Amount</th>
                                        <th width="50"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($_raw['deductions'] as $aDeduction) { ?>
                                    <tr>
                                        <td>
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
                                    <tr class="noHighlight">
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
                                <tr class="noHighlight">
                                    <th width="">Adjustment</th>
                                    <th width="190">Taxed Amount</th>
                                    <th width="80"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($_raw['taxBurdens'] as $aBurden) { ?>
                                <tr>
                                    <td>
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
                                <tr class="noHighlight">
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
                                <tr class="noHighlight">
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
