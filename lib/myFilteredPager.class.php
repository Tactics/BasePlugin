<?php

/**
 * Filter
 * 
 * @package Kindervreugd
 * @author Gert Vrebos
 * @copyright 2008
 * @version $Id$
 * @access public
 */
class myFilteredPager extends sfPropelPager
{
  const TYPE_NULL = 1; // is the given field null (or not), expected 'true', 'false' or ''
  const TYPE_BOOLEAN = 2; // value is either string 'true', string 'false' or '' (last case: doesn't matter, no criterium build)
  const TYPE_FLOAT = 4;
	const TYPE_PROPELDATE = 8;

  protected
    $namespace = '',
    $filter = array(),
    $orderAsc = true,
    $orderBy = '',
    $criteriaDirty = true,
    $inited = false;
  
  private static
    $request = null,
    $attributeHolder = null;
  
  
  /**
   * Initialize static members
   */
  public static function initStatic()
  {
    self::$request = sfContext::getInstance()->getRequest();
    self::$attributeHolder = sfContext::getInstance()->getUser()->getAttributeHolder();
  }

  /**
   * Returns the pager results of the current page
   * 
   * @return array[]Object
   */
  public function getResults()
  {
    if (! $this->inited)
    {
      throw new sfException('You must call init() on the myFilteredPager before fetching results');
    }
    
    return parent::getResults();
  }

  /**
   * Constructor
   * 
   * @param string $class Name of object class to page
   * @param string $namespace Session namespace to store pager state into
   * @param integer $maxPerPage Number of objects per page
   * @param string $defaultOrderBy Default order by field
   * @param boolean $sortAsc default true Default order asc/desc status-
   */
  public function __construct($class, $namespace, $maxPerPage = null, $defaultOrderBy = '', $sortAsc = true)
  {
    $maxPerPage = ($maxPerPage !== null) ? $maxPerPage : sfConfig::get('app_default_pager_size');
   
    // Default order by primary key
    if (! $defaultOrderBy)
    {
  		$tableName = eval("return " . $class . "Peer::TABLE_NAME;");
  		$tmpBuilder = eval("return new " . $class . "MapBuilder();");
  		$tmpBuilder->doBuild();
  		$table_map = $tmpBuilder->getDatabaseMap()->getTable($tableName);

  		foreach($table_map->getColumns() as $column)
      {
  			if ($column->isPrimaryKey())
        {
  				$defaultOrderBy = $column->getFullyQualifiedName();
  				break;
  			}
  		}
    }
    
    parent::__construct($class, $maxPerPage);

    // Initialize
    $this->namespace = $namespace;
    self::initStatic();
    
    // Ordering
    if (self::$request->getParameter('reset'))
    {
      $this->orderAsc = $sortAsc;
      $this->orderBy = $defaultOrderBy;
      $this->setPage(1);
      $this->set('page', 1);
    }
    else
    {
      $this->orderAsc = self::$attributeHolder->get("orderasc", $sortAsc, $this->namespace);
  		$this->setPage(self::updateAndGetRequestParameter("page", $this->namespace, 1));

  		// Op volgorde geklikt ?
  		if (self::$request->hasParameter("orderby"))
      {
  		  // zelfde = keer volgorde om
  		  if (self::$request->getParameter("orderby") == self::$attributeHolder->get("orderby", "-", $this->namespace))
        {
  		    $this->orderBy  = self::$request->getParameter("orderby");
          $this->orderAsc = ! $this->orderAsc;
        }
        // anders: nieuw sorteerveld en ascending
        else
        {
    			$this->orderBy  = self::$request->getParameter('orderby');
    			$this->orderAsc = true;
    		}
  		}
  		// Behoud volgorde (uit sessie)
  		else
      {
        $this->orderBy = self::$attributeHolder->get("orderby", $defaultOrderBy, $this->namespace);
      }
  	}

    self::$attributeHolder->set("orderasc", $this->orderAsc, $this->namespace);
    self::$attributeHolder->set("orderby", $this->orderBy, $this->namespace);
  }
  
