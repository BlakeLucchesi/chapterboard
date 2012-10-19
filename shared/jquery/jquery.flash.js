/**
 * Flash message plugin.
 *
 * Calling this function on a wrapped set will insert the html from
 * the first argument into the wrapped element and show the element.
 * After the length of time set in timeout, the element html will be
 * emptied and the element hidden. 
 *
 * Author: Blake Lucchesi (thisbythem.com)
 *
 * @param string html
 * HTML Text to be placed inside the flash message element.
 *
 * @param object options
 * {timeout: integer} Provide a timeout length (in milliseconds).
 *
 */
(function($) {  
  
  $.fn.flash = function(type, html, timeout) {
    if (!timeout) {
      var timeout = 5000;
    }
    // Provide default settings.
    // var settings = $.extend({
    //   length: 4000
    // },options||{});

    var target = this; // Store for use in setTimeout enclosure.
    this.addClass('message-'+type).html(html).fadeIn('slow');
    setTimeout(function() {
      target.fadeOut('slow').removeClass('message-'+type).html();
    }, timeout);
    return this;
  }
}) (jQuery);