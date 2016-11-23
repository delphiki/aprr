<?php

namespace Aprr\Model;

class Selection implements \JsonSerializable
{
    /** @var string */
    protected $id;

    /** @var string */
    protected $label;

    /** @var TravelBill[] */
    protected $pendingBills;

    /** @var string */
    protected $pendingBillsTotalAmount;

    /**
     * Selection constructor.
     */
    public function __construct($id = null, $label = null)
    {
        $this->id    = $id;
        $this->label = $label;

        $this->pendingBills = [];
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     * @return Selection
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param string $label
     * @return Selection
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * @return TravelBill[]
     */
    public function getPendingBills()
    {
        return $this->pendingBills;
    }

    /**
     * @param TravelBill $bill
     * @return Selection
     */
    public function addPendingBill(TravelBill $bill)
    {
        $this->pendingBills[] = $bill;

        return $this;
    }

    /**
     * @param int $index
     * @return TravelBill
     */
    public function getPendingBill($index)
    {
        return isset($this->pendingBills[$index]) ? $this->pendingBills[$index] : null;
    }

    /**
     * @param TravelBill[] $pendingBills
     * @return Selection
     */
    public function setPendingBills($pendingBills)
    {
        $this->pendingBills = $pendingBills;

        return $this;
    }

    /**
     * @return string
     */
    public function getPendingBillsTotalAmount()
    {
        return $this->pendingBillsTotalAmount;
    }

    /**
     * @param string $pendingBillsTotalAmount
     * @return Selection
     */
    public function setPendingBillsTotalAmount($pendingBillsTotalAmount)
    {
        $this->pendingBillsTotalAmount = $pendingBillsTotalAmount;

        return $this;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'id'           => $this->getId(),
            'label'        => $this->getLabel(),
            'pendingBills' => [
                'travels' => $this->getPendingBills(),
                'amount'  => $this->getPendingBillsTotalAmount(),
            ],
        ];
    }
}
