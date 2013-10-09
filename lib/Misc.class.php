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
class Misc
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


  /**
   * genereer een array van de gegeven $objects, geindexeerd op basis van de gegeven $indexGetter
   *
   * @param array $objects : objects to cache
   * @param string $indexGetter : get method om de index van de cached objects op te halen, default de id van het object
   * @param string $valueGetter : get method om een value van de objects op te halen ipv heel het object
   * @return array
   */
  public static function buildIndexedCache($objects, $indexGetter = 'getId()', $valueGetter = null)
  {
    if (empty($objects))
      return array();

    $keys = eval("return array_map(create_function('\$object', 'return \$object->{$indexGetter};'), \$objects);");

    $values = $valueGetter
      ? eval("return array_map(create_function('\$object', 'return \$object->{$valueGetter};'), \$objects);")
      : $objects
    ;

    return array_combine($keys, $values);
  }

  /**
   * Zet browsersheaders zo dat het document als MS XLS bestand herkend wordt
   */
  public static function setExcelHeaders($filename)
  {
    $response = sfContext::getInstance()->getResponse();

    $response->setContentType('application/ms-excel; charset=utf-8');
    $response->setHttpHeader('Content-Language', 'nl');
    $response->addVaryHttpHeader('Accept-Language');
    $response->addCacheControlHttpHeader('no-cache');
    $response->setHttpHeader('Content-Disposition', 'attachment; filename=' . $filename);
  }

  public static function setCsvHeaders($filename)
  {
    $response = sfContext::getInstance()->getResponse();

    $response->setContentType('text/csv; charset=utf-8');
    $response->setHttpHeader('Content-Language', 'nl');
    $response->addVaryHttpHeader('Accept-Language');
    $response->addCacheControlHttpHeader('no-cache');
    $response->setHttpHeader('Content-Disposition', 'attachment; filename=' . $filename);
  }

  /**
   * geeft geslacht terug op basis van rijksregisternummer
   *
   * @param string $rrn
   * @return char
   */
  public static function getGeslacht($rrn)
  {
    return intval(substr(str_replace(' ', '', $rrn), 6, 3)) % 2 == 0 ? 'V' : 'M';
  }

  /**
   * geeft de geboortedatum terug op basis van rijksregisternummer
   *
   * @param string $rrn rijksregisternummer
   * @param string $format default propeldate
   * @return string geboortedatum
   */
  public static function getGeboortedatum($rrn, $format = 'yyyy-MM-dd')
  {
    self::use_helper('Date');
    if (intval(substr($rrn, 0, 2)) < 20)
    {
      $propelDate = '20' . substr($rrn, 0, 2) . '-' . substr($rrn, 2, 2) . '-' . substr($rrn, 4, 2);
    }
    else
    {
      $propelDate = '19' . substr($rrn, 0, 2) . '-' . substr($rrn, 2, 2) . '-' . substr($rrn, 4, 2);
    }

    return format_date($propelDate, $format);
  }

  /**
   * Geformatteerd printen van var + die()
   * 
   * @param mixed $var
   */
  public static function pre_print_r($var)
  {
    echo '<pre>';
    print_r($var);
    echo '</pre>';
    die();
  }

  /**
   * Geeft het object terug op basis van class en id
   * op voorwaarde dat er een Peer class met retrieveByPK static function bestaat
   * 
   * @param string $object_class
   * @param integer $object_id
   *
   * @return mixed The object
   */
  public static function getObject($object_class, $object_id)
  {
    if (!($object_class && $object_id))
    {
      return null;
    }

    if (!method_exists($object_class . 'Peer', 'retrieveByPK'))
    {
      return null;
    }

    return call_user_func($object_class . 'Peer::retrieveByPK', $object_id);
  }
}
