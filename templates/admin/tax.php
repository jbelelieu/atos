
<div id="holderFixed" class="border">

<div class="borderAlterTop">

<?php if (empty($taxesThisYear)) { ?>

    <div class="highlight">
    <h2>Setup your <?php echo $year; ?> taxes!</h2>

    <p>It looks like you haven't set up your taxes for the year in question. Let's get that started so that you can start estimating your payments.</p>

    <?php if (!$strategiesFound) { ?>

    <hr />
    <h5>No Regional Tax Files Found</h5>
    <p>If looks like you haven't loaded up any regional tax files yet for <?php echo $year; ?>.</p>
    <ul>
        <li>
            Head over to the <a href="https://github.com/jbelelieu/atos_modules/taxFiles
" target="_blank">ATOS modules</a> repo and download the regions that you owe money in.<br />For example, if you live in New York City, you'll want "tax-files/<?php echo $year; ?>/Usa.php", "tax-files/<?php echo $year; ?>UsaNy.php", and "tax-files/<?php echo $year; ?>UsaNyNyc.php".
        </li>
        <li>
            Save the files to the "<?php echo $taxBurdenRegionDir; ?>" directory and reload this page.
        </li>
    </ul>

    <p><b>Can't find your regions?</b><br />Why not contribute back to the community by creating the tax files for your regions? The project docs will explain how to do this.</p>

    <?php
    } else { ?>
    <hr />
    <form action="/tax" method="post">
    <h5>We've found some tax files!</h5>
    <p>It looks like you'll be filing in these regions. Please tell me your filing status for each:</p>

    <table class="alignCenter">
        <tbody>
        <?php foreach ($strategies as $aStrategy) { ?>
            <tr>
                <td width="300" class="textRight">
                    <div class="emoji_bump">
                    <?php echo camelToEnglish($aStrategy['region']); ?>
                    </div>
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
<?php } ?>

<div class="pad">

<div class="halfHalfColumns">
    <div>
    <h2 class="noMarginTop">Tax Years</h2>
    </div>
    <div class="textRight">
        <div class="">
            <form action="/tax" method="get">
                <input type="number" name="year" value="<?php echo (isset($_GET['year'])) ? $_GET['year'] : date('Y'); ?>" style="width:120px;" /> <button type="submit">Go</button>
            </form>
        </div>
    </div>
</div>


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
                        <b><?php echo $aTaxYear[$region]['_class']::REGION; ?>:</b> <?php echo snakeToEnglish($strategy); ?><br />
                    <?php
    } ?>
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
                            href="/tax?action=deleteYear&year=<?php echo $aTaxYear['year']; ?>">
                                <?php echo putIcon('fi-sr-trash'); ?>
                        </a>
                    </div>
                </td>
            </tr>
        <?php
    } ?>
    </tbody>
</table>

</div>
</div>
</div>