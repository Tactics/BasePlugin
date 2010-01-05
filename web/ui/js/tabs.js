(function($) {

  $.tt = $.tt || {};
  
  $.fn.tt_tabs = function(options)
  {
    $this = $(this);
		options = $.extend({}, options);
    
    $this
      .addClass('tt-tabpanes')
      .children('ul:first')
      .addClass('tt-tabs')
      .tabs($this.children('div'), {tabs: 'li'}); //.history();)
  }
  
})(jQuery);
