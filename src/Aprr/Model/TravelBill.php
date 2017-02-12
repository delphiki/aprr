<?php

namespace Aprr\Model;

class TravelBill implements \JsonSerializable
{
    /** @var string */
    protected $id;

    /** @var \DateTime */
    protected $date;

    /** @var string */
    protected $entrance;

    /** @var string */
    protected $entranceCode;

    /** @var string */
    protected $exit;

    /** @var string */
    protected $exitCode;

    /** @var string */
    protected $class;

    /** @var string */
    protected $amount;

    /** @var string */
    protected $supportNumber;

    /** @var string */
    protected $state;

    /** @var string */
    protected $title;

    /** @var string */
    protected $code;

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     * @return TravelBill
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

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
    public function getEntranceCode()
    {
        return $this->entranceCode;
    }

    /**
     * @param string $entranceCode
     * @return TravelBill
     */
    public function setEntranceCode($entranceCode)
    {
        $this->entranceCode = $entranceCode;

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
    public function getExitCode()
    {
        return $this->exitCode;
    }

    /**
     * @param string $exitCode
     * @return TravelBill
     */
    public function setExitCode($exitCode)
    {
        $this->exitCode = $exitCode;

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

    /**
     * @return string
     */
    public function getSupportNumber()
    {
        return $this->supportNumber;
    }

    /**
     * @param string $supportNumber
     * @return TravelBill
     */
    public function setSupportNumber($supportNumber)
    {
        $this->supportNumber = $supportNumber;

        return $this;
    }

    /**
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param string $state
     * @return TravelBill
     */
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return TravelBill
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param string $code
     * @return TravelBill
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @param $data
     * @return TravelBill
     */
    public static function createFromRawData($data)
    {
        $bill = new self();

        $fieldsMatching = [
            'id' => 'IdTrajet',
            'date' => 'Date',
            'entrance' => 'GareEntreeLibelle',
            'entranceCode' => 'GareEntreeCode',
            'exit' => 'GareSortieLibelle',
            'exitCode' => 'GareSortieCode',
            'class' => 'ClasseVehicule',
            'amount' => 'MontantHorsRemiseTTC',
            'supportNumber' => 'NumeroSupport',
            'state' => 'Etat',
            'title' => 'Titre',
            'code' => 'Code',
        ];

        foreach ($fieldsMatching as $key => $field) {
            if (isset($data[$field])) {
                $method = sprintf('set%s', ucfirst($key));

                $bill->$method($data[$field]);
            }
        }

        return $bill;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'id' => $this->getId(),
            'date' => $this->getDate(),
            'entrance' => $this->getEntrance(),
            'entranceCode' => $this->getEntranceCode(),
            'exit' => $this->getExit(),
            'exitCode' => $this->getExitCode(),
            'class' => $this->getClass(),
            'amount' => $this->getAmount(),
            'supportNumber' => $this->getSupportNumber(),
            'state' => $this->getState(),
            'title' => $this->getTitle(),
            'code' => $this->getCode(),
        ];
    }
}
