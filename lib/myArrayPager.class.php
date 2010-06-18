<?php

/*
 * Dit bestand maakt deel uit van een applicatie voor Digipolis Antwerpen.
 * 
 * (c) 2008 Tactics BVBA
 *
 * Recht werd verleend om dit bestand te gebruiken als onderdeel van de genoemde
 * applicatie. Mag niet doorverkocht worden, noch rechtstreeks noch via een
 * derde partij. Meer informatie in het desbetreffende aankoopcontract. 
 */
 
/**
 * Biedt pager functionaliteiten voor arrays
 *
 * @package koi
 * @author Gert Vrebos
 */
class myArrayPager extends sfPager
{
  protected $resultsArray = null;
 
  /**
   * Constructor
   */
  public function __construct($class = null, $maxPerPage = 10)
  {
    parent::__construct($class, $maxPerPage);
  }
 
  /**
   * Initialiseer de pager
   */
  public function init()
  {
    $this->setNbResults(count($this->resultsArray));
 
    if (($this->getPage() == 0 || $this->getMaxPerPage() == 0))
    {
     $this->setLastPage(0);
    }
    else
    {
     $this->setLastPage(ceil($this->getNbResults() / $this->getMaxPerPage()));
    }
  }
 
  /**
   * Zet de array waarover de pager moet gaan
   * 
   * @param array $array
   */
  public function setResultArray($array)
  {
    $this->resultsArray = $array;
  }
 
  /**
   * Geeft de array van deze pager terug
   * 
   * @return array De gevraagde array
   */
  public function getResultArray()
  {
    return $this->resultsArray;
  }
 
  /**
   * Geeft het object op de aangegeven index in de  pager
   * 
   * @param integer $offset
   */
  public function retrieveObject($offset)
  {
    return $this->resultsArray[$offset];
  }
 
  /**
   * Geeft een pagina uit de resultaten
   * 
   * @return array Een lijst met de resultaten op de pagina
   */
  public function getResults()
  {
    return array_slice($this->resultsArray, ($this->getPage() - 1) * $this->getMaxPerPage(), $this->maxPerPage);
  }
 
}