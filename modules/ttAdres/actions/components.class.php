<?php
class ttAdresComponents extends sfComponents
{
  public function executeEdit()
  {
    // Gegeven object moet van class ttAdresGegevens zijn.
    if (get_class($this->object) != ttAdresGegevens::class)
      throw new Exception('Het gegeven object is geen ttAdresGegevens');

    // Mogelijke parameters.
    $this->prefix = isset($this->prefix) ? $this->prefix : '';
    $this->disabled = isset($this->disabled) ? $this->disabled : false;
    $this->edit_land  = isset($this->edit_land) ? $this->edit_land : true;

    // Namen van de velden correct zetten met gebruik van gegeven prefix.
    $this->gemeente_id = $this->prefix .  'gemeente_id';
    $this->gemeente_zoekveld = $this->gemeente_id . '_zoekveld';
    $this->field_landid = $this->prefix . 'land_id';
    $this->field_postcode = $this->prefix . 'postcode';
    $this->field_gemeente = $this->prefix . 'gemeente';
    $this->field_straat = $this->prefix . 'straat';
    $this->field_nummer = $this->prefix . 'nummer';
    $this->field_bus = $this->prefix . 'bus';

    $c = new Criteria();
    $c->addAscendingOrderByColumn(LandPeer::NAAM);
    $this->landen = LandPeer::doSelect($c);
  }
}