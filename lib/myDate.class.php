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
 * Implementeert een datum en biedt allerlei operaties hierop
 *
 * @package koi
 * @author Gert Vrebos
 */
class myDate
{
	/*
	 * Units of time
	 */
	const SECOND	= 0;
	const MINUTE	= 1;
	const HOUR		= 2;
	const DAY		  = 3;
	const WEEK		= 4;
	const MONTH		= 5;
	const QUARTER	= 6;
	const YEAR		= 7;
	const DECADE	= 8;
	const CENTURY	= 9;
	const MILLENIUM	= 10;
	
	/*
	 * Days of the week
	 */
	const SUNDAY	= 0;
	const MONDAY	= 1;
	const TUESDAY	= 2;
	const WEDNESDAY	= 3;
	const THURSDAY	= 4;
	const FRIDAY	= 5;
	const SATURDAY	= 6;
	
	/*
	 * Months of the year
	 */
	const JANUARY	= 1;
	const FEBRUARY	= 2;
	const MARCH		= 3;
	const APRIL		= 4;
	const MAY		= 5;
	const JUNE		= 6;
	const JULY		= 7;
	const AUGUST	= 8;
	const SEPTEMBER	= 9;
	const OCTOBER	= 10;
	const NOVEMBER	= 11;
	const DECEMBER	= 12;


  private static $periodLengthInSeconds = array(
    self::SECOND => 1,
    self::MINUTE => 60,
    self::HOUR => 3600,
    self::DAY => 86400,
    self::WEEK => 604800,
    self::MONTH => 2630880,
    self::YEAR => 31570560,
    self::DECADE => 315705600,
    self::CENTURY => 3157056000,
    self::MILLENIUM => 31570560000
  );

  private static $periodLengthInDays = array(
    self::SECOND => 1,1574074074074074074074074074074e-5,
    self::MINUTE => 6.9444444444444444444444444444444e-4,
    self::HOUR => 0,041666666666666666666666666666667,
    self::DAY => 1,
    self::WEEK => 7,
    self::MONTH => 30.436875,
    self::YEAR => 365.2425,
    self::DECADE => 3652.425,
    self::CENTURY => 36524.25,
    self::MILLENIUM => 365242.5
  );

	const MAX_YEAR = 9999;

	private $day;
	private $month;
	private $year;
	
	private $hours;
	private $minutes;
	private $seconds;

	/**
	 * Magical constructor, provide timestamp or day, month, year
	 * 
	 * @param integer $yearOrTimestamp
	 * @param integer $month
	 * @param integer $day
	 */
	public function __construct($yearOrTimestamp = null, $month = 1, $day = 1)
  {	
		if ($yearOrTimestamp instanceof myDate)
    {
      $this->day   = $yearOrTimestamp->day;
      $this->month = $yearOrTimestamp->month;
      $this->year  = $yearOrTimestamp->year;
    }
    else if (! is_numeric($yearOrTimestamp) && (strpos($yearOrTimestamp, '-') !== false))
    {
      $this->setFromPropelDate($yearOrTimestamp);
    }
		else if ($yearOrTimestamp != null && $yearOrTimestamp > self::MAX_YEAR)
    {
			$this->setFromTimestamp($yearOrTimestamp);
		}
		else if ($yearOrTimestamp != null)
    {
		  $this->set($yearOrTimestamp, $month, $day);
		}
		else
    {
		  $this->now();
		  $this->clearTime();
		}
	}

	/**
	 * Setters
	 */
	public function set($year, $month = 1, $day = 1, $hours = 0, $minutes = 0, $seconds = 0)
  {
		$this->day   = ($day !== null)	  ? $day   : strftime("%d");
		$this->month = ($month !== null) ?	$month : strftime("%m");
		$this->year  = ($year !== null)  ? $year  : strftime("%Y");

		$this->hours   = ($hours !== null)	  ? $hours   : strftime("%H");
		$this->minutes = ($minutes !== null) ?	$minutes : strftime("%M");
		$this->seconds = ($seconds !== null) ? $seconds : strftime("%S");
		
		return $this;
	}

  /**
   * Sets the myDate object to the time represented by the given timestamp
   * 
   * @param timestamp
   */
	public function setFromTimeStamp($ts)
  {
	  $date = getdate($ts);
	  $this->day = $date["mday"];
	  $this->month = $date["mon"];
	  $this->year = $date["year"];
	  $this->hours = $date["hours"];
	  $this->minutes = $date["minutes"];
	  $this->seconds = $date["seconds"];
	  
	  return $this;
	}
	
