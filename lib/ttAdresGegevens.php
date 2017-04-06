<?php
class ttAdresGegevens
{
  private $land_id;
  private $gemeente;
  private $postcode;
  private $straat;
  private $nummer;
  private $bus;

  /**
   * @param Persoon $persoon
   *
   * @return ttAdresGegevens
   */
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
   * @param Organisatie $organisatie
   * @param string $prefix
   *
   * @return ttAdresGegevens
   */
  public static function createFromOrganisatie(Organisatie $organisatie, $prefix = '')
  {
    $adresGegevens = new self();
    $landGetter = 'get'.ucfirst(strtolower($prefix)).'LandId';
    $adresGegevens->land_id = $organisatie->$landGetter();
    $gemeenteGetter = 'get'.ucfirst(strtolower($prefix)).'Gemeente';
    $adresGegevens->gemeente = $organisatie->$gemeenteGetter();
    $postcodeGetter = 'get'.ucfirst(strtolower($prefix)).'Postcode';
    $adresGegevens->postcode = $organisatie->$postcodeGetter();
    $straatGetter = 'get'.ucfirst(strtolower($prefix)).'Straat';
    $adresGegevens->straat = $organisatie->$straatGetter();
    $nummerGetter = 'get'.ucfirst(strtolower($prefix)).'Nummer';
    $adresGegevens->nummer = $organisatie->$nummerGetter();
    $busGetter = 'get'.ucfirst(strtolower($prefix)).'Bus';
    $adresGegevens->bus = $organisatie->$busGetter();
    return $adresGegevens;
  }

  /**
   * @param OrganisatieContact $contact
   *
   * @return ttAdresGegevens
   */
  public static function createFromOrganisatieContact(OrganisatieContact $contact)
  {
    $adresGegevens = new self();
    $adresGegevens->land_id = $contact->getLandId();
    $adresGegevens->gemeente = $contact->getGemeente();
    $adresGegevens->postcode = $contact->getPostcode();
    $adresGegevens->straat = $contact->getStraat();
    $adresGegevens->nummer = $contact->getNummer();
    $adresGegevens->bus = $contact->getBus();
    return $adresGegevens;
  }

  /**
   * @param Vacature $vacature
   *
   * @return ttAdresGegevens
   */
  public static function createFromVacature(Vacature $vacature)
  {
    $adresGegevens = new self();
    $adresGegevens->land_id = 'BE';
    $adresGegevens->gemeente = $vacature->getGemeente();
    $adresGegevens->postcode = $vacature->getPostcode();
    $adresGegevens->straat = $vacature->getStraat();
    $adresGegevens->nummer = $vacature->getNummer();
    $adresGegevens->bus = $vacature->getBus();
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