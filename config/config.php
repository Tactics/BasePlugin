<?php

// register 
sfPropelBehavior::registerHooks('related_object', array(
  ':delete:pre' => array('ttRelatedObjectBehavior', 'preDelete'),  
));

// ttRelatedObjectBehavior:  register behavior hooks
sfPropelBehavior::registerMethods('related_object', array (
  array ('ttRelatedObjectBehavior', 'getObject'),
  array ('ttRelatedObjectBehavior', 'setObject'),
));
