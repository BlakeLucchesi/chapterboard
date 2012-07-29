// Mimick the way drupal attaches js behaviors from each module.
// All event linsteners get put inside Kohana.behaviors.fn() and are always
// rebound automagically after page load and/or ajax events.
var Kohana = Kohana || { 'settings': {}, 'behaviors': {}, 'themes': {}, 'locale': {} };
Kohana.jsEnabled = document.getElementsByTagName && document.createElement && document.createTextNode && document.documentElement && document.getElementById;

Kohana.attachBehaviors = function(context) {
  context = context || document;
  if (Kohana.jsEnabled) {
    // Execute all of them.
    jQuery.each(Kohana.behaviors, function() {
      this();
    });
  }
};

Kohana.behaviors.site = function() {
  // External links in a new window.
  $("#main a[href^=http]").each(function(){
    if(this.href.indexOf(location.hostname) == -1) {
      $(this).attr('target', '_blank');
    }
  });

  $('table.sort').tablesorter();
  
  // Table select.
  $('table.select th input:checkbox').click(function() {
    var is_checked = this.checked;
    $('input:checkbox', $(this).parents('table')).each(function() {
      this.checked = is_checked;
    });
  });
  
  $('.hoverable').live('mouseover',
    function() {
      $(this).addClass('hover');
    }
    ).live('mouseout',
    function() {
      $(this).removeClass('hover');
    }
  );
  $('.hoverable').live('click', function() {
    if ($(this).find('a.hoverclick').size()) {
      window.location = $(this).find('a.hoverclick').attr('href');
    }
  });
  
  $('.form-tip[title], .help-tip[title]').qtip({style: 'cb', position: { corner: { target: 'topLeft', tooltip: 'bottomRight' }}});
    
  $('.date-pick').datepicker();

  // Administration Links
  $('.admin-hover').hover(function() {
    $(this).find('.admin-links').show();
  }, function() {
    $(this).find('.admin-links').hide();
  });
  
  // Administration link alerts
  $('a.alert').click(function() {
    return confirm($(this).attr('title'));
  });
  
  // Hide the help message text.
  $('a.close-message').click(function() {
    $.get('/help/hide/'+ $(this).attr('key'));
    $(this).parents('.message-hideable').fadeOut();
  });
  
  
  $('#poll-form :checkbox').change(function() {
    if ($('#poll-form :checked').size()) {
      $('#poll-choices').slideDown();
    }
    else {
      $('#poll-choices').slideUp();
    }
  })
  $('#add-poll-option').click(function(e) {
    var next = $('input[type=text]', '#poll-choices').size();
    $(this).before('<br /><label>Option '+ next +'</label><input type="text" name="poll[]" />');
    e.preventDefault();
  });
  
  $('select', '#payment-type-select').change(function() {
    if ($('option:selected', '#payment-type-select').val() == 'check') {
      $('#check_no').show().find('input').focus();
    }
    else {
      $('#check_no').hide();
    }
  });
}

Kohana.behaviors.upload = function() {
  $('#attach').change(upload_attached_file);
  $('button#upload.unbound').click(upload_attached_file);
  $('button#upload.unbound').hide(); // Hide the 'attach' button, use auto attach from above.
  
  $('a.remove-file').live('click', function() {
    $('#attachments').load('/upload/remove', {key: $('#hidden-key').val(), filehash: $(this).attr('filehash')});
    return false;
  });
}

upload_attached_file = function() {
  var options = {
    url:           '/upload/file',
    target:        '#attachments',   // target element(s) to be updated with server response 
    beforeSubmit:  showUploadRequest,  // pre-submit callback 
    success:       showUploadResponse  // post-submit callback
  }
  $(this).parents('form').ajaxSubmit(options);
  $(this).removeClass('unbound');
  return false;
}

Kohana.behaviors.search_box = function() {
  if ( ! $('#name', '#search').val()) {
    $('#name', '#search').val('Search members...');
  }
  $('#name', '#search').focus(function() {
    if ($('#name', '#search').val() == 'Search members...') {
      $('#name', '#search').val('');
    }
  });
  $('#name', '#search').blur(function() {
    if ($('#name', '#search').val() == '') {
      $('#name', '#search').val('Search members...');
    }
  });
}

function showUploadRequest(formData, jqForm, options) {
  $('#upload-wrapper').addClass('loading');
}

function showUploadResponse(responseText, statusText) {
  $('#upload-wrapper').removeClass('loading');
  $('#attach').val('');
}

$.fn.qtip.styles.cb = { // Last part is the name of the style
   color: 'black',
   textAlign: 'center',
   border: {
     width: 6,
     radius: 5
   },
   tip: 'bottomRight',
   name: 'light' // Inherit the rest of the attributes from the preset dark style
}

// add parser through the tablesorter addParser method 
$.tablesorter.addParser({ 
   // set a unique id 
   id: 'link_sort', 
   is: function(s) { 
       // return false so this parser is not auto detected 
       return false; 
   }, 
   format: function(s) { 
       // format your data for normalization 
       s = jQuery.trim(s);
       var result = s.replace(/<a href="[^"]+">([^<]+)<.*/i, '$1').toLowerCase();
       return result;
   }, 
   // set type, either numeric or text 
   type: 'text' 
});

$.tablesorter.addParser({ 
   // set a unique id 
   id: 'admin_link_sort', 
   is: function(s) { 
       // return false so this parser is not auto detected 
       return false; 
   }, 
   format: function(s) { 
       // format your data for normalization 
       s = jQuery.trim(s);
       var result = s.match(/<a href="[^"]+" class="title-link">([^<]+)<.*/i, '$1');
       if (result == null) {
         return false;
       }
       return result[1].toLowerCase();
   }, 
   // set type, either numeric or text 
   type: 'text' 
});

$.tablesorter.addParser({ 
   // set a unique id 
   id: 'thousands_sorter', 
   is: function(s) {
       // return false so this parser is not auto detected 
       return s.search(/^[0-9\,\.]+$/) > -1 ? true : false;
       // return true; 
   }, 
   format: function(s) { 
       // format your data for normalization 
       s = jQuery.trim(s);
       return s.replace(/\,/g,'');
   }, 
   // set type, either numeric or text 
   type: 'numeric' 
});

/**
 * Parse an input value and make sure it is a positive
 * floating number, otherwise return 0.00.
 *
 * Should be used where we want a positive value for charges or payments.
 */
function parseMoney(value) {
  var temp;
  temp = parseFloat(value.replace(/[^0-9\.]/i, ''));
  if (temp <= 0 || isNaN(temp)) {
    return '0.00';
  }
  return temp.toFixed(2);
}

$(document).ready(function() {
  Kohana.attachBehaviors();
  $().ajaxComplete(function() {
    Kohana.attachBehaviors();
  })
});