<?php

/**
 * ttTextTools
 * 
 * Bevat allerlei functies mbt het behandelen van text
 * 
 * @package CSJ
 * @author Tactics bvba
 * @copyright 2010
 * @version $Id$
 * @access public
 */
class ttTextTools
{
  
  /**
   * Checks to see if a string is utf8 encoded.
   *
   * 
   * NOTE: This function checks for 5-Byte sequences, UTF8
   *       has Bytes Sequences with a maximum length of 4.
   *
   * @author bmorel at ssi dot fr (modified) (WordPress!)
   * @since 1.2.1
   *
   * @param string $str The string to be checked
   * @return bool True if $str fits a UTF-8 model, false otherwise.
   */
  static function seems_utf8($str)
  {
     $length = strlen($str);
     for ($i=0; $i < $length; $i++) {
        $c = ord($str[$i]);
        if ($c < 0x80) $n = 0; # 0bbbbbbb
        elseif (($c & 0xE0) == 0xC0) $n=1; # 110bbbbb
        elseif (($c & 0xF0) == 0xE0) $n=2; # 1110bbbb
        elseif (($c & 0xF8) == 0xF0) $n=3; # 11110bbb
        elseif (($c & 0xFC) == 0xF8) $n=4; # 111110bb
        elseif (($c & 0xFE) == 0xFC) $n=5; # 1111110b
        else return false; # Does not match any model
        for ($j=0; $j<$n; $j++) { # n bytes matching 10bbbbbb follow ?
           if ((++$i == $length) || ((ord($str[$i]) & 0xC0) != 0x80))
              return false;
        }
     }
     return true;
  }
  
