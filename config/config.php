<?php

// ttRelatedObjectBehavior:  register behavior hooks
sfPropelBehavior::registerMethods('related_object', array (
  array ('ttRelatedObjectBehavior', 'getObject'),
  array ('ttRelatedObjectBehavior', 'setObject'),
));