  /**
   * Get a parameter from the request parameters, if available update session state, otherwise
   * get last parameter value from session state.
   * 
   * @param string $name
   * @param string $namespace
   * @param mixed $default
   * @param boolean $checkReset
   * 
   * @return mixed
   */
  static public function updateAndGetRequestParameter($name, $namespace, $default = null, $checkReset = false)
  { 
    
    if (! self::$request)
    {
      self::initStatic();
    }

    // If parameter supplied in request: update the parameter holder
  	if (self::$request->hasParameter($name) || ($checkReset && self::$request->hasParameter('reset')))
    {
  		self::$attributeHolder->set($name, self::$request->getParameter($name, $default), $namespace);
  	}
  	
  	// Default
    if (! self::$attributeHolder->has($name, $namespace))
  	{
      self::$attributeHolder->set($name, $default, $namespace);
  	}

  	return self::$attributeHolder->get($name, null, $namespace);
  }
  
  
  /**
   * Prepare a string criterium (wildcart support)
   * 
   * @param mixed $field
   * 
   * @return mixed
   */
  public function prepareValue($field)
  {
    $value = $field['value'];
    
    // Criteria::LIKE
    if ($field['comparison'] == Criteria::LIKE)
    {
      $value = str_replace(array('%'), array('\%'), $value);

      if (strpos($value, '*') === false && strpos($value, '?') === false) {
    		$value = "*$value*";
    	}
    
    	$value = str_replace(array('*', '?'), array('%', '_'), $value);
    }
    
    // Propel date preprocess
    if ($field['type'] & self::TYPE_PROPELDATE)
    {
      if ($value)
      {
        $value = myDateTools::cultureDateToPropelDate($value);
      }
    }
    
    // Input float value
    if ($field['type'] & self::TYPE_FLOAT)
    {
      $value = floatval(str_replace(',', '.', $value));
    }
    
    // Input boolean or isnull value
    if ($field['type'] & (self::TYPE_BOOLEAN | self::TYPE_NULL))
    {
      if (! in_array($value, array('true', 'false', '')))
      {
        throw new sfException('Invalid filter value, expected one of the following strings: "true", "false" or "" (empty string)');
      }
      
      $value = ($value == 'true') ? 1 : 0;
    }
      
    return $value;
  }
  
  
  /**
   * Add an item to the filter
   * 
   * @param string $filterField
   * @param array $options
   * 
   * @return mixed
   */
  public function add($filterField, $options = array())
  {
    $dbFieldname    = isset($options['dbFieldname']) ? $options['dbFieldname'] : $filterField; 
    $filterField    = str_replace('.', '_', $filterField);
    $addToCriteria  = isset($options['addToCriteria']) ? $options['addToCriteria'] : true;
    $type           = isset($options['type']) ? $options['type'] : null;
    
    if ($type && ! in_array($type, array(self::TYPE_BOOLEAN, self::TYPE_FLOAT, self::TYPE_NULL, self::TYPE_PROPELDATE)))
    {
      throw new sfException("Invalid filter type '$type', use class constants.");
    }
    
    // checkReset: nodig om checkboxes te resetten naar hun default value
    $checkReset     = isset($options['checkReset']) ? $options['checkReset'] : false;
    $default        = isset($options['default']) ? $options['default'] : null;
    $value          = isset($options['value']) ? $options['value'] : self::updateAndGetRequestParameter($filterField, $this->namespace, $default, $checkReset);
    $comparison     = isset($options['comparison']) ? $options['comparison'] : (is_array($value) ? Criteria::IN : Criteria::EQUAL);
    
    $this->filter[$filterField] = array(
      'dbFieldname' => $dbFieldname,
      'value' => $value,
      'comparison' => $comparison,
      'addToCriteria' => $addToCriteria,
      'type' => $type
    );

    $this->criteriaDirty = true;
    
    return $value;
  }
  
  /**
   * Add an item to the filter and return the current value; (=alias for add)
   * 
   * @param string $filterField
   * @param array $options
   * 
   * @return mixed
   */
  public function addAndGet($filterField, $options = array())
  {
    return self::add($filterField, $options);
  }  
  
  /**
   * Get the value of a field that has been added to the filter
   * 
   * @param string $filterField
   * @param string $defaultValue
   * 
   * @return mixed
   */
  public function get($filterField, $defaultValue = null)
  {
    $filterField = str_replace('.', '_', $filterField);
    return isset($this->filter[$filterField]) ? $this->filter[$filterField]['value'] : $defaultValue; 
  }
  
  /**
   * Set the value of a filter field (and remember in session)
   * 
   * @param string $filterField
   * @param mixed $value
   */
  public function set($filterField, $value)
  {
    $filterField    = str_replace('.', '_', $filterField);
    
    // set in session
    self::$attributeHolder->set($filterField, $value, $this->namespace);
    
    // set in current filter (for criteria creation)
    $this->overwrite($filterField, $value);
  }


  /**
   * Overwrite the value of a field for use in the internal criteria object
   * 
   * @param string $filterField
   * @param mixed $value
   */
  public function overwrite($filterField, $value)
  {
    $filterField    = str_replace('.', '_', $filterField);

    if (isset($this->filter[$filterField]))
    {
      $this->filter[$filterField]['value'] = $value;
      $this->criteriaDirty = true;
    }
  }
  
  /**
   * Reset the internal criteria object
   */
  public function clearCriteria()
  {
    $this->criteria = new Criteria();
    $this->criteriaDirty = true;
  }
  