	/**
	 * Sets the day of the month
	 */
	public function setDay($d)
	{
	  $this->day = $d;
	  return $this;
	}

	/**
	 * Sets the month
	 */
	public function setMonth($m)
	{
		$this->month = $m;
        if ($this->day > $this->getDaysInMonth())
        {
          $this->day = $this->getDaysInMonth();
        }	  return $this;
	}

	/**
	 * Sets the year
	 */
	public function setYear($y)
	{
	  $this->year = $y;
	  return $this;
	}

	/**
	 * Sets the hours
	 */
	public function setHours($h)
	{
	  $this->hours = $h;
	  return $this;
	}

	/**
	 * Sets the minutes
	 */
	public function setMinutes($m)
	{
	  $this->minutes = $m;
	  return $this;
	}

	/**
	 * Sets the seconds
	 */
	public function setSeconds($s)
	{
	  $this->seconds = $s;
	  return $this;
	}
	
	/**
	 * Sets the time from a formatted string (HH:mm:ss of HH:mm of HH)
	 */
	public function setTime($t)
	{
    if (! $t)
    {
      $this->clearTime();
    }
    else
	  {
      $t = explode(':', $t);
      $this->hours = $t[0];
      $this->minutes = isset($t[1]) ? $t[1] : '0';
      $this->seconds= isset($t[2]) ? $t[2] : '0';
    }
    
    return $this;
	}


	/**
	 * Zet de datum en tijd op nu
	 * 
	 * @return myDate
	 */
	public function now()
  {
	  $this->day = strftime("%d");
	  $this->month = strftime("%m");
	  $this->year = strftime("%Y");
	  
	  $this->hours = strftime("%H");
	  $this->minutes = strftime("%M");
	  $this->seconds = strftime("%S");

	  return $this;
	}
	
	/**
	 * Zet de datum op de opgegeven propeldatum
	 * 
	 * @return myDate
	 */
  public function setFromPropelDate($p)
  {
	  if ($p == null || $p == "")
    {
			return false;
		}

		list($y, $m, $d) = explode("-", $p);
		$this->set($y, $m, $d);
		
	  return $this;
	}

	/**
	 * Zet de datum op de opgegeven Julian Date
	 * 
	 * @return myDate
	 */
  public function setJulianDay($jd)
  {
	  $date = cal_from_jd($jd, CAL_GREGORIAN);
		
		$this->day   = $date["day"];
		$this->month = $date["month"];
		$this->year  = $date["year"];
	  
	  return $this;
	}
	
  /**
   * Clears all time fields (reset to 00:00:00)
   */
  public function clearTime()
  {
    $this->hours = 0;
    $this->minutes = 0;
    $this->seconds = 0;
    
    return $this;
  }
	
	/**
	 * Maak een kopie van het opgegeven datum object
	 *
	 * @return myDate A copy of the current object
	 */
	public function copy()
	{
    return clone($this);
  }

	/**
	 * Getters
	 */
	public function getDay()
	{
	  return $this->day;
	}

	public function getMonth()
	{
	  return $this->month;
	}

	public function getYear()
	{
	  return $this->year;
	}

  public function getQuarter()
  {
    return ceil($this->month / 3);
  }
	public function getHours()
	{
	  return $this->hours;
	}

	public function getMinutes()
	{
	  return $this->minutes;
	}

	public function getSeconds()
	{
	  return $this->seconds;
	}
	
	public function getTimestamp()
  {
	  return mktime($this->hours, $this->minutes, $this->seconds, $this->month, $this->day, $this->year );
	}
	
	public function getPropelDate()
  {
    return sprintf('%02u-%02u-%02u', $this->year, $this->month, $this->day);
	}

	public function getJulianDay() 
  {
	  return cal_to_jd(CAL_GREGORIAN, $this->month, $this->day, $this->year);
	}

	public function getDaysInMonth()
  {
		return cal_days_in_month(CAL_GREGORIAN, $this->month, $this->year);
	}	

  public function getSecondsPastMidnight()
  {
    return $this->hours * 3600 + $this->minutes * 60 + $this->seconds; 
  }

