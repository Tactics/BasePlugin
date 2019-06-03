<?php

if(! function_exists('__'))
  \Misc::use_helper('I18N');


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
 * Bevat een aantal datumfuncties
 *
 * @package koi
 * @author Gert Vrebos
 */
class myDateTools
{

    
    public static function isValidDate($date, $culture = null)
    {

      if (!$date) return false;
      if (!$culture)
          $culture = sfContext::getInstance()->getUser()->getCulture();

      $dateFormatInfo = @sfDateTimeFormatInfo::getInstance($culture);
      $dateFormat = $dateFormatInfo->getShortDatePattern();

      
      // We construct the regexp based on date format
      $dateRegexp = preg_replace('/[dmy]+/i', '(\d+)', $dateFormat);

      // We parse date format to see where things are (m, d, y)
      $a = array(
          'd' => strpos($dateFormat, 'd'),
          'm' => strpos($dateFormat, 'M'),
          'y' => strpos($dateFormat, 'y'),
      );
      $tmp = array_flip($a);
      ksort($tmp);
      $i = 0;
      $c = array();
      foreach ($tmp as $value) $c[++$i] = $value;
      $datePositions = array_flip($c);

      // We find all elements
      preg_match("~$dateRegexp~", $date, $matches);

      if (count($matches) < 4)
          return false;

      $m = $matches[$datePositions['m']];
      $d = $matches[$datePositions['d']];
      $y = $matches[$datePositions['y']];
      // Extra controle voor propel
      if (! strtotime(myDateTools::cultureDateToPropelDate($date))) return false;
      
      if (!checkdate($m, $d, $y))
          return false;

      return true;
    }

	/**
	 * Convert a datestring from userinput (through Calendar) to a propel-parsable date string.
	 * Is used to set dates on propel objects directly from userinput.
	 */
	static public function cultureDateToPropelDate($culture_date, $options = array())
  {
	  $date = null;

    $result = sfI18N::getDateForCulture($culture_date, sfContext::getInstance()->getUser()->getCulture());
    
    if (is_array($result))
		{
		  list($d, $m, $y) = $result;
		  $date = sprintf('%02u-%02u-%02u', $y, $m, $d);
		}
    
    return $date;
	}

  /**
   * Get a cultured date from a propel date
   */
  static public function propelDateToCultureDate($propeldate, $format = 'd')
  {
    static $dateFormat = null;
    
    if (! $dateFormat)
    {
      $dateFormat = new sfDateFormat(sfContext::getInstance()->getUser()->getCulture());
    }
    
    return $dateFormat->format($propeldate, $format);
  }

	/**
	 * Convert a time array from userinput (through select_time_tag helper) to a propel-parsable time string.
	 * Is used to set times on propel objects directly from userinput.
	 */
	static public function formTimeToPropelTime($time_array)
  {
    if (
					 ! is_array($time_array)
				|| ! is_numeric($time_array["hour"])
				|| ! is_numeric($time_array["minute"])
			 )
		{
		  return null;
		}
		else
		{
		  return sprintf('%02u', $time_array["hour"]) . ":" . sprintf('%02u', $time_array["minute"]);
		}
	}
  
  /**
	 * Convert a propeltime to minutes
	 * Is used to make calculations
	 */
	static public function propelTimeToMinutes($propel_time)
  {
    $tmp = explode(':', $propel_time);
    if (
					 count($tmp) != 2
				|| ! is_numeric($tmp[0])
				|| ! is_numeric($tmp[1])
			 )
		{
		  return null;
		}
		else
		{
		  return $tmp[0] * 60 + $tmp[1];
		}
	}
	
	/**
	 * Combination of cultureDateToPropelDate and formTimeToPropelTime
   * 
   * Works with either time_array or time in the culture_date
	 */
	static public function cultureDateTimeToPropelDatetime($culture_date, $time_array = null)
  {
    if (! $culture_date)
    {
      return null;
    }
    
    if ($time_array === null)
    {
      $ts = sfI18N::getTimestampForCulture($culture_date, sfContext::getInstance()->getUser()->getCulture());
      
      return $date = date('Y-m-d H:i:s', $ts);
    }
    
		return $culture_date ? (myDateTools::cultureDateToPropelDate($culture_date) . " " . myDateTools::formTimeToPropelTime($time_array)) : null;
	}
  
  
  /**
	 * Combination of cultureDateToPropelDate and formTimeToPropelTime
   * 
   * Works with either time_array or time in the culture_date
	 */
	static public function cultureDateTimeToTimestamp($culture_date, $time_array = null)
  {
    $propelDate = self::cultureDateTimeToPropelDatetime($culture_date, $time_array);
    return strtotime($propelDate);
	}
  
	
	static public function cultureDateToMyDate($culture_date)
  {
		$tmp = new myDate();
		$tmp->setFromPropelDate(myDateTools::cultureDateToPropelDate($culture_date));
		return $tmp;
	}

  /**
   * Get a cultured date from a propel date
   */
  static public function myDateToCultureDate($mydate, $format = 'd')
  {
    return $mydate->format($format);    
  }


