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
                'remainining' => $taxable,
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
            ],
            'deductions'
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
            ],
            'adjustments'
        );
    }
    
    /**
     * @param array $data
     * @return void
     */
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
    public function createMoneyAside(array $data)
    {
        $statement = $this->db->prepare('
            INSERT INTO tax_aside (
                year,
                created_at,
                \'group\',
                amount,
                title
            )
            VALUES (
                :year,
                :created_at,
                :group,
                :amount,
                :title
            )
        ');
        $statement->bindParam(':year', $data['year']);
        $statement->bindParam(':created_at', $data['created_at']);
        $statement->bindParam(':group', $data['group']);
        $statement->bindParam(':amount', $data['amount']);
        $statement->bindParam(':title', $data['title']);

        $statement->execute();

        redirect(
            "/tax",
            null,
            'Your money has been logged.'
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
            ],
            'adjustments'
        );
    }

    /**
     * @param integer $asideId
     * @return void
     */
    public function deleteMoneyAside(int $asideId)
    {
        $statement = $this->db->prepare('
            DELETE FROM tax_aside
            WHERE id = :id
        ');
        $statement->bindParam(':id', $asideId);
        $statement->execute();

        redirect(
            "/tax",
            null,
            'Your item has been deleted.'
        );
    }

    /**
     * @param int $year
     * @param bool $skipRedirect
     * @return void
     */
    public function deleteYear(int $year, bool $skipRedirect = false)
    {
        try {
            $this->db->beginTransaction();

            $statement = $this->db->prepare('
                DELETE FROM tax
                WHERE year = :year
            ');
            $statement->bindParam(':year', $year);
            $statement->execute();

            $statement = $this->db->prepare('
                DELETE FROM tax_adjustment
                WHERE year = :year
            ');
            $statement->bindParam(':year', $year);
            $statement->execute();

            $statement = $this->db->prepare('
                DELETE FROM tax_deduction
                WHERE year = :year
            ');
            $statement->bindParam(':year', $year);
            $statement->execute();

            $statement = $this->db->prepare('
                DELETE FROM tax_payment
                WHERE year = :year
            ');
            $statement->bindParam(':year', $year);
            $statement->execute();
        
            $this->db->commit();

            if (!$skipRedirect) {
                redirect("/tax", null, 'Tax year deleted.');
            }
        } catch (\PDOException $e) {
            $this->db->rollBack();

            systemError($e->getMessage());
        }
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
            ],
            'deductions'
        );
    }

    /**
     * @return array
     */
    public function getMoneyAside(int $year)
    {
        $statement = $this->db->prepare("
            SELECT *
            FROM tax_aside
            WHERE year = :year
            ORDER BY created_at ASC
        ");
        $statement->bindParam(':year', $year);
        $statement->execute();

        return $statement->fetchAll();
    }

    /**
     * @param integer $year
     * @return integer
     */
    public function getTotalAsideForYear(int $year): int
    {
        $statement = $this->db->prepare("
            SELECT SUM(amount) as total
            FROM tax_aside
            WHERE year = :year
        ");
        $statement->bindParam(':year', $year);
        $statement->execute();

        $data = $statement->fetch();

        return $data['total'];
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
    public function getTax(int $year)
    {
        $statement = $this->db->prepare("
            SELECT *
            FROM tax
            WHERE year = :year
        ");
        $statement->bindParam(':year', $year);
        $statement->execute();

        $data = $statement->fetch();

        if ($data) {
            $data['strategies'] = json_decode($data['strategies'], true);
        }

        return $data;
    }

    /**
     * @return array
     */
    public function getTaxes()
    {
        $statement = $this->db->prepare("
            SELECT *
            FROM tax
            ORDER BY year DESC
        ");
        $statement->execute();

        $taxes = $statement->fetchAll();
        foreach ($taxes as &$tax) {
            $tax['strategies'] = json_decode($tax['strategies'], true);
        }

        return $taxes;
    }

    /**
     * @param integer $year
     * @return array
     */
    public function getTaxDeductions(int $year)
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
    public function getTaxAdjustments(int $year)
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
     * @param array $data
     * @return void
     */
    public function setupTaxes(array $data)
    {
        $year = (empty($data['year'])) ? date('Y') : $data['year'];

        $this->deleteYear($year, true);

        try {
            $this->db->beginTransaction();

            $statement = $this->db->prepare('
                INSERT INTO tax (year, strategies)
                VALUES (:year, :strategies)
            ');
            $statement->bindParam(':year', $year);
            $statement->bindParam(':strategies', json_encode($data['strategies']));
            $statement->execute();

            foreach ($data['strategies'] as $region => $aStrategy) {
                $combine = 'modules\tax\Y' . $year . '\\' . $region;
                $class = new $combine();

                $up = 0;
                foreach ($class::ESTIMATED_TAXES_DUE as $date) {
                    $statement = $this->db->prepare('
                        INSERT INTO tax_payment (
                            created_at,
                            amount,
                            year,
                            region,
                            payment_order
                        )
                        VALUES (
                            :created_at,
                            0,
                            :year,
                            :region,
                            :payment_order
                        )
                    ');

                    $statement->bindParam(':created_at', $date);
                    $statement->bindParam(':year', $year);
                    $statement->bindParam(':region', $region);
                    $statement->bindParam(':payment_order', $up);

                    $statement->execute();

                    $up++;
                }
            }

            $this->db->commit();

            redirect("/tax", null, 'Your taxes for ' . $year . ' are ready to go!');
        } catch (\PDOException $e) {
            $this->db->rollBack();

            systemError($e->getMessage());
        }
    }
}
