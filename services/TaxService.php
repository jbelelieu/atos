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
    public function calculateTax(array $brackets, $taxableIncome): array
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
            'tax' => $tax,
            '_tax' => formatMoney($tax * 100),
            'taxedAt' => $taxedAt,
            'taxableIncome' => $taxableIncome,
        ];
    }


    public function createDeduction(array $data)
    {
    }

    public function createAdjustment(array $data)
    {
    }
    
    public function createEstimatedPayments(array $data)
    {
        // Delete all entries for this year
        $statement = $this->db->prepare('
            DELETE FROM tax_payments
            WHERE year = :year
        ');
        $statement->bindParam(':year', $data['year']);
        $statement->execute();

        // Recreate updated entires
        $mainKey = 0;
        foreach ($data['region'] as $regionKey => $paymentOrder) {
            $secondKey = 0;
            foreach ($paymentOrder as $anOrder => $anOrderAmount) {
                $statement = $this->db->prepare('
                    INSERT INTO tax_payments (created_at, amount, year, region, payment_order)
                    VALUES (:created_at, :amount, :year, :region, :payment_order)
                ');
                $statement->bindParam(':created_at', $data['dates'][$regionKey][$secondKey]);
                $statement->bindParam(':amount', $anOrderAmount);
                $statement->bindParam(':year', $data['year']);
                $statement->bindParam(':region', $regionKey);
                $statement->bindParam(':payment_order', $anOrder);
                $statement->execute();

                $secondKey++;
            }

            $mainKey++;
        }

        redirect(
            "/tax/render",
            null,
            'Your estimated taxes have been updated.',
            null,
            false,
            [
                'year' => $data['year'],
                'estimate' => $data['estimate'],
                'income' => $data['income'],
            ]
        );
    }

    /**
     * @return array
     */
    public function getEstimatedPaymentsForYear(int $year)
    {
        $statement = $this->db->prepare("
            SELECT *
            FROM tax_payments
            WHERE year = :year
            ORDER BY region ASC, payment_order ASC
        ");
        $statement->bindParam(':year', $year);
        $statement->execute();

        return $statement->fetchAll();
    }
    
    /**
     * @param integer $year
     * @return void
     */
    public function getAdditionalTaxBurdens(int $year)
    {
        // TODO
        $additionMock = [
            [
                'amount' => 3000,
                'percent' => 15,
                'title' => 'Capital Gains',
            ],
        ];
        //

        $additionalTax = 0;

        $data = [];

        foreach ($additionMock as $anAddition) {
            $amount = $anAddition['amount'] * ($anAddition['percent'] / 100);

            $additionalTax += $amount;

            $data[] = [
                ...$anAddition,
                '_amount' => formatMoney($anAddition['amount'] * 100),
                'adjustment' => formatMoney($amount * 100),
            ];
        }

        return [
            'adjustment' => floatval($additionalTax),
            'data' => $data,
        ];
    }

    /**
     * @param integer $year
     * @return void
     */
    public function getDeductions(int $year)
    {
        // TODO
        $deductionMock = [
            [
                'amount' => 12950,
                'percent' => 100,
                'title' => 'Standard Deduction',
            ],
            // [
            //     'amount' => 51000,
            //     'percent' => 100,
            //     'title' => 'SEP IRA Contribution',
            // ]
        ];
        //

        $adjustments = 0;

        $data = [];

        foreach ($deductionMock as $aDeduction) {
            $amount = $aDeduction['amount'] * ($aDeduction['percent'] / 100);

            $adjustments += $amount;
            
            $data[] = [
                ...$aDeduction,
                'adjustment' => formatMoney($amount * 100),
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
            'Usa' => 'single',
            'UsaNy' => 'single',
            'UsaNyNyc' => 'single',
        ];
    }
}