	static public function cultureDateToTimestamp($culture_date)
  {
		$tmp = new myDate();
		$tmp2 = myDateTools::cultureDateToPropelDate($culture_date);
		if (! $tmp2) return null;
		$tmp->setFromPropelDate($tmp2);
		return $tmp->getTimestamp();
	}
	
  /**
   * Converteert een propeldatum naar een timestamp
   * 
   * @param propeldate
   * 
   * @return timestamp
   */
	static public function propelDateToTimeStamp($propeldate)
	{
		$tmp = new myDate();
		$tmp->setFromPropelDate($propeldate);
		return $tmp->getTimestamp();    
  }


  /**
   * Converteert een timestamp naar een propeldate
   * 
   * @param timestamp
   * 
   * @return propeldate
   */
	static public function timestamptoPropelDate($timestamp)
	{
		$tmp = new myDate($timestamp);
		return $tmp->getPropelDate();
  }
	
  /**
   * Geef een fuzzy datum terug (werkt momenteel enkel voor data in het verleden)
   */
  static public function getFuzzyDatum($ts)
  {
    $now = time();
    $today = mktime(0, 0, 0);
    $startOfMonth = mktime(0,0,0, date('m'), 1);
    $dayMapping = array('Monday' => __('Maandag'), 'Tuesday' => __('Dinsdag'), 'Wednesday' => __('Woensdag'), 'Thursday' => __('Donderdag'), 'Friday' => __('Vrijdag'), 'Saturday' => __('Zaterdag'), 'Sunday' => __('Zondag'));

    if ($ts + 3600 > $now)
    {
      if ($ts + 60 > $now)
      {
        return __('minder dan een minuut geleden');
      }
      else if ($ts + 120 > $now)
      {
        return __('&eacute;&eacute;n minuut geleden');
      }
      else if ((($ts + 1860) > $now) && (($ts + 1740) < $now))
      {
        return __('een half uur geleden');
      }
      else if ((($ts + 2760) > $now) && (($ts + 2640) < $now))
      {
        return __('drie kwartier geleden');
      }

      return floor(($now - $ts) / 60) . ' ' . __('minuten geleden');
    }
    else if ($ts > $today)
    {
      return floor(($now - $ts) /3600) . ' ' . __('uur geleden');
    }
    else if ($ts >= ($today - (3600 * 24)))
    {
      return __('gisteren');
    }
    else if ($ts >= ($today - (3600 * 48)))
    {
      return __('eergisteren');
    }
    else if ($ts >= ($today - (3600 * 24 * 7)))
    {
      if (! defined('format_date'))
      {
        Misc::use_helper('Date');
      }

      return format_date($ts, 'EEEE');
    }
    else if ($ts >= $startOfMonth)
    {
      return floor(($now - $ts) / (3600 * 24)) . ' ' . __('dagen geleden');
    }
    
    $vorigeMaand = new myDate($startOfMonth);
    $vorigeMaand->subtractMonth();
    
    if ($ts >= $vorigeMaand->getTimestamp())
    {
      return __('vorige maand');
    }
    
    $now = new myDate();
    
    list($d, $m, $j) = $now->differenceWith(new myDate($ts));
    
    $txt = '';
    
    if ($j)
    {
      $txt = (($j == 1) ? __('��n') : $j) . ' ' . __('jaar');
    }
    
    if ($m)
    {
      if ($j)
      {
        $txt .= ' ' . __('en') . ' ';
      }
      if ($m == 1)
      {
        $txt .= __('��n maand');
      }
      else
      {
        $txt .= $m . ' ' . __('maanden');
      }
    }
    
    return $txt . ' ' . __('geleden');
  }
  
  private static $timestampRangeCache = array();
  
  	
	/**
   * 
   * @param timerstamp $beginTs
   * @param timestamp $eindTs
   * 
   * @return array
   */
  public static function getTimestampsFromRange($beginTs, $eindTs, $getter = 'getPropelDate')
  {
    $key = $beginTs . '_' . $eindTs . '_' . $getter;
    if(! isset(self::$timestampRangeCache[$key]))
    {
       $timestamps = array();
       $begindatum = new myDate($beginTs);
       $einddatum = new myDate($eindTs);
       for ($d = $begindatum->copy(); $d->isBeforeOrEquals($einddatum); $d->nextDay())
       {
         $timestamps[$d->getTimestamp()] = $d->$getter();
       }
       self::$timestampRangeCache[$key] = $timestamps;
    }
    
    return self::$timestampRangeCache[$key];
  }
  
  private static $myDateRangeCache = array();
  
  /**
   * Geeft reeks terug van mydate 
   * 
   * @param myDate $beginDate
   * @param myDate $eindDate
   * @return array
   */
  public static function getRange($beginDate, $eindDate)
  {
    $key = $beginDate->getTimestamp() . '_' . $eindDate->getTimestamp();
    if(! isset(self::$myDateRangeCache[$key]))
    {
       for ($d = $beginDate->copy(); $d->isBeforeOrEquals($eindDate); $d->nextDay())
       {
         $dates[$d->getTimestamp()] = $d->copy();
       }
       self::$myDateRangeCache[$key] = $dates;
    }
    
    return self::$myDateRangeCache[$key];
  }
}

?>