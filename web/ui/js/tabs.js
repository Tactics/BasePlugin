(function($) {

  $.tt = $.tt || {};
  
  $.tools.tabs.addEffect('tt_custom',  function(i, done) {
		// load via ajax if href is an url
    if (this.getTabs().eq(i).find('a').attr("href").substring(0,1) != "#")
    {
      this.getPanes().hide().eq(i).load(this.getTabs().eq(i).find('a').attr("href"), done).show();
    }
    // default: show pane
    else
    {
      this.getPanes().hide().eq(i).show();
  		done.call();
    }
	}); 
  
  $.fn.tt_tabs = function(options)
  {
    $this = $(this);
		options = $.extend({tabs: 'li', effect: 'tt_custom'}, options);
    
    // Not yet tabs
    if (! $this.hasClass("tt-tabpanes"))
    {
      return $this
        .addClass('tt-tabpanes')
        .children('ul:first')
        .addClass('tt-tabs')
        .tools_tabs($this.children('div'), options); //.history();)
    }
          
    // Returns api (if no options)
    return $this.tools_tabs(options);
  }
  
})(jQuery);
