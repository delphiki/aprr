<?php

namespace Aprr;

use Aprr\Model\TravelBill;
use Aprr\Model\User;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;

class Crawler implements \JsonSerializable
{

    const URL_LOGIN   = 'https://espaceclient.aprr.fr/aprr/Pages/connexion.aspx';
    const URL_ACCOUNT = 'https://espaceclient.aprr.fr/aprr/Pages/accueil.aspx';
    const URL_BILLS   = 'https://espaceclient.aprr.fr/aprr/Pages/MaConsommation/conso_trajets_nonfacture.aspx';

    /** @var string */
    private $hub;

    /** @var RemoteWebDriver */
    private $driver;

    /** @var User */
    private $user;

    /** @var array */
    private $pendingBills;

    /**
     * Crawler constructor.
     * @param string $hub
     */
    public function __construct($hub = 'http://localhost:4444/wd/hub')
    {
        $this->hub = $hub;

        $this->user = new User();
        $this->pendingBills = [];

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
     * @return array
     */
    public function getPendingBills()
    {
        return $this->pendingBills;
    }

    /**
     * @param array $pendingBills
     * @return $this
     */
    public function setPendingBills(array $pendingBills)
    {
        $this->pendingBills = $pendingBills;

        return $this;
    }

    /**
     * @param TravelBill $bill
     * @return $this
     */
    public function addPendingBill(TravelBill $bill)
    {
        $this->pendingBills[] = $bill;

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

        if (count($this->driver->findElements(WebDriverBy::id('espace_client'))) === 0) {
            return false;
        }

        $this->loadUserInfo($clientId);

        return $this->driver->getCurrentURL() === self::URL_ACCOUNT;
    }

    /**
     * Loads the user info from header
     * @param string $clientId
     */
    protected function loadUserInfo($clientId)
    {
        $header = $this->driver->findElement(WebDriverBy::id('espace_client'));

        $this->user
            ->setClientId($clientId)
            ->setFullname(
                $header->findElement(WebDriverBy::cssSelector('.name'))->getText()
            )
            ->setEmail(
                $header
                    ->findElement(WebDriverBy::cssSelector('.mail'))
                    ->getAttribute('innerHTML')
            )
            ->setAddress(
                str_replace('<br>', ' ', (
                    $header
                        ->findElement(WebDriverBy::cssSelector('.adresse'))
                        ->getAttribute('innerHTML'))
                )
            );
    }

    /**
     * @return $this
     */
    public function loadPendingBills()
    {
        $this->driver->navigate()->to(self::URL_BILLS);

        $jsScript = '
            var callback = arguments[arguments.length - 1];
            $.ajax({
                type: "POST",
                url: DataTableSelection.Config.ajaxUrl(),
                data: createJson(false, false, 1000),
                contentType: "application/json",
                dataType: "json",
                success: function (items) { callback(items); }
            });'
        ;

        $bills = $this->driver->executeAsyncScript($jsScript);

        foreach ($bills as $bill) {
            $this->addPendingBill(TravelBill::createFromRawData($bill));
        }

        return $this;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'user' => $this->getUser(),
            'pendingBills' => $this->getPendingBills()
        ];
    }
}
