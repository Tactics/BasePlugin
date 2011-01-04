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
 * Bevat functies om eenvoudiger de database te benaderen
 *
 * @package 
 * @author Gert Vrebos
 */
class myDbTools
{
  
  /**
   * Voert een query uit en geeft de resultset terug
   */
  static public function getResultSet($sql)
  {
    $con = Propel::getConnection();
    
    $stmt = $con->createStatement();
    $rs = $stmt->executeQuery($sql, ResultSet::FETCHMODE_NUM);
		
		return $rs;
  }
    
  /**
   * Voert een opgegeven query uit
   */
  static public function executeSql($sql)
  {
    myDbTools::getResultSet($sql); 
  }


  /**
   * Haalt 1 waarde op uit de database via een opgegeven SQL query
   * 
   * @param string $sql
   * @param string $getter
   * @param integer $positie
   * 
   * @result mixed De gevraagde waarde
   */
  static public function getSingleValueFromSQL($sql, $getter = 'getInt', $positie = 1)
  {
    $rs = myDbTools::getResultSet($sql);
    if ($rs->first())
    {  
  		if ($getter == 'getint')
  		{
  		  return intval($rs->$getter($positie));  
  		}
  		else
      {
        return $rs->$getter($positie);
      }
    }
    return null;
  }

  
  
  /**
   * Geeft de inhoud van een result terug in een 2D array
   * 
   * Options: pk = primary key veld waarop de resulterende array geindexeerd wordt.
   *            default = 'id'
   *            indien het pk veld niet bestaat wordt de array niet specifiek geindexeerd
   *          fetchMode
   * 
   * @param string $sql
   * @param array optional $options
   * 
   * @return array
   */
  static public function resultSetToArray($rs, $options = array())
  {
    $pk = isset($options['pk']) ? $options['pk'] : 'ID';
    $tmp = explode('.', $pk);
    $pk = strtoupper(end($tmp)); // Enkel deel achter de punt
    $fetchMode = isset($options['fetchMode']) ? $options['fetchMode'] : ResultSet::FETCHMODE_ASSOC;
    
    $rs->setFetchMode($fetchMode);
    
    $result = array();
    
    while($rs->next())
    {
      $row = $rs->getRow();
      if (isset($row[$pk]))
      {
        $result[$row[$pk]] = $row;
      }
      else
      {
        $result[] = $row;
      }      
    }
    
    return $result;
  }
  
  /**
   * Geeft het volledige resultaat van een SQL query als 2D array terug
   *
   * Opgelet: enkel gebruiken voor gegarandeerd beperkte resultsets
   * (met limit, of by design)
   *
   * @param string $sql
   *
   * @return array
   */
  static public function getListFromSQL($sql, $getter = 'getInt', $positie = 1)
  {
    $rs = myDbTools::getResultSet($sql);

    $result = array();
    while ($rs->next())
    {
      $result[] = $rs->$getter($positie);
    }

    return $result;
  }
}

