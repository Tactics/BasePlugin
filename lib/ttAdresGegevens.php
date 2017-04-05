<?php
class ttAdresGegevens
{
  private $land_id;
  private $gemeente;
  private $postcode;
  private $straat;
  private $nummer;
  private $bus;

  public static function createFromPersoon(Persoon $persoon)
  {
    $adresGegevens = new self();
    $adresGegevens->land_id = $persoon->getLandId();
    $adresGegevens->gemeente = $persoon->getGemeente();
    $adresGegevens->postcode = $persoon->getPostcode();
    $adresGegevens->straat = $persoon->getStraat();
    $adresGegevens->nummer = $persoon->getNummer();
    $adresGegevens->bus = $persoon->getBus();
    return $adresGegevens;
  }


  /**
   * @return mixed
   */
  public function getLandId()
  {
    return $this->land_id;
  }

  /**
   * @return mixed
   */
  public function getGemeente()
  {
    return $this->gemeente;
  }

  /**
   * @return mixed
   */
  public function getPostcode()
  {
    return $this->postcode;
  }

  /**
   * @return mixed
   */
  public function getStraat()
  {
    return $this->straat;
  }

  /**
   * @return mixed
   */
  public function getNummer()
  {
    return $this->nummer;
  }

  /**
   * @return mixed
   */
  public function getBus()
  {
    return $this->bus;
  }
}