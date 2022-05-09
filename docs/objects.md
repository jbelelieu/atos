
# Generated Templates

Variables can be used as follows:

```
<?php echo $variableName; ?>
```

Please use all existing templates as guides when creating custom templates.

## Invoice Template

- Location: `templates/invoice/invoice.php`

| Variable       | Type    | Description                                                                                  |
| -------------- | ------- | -------------------------------------------------------------------------------------------- |
| client         | array   | CompanyArray                                                                                 |
| collection     | array   | CollectionArray                                                                              |
| company        | array   | CompanyArray                                                                                 |
| displayStories | boolean | Determines if we are displaying the story table or not                                       |
| dueDate        |         | Days until invoice is due                                                                    |
| logo           |         | <img> tag for your logo                                                                      |
| css            |         | CSS injected via "assets/alternative_view.css"                                               |
| project        | array   | ProjectArray                                                                                 |
| rateTypes      |         | Table rows (generated via templates/invoice/snippets/rates_table_entry.php)                  |
| sentOn         |         | Date the invoice was generated                                                               |
| stories        |         | Table rows (generated via templates/invoice/snippets/story_table_completed_header_entry.php) |
| total          |         | Total due for this invoice                                                                   |
| totalHours     |         | Total units billed                                                                           |


## Report Template

- Location: `templates/report/default_template.php`
  - Note that you can create [custom](reports.md) report templates.
    
| Variable    | Type  | Description                                      |
| ----------- | ----- | ------------------------------------------------ |
| message     |       | Custom message, if any                           |
| title       |       | Custom title, if any                             |
| client      | array | CompanyArray                                     |
| project     | array | ProjectArray                                     |
| company     | array | CompanyArray                                     |
| stories     |       | $results                                         |
| collections | array | CollectionArray                                  |
| logo        |       | <img> tag for your logo                          |
| css         |       | file_get_contents('assets/alternative_view.css') |


## Tax Estimate Template

```
'logo' => logo(),
'css' => file_get_contents('assets/alternative_view.css'),
'queryString' => $queryString,
'year' => $year,
'displayType' => $displayType,
'estimatedTaxes' => $estimatedTaxes,
'regionTotals' => $regionTotals,
'attentionMessage' => $attentionMessage,
'dayNumber' => $dayInTheYear,
'estimateMode' => $doProjectedEstimate,
'averages' => [
    'monthly' => [
        'postTaxIncome' => formatMoney($postTaxMonthlyAverage * 100),
        'tax' => formatMoney($averageMonthlyTax * 100),
        'preTax' => formatMoney($preTaxMonthlyAverage * 100),
    ],
    'daily' => [
        'postTaxIncome' => formatMoney($postTaxDailyAverage * 100),
        'tax' => formatMoney($averageDailyTax * 100),
        'preTax' => formatMoney($preTaxDailyAverage * 100),
    ],
    'actual' => [
        'daily' => [
            'preTax' => formatMoney($currentDailyAverage * 100), // baseIncome / dayNumberInTheYear
        ],
        'monthly' => [
            'preTax' => formatMoney($currentMonthlyAverage * 100), // baseIncome / (dayNumberInTheYear / (365/30))
        ]
    ],
],
'income' => [
    'postTaxMoney' => formatMoney($postTaxMoney * 100),
    'baseIncome' => formatMoney($baseIncome * 100),
    'additionalEstimate' => formatMoney($additionalEstimate * 100),
    'additionalTaxBurdens' => formatMoney($taxBurdens['adjustment'] * 100),
    'deductions' => formatMoney($deductions['adjustment'] * 100),
    'taxableIncome' => formatMoney($taxableIncome * 100),
    'totalDailyAverage' => formatMoney($totalDailyAverage * 100),
],
'taxes' => [
    'totalTax' => formatMoney($tax * 100),
    'effectiveRate' => $taxableIncome > 0 ? round($tax / $taxableIncome, 2) * 100 : 0,
    'regions' => $finalData,
],
'_raw' => [
    'taxBurdens' => $taxBurdens['data'],
    'deductions' => $deductions['data'],
],
```

# Objects

## CollectionObject

- Maps to table in SQLite3
  
## CompanyObject

- Maps to table in SQLite3

## ProjectObject  

- Maps to table in SQLite3

