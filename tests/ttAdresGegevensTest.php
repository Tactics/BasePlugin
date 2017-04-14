<?php

namespace plugins\ttBasePlugin\tests;

use ttAdresGegevens;

class ttAdresGegevensTest extends \PHPUnit_Framework_TestCase
{
  /**
   * @test
   */
  public function kan_aanmaken_op_basis_van_persoon()
  {
    $persoon = new \Persoon();
    $persoon->setLandId('BE');
    $persoon->setPostcode('2940');
    $persoon->setGemeente('Hoevenen');
    $persoon->setStraat('Kerkstraat');
    $persoon->setNummer('115');
    $persoon->setBus('B');

    $adresGegevens = ttAdresGegevens::createFromPersoon($persoon);

    $this->assertEquals('BE', $adresGegevens->getLandId());
    $this->assertEquals('2940', $adresGegevens->getPostcode());
    $this->assertEquals('Hoevenen', $adresGegevens->getGemeente());
    $this->assertEquals('Kerkstraat', $adresGegevens->getStraat());
    $this->assertEquals('115', $adresGegevens->getNummer());
    $this->assertEquals('B', $adresGegevens->getBus());
  }

  /**
   * @test
   */
  public function kan_aanmaken_op_basis_van_organisatie()
  {
    $organisatie = new \Organisatie();
    $organisatie->setLandId('BE');
    $organisatie->setPostcode('2940');
    $organisatie->setGemeente('Hoevenen');
    $organisatie->setStraat('Kerkstraat');
    $organisatie->setNummer('115');
    $organisatie->setBus('B');

    $adresGegevens = ttAdresGegevens::createFromOrganisatie($organisatie);

    $this->assertEquals('BE', $adresGegevens->getLandId());
    $this->assertEquals('2940', $adresGegevens->getPostcode());
    $this->assertEquals('Hoevenen', $adresGegevens->getGemeente());
    $this->assertEquals('Kerkstraat', $adresGegevens->getStraat());
    $this->assertEquals('115', $adresGegevens->getNummer());
    $this->assertEquals('B', $adresGegevens->getBus());
  }

  /**
   * @test
   */
  public function kan_aanmaken_op_basis_van_organsiatie_met_prefix()
  {
    $organisatie = new \Organisatie();
    $organisatie->setPostLandId('BE');
    $organisatie->setPostPostcode('2940');
    $organisatie->setPostGemeente('Hoevenen');
    $organisatie->setPostStraat('Kerkstraat');
    $organisatie->setPostNummer('115');
    $organisatie->setPostBus('B');

    $adresGegevens = ttAdresGegevens::createFromOrganisatie($organisatie, 'post');

    $this->assertEquals('BE', $adresGegevens->getLandId());
    $this->assertEquals('2940', $adresGegevens->getPostcode());
    $this->assertEquals('Hoevenen', $adresGegevens->getGemeente());
    $this->assertEquals('Kerkstraat', $adresGegevens->getStraat());
    $this->assertEquals('115', $adresGegevens->getNummer());
    $this->assertEquals('B', $adresGegevens->getBus());
  }
  
  /**
   * @test
   */
  public function kan_aanmaken_op_basis_van_een_organisatiecontact()
  {
    $organisatieContact = new \OrganisatieContact();
    $organisatieContact->setLandId('BE');
    $organisatieContact->setPostcode('2940');
    $organisatieContact->setGemeente('Hoevenen');
    $organisatieContact->setStraat('Kerkstraat');
    $organisatieContact->setNummer('115');
    $organisatieContact->setBus('B');

    $adresGegevens = ttAdresGegevens::createFromOrganisatieContact($organisatieContact);

    $this->assertEquals('BE', $adresGegevens->getLandId());
    $this->assertEquals('2940', $adresGegevens->getPostcode());
    $this->assertEquals('Hoevenen', $adresGegevens->getGemeente());
    $this->assertEquals('Kerkstraat', $adresGegevens->getStraat());
    $this->assertEquals('115', $adresGegevens->getNummer());
    $this->assertEquals('B', $adresGegevens->getBus());
  }

  /**
   * @test
   */
  public function kan_aanmaken_op_basis_van_een_vacature()
  {
    $vacature = new \Vacature();
    $vacature->setPostcode('2940');
    $vacature->setGemeente('Hoevenen');
    $vacature->setStraat('Kerkstraat');
    $vacature->setNummer('115');
    $vacature->setBus('B');

    $adresGegevens = ttAdresGegevens::createFromVacature($vacature);

    $this->assertEquals('BE', $adresGegevens->getLandId());
    $this->assertEquals('2940', $adresGegevens->getPostcode());
    $this->assertEquals('Hoevenen', $adresGegevens->getGemeente());
    $this->assertEquals('Kerkstraat', $adresGegevens->getStraat());
    $this->assertEquals('115', $adresGegevens->getNummer());
    $this->assertEquals('B', $adresGegevens->getBus());
  }
}
