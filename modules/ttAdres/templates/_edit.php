<?php
/** @var sfRequest $sf_request */
/** @var boolean $edit_land */
/** @var boolean $disabled */
?>

<div class="row">
  <div class="field-container span-half-1">
    <label class="label required" for="<?php echo $field_landid; ?>">Land</label>
    <div class="field-layout-wrapper">
      <div class="prefixWrapper">
        <?php if ($edit_land) : ?>

          <select class="field full <?php echo $sf_request->hasError($field_landid) ? 'invalid' : ''; ?>" name="<?php echo $field_landid; ?>" id="<?php echo $field_landid; ?>" <?php if ($disabled) echo 'disabled="disabled"'; ?>>
            <option value="">-Onbepaald-</option>
            <?php
            echo objects_for_select($landen, 'getIso', 'getNaam', $object->getLandId() ?: 'BE');
            ?>
          </select>
        <?php else:  ?>
          <?php echo input_hidden_tag($field_landid, $object->getLandId() ?: 'BE'); ?>
          <?php echo $object->getLandId() ?: 'BE'; ?>
        <?php endif;?>
      </div>
    </div>
  </div>
  <div class="field-container span-half-2">
    <label class="label required" for="<?php echo $gemeente_zoekveld; ?>">Postcode of gemeente</label>
    <div class="field-layout-wrapper">
      <div class="prefixWrapper">

        <?php echo input_tag($gemeente_zoekveld, trim($object->getPostcode() . ' ' . $object->getGemeente()), array('class' => 'field full '.($sf_request->hasError($field_gemeente) ? 'invalid' : ''), ($disabled ? 'disabled' : '') => ($disabled ? 'disabled' : ''))); ?>

        <?php echo input_tag($field_postcode, $object->getPostcode(), array (
          'style' => 'width: 100px; float:left; margin-right: 25px;', 'class' => 'field full '.($sf_request->hasError($field_postcode) ? 'invalid' : ''), ($disabled ? 'disabled' : '') => ($disabled ? 'disabled' : '')
        )) ?>
        <?php echo input_tag($field_gemeente, $object->getGemeente(), array (
          'style' => 'width: 240px;', 'class' => 'field full '.($sf_request->hasError($field_gemeente) ? 'invalid' : ''), ($disabled ? 'disabled' : '') => ($disabled ? 'disabled' : '')
        )) ?>
      </div>
    </div>
  </div>
</div>
<div class="row">
  <div class="field-container span-half-1">
    <label class="label required" for="<?php echo $field_straat; ?>">Straat</label>
    <div class="field-layout-wrapper">
      <div class="prefixWrapper">
        <?php echo input_tag($field_straat, $object->getStraat(), array('class' => 'full field '.($sf_request->hasError($field_straat) ? 'invalid' : ''), ($disabled ? 'disabled' : '') => ($disabled ? 'disabled' : ''))) ?>
      </div>
    </div>
  </div>
  <div class="field-container span-3-7">
    <label class="label required" for="<?php echo $field_nummer; ?>">Nummer</label>
    <div class="field-layout-wrapper">
      <div class="prefixWrapper">
        <?php echo input_tag($field_nummer, $object->getNummer(), array (
          'class' => 'field full '.($sf_request->hasError($field_nummer) ? 'invalid' : ''), ($disabled ? 'disabled' : '') => ($disabled ? 'disabled' : '')
        )) ?>
      </div>
    </div>
  </div>
  <div class="field-container span-3-10">
    <label class="label" for="<?php echo $field_bus; ?>">Bus</label>
    <div class="field-layout-wrapper">
      <div class="prefixWrapper">
        <?php echo input_tag($field_bus, $object->getBus(), array (
          'class' => 'field full', ($disabled ? 'disabled' : '') => ($disabled ? 'disabled' : '')
        )) ?>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">

  jQuery(function($)
  {

    function updateGemeenteEditor()
    {
      if ($('#<?php echo $field_landid; ?>').val() === 'BE')
      {
        $('#<?php echo $gemeente_zoekveld; ?>').val($.trim($('#<?php echo $field_postcode; ?>').val() + ' ' + $('#<?php echo $field_gemeente; ?>').val())).show();
        $('#<?php echo $field_postcode; ?>').hide();
        $('#<?php echo $field_gemeente; ?>').hide();
      }
      else
      {
        $('#<?php echo $gemeente_zoekveld; ?>').hide();
        $('#<?php echo $field_postcode; ?>').show();
        $('#<?php echo $field_gemeente; ?>').show();
      }
    }

    jQuery('#<?php echo $field_landid; ?>').change(function()
    {
      updateGemeenteEditor();
    });

    jQuery('#<?php echo $gemeente_zoekveld;?>')
      .autocomplete('<?php echo url_for('ajax/gemeentes'); ?>', {
        mustMatch: true,
        max: 10,
        scroll: true,
        scrollHeight: 300,
        width: 300,
        cacheLength: 0,
        minChars: 3,
        delay: 800,
        parse: function(data)
        {
          return $.map(eval(data), function(row) {
            return {
              data: row,
              value: row.code + ' ' + row.gemeente,
              result: row.code + ' ' + row.gemeente
            }
          });
        },
        formatItem: function(data)
        {
          return data.code + ' ' + data.gemeente;
        }
      })
      .result(function(e, item)
      {
        jQuery('#<?php echo $field_postcode;?>').val(item.code);
        jQuery('#<?php echo $field_gemeente;?>').val(item.gemeente);
        jQuery('#<?php echo $gemeente_zoekveld;?>').val(item.code + ' ' + item.gemeente );
      })
      .change(function()
      {
        if (jQuery(this).val() === '')
        {
          jQuery('#<?php echo $gemeente_id;?>').val('');
        }
      });

    updateGemeenteEditor();

    $('#<?php echo $field_straat;?>')
      .autocomplete('<?php echo url_for('ajax/straten'); ?>', {
        mustMatch: false,
        max: 10,
        scroll: true,
        scrollHeight: 300,
        width: 300,
        cacheLength: 0,
        extraParams: {postcode: function(){return $('#<?php echo $field_postcode; ?>').val();}},
        parse: function(data)
        {
          return $.map(eval(data), function(row)
          {
            return {
              data: row,
              value: row.straat,
              result: row.straat
            }
          });
        },
        formatItem: function(data)
        {
          return data.straat;
        }
      });
  });
</script>
<style>
  #straat_auto_complete ul
  {
    list-style: none none;
  }
</style>

