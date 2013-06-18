<?php
include_partial('breadcrumb');

 function sortByTime($a, $b) {
   return strcmp($b['time'], $a['time']);
 }
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
      usort($actionEntries, 'sortByTime');
      foreach($actionEntries as $detailLine)
      {
        $table->addRow(array(
                        $action,
                        $detailLine['database_timer'] . ' ms',
                        $detailLine['view_timer'] . ' ms',
                        $detailLine['time'] . ' ms',
                        $detailLine['detail']
                          )
                      );
      }
    }

    echo $table;
    ?>
    </div>
    <h2 class="pageblock">Database</h2>
    <div class="pageblock">
    <?php
    $table = new myTable(
                        array(
                          array("text" => 'Time', 'width' => '75px'),
                          array("text" => 'Query'),
                        )
                      );
    usort($detailInfo['database'], 'sortByTime');
    foreach($detailInfo['database'] as $query => $line)
    {
      $table->addRow(array(
                      $line['time'] . ' ms',
                      $line['query'],
                          )
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
  });
</script>