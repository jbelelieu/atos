<!doctype html>
<html class="no-js" lang="">

<head>
    <meta charset="utf-8">
    <title><?php echo $project['title'] . ": " . $collection['title']; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style type="text/css">
        <?php echo $css; ?>
    </style>
</head>

<body>
    <div id="holderFixed">

        <?php echo $logo; ?>

        <!-- <div class=" noprint" style="padding-bottom:12px;">
            <nav class="marginTop columns5050">
                <div><a href="/invoice">Back to Dashboard</a></div>
                <div class="textRight"><a class="blue bold" href="/invoice?collection=<?php echo $collection['id']; ?>&save=1">Save Invoice</a></div>
            </nav>
        </div> -->

        <div class="border">
            <div class="borderSection pad">
                <div class="columns2575">
                    <div class="textRight">
                        <h4>
                            <span class="larger">
                                <?php echo $project['title']; ?>
                            </span>
                            <br /><br />
                            Sent On<br />
                            <?php echo $sentOn; ?>
                            <?php if (!empty($dueDate)) { ?>
                                <br /><br />
                                Due on<br />
                                <?php echo $dueDate; ?>
                            <?php } ?>
                        </h4>
                    </div>
                    <div>
                        <table width="100%">
                            <tr>
                                <th><b>Billing Party</b></th>
                                <th><b>Bill To</b></th>
                            </tr>
                            <tr>
                                <td width="50%" valign="top">
                                    <b><?php echo $company['title']; ?></b><br />
                                    <?php echo $company['address']; ?>
                                </td>
                                <td width="50%" valign="top">
                                    <b><?php echo $client['title']; ?></b><br />
                                    <?php echo $client['address']; ?>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="borderSection borderSectionTop pad">
                <div class="columns2575">
                    <div class="textRight">
                        <h4>
                            <span class="larger">
                                <?php echo $collection['title']; ?>
                            </span>
                            <br /><br />
                            Invoiced &raquo;
                        </h4>
                    </div>
                    <div>
                        <table width="100%">
                            <thead>
                                <tr class="noHighlight">
                                    <th width="50%">Line Item</th>
                                    <th width="18%">Rate</th>
                                    <th width="12%">Units</th>
                                    <th width="20%">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php echo $rateTypes; ?>
                                <tr class="noHighlight">
                                    <td valign="top" colspan="2"></td>
                                    <td>
                                        <?php echo $totalHours; ?>
                                    </td>
                                    <td valign="top" class="totalFocus">
                                        <?php echo $total; ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <?php if (!empty($client['instructions'])) { ?>
                <div class="sunk columns2575 pad">
                    <div class="textRight">
                        <h4>Instructions &raquo;</h4>
                    </div>
                    <div>
                        <?php echo $client['instructions']; ?>
                    </div>
                </div>
            <?php } ?>

            <?php if ($displayStories) { ?>
            <div class="borderSectionTop">
                <table width="100%" style="font-size:90%;">
                    <thead>
                        <tr class="noHighlight">
                            <th width="100">Task #</th>
                            <th width="">Description</th>
                            <th width="155">Rate Type</th>
                            <th width="60">Hrs</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php echo $stories; ?>
                    </tbody>
                </table>
            </div>
            <?php } ?>
        </div>

        <div class="invoiceFooter">This invoice was generated by <a
                href="https://github.com/jbelelieu/atos" target="_blank">ATOS
                software</a>.</div>
    </div>
</body>
</html>