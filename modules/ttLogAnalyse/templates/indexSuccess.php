<?php
include_partial('breadcrumb');
?>
<style>
  tr.no_issue
  {
    display:none;
  }
  
  table.grid tbody td > div {
    height:auto !important
  }
</style>
<h2 class="pageblock">Opties</h2>
<div class="pageblock">
  <table class="formtable">
    <tr>
      <th>Toon alle</th>
      <td><?php echo checkbox_tag('show_all');?></td>
    </tr>
  </table>
</div>
<div id="log_tabs">
<ul  class="tt-tabs">
  <?php foreach($detailPerFile as $entry => $detailInfo):?>
  <li><a href="#log_<?php $entry;?>"><?php echo $entry;?></a></li>
  <?php endforeach;?>
</ul>
  <?php foreach($detailPerFile as $entry => $detailInfo):?>
  <div id="log_<?php echo $entry;?>">
    <h2 class="pageblock">Template</h2>
    <div class="pageblock">
  <?php
    $table = new myTable(
                        array(
                          array("text" => 'Action'),
                          array("text" => 'Database time', 'width' => '75px'),
                          array("text" => 'Template time', 'width' => '75px'),  
                          array("text" => 'Total time', 'width' => '75px'),
                          array("text" => 'Detail')
                        )
                      );
    foreach($detailInfo['template'] as $action => $actionEntries) {
      foreach($actionEntries as $detailLine)
      {
        $options = array();
        if(! ($detailLine['database_timer'] > 500  ||
                $detailLine['timer'] > 2000
                ))
        {
          $options['class'] = 'no_issue';
        }
        else
        {
          $options['class'] = 'issue';
        }
        $table->addRow(array(
                        $action,
                        $detailLine['database_timer'] . ' ms',
                        $detailLine['view_timer'] . ' ms',
                        $detailLine['timer'] . ' ms',
                        $detailLine['detail']
                          ), $options
                      );
      }
    }
    echo $table;
    echo '<br/>';
    $table = new myTable(
                        array(
                          array("text" => 'Time', 'width' => '75px'),
                          array("text" => 'Query'),
                        )
                      );
    foreach($detailInfo['database'] as $line) 
    {
      $options = array();
      if(! ($line['time'] > 500))
      {
        $options['class'] = 'no_issue';
      }
      else
      {
        $options['class'] = 'issue';
      }
      $table->addRow(array(
                      $line['time'] . ' ms',
                      $line['query'] . ' ms',
                        ), $options
                    );
    }
    echo $table;
  ?>
   </div>
  </div>
  <?php endforeach;?>
</div>
  
<script type='text/javascript'>
  jQuery(function($)
  {
    jQuery('#log_tabs').tt_tabs({cache:false});
    jQuery('#show_all').change(function()
    {
      if($(this).is(':checked'))
      {
        $('.no_issue').show();
        $('.issue').css('background-color', 'red');
        $('.issue').css('color', 'white');
      }
      else
      {
        $('.no_issue').hide();
        $('.issue').css('background-color', 'white');
        $('.issue').css('color', 'black');
        
      }
    })
  });
</script>