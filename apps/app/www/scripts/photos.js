Kohana.behaviors.photos = function() {
  // Catch next/previous arrow buttons.
  $().keyup(function(event) {
    if ($(event.target).attr('id') != 'body') {
      switch (event.keyCode) {
        case 39:
          var url = $('#next-photo').attr('href');
          break;
        case 37:
          var url = $('#previous-photo').attr('href');
          break;
      }
      if (url) {
        window.location = url;
      }
    }
  });
};