  /**
   * Converts all accent characters to ASCII characters.
   *
   * If there are no accent characters, then the string given is just returned.
   *
   * @author WordPress
   * @since 1.2.1
   *
   * @param string $string Text that might have accent characters
   * @return string Filtered string with replaced "nice" characters.
   */
  static function remove_accents($string)
  {
     if ( !preg_match('/[\x80-\xff]/', $string) )
        return $string;
  
     if (self::seems_utf8($string)) {
        $chars = array(
        // Decompositions for Latin-1 Supplement
        chr(195).chr(128) => 'A', chr(195).chr(129) => 'A',
        chr(195).chr(130) => 'A', chr(195).chr(131) => 'A',
        chr(195).chr(132) => 'A', chr(195).chr(133) => 'A',
        chr(195).chr(135) => 'C', chr(195).chr(136) => 'E',
        chr(195).chr(137) => 'E', chr(195).chr(138) => 'E',
        chr(195).chr(139) => 'E', chr(195).chr(140) => 'I',
        chr(195).chr(141) => 'I', chr(195).chr(142) => 'I',
        chr(195).chr(143) => 'I', chr(195).chr(145) => 'N',
        chr(195).chr(146) => 'O', chr(195).chr(147) => 'O',
        chr(195).chr(148) => 'O', chr(195).chr(149) => 'O',
        chr(195).chr(150) => 'O', chr(195).chr(153) => 'U',
        chr(195).chr(154) => 'U', chr(195).chr(155) => 'U',
        chr(195).chr(156) => 'U', chr(195).chr(157) => 'Y',
        chr(195).chr(159) => 's', chr(195).chr(160) => 'a',
        chr(195).chr(161) => 'a', chr(195).chr(162) => 'a',
        chr(195).chr(163) => 'a', chr(195).chr(164) => 'a',
        chr(195).chr(165) => 'a', chr(195).chr(167) => 'c',
        chr(195).chr(168) => 'e', chr(195).chr(169) => 'e',
        chr(195).chr(170) => 'e', chr(195).chr(171) => 'e',
        chr(195).chr(172) => 'i', chr(195).chr(173) => 'i',
        chr(195).chr(174) => 'i', chr(195).chr(175) => 'i',
        chr(195).chr(177) => 'n', chr(195).chr(178) => 'o',
        chr(195).chr(179) => 'o', chr(195).chr(180) => 'o',
        chr(195).chr(181) => 'o', chr(195).chr(182) => 'o',
        chr(195).chr(182) => 'o', chr(195).chr(185) => 'u',
        chr(195).chr(186) => 'u', chr(195).chr(187) => 'u',
        chr(195).chr(188) => 'u', chr(195).chr(189) => 'y',
        chr(195).chr(191) => 'y',
        // Decompositions for Latin Extended-A
        chr(196).chr(128) => 'A', chr(196).chr(129) => 'a',
        chr(196).chr(130) => 'A', chr(196).chr(131) => 'a',
        chr(196).chr(132) => 'A', chr(196).chr(133) => 'a',
        chr(196).chr(134) => 'C', chr(196).chr(135) => 'c',
        chr(196).chr(136) => 'C', chr(196).chr(137) => 'c',
        chr(196).chr(138) => 'C', chr(196).chr(139) => 'c',
        chr(196).chr(140) => 'C', chr(196).chr(141) => 'c',
        chr(196).chr(142) => 'D', chr(196).chr(143) => 'd',
        chr(196).chr(144) => 'D', chr(196).chr(145) => 'd',
        chr(196).chr(146) => 'E', chr(196).chr(147) => 'e',
        chr(196).chr(148) => 'E', chr(196).chr(149) => 'e',
        chr(196).chr(150) => 'E', chr(196).chr(151) => 'e',
        chr(196).chr(152) => 'E', chr(196).chr(153) => 'e',
        chr(196).chr(154) => 'E', chr(196).chr(155) => 'e',
        chr(196).chr(156) => 'G', chr(196).chr(157) => 'g',
        chr(196).chr(158) => 'G', chr(196).chr(159) => 'g',
        chr(196).chr(160) => 'G', chr(196).chr(161) => 'g',
        chr(196).chr(162) => 'G', chr(196).chr(163) => 'g',
        chr(196).chr(164) => 'H', chr(196).chr(165) => 'h',
        chr(196).chr(166) => 'H', chr(196).chr(167) => 'h',
        chr(196).chr(168) => 'I', chr(196).chr(169) => 'i',
        chr(196).chr(170) => 'I', chr(196).chr(171) => 'i',
        chr(196).chr(172) => 'I', chr(196).chr(173) => 'i',
        chr(196).chr(174) => 'I', chr(196).chr(175) => 'i',
        chr(196).chr(176) => 'I', chr(196).chr(177) => 'i',
        chr(196).chr(178) => 'IJ',chr(196).chr(179) => 'ij',
        chr(196).chr(180) => 'J', chr(196).chr(181) => 'j',
        chr(196).chr(182) => 'K', chr(196).chr(183) => 'k',
        chr(196).chr(184) => 'k', chr(196).chr(185) => 'L',
        chr(196).chr(186) => 'l', chr(196).chr(187) => 'L',
        chr(196).chr(188) => 'l', chr(196).chr(189) => 'L',
        chr(196).chr(190) => 'l', chr(196).chr(191) => 'L',
        chr(197).chr(128) => 'l', chr(197).chr(129) => 'L',
        chr(197).chr(130) => 'l', chr(197).chr(131) => 'N',
        chr(197).chr(132) => 'n', chr(197).chr(133) => 'N',
        chr(197).chr(134) => 'n', chr(197).chr(135) => 'N',
        chr(197).chr(136) => 'n', chr(197).chr(137) => 'N',
        chr(197).chr(138) => 'n', chr(197).chr(139) => 'N',
        chr(197).chr(140) => 'O', chr(197).chr(141) => 'o',
        chr(197).chr(142) => 'O', chr(197).chr(143) => 'o',
        chr(197).chr(144) => 'O', chr(197).chr(145) => 'o',
        chr(197).chr(146) => 'OE',chr(197).chr(147) => 'oe',
        chr(197).chr(148) => 'R',chr(197).chr(149) => 'r',
        chr(197).chr(150) => 'R',chr(197).chr(151) => 'r',
        chr(197).chr(152) => 'R',chr(197).chr(153) => 'r',
        chr(197).chr(154) => 'S',chr(197).chr(155) => 's',
        chr(197).chr(156) => 'S',chr(197).chr(157) => 's',
        chr(197).chr(158) => 'S',chr(197).chr(159) => 's',
        chr(197).chr(160) => 'S', chr(197).chr(161) => 's',
        chr(197).chr(162) => 'T', chr(197).chr(163) => 't',
        chr(197).chr(164) => 'T', chr(197).chr(165) => 't',
        chr(197).chr(166) => 'T', chr(197).chr(167) => 't',
        chr(197).chr(168) => 'U', chr(197).chr(169) => 'u',
        chr(197).chr(170) => 'U', chr(197).chr(171) => 'u',
        chr(197).chr(172) => 'U', chr(197).chr(173) => 'u',
        chr(197).chr(174) => 'U', chr(197).chr(175) => 'u',
        chr(197).chr(176) => 'U', chr(197).chr(177) => 'u',
        chr(197).chr(178) => 'U', chr(197).chr(179) => 'u',
        chr(197).chr(180) => 'W', chr(197).chr(181) => 'w',
        chr(197).chr(182) => 'Y', chr(197).chr(183) => 'y',
        chr(197).chr(184) => 'Y', chr(197).chr(185) => 'Z',
        chr(197).chr(186) => 'z', chr(197).chr(187) => 'Z',
        chr(197).chr(188) => 'z', chr(197).chr(189) => 'Z',
        chr(197).chr(190) => 'z', chr(197).chr(191) => 's',
        // Euro Sign
        chr(226).chr(130).chr(172) => 'E',
        // GBP (Pound) Sign
        chr(194).chr(163) => '');
  
        $string = strtr($string, $chars);
     } else {
        // Assume ISO-8859-1 if not UTF-8
        $chars['in'] = chr(128).chr(131).chr(138).chr(142).chr(154).chr(158)
           .chr(159).chr(162).chr(165).chr(181).chr(192).chr(193).chr(194)
           .chr(195).chr(196).chr(197).chr(199).chr(200).chr(201).chr(202)
           .chr(203).chr(204).chr(205).chr(206).chr(207).chr(209).chr(210)
           .chr(211).chr(212).chr(213).chr(214).chr(216).chr(217).chr(218)
           .chr(219).chr(220).chr(221).chr(224).chr(225).chr(226).chr(227)
           .chr(228).chr(229).chr(231).chr(232).chr(233).chr(234).chr(235)
           .chr(236).chr(237).chr(238).chr(239).chr(241).chr(242).chr(243)
           .chr(244).chr(245).chr(246).chr(248).chr(249).chr(250).chr(251)
           .chr(252).chr(253).chr(255);
  
        $chars['out'] = "EfSZszYcYuAAAAAACEEEEIIIINOOOOOOUUUUYaaaaaaceeeeiiiinoooooouuuuyy";
  
        $string = strtr($string, $chars['in'], $chars['out']);
        $double_chars['in'] = array(chr(140), chr(156), chr(198), chr(208), chr(222), chr(223), chr(230), chr(240), chr(254));
        $double_chars['out'] = array('OE', 'oe', 'AE', 'DH', 'TH', 'ss', 'ae', 'dh', 'th');
        $string = str_replace($double_chars['in'], $double_chars['out'], $string);
     }
  
     return $string;
  }

/**
   * Basic converter from HTML text to plain text with optional word wrap
   *
   * @param the html text to convert
   * @param number of characters to wrap by, 0 means no wrapping.
   */
	static public function htmlToPlain($htmlsource, $wordwrap = 80)
  {
		$htmlsource    = strip_tags($htmlsource, '<br><p>');
		$htmlsource    = preg_replace('/<br[^>]*>/i', "\n", $htmlsource);
		$htmlsource    = preg_replace('/<p[^>]*>/i', "\n", $htmlsource);
		$htmlsource    = preg_replace('/<\/p[^>]*>/i', "\n", $htmlsource);

    if ($wordwrap)
		{
      $htmlsource    = wordwrap($htmlsource, $wordwrap);
    }

		$table         = array_flip(get_html_translation_table(HTML_ENTITIES));
		$htmlsource  = strtr($htmlsource, $table);

		return $htmlsource;
	}