  public function setSecondsPastMidnight($s)
  {
    $this->hours = intval($s / 3600);
    $this->minutes = intval(($s % 3600) / 60 );
    $this->seconds = $s % 60;
  }

	public function getWeekOfYear()
	{
	  return date('W', $this->getTimestamp());
	}


  /**
   * Return the day of week number
   *
   * From PEAR::Calc
   *
   * @return integer day of week, 0-6 (0: sunday)
   */
  public function getDayOfWeek()
  {
    $day = $this->day;
    
    if ($this->month > 2)
    {
      $month = $this->month - 2;
      $year = $this->year;
    } 
    else {
      $month = $this->month + 10;
      $year = $this->year - 1;
    }
    
    $day = (floor((13 * $month - 1) / 5) +
            $day + ($year % 100) +
            floor(($year % 100) / 4) +
            floor(($year / 100) / 4) - 2 *
            floor($year / 100) + 77);
    
    $weekday_number = $day - 7 * floor($day / 7);
    return $weekday_number;
  }



    /**
     * Converts from Gregorian Year-Month-Day to ISO Year-WeekNumber-WeekDay
     *
     * Uses ISO 8601 definitions.  Algorithm by Rick McCarty, 1999 at
     * http://personal.ecu.edu/mccartyr/ISOwdALG.txt .
     * Transcribed to PHP by Jesus M. Castagnetto.
     *
     *
     * @return string  the date in ISO Year-WeekNumber-WeekDay format
     *
     * @access public
     * @static
     */
    function getISO()
    {
      $year = $this->year;
      $month = $this->month;
      $day = $this->day;
      
      $mnth = array (0, 31, 59, 90, 120, 151, 181, 212, 243, 273, 304, 334);
      $y_isleap = $this->isLeapYear();
      $this->previousYear();
      $y_1_isleap = $this->isLeapYear();
      $this->nextYear();
      $day_of_year_number = $day + $mnth[$month - 1];
      if ($y_isleap && $month > 2) {
          $day_of_year_number++;
      }
      // find Jan 1 weekday (monday = 1, sunday = 7)
      $yy = ($year - 1) % 100;
      $c = ($year - 1) - $yy;
      $g = $yy + intval($yy / 4);
      $jan1_weekday = 1 + intval((((($c / 100) % 4) * 5) + $g) % 7);
      // weekday for year-month-day
      $h = $day_of_year_number + ($jan1_weekday - 1);
      $weekday = 1 + intval(($h - 1) % 7);
      // find if Y M D falls in YearNumber Y-1, WeekNumber 52 or
      if ($day_of_year_number <= (8 - $jan1_weekday) && $jan1_weekday > 4){
        $yearnumber = $year - 1;
        if ($jan1_weekday == 5 || ($jan1_weekday == 6 && $y_1_isleap)) {
          $weeknumber = 53;
        } else {
          $weeknumber = 52;
        }
      } else {
        $yearnumber = $year;
      }
      // find if Y M D falls in YearNumber Y+1, WeekNumber 1
      if ($yearnumber == $year) {
        if ($y_isleap) {
          $i = 366;
        } else {
          $i = 365;
        }
        if (($i - $day_of_year_number) < (4 - $weekday)) {
          $yearnumber++;
           $weeknumber = 1;
        }
      }
      // find if Y M D falls in YearNumber Y, WeekNumber 1 through 53
      if ($yearnumber == $year) {
        $j = $day_of_year_number + (7 - $weekday) + ($jan1_weekday - 1);
        $weeknumber = intval($j / 7);
        if ($jan1_weekday > 4) {
          $weeknumber--;
        }
      }
      // put it all together
      if ($weeknumber < 10) {
        $weeknumber = '0'.$weeknumber;
      }
      return $yearnumber . '-' . $weeknumber . '-' . $weekday;
    }
    
    
  /**
   * Return the weeknumber of the current date
   *
   * From PEAR::Calc
   *
   * @return integer week in year (1- 53)
   */
    function weekOfYear()
    {
      $iso = $this->getIso();
      $parts  = explode('-', $iso);
      $week_number = intval($parts[1]);
      return $week_number;
    }

	/**
	 * Manipulators
	 */
	
