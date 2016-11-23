<?php

namespace Aprr\Model;

class TravelBill implements \JsonSerializable
{
    /** @var \DateTime */
    protected $date;

    /** @var string */
    protected $entrance;

    /** @var string */
    protected $entranceTime;

    /** @var string */
    protected $exit;

    /** @var string */
    protected $exitTime;

    /** @var string */
    protected $class;

    /** @var string */
    protected $amount;

    /**
     * @return string
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param string $date
     * @return TravelBill
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * @return string
     */
    public function getEntrance()
    {
        return $this->entrance;
    }

    /**
     * @param string $entrance
     * @return TravelBill
     */
    public function setEntrance($entrance)
    {
        $this->entrance = $entrance;

        return $this;
    }

    /**
     * @return string
     */
    public function getEntranceTime()
    {
        return $this->entranceTime;
    }

    /**
     * @param string $entranceTime
     * @return TravelBill
     */
    public function setEntranceTime($entranceTime)
    {
        $this->entranceTime = $entranceTime;

        return $this;
    }

    /**
     * @return string
     */
    public function getExit()
    {
        return $this->exit;
    }

    /**
     * @param string $exit
     * @return TravelBill
     */
    public function setExit($exit)
    {
        $this->exit = $exit;

        return $this;
    }

    /**
     * @return string
     */
    public function getExitTime()
    {
        return $this->exitTime;
    }

    /**
     * @param string $exitTime
     * @return TravelBill
     */
    public function setExitTime($exitTime)
    {
        $this->exitTime = $exitTime;

        return $this;
    }

    /**
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @param string $class
     * @return TravelBill
     */
    public function setClass($class)
    {
        $this->class = $class;

        return $this;
    }

    /**
     * @return string
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param string $amount
     * @return TravelBill
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    public function jsonSerialize()
    {
        return [
            'date'         => $this->getDate(),
            'entrance'     => $this->getEntrance(),
            'entranceTime' => $this->getEntranceTime(),
            'exit'         => $this->getExit(),
            'exitTime'     => $this->getExitTime(),
            'amount'       => $this->getAmount(),
        ];
    }
}