  /**
   * Basic converter van plain text to HTML
   *
   * @param the html tekst om te converteren
   */
	static public function plainToHtml($txt)
  {
    if (! $txt)
    {
      return '';
    }

	  // Kills double spaces and spaces inside tags.
	  while( !( strpos($txt,'  ') === FALSE ) ) $txt = str_replace('  ',' ',$txt);
	  $txt = str_replace(' >','>',$txt);
	  $txt = str_replace('< ','<',$txt);

	  // Transforms accents in html entities.
	  $txt = htmlentities($txt);

	  // We need some HTML entities back!
	  $txt = str_replace('&quot;','"',$txt);
	  $txt = str_replace('&lt;','<',$txt);
	  $txt = str_replace('&gt;','>',$txt);
	  $txt = str_replace('&amp;','&',$txt);

	  // Ajdusts links - anything starting with HTTP opens in a new window
	  $txt = str_ireplace("<a href=\"http://","<a target=\"_blank\" href=\"http://",$txt);
	  $txt = str_ireplace("<a href=http://","<a target=\"_blank\" href=http://",$txt);

	  // Basic formatting
	  $eol = ( strpos($txt,"\r") === FALSE ) ? "\n" : "\r\n";
	  $html = '<p>'.str_replace("$eol$eol","</p><p>",$txt).'</p>';
	  $html = str_replace("$eol","<br />\n",$html);
	  $html = str_replace("</p>","</p>\n\n",$html);
	  $html = str_replace("<p></p>","<p>&nbsp;</p>",$html);

	  // Wipes <br> after block tags (for when the user includes some html in the text).
	  $wipebr = Array("table","tr","td","blockquote","ul","ol","li");

	  for ($x = 0; $x < count($wipebr); $x++)
    {
	    $tag = $wipebr[$x];
	    $html = str_ireplace("<$tag><br />","<$tag>",$html);
	    $html = str_ireplace("</$tag><br />","</$tag>",$html);
	  }

	  return $html;
	}


