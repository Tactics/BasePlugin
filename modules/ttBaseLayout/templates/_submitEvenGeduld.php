<?php
/**
 * @var string $tekst De tekst op de submitbutton
 * @var string $replaceTekst De replacement tekst op de submitton tijdens actie
 */
if (! isset($replaceTekst)) $replaceTekst = 'Even geduld...';
$randomHash = sha1(time());
?>
<?php echo submit_tag(__($tekst), array('id' => $randomHash)); ?>
<script type="text/javascript">
  jQuery(document).ready(function($){
    var eSubmit = $('#<?php echo $randomHash; ?>');
    eSubmit.click(function(){
      eSubmit.attr('disabled', 'disabled').val('<?php echo $replaceTekst; ?>');
      eSubmit.parents('form').submit();
    })
  });
</script>