	/**
	 * Add n units to the current date
	 *
	 * @param integer unit one of the unit types (see class constants)
	 * @param integer the number of units to add
	 */
	public function addUnits($unit, $n)
	{
    switch($unit) {
      case self::SECOND:
        return $this->addSeconds($n);
        break;
      case self::MINUTE:
        return $this->addMinutes($n);
        break;
      case self::HOUR:
        return $this->addHours($n);
        break;
      case self::DAY:
        return $this->addDays($n);
        break;
      case self::WEEK:
        return $this->addWeeks($n);
        break;
      case self::MONTH:
        return $this->addMonths($n);
        break;
      case self::YEAR:
        return $this->addYears($n);
        break;
      case self::DECADE:
        return $this->addYears($n * 10);
        break;
      case self::CENTURY:
        return $this->addYears($n * 100);
        break;
      case self::MILLENIUM:
        return $this->addYears($n * 1000);
        break;
      default:
        throw new exception("Wrong unit type '$unit'");
    }
  }
  
	/**
	 * Subtract n units to the current date
	 *
	 * @param integer unit one of the unit types (see class constants)
	 * @param integer the number of units to subtract
	 */
  public function subtractUnits($unit, $n)
  {
    return $this->addUnits($unit, 0 - $n);
  }
	
	
	public function addDays($n)
  {
		$this->setJulianDay($this->getJulianDay() + $n);
	  
	  return $this;
	}
	
	public function nextDay()
  {
	  $this->addDays(1);
	  
	  return $this;
	}
	
	public function tomorrow()
	{
    return $this->nextDay();
  }

	public function subtractDays($n)
  {
		$this->setJulianDay($this->getJulianDay() - $n);
	  return $this;
	}

	public function addWorkingDays($n)
  {
    if (! $n > 0)
      return $this;
    while($n > 0)
    {
      $this->setJulianDay($this->getJulianDay() + 1);
      if ($this->getDayOfWeek() != 0 && $this->getDayOfWeek() != 6)
      {
        $n--;
      }
    }
		
	  return $this;
	}

  public function subtractWorkingDays($n)
  {
    if (! $n > 0)
      return $this;
    while($n > 0)
    {
      $this->setJulianDay($this->getJulianDay() - 1);
      if ($this->getDayOfWeek() != 0 && $this->getDayOfWeek() != 6)
      {
        $n--;
      }
    }

    return $this;
  }
	
	public function previousDay()
  {
		return $this->subtractDays(1);
	}

  public function yesterday()
  {
    return $this->previousDay();
  }

	public function subtractMonth()
  {
	  if ($this->month == 1)
    {
		  $this->month = 12;
		  $this->year--;
		}
		else {
		  $this->month--;
		}
	  
	  return $this;
	}

	public function subtractMonths($n)
  {
    $this->year -= floor($n / 12);
		
		$rest = $n % 12;

	  if (($this->month - $rest) < 1) {
		  $this->month = ($this->month - $rest) + 12;
		  $this->year--;
		}
		else {
		  $this->month -= $rest;
		}
        if ($this->day > $this->getDaysInMonth())
        {
          $this->day = $this->getDaysInMonth();
        }	  
	  return $this;
	}

	public function previousMonth()
  {
		return $this->subtractMonth(1);
	}
	
	public function addMonth()
  {
	  if ($this->month == 12) {
		  $this->month = 1;
		  $this->year++;
		}
		else {
		  $this->month++;
		}
        if ($this->day > $this->getDaysInMonth())
        {
          $this->day = $this->getDaysInMonth();
        }	  
	  return $this;
	}

	public function nextMonth()
  {
		return $this->addMonth(1);
	}

	public function addMonths($n)
  {
	  $this->year += floor($n / 12);
		
		$rest = $n % 12;

	  if (($this->month + $rest) > 12) {
		  $this->month = ($this->month + $rest) - 12;
		  $this->year++;
		}
		else {
		  $this->month += $rest;
		}
        if ($this->day > $this->getDaysInMonth())
        {
          $this->day = $this->getDaysInMonth();
        }	  
	  return $this;
	}
	
	public function nextYear()
  {
	  $this->year++;
	  
	  return $this;
	}

	public function addYears($n)
  {
	  $this->year += $n;
	  
	  return $this;
	}
	
	public function previousYear()
  {
	  $this->year--;
	  
	  return $this;
	}
	
	public function subtractYears($n)
  {
		$this->year -= $n;
	  
	  return $this;
	}
	
	public function nextDecade()
	{
    return $this->addDecades(1);
	}

