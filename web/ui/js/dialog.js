(function($) {

  $.tt = $.tt || {};
  
  /**
   * Shows an nice alert box as alternative to native alert() 
   */  
  $.tt.alert = function(title, html, options)
  {
    var defaults =
    {
      top: 250,
      expose: {
          color: '#333', 
          loadSpeed: 200, 
          opacity: 0.7 
      },
      closeOnClick: false,
      buttons: {
        ok: function(){}
      },
      className: ''
    };
    
    if (options && options.buttons)
    {
      defaults.buttons = null;
    }
    
    var options = $.extend(true, {}, defaults, options);
    options.api = true;
    
    if (div = $('#tt_alertdialog_div'))
    {
      div.remove();
    }
    
    $('body').append('<div id="tt_alertdialog_div" class="ttBase-dialog-modal ' + options.className + '"></div>');
    div = $('#tt_alertdialog_div');
    
    div
      .html('')
      .append("<h2>" + title + "</h2>")
      .append(html);
    
    
    if (options.buttons)
    {
      buttons = $('<p class="buttons">');
      
      $.each(options.buttons, function(i, val)
      {
        $('<button class="close">' + i + '</button>')
          .appendTo(buttons)
          .click(function(){val.apply(this)}); 
      });
      
      div.append(buttons);
    }
    
    div.overlay(options).load();
    
    return div;
  }

  /**
   * Shows an nice confirm box as alternative to native confirm() 
   */  
  $.tt.confirm = function(title, text, callback, options)
  {
    defaults = {
      buttons: {
				'Ja': function() {
					callback ? callback(true, this) : null;
				},
				'Nee': function() {
					callback ? callback(false, this) : null;
				}
			},
      
      className: 'confirm'
		};
    
    options = $.extend({}, defaults, options);
		
		return $.tt.alert(title, text, options);
  }

  /**
   * Shows an nice prompt box as alternative to native prompt() 
   */  
  $.tt.prompt = function(title, text, callback, options)
  {
    defaults = {
      buttons: {
				'OK': function() {
				  $(this).dialog('close');
					callback ? callback($(this).find(':text').val(), this) : null;
				},
				'Annuleren': function() {
					$(this).dialog('close');
					callback ? callback(false, this) : null;
				}
			},
      
      className: 'prompt'
		};

    options = $.extend({}, defaults, options);
		
    text += '<p><input type="text"/></p>';
    
		div = $.tt.alert(title, text, options);
    div.find('input').focus();
  }
  
})(jQuery);
