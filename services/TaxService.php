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

    /**
     * @param array $data
     * @return void
     */
    public function createDeduction(array $data)
    {
        $year = (empty($data['year'])) ? date('Y') : $data['year'];

        $statement = $this->db->prepare('
            INSERT INTO tax_deduction (year, title, amount)
            VALUES (:year, :title, :amount)
        ');
        $statement->bindParam(':year', $year);
        $statement->bindParam(':title', $data['title']);
        $statement->bindParam(':amount', $data['amount']);
        $statement->execute();

        redirect(
            "/tax/render",
            null,
            'Your deduction has been added.',
            null,
            false,
            [
                'year' => $year,
                'estimate' => $data['estimate'],
                'income' => $data['income'],
            ]
        );
    }

    /**
     * @param array $data
     * @return void
     */
    public function createAdjustment(array $data)
    {
        $year = (empty($data['year'])) ? date('Y') : $data['year'];

        $statement = $this->db->prepare('
            INSERT INTO tax_adjustment (year, title, taxable_percent, taxable_amount)
            VALUES (:year, :title, :taxable_percent, :taxable_amount)
        ');
        $statement->bindParam(':year', $year);
        $statement->bindParam(':title', $data['title']);
        $statement->bindParam(':taxable_percent', $data['taxable_percent']);
        $statement->bindParam(':taxable_amount', $data['taxable_amount']);

        $statement->execute();

        redirect(
            "/tax/render",
            null,
            'Your adjustment has been added.',
            null,
            false,
            [
                'year' => $year,
                'estimate' => $data['estimate'],
                'income' => $data['income'],
            ]
        );
    }
    
    public function createEstimatedPayments(array $data)
    {
        $year = (empty($data['year'])) ? date('Y') : $data['year'];

        // Delete all entries for this year
        $statement = $this->db->prepare('
            DELETE FROM tax_payment
            WHERE year = :year
        ');
        $statement->bindParam(':year', $year);
        $statement->execute();

        // Recreate updated entires
        $mainKey = 0;
        foreach ($data['region'] as $regionKey => $paymentOrder) {
            $secondKey = 0;
            foreach ($paymentOrder as $anOrder => $anOrderAmount) {
                $statement = $this->db->prepare('
                    INSERT INTO tax_payment (created_at, amount, year, region, payment_order)
                    VALUES (:created_at, :amount, :year, :region, :payment_order)
                ');
                $statement->bindParam(':created_at', $data['dates'][$regionKey][$secondKey]);
                $statement->bindParam(':amount', $anOrderAmount);
                $statement->bindParam(':year', $year);
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
                'year' => $year,
                'estimate' => $data['estimate'],
                'income' => $data['income'],
            ]
        );
    }

    /**
     * @param array $data
     * @return void
     */
    public function deleteAdjustment(array $data)
    {
        $year = (empty($data['year'])) ? date('Y') : $data['year'];

        $statement = $this->db->prepare('
            DELETE FROM tax_adjustment
            WHERE id = :id
        ');
        $statement->bindParam(':id', $data['id']);
        $statement->execute();

        redirect(
            "/tax/render",
            null,
            'Adjustment deleted, very nice!',
            null,
            false,
            [
                'year' => $year,
                'estimate' => $data['estimate'],
                'income' => $data['income'],
            ]
        );
    }

    /**
     * @param array $data
     * @return void
     */
    public function deleteDeduction(array $data)
    {
        $year = (empty($data['year'])) ? date('Y') : $data['year'];

        $statement = $this->db->prepare('
            DELETE FROM tax_deduction
            WHERE id = :id
        ');
        $statement->bindParam(':id', $data['id']);
        $statement->execute();

        redirect(
            "/tax/render",
            null,
            'Deduction deleted; I hope you have some other ones...',
            null,
            false,
            [
                'year' => $year,
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
            FROM tax_payment
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
        $adjustments = $this->getTaxAdjustments($year);

        $additionalTax = 0;

        $data = [];

        foreach ($adjustments as $anAddition) {
            $amount = $anAddition['taxable_amount'] * ($anAddition['taxable_percent'] / 100);

            $additionalTax += $amount;

            $data[] = [
                ...$anAddition,
                '_amount' => formatMoney($anAddition['taxable_amount'] * 100),
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
     * @return array
     */
    public function getTaxDeductions(int $year): array
    {
        $statement = $this->db->prepare("
            SELECT *
            FROM tax_deduction
            WHERE year = :year
            ORDER BY title ASC
        ");
        $statement->bindParam(':year', $year);
        $statement->execute();

        return $statement->fetchAll();
    }

    /**
     * @param integer $year
     * @return array
     */
    public function getTaxAdjustments(int $year): array
    {
        $statement = $this->db->prepare("
            SELECT *
            FROM tax_adjustment
            WHERE year = :year
            ORDER BY title ASC
        ");
        $statement->bindParam(':year', $year);
        $statement->execute();

        return $statement->fetchAll();
    }

    /**
     * @param integer $year
     * @return void
     */
    public function getDeductions(int $year)
    {
        $deductions = $this->getTaxDeductions($year);

        $adjustments = 0;

        $data = [];

        foreach ($deductions as $aDeduction) {
            $adjustments += $aDeduction['amount'];
            
            $data[] = [
                ...$aDeduction,
                'adjustment' => formatMoney($aDeduction['amount'] * 100),
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