	public function previousDecade()
	{
    return $this->addDecades(-1);
	}
	
	public function addDecades($n)
	{
    return $this->addYears($n * 10);
	}

	public function subtractDecades($n)
	{
    return $this->addYears($n * -10);
	}

	public function nextCentury()
	{
    return $this->addDecades(1);
	}

	public function previousCentury()
	{
    return $this->addDecades(-1);
	}
	
	public function addCenturies($n)
	{
    return $this->addYears($n * 100);
	}

	public function subtractCenturies($n)
	{
    return $this->addYears($n * -100);
	}
	
	public function nextMillenium()
	{
    return $this->addMillenia(1);
	}

	public function previousMillenium()
	{
    return $this->addMillenia(-1);
	}
	
	public function addMillenia($n)
	{
    return $this->addYears($n * 1000);
	}

	public function subtractMillenia($n)
	{
    return $this->addYears($n * -1000);
	}

  public function nextWeek()
  {
    return $this->addDays(7);
  }

  public function previousWeek()
  {
    return $this->subtractDays(7);
  }

  public function addWeeks($n)
  {
    return $this->addDays(7 * $n);
  }

  public function subtractWeeks($n)
  {
    return $this->subtractDays(7 * $n);
  }


  public function beginOfMonth()
  {
    $this->setDay(1);
    return $this;
  }
  
  public function endOfMonth()
  {
    return $this->setDay($this->getDaysInMonth());
  }
  
  public function beginOfWeek()
  {
    $dayOfWeek = $this->getDayOfWeek();
    return $this->subtractDays((($dayOfWeek == self::SUNDAY) ? 7 : $dayOfWeek) - 1);
  }

  public function endOfWeek()
  {
    $dayOfWeek = $this->getDayOfWeek();
    return $this->addDays(($dayOfWeek == self::SUNDAY) ? 0 : (7 - $dayOfWeek));
  }
  
  public function beginOfYear()
  {
    return $this->setMonth(1)->setDay(1);
  }
  
  public function endOfYear()
  {
    return $this->setMonth(12)->setDay(31);
  }

  public function addSeconds($n)
  {
    $newSeconds = $this->getSecondsPastMidnight() + $n;
    
    if ($newSeconds > 0)
    {
      $this->setSecondsPastMidnight($newSeconds % 86400);
      
      if ($d = intval($newSeconds / 86400))
      {
        $this->addDays($d);
      }
    }
    else
    {
      $this->setSecondsPastMidnight(86400 - abs($newSeconds % 86400));
      
      if ($d = intval($newSeconds / 86400))
      {
        $this->addDays($d - 1);
      }
      
    }
    
    return $this;
  }
  
  public function subtractSeconds($n)
  {
    return $this->addSeconds(0 - $n);
  }
    
  public function addMinutes($n)
  {
    return $this->addSeconds($n * 60);
  }
  
  public function subtractMinutes($n)
  {
    return $this->addSeconds(0 - ($n * 60));
  }
  
  public function addHours($n)
  {
    return $this->addSeconds($n * 3600);
  }
  
  public function subtractHours($n)
  {
    return $this->addSeconds(0 - ($n * 3600));
  }

	/**
	 * Comparison
	 */
	
	public function compareTo($otherDate) {

		$jd1 = $this->getJulianDay();
		$jd2 = $otherDate->getJulianDay();
		
		if ($jd1 < $jd2) return -1;
		if ($jd1 > $jd2) return 1;

		return 0;
	}
	
	public function compareMonthYearTo($otherDate) {
	  $d1_copy = $this->copy();
	  $d1_copy->day = $otherDate->day;
	  
	  return $d1_copy->compareTo($otherDate);
	}
	
	public function isAfter($otherDate) {	
	  return ($this->compareTo($otherDate) == 1);
	}

	public function isAfterOrEquals($otherDate) {	
	  return ($this->compareTo($otherDate) != -1);
	}

	public function isBefore($otherDate) {
	  return ($this->compareTo($otherDate) == -1);
	}

	public function isBeforeOrEquals($otherDate) {
	  return ($this->compareTo($otherDate) != 1);
	}

	public function equals($otherDate) {
	  return ($this->compareTo($otherDate) == 0);
	}
	
	

	/**
	 * Math with integer results
	 */
	
