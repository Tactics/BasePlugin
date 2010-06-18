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
 * Bevat een aantal functies die nergens anders thuis horen
 *
 * @package koi
 * @author Gert Vrebos
 */
class ttMisc
{
  /**
   * Misc::use_helper()
   *
   * Statical helper function to include a helper library
   * from a location other than a symfony template
   */
    public static function use_helper()
    {
			sfLoader::loadHelpers(func_get_args(), sfContext::getInstance()->getModuleName());      
    }
    

  /**
   * Misc::create_uuid()
   *
   * Generate a new unique and random UUID
   *
   * @return the generated UUID
   */
    public static function create_uuid()
    {
      if (function_exists('com_create_guid')){
        return trim(com_create_guid(), '{}');
      }
      else
      {
        mt_srand((double)microtime()*10000);//optional for php 4.2.0 and up.
        $charid = strtoupper(md5(uniqid(rand(), true)));
        $hyphen = chr(45);// "-"
        $uuid = substr($charid, 0, 8).$hyphen
                .substr($charid, 8, 4).$hyphen
                .substr($charid,12, 4).$hyphen
                .substr($charid,16, 4).$hyphen
                .substr($charid,20,12);
                
        return $uuid;
      }
  }
  
  /**
   * Is the given UUID valid? 
   *
   * From: sfPropelUuidBehaviorPlugin-0.9.1
   * Copyright (c) 2007 Tristan Rivoallan
   */
  public static function is_uuid($uuid)
  {
    $pattern = '/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/';
    return (bool)preg_match($pattern, $uuid);       
  }


  /**
   * Genereer een random wachtwoord op basis van lengte
   *
   * @param integer $length (optional) Lengte van het gevraagde wachtwoord
   * 
   * @return string Random wachtwoord
   */
  public static function create_password($length = 7)
  {
    $chars = "abcdefghijkmnopqrstuvwxyz023456789";
    srand((double)microtime()*1000000);
    $i = 0;
    $pass = '';

    while ($i <= $length) {
        $num = rand() % 33;
        $tmp = substr($chars, $num, 1);
        $pass = $pass . $tmp;
        $i++;
    }

    return $pass;    
  }

}