  /**
   * Add all filter criteria to the given propel criteria object
   */
  public function buildCriteria()
  {
    if (! $this->criteria)
    {
      $this->clearCriteria();
    }
    
    // Group by db_fieldname
    $grouped = array();
    
    foreach($this->filter as $key => $field)
    {
      if (! $field['addToCriteria'])
      {
        continue;
      }

      if ($field['value'] == 'ISNULL')
      {
        $this->criteria->add($field['dbFieldname'], null, Criteria::ISNULL);
      }
      else if ($field['value'] != '')
      {
        $value = $this->prepareValue($field);
        
        if ($field['type'] & self::TYPE_NULL)
        {
          $field['comparison'] = $value ? Criteria::ISNOTNULL : Criteria::ISNULL;
          $value = null;
        }
        
        $this->criteria->addAnd($field['dbFieldname'], $value, $field['comparison']);
      }
    }
    
    // Ordering
    // nakijken of orderBy van de pager niet in de criteria overschreven werd
    // indien dit wel gebeurde, moet de orderBy van de pager genegeerd worden    
    $critOrderByColumns = $this->criteria->getOrderByColumns();
		if (($this->orderBy != "") && (! in_array($this->orderBy . ' ' . Criteria::DESC, $critOrderByColumns)) && (! in_array($this->orderBy . ' ' . Criteria::ASC, $critOrderByColumns)))
    {
		  $this->orderAsc ?
        $this->criteria->addAscendingOrderByColumn($this->orderBy)
      : $this->criteria->addDescendingOrderByColumn($this->orderBy);
    }

    $this->criteriaDirty = false;
  }
  
  
  /**
   * Initialize the pager
   */
  public function init()
  { 
    $this->inited = true;
    
    if ($this->criteriaDirty)
    {
      $this->buildCriteria();
    }
    
    parent::init();
  }
  
  
  /**
   * Return the current criteria object
   * 
   * @return Criteria
   */
  public function getCriteria()
  {
    if (! $this->criteria)
    {
      $this->clearCriteria();
    }
    
    return parent::getCriteria();
  }
  
  
  /**
   * Get all filter falues (in array)
   * 
   * @return array
   */
  public function getValues()
  {
    $result = array();
    
    foreach($this->filter as $filter_field => $field)
    {
      $result[$filter_field] = $field['value'];
    }
    
    return $result;
  }
  
  /**
   * Get order by field
   * 
   * @return string
   */
  public function getOrderBy()
  {
    return $this->orderBy;
  }
  
  /**
   * Zet order ascending/descending
   * 
   * @param boolean $asc
   */
  public function setOrderAsc($asc = true)
  {
    $this->orderAsc = $asc;
  }

  /**
   * Get order ascending/descending
   * 
   * @return boolean
   */
  public function getOrderAsc()
  {
    return $this->orderAsc;
  }
  
  /**
   * Get first object
   * 
   * @return Object
   */
  public function getFirstObject()
  {
    return $this->getObjectByCursor(1);
  }
  
  /**
   * Get last object
   * 
   * @return Object
   */
  public function getLastObject()
  {
    return $this->getObjectByCursor($this->getNbResults());
  }

  /**
   * Get next object based on a given primary key
   *
   * @param integer $currentPk : the id of the object right before the one we need
   * @param int|string $pkColumn : column number of the primary key
   * @param string $peerMethod
   *
   * @return Object
   */
  public function getNextObjectByPk($currentPk, $pkColumn = 1, $peerMethod = 'doSelectRS')
  {
    $c = $this->getCriteria();
    $c->setLimit(null);
    $c->setOffset(null);
    $rs = call_user_func(array($this->getClassPeer(), $peerMethod), $c);
    
    while($rs->next() && ($rs->getInt($pkColumn) != $currentPk)) {}

    $nextPk = $rs->next() ? $rs->getInt($pkColumn) : null;
    return $nextPk ? call_user_func(array($this->getClassPeer(), 'retrieveByPk'), $nextPk) : null;
  }

  /**
   * Get next object based on a given primary key
   *
   * @param integer $currentPk : the id of the object right before the one we need
   * @param int|string $pkColumn : column number of the primary key
   * @param string $peerMethod
   *
   * @return Object
   */
  public function getPreviousObjectByPk($currentPk, $pkColumn = 1, $peerMethod = 'doSelectRS')
  {
    $c = $this->getCriteria();
    $c->setLimit(null);
    $c->setOffset(null);
    $rs = call_user_func(array($this->getClassPeer(), $peerMethod), $c);
    $rs->last();
    
    while(($rs->getInt($pkColumn) != $currentPk) && $rs->previous()) {}

    $previousPk = $rs->previous() ? $rs->getInt($pkColumn) : null;
    return $previousPk ? call_user_func(array($this->getClassPeer(), 'retrieveByPk'), $previousPk) : null;
  }

  /**
   * Overschrijven van de default
   *
   * @param $offset
   *
   * @return Object/null
   */

  protected function retrieveObject($offset)
  {
    // TODO: check op doSelect & doSelectRs + afhandeling van beide gevallen.
    $cForRetrieve = clone $this->getCriteria();
    $cForRetrieve->setOffset($offset - 1);
    $cForRetrieve->setLimit(1);

    $results = call_user_func(array($this->getClassPeer(), 'doSelect'), $cForRetrieve);

    return is_array($results) && isset($results[0]) ? $results[0] : null;
  }

  /**
   * Sets the criteria to dirty, which will enable rebuilding the criteria
   * 
   * @param bool $criteriaDirty
   */
  public function setCriteriaDirty($criteriaDirty = true)
  {
    $this->criteriaDirty = $criteriaDirty;
  }
}