	/**
	 * Return difference in days between this and otherDate
	 *
	 * @return integer   The difference in days
	 */
	public function differenceInDays($otherDate) {
		$jd1 = $this->getJulianDay();
		$jd2 = $otherDate->getJulianDay();
		
		return ($jd1 - $jd2);
	}
	
	/**
	 * Return difference in days, months, years between this and otherDate.
   * Result is the result of this - otherdate (ie negative values if this < otherdate)
	 *
	 * @param myDate the second date
	 *
	 * @return array   (days, months, years)
	 */
	public function differenceWith($otherDate)
  {  
    $negative = $this->isBefore($otherDate);
    $d1 = $negative ? $otherDate->copy() : $this->copy();
    $d2 = !$negative ? $otherDate->copy() : $this->copy();

    $years = $d1->getYear() - $d2->getYear();
    $pullOffYear = (($d1->getMonth() < $d2->getMonth()) || (($d1->getMonth() == $d2->getMonth()) && ($d1->getDay() < $d2->getDay())));
    $years -= $pullOffYear ? 1 : 0;
    
    $months = $d1->getMonth() - $d2->getMonth() + ($pullOffYear ? 12 : 0);       
    $pullOffMonth = $d1->getDay() < $d2->getDay();
    $months -= $pullOffMonth ? 1 : 0;
    
    $days = $d1->getDay() - $d2->getDay() + ($pullOffMonth ? $d2->getDaysInMonth() : 0);
	  
    // echo ' === diff: ' . $this->format() . ' - ' . $otherDate->format() . " = $days d, $months m, $years y === ";
    	  
	  return $negative ? array(0 - $days, 0 - $months, 0 - $years) : array($days, $months, $years);
	}
	
	
	/**
	 * Return time difference (independant of the date) in seconds 
	 */
	public function timeDifferenceInSecondsWith($otherDate)
	{
    return $this->getSecondsPastMidnight() - $otherDate->getSecondsPastMidnight();
	}
	
	/**
	 * Return full difference in 
	 */
	public function differenceInSecondsWith($otherDate)
	{
    return ($this->differenceInDays($otherDate) * 86400) + $this->timeDifferenceInSecondsWith($otherDate);
	}
	
  /**
   * Format this date using the symfony format_date helper
   *
   * @param string    formatting string, defaults to 'd'
   *
   * @return string   The localized formatted date
   */
	public function format ($format = 'd')
  {
    Misc::use_helper('Date');
    
    return format_date($this->getTimestamp(), $format);
	}
	
	/**
	 * Default string representation
	 */
  public function __toString()
  {
    return '[myDate] ' . $this->format();
  }

  /**
   * Returns if the given combination of day, month, year is a valid date
   *
   * @param integer   The day
   * @param integer   The month
   * @param integer   The year
   *
   * @return boolean  Whether the date is correct or not
   *
   * @access public
   * @static
   */
  public static function isValidDate($day, $month, $year)
  {
    if ($year < 0 || $year > 9999) {
			return false;
    }
    if (!checkdate($month, $day, $year)) {
			return false;
    }
    return true;
  }

	/**
	 * Check if this date is valid
	 *
	 * @return boolean
	 */
	public function isValid()
	{
		return myDate::isValidDate($this->day, $this->month, $this->year);
	}	
	
	
  /**
   * Returns true for a leap year, else false
   *
   * @return boolean
   *
   * @access public
   */
  public function isLeapYear()
  {
    if ($this->year < 1000) {
      return false;
    }
    if ($this->year < 1582) {
      // pre Gregorio XIII - 1582
      return ($this->year % 4 == 0);
    } else {
      // post Gregorio XIII - 1582
      return (($this->year % 4 == 0) && ($this->year % 100 != 0)) || ($this->year % 400 == 0);
    }
  }
  
  /**
   * Returns the earliest of two or more myDates
   */
  public static function min()
  {
    $earliest = null;
    
    foreach($arg_list as $date)
    {
      if (($latest == null) || $date->isBefore($earliest))
      {
        $earliest = $date;
      } 
    }
    
    return $earliest;
  }
  
  /**
   * Returns the latest of two or more myDates
   */
  public static function max()
  {
    $latest = null;
    
    foreach($arg_list as $date)
    {
      if (($latest == null) || $date->isAfter($latest))
      {
        $latest = $date;
      }
    }
    
    return $latest;
  }
}