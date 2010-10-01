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
 * Bevat een aantal functies om gegevens van en naar javascript te converteren
 *
 * @package koi
 * @author Gert Vrebos
 */
class myJavascriptTools
{

  /**
   * Maakt een javascript array van de opgegeven array
   * 
   * @param array $arr
   * @param boolean $assoc
   * @param integer $level
   */
	public static function makeArray($arr, $assoc = false, $level = 1)
  { 
	  $output =  "[\n";
	  $output .= str_repeat("  ", $level + 1);

		$first = true;

	  foreach($arr as $name => $value)
    {
			if ($assoc) $output .= "['$name', ";
		  
			if (is_bool($value))
      {
			  $output .= ($value ? "true" : "false");
			}
			else if (is_int($value) || is_float($value))
      {
			  $output .= $value;
			}
			else if (is_array($value))
      {
			  if (! $first) $output .= "\n" . str_repeat("  ", $level + 1);
			  $output .= self::makeArray($value, $assoc, $level + 1);
			}
			else if (is_object($value))
      {
			  $output .= "'" . myJavascriptTools::jsAddSlashes($value->__toString()) . "'";
			}
			else
      {
			  $output .= "'" . myJavascriptTools::jsAddSlashes($value) . "'";
			}

			if ($assoc) $output .= "]";

		  $output .= ", "; 
		  $first = false;
		}
		
		// Verwijder de laatste ', '
		return substr($output, 0, strlen($output) - 2) . "\n" . str_repeat("  ", $level) . "]";
	}
	
	/**
	 * Voegt javascript slashes toe aan een string
	 */
	public static function jsAddSlashes($str)
  {
	   $pattern = array(
	       "/\\\\/"  , "/\n/"    , "/\r/"    , "/\"/"    ,
	       "/\'/"    , "/&/"    , "/</"    , "/>/"
	   );
	   $replace = array(
	       "\\\\\\\\", "\\n"    , "\\r"    , "\\\""    ,
	       "\\'"    , "\\x26"  , "\\x3C"  , "\\x3E"
	   );
	   return preg_replace($pattern, $replace, $str);
	}

  /**
   * Maakt een JSON notatie van de opgegeven array
   * 
   * @param array $data
   * @param integer $level
   */
	public static function makeJSON($data, $level = 1)
  {
	  if (! is_array($data) || count($data) < 1) return "{}";
	  
	  $output =  "{\n";
	  
	  foreach($data as $name => $value) {
		  $output .= str_repeat("  ", $level + 1) .  $name . ": ";
		  
			if (is_bool($value)) {
			  $output .= ($value ? "true" : "false");
			}
			else if (is_int($value) || is_float($value)) {
			  $output .= $value;
			}
			else if (is_array($value)) {
			  $output .= self::makeJSON($value, $level + 1);
			}
			else if (is_object($value)) {
			  $output .= "'" . self::jsAddSlashes($value->__toString()) . "'";
			}
			else {
			  $output .= "'" . self::jsAddSlashes($value) . "'";
			}
			
			$output .= ",\n";
		}
		
		// remove final ', '
		return substr($output, 0, strlen($output) - 2) . "\n" . str_repeat("  ", $level) . "}";
	  
	}	

	
}