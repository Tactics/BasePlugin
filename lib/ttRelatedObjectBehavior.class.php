<?php

/**
 * @package    symfony
 * @subpackage ttDmsHasStorageBehavior
 * @author     Gert Vrebos <gert.vrebos@tactics.be>
 */
 
/**
 * ttRelatedObjectBehavior
 * 
 * Eenvoudig behavior een class te voorzien van een link naar
 * een willekeurig ander object van een onbekend type
 * 
 * class en primary key worden dus opgeslagen in 2 database velden
 * 
 * primary key moet van het type integer zijn
 * 
 * standaard velden zijn object_id en object_class, te configureren
 * met parameters aan het behavior:
 *   object_class_column
 *   object_id_column
 * 
 * @package CSJ
 * @author Tactics bvba
 * @copyright 2010
 * @access public
 */
class ttRelatedObjectBehavior
{
  
  /**
   * Zet het gerelateerde object
   * 
   * @param mixed object
   */
  public function setObject($object, $relatedObject)
  {
    $peerClass = get_class($object) . 'Peer';
    
    $objectClassColumn = sfConfig::get('propel_behavior_act_as_sortable_'. get_class($object). '_object_class_column', 'object_class');
    $objectIdColumn = sfConfig::get('propel_behavior_act_as_sortable_'. get_class($object). '_object_id_column', 'object_id');
    
    $classSetter = 'set' . call_user_func(array($peerClass, 'translateFieldName'), $objectClassColumn, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_PHPNAME);
    $idSetter = 'set' . call_user_func(array($peerClass, 'translateFieldName'), $objectIdColumn, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_PHPNAME);
    
    if (is_object($relatedObject))
    {
      $object->$classSetter(get_class($relatedObject));
      $object->$idSetter($relatedObject->getPrimaryKey());
    }
    else
    {
      $this->$classSetter(null);
      $this->$idSetter(null);
    }
  }
  
  
  /**
   * Geeft het gerelateerde object
   * 
   * @return mixed object
   */
  public function getObject($object)
  {
    $peerClass = get_class($object) . 'Peer';
    
    $objectClassColumn = sfConfig::get('propel_behavior_act_as_sortable_'. get_class($object). '_object_class_column', 'object_class');
    $objectIdColumn = sfConfig::get('propel_behavior_act_as_sortable_'. get_class($object). '_object_id_column', 'object_id');
    
    $classGetter = 'get' . call_user_func(array($peerClass, 'translateFieldName'), $objectClassColumn, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_PHPNAME);
    $idGetter = 'get' . call_user_func(array($peerClass, 'translateFieldName'), $objectIdColumn, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_PHPNAME);

    if ($object->$classGetter() && $object->$idGetter())
    {
      $relatedObjectPeerClass = $object->$classGetter() . 'Peer'; 
      return call_user_func(array($relatedObjectPeerClass, 'retrieveByPk'), $object->$idGetter());
    }
    
    return null;
  }
  
}