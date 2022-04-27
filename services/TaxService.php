<?php

namespace services;

use services\BaseService;

/**
 * ATOS: "Built by freelancer 🙋‍♂️, for freelancers 🕺 🤷 💃🏾 "
 *
 * Service for all things tax related.
 *
 * Disclaimer: I am not an accountant and only use this as
 * a general guide for how much I need to pay in estimated
 * taxes. Use at your own risk: no guarantees of accuracy!
 * Also note that I based this on US-tax law; I have no idea
 * if it is applicable to other countries.
 *
 * @author @jbelelieu
 * @copyright Humanity, any year.
 * @package Services
 * @license AGPL-3.0 License
 * @link https://github.com/jbelelieu/atos
 */
class TaxService extends BaseService
{
    /**
     * This would be set by tax year and maps to that array key
     * within the tax file.
     *
     * @return void
     */
    public function getTaxStrategy()
    {
        // TODO: Grab from DB.
        return 'single';
    }

    /**
     * @param array $brackets
     * @param integer $taxableIncome
     * @return void
     */
    public function calculateTax(array $brackets, int $taxableIncome): array
    {
        $tax = 0;
        $lastKnownCeiling = 0;

        $taxable = $taxableIncome;

        foreach ($brackets as $percentTaxed => $upTo) {
            $taxableInThisBracket = (!$upTo)
                ? $taxable
                : $upTo - $lastKnownCeiling;

            $taxableAmountInBracket = ($taxable >= $taxableInThisBracket)
                ? $taxableInThisBracket
                : $taxable;

            $taxable -= $taxableAmountInBracket;

            $thisTaxBurden = $taxableAmountInBracket * ($percentTaxed / 100);

            $tax += $thisTaxBurden;

            $taxedAt[] = [
                'maximumTaxable' => $taxableInThisBracket,
                'taxableAmountInBracket' => $taxableAmountInBracket,
                'rate' => $percentTaxed,
                'tax' => round($thisTaxBurden),
            ];

            $lastKnownCeiling = $upTo;

            if ($taxable <= 0) {
                break;
            }
        }

        return [
            'tax' => round($tax),
            'taxedAt' => $taxedAt,
            'taxableIncome' => $taxableIncome,
        ];
    }

    /**
     * @param integer $year
     * @return void
     */
    public function getAdditionalTaxBurdens(int $year)
    {
        $additionMock = [
            [
                'isReduction' => true,
                'amount' => 3000,
                'percent' => 15,
                'title' => 'Capital Gains',
            ],
        ];

        $additionalTax = 0;

        $data = [];

        foreach ($additionMock as $anAddition) {
            $amount = $anAddition['amount'] * ($anAddition['percent'] / 100);

            $additionalTax += $amount;

            $data[] = [
                ...$anAddition,
                'adjustment' => $amount,
            ];
        }

        return [
            'adjustment' => $additionalTax,
            'data' => $additionMock,
        ];
    }

    /**
     * @param integer $year
     * @return void
     */
    public function getDeductions(int $year)
    {
        $deductionMock = [
            [
                'isReduction' => true,
                'amount' => 12950,
                'percent' => 100,
                'title' => 'Standard Deduction',
            ],
            // [
            //     'isReduction' => true,
            //     'amount' => 9000,
            //     'percent' => 100,
            //     'title' => 'SEP IRA Contribution',
            // ]
        ];

        $adjustments = 0;

        $data = [];

        foreach ($deductionMock as $aDeduction) {
            $amount = $aDeduction['amount'] * ($aDeduction['percent'] / 100);

            $adjustments += $amount;
            
            $data[] = [
                ...$aDeduction,
                'adjustment' => $amount,
            ];
        }

        return [
            'adjustment' => $adjustments,
            'data' => $data,
        ];
    }
    
    /**
     * Note that this returns the true amount, not a dollar cents
     * representation of the value, like elsewhere in the app.
     *
     * @param integer $year
     * @return integer
     */
    public function getTotalBaseIncomeByYear(int $year): float
    {
        $statement = $this->db->prepare("
            SELECT
                SUM(story_hour_type.rate * story.hours/ 100) as totalValue
            FROM
                story
            JOIN
                story_hour_type ON story.rate_type = story_hour_type.id
            JOIN
                story_status ON story.status = story_status.id
            WHERE
                story_status.is_billable_state = true
                AND ended_at >= :dateLow
                AND ended_at < :dateHigh
        ");

        $dateLow = $year . '-01-01';
        $year++;
        $dateHigh = $year . '-01-01';

        $statement->bindParam(':dateLow', $dateLow);
        $statement->bindParam(':dateHigh', $dateHigh);

        $statement->execute();

        $total = $statement->fetch();

        return ($total) ? (float) $total['totalValue'] : 0;
    }

    /**
     * TODO: We would grab this from the DB based on
     *       known tax regions (used submitted) per year.
     *
     * @return array
     */
    public function getTaxRegions(int $year): array
    {
        return [
            'Usa',
            'UsaNy',
            'UsaNyNyc',
        ];
    }
}