	/**
	 * Trimt meer karakters dan de PHP trim
	 *
	 * @param string De te trimmen tekst
	 *
	 * @return string
	 */
	public static function realTrim($text)
  {
	  return trim($text,"\xA0\x7f..\xff\x0..\x1f ");
	}

  /**
   * Strip tekst
   *
   * @param de tekst
   *
   * @return string De geconverteerde tekst
   */
  public static function stripText($text)
  {
    $text = strtolower($text);

    // strip all non word chars
    $text = preg_replace('/\W/', ' ', $text);

    // replace all white space sections with a dash
    $text = preg_replace('/\ +/', '-', $text);

    // trim dashes
    $text = preg_replace('/\-$/', '', $text);
    $text = preg_replace('/^\-/', '', $text);

    return $text;
  }

	/**
	 * Checks email address validity
	 *
	 * isValidEmailAddress
	 * from: http://iamcal.com/publish/articles/php/parsing_email
	 *
	 * @param string $adres Het te testen adres
	 *
	 * @return boolean true indien geldig, false anders
	 */
	static function isGeldigEmailAdres ($adres)
  {
		$qtext = '[^\\x0d\\x22\\x5c\\x80-\\xff]';
		$dtext = '[^\\x0d\\x5b-\\x5d\\x80-\\xff]';
		$atom = '[^\\x00-\\x20\\x22\\x28\\x29\\x2c\\x2e\\x3a-\\x3c\\x3e\\x40\\x5b-\\x5d\\x7f-\\xff]+';
		$quoted_pair = '\\x5c[\\x00-\\x7f]';
		$domain_literal = "\\x5b($dtext|$quoted_pair)*\\x5d";
		$quoted_string = "\\x22($qtext|$quoted_pair)*\\x22";
		$domain_ref = $atom;
		$sub_domain = "($domain_ref|$domain_literal)";
		$word = "($atom|$quoted_string)";
		$domain = "$sub_domain(\\x2e$sub_domain)*";
		$local_part = "$word(\\x2e$word)*";
		$addr_spec = "$local_part\\x40$domain";

		return preg_match("!^$addr_spec$!", $adres) ? 1 : 0;
	}

	/**
	 * Formateert een XMLstring geindenteerd
	 */
	public static function formatXmlString($xml)
  {
    // add marker linefeeds to aid the pretty-tokeniser (adds a linefeed between all tag-end boundaries)
    $xml = preg_replace('/(>)(<)(\/*)/', "$1\n$2$3", $xml);

    // now indent the tags
    $token      = strtok($xml, "\n");
    $result     = ''; // holds formatted version as it is built
    $pad        = 0; // initial indent
    $matches    = array(); // returns from preg_matches()

    // scan each line and adjust indent based on opening/closing tags
    while ($token !== false) :

      // test for the various tag states

      // 1. open and closing tags on same line - no change
      if (preg_match('/.+<\/\w[^>]*>$/', $token, $matches)) :
        $indent=0;
      // 2. closing tag - outdent now
      elseif (preg_match('/^<\/\w/', $token, $matches)) :
        $pad--;
      // 3. opening tag - don't pad this one, only subsequent tags
      elseif (preg_match('/^<\w[^>]*[^\/]>.*$/', $token, $matches)) :
        $indent=1;
      // 4. no indentation needed
      else :
        $indent = 0;
      endif;

      // pad the line with the required number of leading spaces
      $line    = str_pad($token, strlen($token)+$pad, ' ', STR_PAD_LEFT);
      $result .= $line . "\n"; // add to the cumulative result, with linefeed
      $token   = strtok("\n"); // get the next token
      $pad    += $indent; // update the pad size for subsequent lines
    endwhile;

    return $result;
  }

}