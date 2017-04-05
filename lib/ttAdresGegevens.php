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