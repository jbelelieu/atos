<?php if (empty($taxesThisYear)) { ?>

<div class="border attentionTop">
    <div class="formBox padLess">

    <h5>Setup your <?php echo $year; ?> taxes!</h5>

    <p>It looks like you haven't set up your taxes for the year in question. Let's get that started so that you can start estimating your payments.</p>

    <?php
    if (!$strategiesFound) {
        ?>

        <h1>No Regional Tax Burdens Found</h1>
        <p>If looks like you haven't loaded up any regional tax files.</p>
        <ul>
            <li>
                Head over to the <a href="https://github.com/jbelelieu/atos_modules/taxFiles
" target="_blank">ATOS modules</a> repo and download the regions that you owe money in. For example, if you live in New York City, you'll want "Usa", "UsaNy", and "UsaNyNyc".
            </li>
            <li>
                Place the files in the "<?php echo $taxBurdenRegionDir; ?>"" directory.
            </li>
        </ul>

        <p>Can't find your regions? No problem, you can create your own and contribute back to the community! The project docs will explain how to do this.</p>

    <?php
    } else { ?>
        <form action="/tax" method="post">
        <h1>We've found some tax files!</h1>
        <p>It looks like you'll be filing in these regions. Please tell me your filing status for each:</p>
    
        <table class="alignCenter">
            <tbody>
            <?php foreach ($strategies as $aStrategy) { ?>
                <tr>
                    <td width="300" class="textRight">
                        <?php echo $aStrategy['_class']::REGION; ?>
                    </td>
                    <td>
                        <select style="width:200px;" name="strategies[<?php echo $aStrategy['title']; ?>]">
                            <?php foreach ($aStrategy['strategies'] as $aFilingStatus) { ?>
                                <option value="<?php echo $aFilingStatus; ?>">
                                    <?php echo snakeToEnglish($aFilingStatus); ?>
                                </option>
                            <?php } ?>
                        </select>
                    </td>
                </tr>
            <?php } ?>
                <tr>
                    <td width="300" class="textRight"></td>
                    <td>
                        <input type="hidden" name="year" value="<?php echo $year; ?>" />
                        <input type="hidden" name="action" value="setupTaxes" />
                        <button type="submit">Setup Taxes</button>
                    </td>
                </tr>
            </tbody>
        </table>
        </form>
    <?php } ?>
    </div>
</div>

<?php } ?>


<h1>Tax Years</h1>
<table>
    <thead>
        <tr>
            <th>Year</th>
            <th>Statuses</th>
            <th>Generate Estimates</th>
            <th width="42"></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($taxes as $aTaxYear) { ?>
            <tr>
                <td>
                    <a href="/tax/render?year=<?php echo $aTaxYear['year']; ?>">
                        <?php echo $aTaxYear['year']; ?>
                    </a>
                </td>
                <td>
                    <?php foreach ($aTaxYear['strategies'] as $region => $strategy) { ?>
                        <b><?php echo $strategies[$region]['_class']::REGION; ?>:</b> <?php echo snakeToEnglish($strategy); ?><br />
                    <?php } ?>
                </td>
                <td>
                    <nav class="strong" style="margin-bottom:12px;">
                    <a href="/tax/render?year=<?php echo $aTaxYear['year']; ?>">
                        Actual Current
                    </a><a href="/tax/render?year=<?php echo $aTaxYear['year']; ?>&estimate=true">
                        Projected
                    </a>
                    </nav>
                    
                    <form action="/tax/render" method="get">
                        <input type="hidden" name="year" value="<?php echo $aTaxYear['year']; ?>" />
                        <input type="hidden" name="estimate" value="true" />
                        $<input type="number" name="income" placeholder="250000" style="width:100px;" /> <button type="submit">Project</button>
                    </form>
                </td>
                <td>
                    <div class="textRight emoji_bump">
                        <a
                            title="Delete"
                            onclick="return confirm('This will delete the status - are you sure?')"
                            href="<?php echo $deleteLink; ?>">
                                <?php echo putIcon('fi-sr-trash'); ?>
                        </a>
                    </div>
                </td>
            </tr>
        <?php
    } ?>
    </tbody>
</table>
