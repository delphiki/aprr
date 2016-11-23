<?php

namespace Aprr;

use Aprr\Model\Selection;
use Aprr\Model\TravelBill;
use Aprr\Model\User;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverSelect;

class Crawler implements \JsonSerializable
{

    const URL_LOGIN   = 'https://espaceclient.aprr.fr/aprr/Pages/connexion.aspx';
    const URL_ACCOUNT = 'https://espaceclient.aprr.fr/aprr/Pages/accueil.aspx';
    const URL_BILLS   = 'https://espaceclient.aprr.fr/aprr/Pages/MaConsommation/conso_trajets_nonfacture.aspx';

    /** @var string */
    private $hub;

    /** @var RemoteWebDriver */
    private $driver;

    /** @var array */
    private $contracts;

    /** @var array */
    private $badges;

    /** @var User */
    private $user;

    /**
     * Crawler constructor.
     * @param string $hub
     */
    public function __construct($hub = 'http://localhost:4444/wd/hub')
    {
        $this->hub = $hub;

        $this->init();
    }

    /**
     * Init the WebDriver
     */
    private function init()
    {
        if ($this->getHub()) {
            $this->setDriver(RemoteWebDriver::create($this->hub, DesiredCapabilities::firefox()));
        }
    }

    /**
     * @return string
     */
    public function getHub()
    {
        return $this->hub;
    }

    /**
     * @param string $hub
     * @return Crawler
     */
    public function setHub($hub)
    {
        $this->hub = $hub;

        $this->init();

        return $this;
    }

    /**
     * @return RemoteWebDriver
     */
    public function getDriver()
    {
        return $this->driver;
    }

    /**
     * @param RemoteWebDriver $driver
     * @return Crawler
     */
    public function setDriver($driver)
    {
        $this->driver = $driver;

        return $this;
    }

    /**
     * @return array
     */
    public function getContracts()
    {
        return $this->contracts;
    }

    /**
     * @param array $contracts
     * @return Crawler
     */
    public function setContracts($contracts)
    {
        $this->contracts = $contracts;

        return $this;
    }

    /**
     * @return array
     */
    public function getBadges()
    {
        return $this->badges;
    }

    /**
     * @param array $badges
     * @return Crawler
     */
    public function setBadges($badges)
    {
        $this->badges = $badges;

        return $this;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     * @return Crawler
     */
    public function setUser(User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @param string $clientId
     * @param string $password
     * @return bool
     */
    public function login($clientId, $password)
    {
        $this->driver->navigate()->to(self::URL_LOGIN);
        $this->driver->findElement(WebDriverBy::id('ctl00_PlaceHolderMain_TextBoxLogin'))->sendKeys($clientId);
        $this->driver->findElement(WebDriverBy::id('ctl00_PlaceHolderMain_TextBoxPass'))->sendKeys($password);

        $this->driver->findElement(WebDriverBy::id('ctl00_PlaceHolderMain_CheckBoxMemoriserMail'))->click();

        $this->driver->findElement(WebDriverBy::id('ctl00_PlaceHolderMain_ImageButtonConnection'))->click();

        if (!$this->driver->getCurrentURL() === self::URL_ACCOUNT) {
            return false;
        }

        $this->loadUserInfo($clientId);

        return true;
    }

    /**
     * Loads the user info from header
     * @param string $clientId
     */
    protected function loadUserInfo($clientId)
    {
        $header = $this->driver->findElement(WebDriverBy::id('espace_client'));

        $user = new User();

        $user->setClientId($clientId);
        $user->setFullname($header->findElement(WebDriverBy::cssSelector('.name'))->getText());
        $user->setEmail($header->findElement(WebDriverBy::cssSelector('.mail'))->getAttribute('innerHTML'));
        $user->setAddress(str_replace('<br>', ' ', ($header->findElement(WebDriverBy::cssSelector('.adresse'))->getAttribute('innerHTML'))));

        $this->setUser($user);
    }

    /**
     * Loads contracts and badges into arrays
     */
    public function loadContractsAndBadges()
    {
        $this->driver->navigate()->to(self::URL_BILLS);

        $contractSelect = $this->driver->findElement(WebDriverBy::id('ctl00_PlaceHolderMain_blocTrajets_ddlOffre'));
        $badgeSelect = $this->driver->findElement(WebDriverBy::id('ctl00_PlaceHolderMain_blocTrajets_ddlNumBadge'));

        foreach ($contractSelect->findElements(WebDriverBy::tagName('option')) as $contract) {
            if ($contract->getAttribute('value') !== '') {
                $this->contracts[] = new Selection($contract->getAttribute('value'), $contract->getText());
            }
        }

        foreach ($badgeSelect->findElements(WebDriverBy::tagName('option')) as $badge) {
            if ($badge->getAttribute('value') !== '') {
                $this->badges[] = new Selection($badge->getAttribute('value'), $badge->getText());
            }
        }
    }

    /**
     * @param Selection $contract
     * @return Selection
     */
    public function getContractPendingBills(Selection $contract)
    {
        return $this->getPendingBills('ctl00_PlaceHolderMain_blocTrajets_ddlOffre', $contract);
    }

    /**
     * @param Selection $badge
     * @return Selection
     */
    public function getBadgePendingBills($badge)
    {
        return $this->getPendingBills('ctl00_PlaceHolderMain_blocTrajets_ddlNumBadge', $badge);
    }

    /**
     * @param string    $selectId
     * @param Selection $selection
     * @return Selection
     */
    private function getPendingBills($selectId, Selection $selection)
    {
        $this->driver->navigate()->to(self::URL_BILLS);

        $select = $this->driver->findElement(WebDriverBy::id($selectId));
        $selectDriver = new WebDriverSelect($select);
        $selectDriver->selectByValue($selection->getId());

        $billsTableElements = $this->driver->findElements(WebDriverBy::className('trajets_nonfacture'));
        if (count($billsTableElements) === 0) {
            return $selection;
        }

        foreach ($billsTableElements[0]->findElements(WebDriverBy::tagName('tr')) as $tr) {
            $tds = $tr->findElements(WebDriverBy::tagName('td'));
            if (count($tds) > 2) {
                $bill = new TravelBill();

                $bill->setDate($tds[1]->getText());
                $bill->setEntrance($tds[2]->getText());
                $bill->setEntranceTime($tds[2]->findElement(WebDriverBy::cssSelector('div > span'))->getAttribute('innerHTML'));
                $bill->setExit($tds[3]->getText());
                $bill->setExitTime($tds[3]->findElement(WebDriverBy::cssSelector('div > span'))->getAttribute('innerHTML'));
                $bill->setClass($tds[4]->getText());
                $bill->setAmount($tds[5]->getText());

                $selection->addPendingBill($bill);
            } elseif ($tr->getAttribute('class') === 'total') {
                $selection->setPendingBillsTotalAmount($tr->getText());
            }
        }

        return $selection;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'user'      => $this->getUser(),
            'contracts' => $this->getContracts(),
            'badges'    => $this->getBadges(),
        ];
    }
}
