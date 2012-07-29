
Kohana.behaviors.permissions = function() {
  // Bind all submit forms to ajax submit.
  var options = {
    resetForm: true,
    dataType:   'json',
    type:       'get',
    success:    permission_add
  }
  $('form.ajax').ajaxForm(options);
  
  $('.member').live('mouseover', function() { $(this).find('span').show(); })
    .live('mouseout', function() { $(this).find('span').hide(); });

  // Handle delete action.
  $('.member a.remove').live('click', function() {
    var element = $(this).closest('.member'); // Store so we can hide it later.
    $.getJSON(
      '/members/permissions',
      {action: 'remove', role_key: $(this).attr('role_key'), user_id: $(this).attr('user_id')},
      function(data) {
        if (data.status == 'success') {
          element.remove();
        }
        else if (data.status == 'error' && data.message != '') {
          alert(data.message);
        }
      }
    );
    return false;
  });

}


/**
 * Add new permission ajax submit callback
 * Adds a new member to the list or shows error message and then fades
 * it out.
 *
 * Bind our newly added delete links.
 */
function permission_add(data) {
  $(data.data + " input:text:first").focus();
  if (data.status == 'success') {
    $(data.data).next().prepend(data.message).find('.member:first').hide().fadeIn('slow');
  }
  else {
    $(data.data).prepend(data.message);
    setTimeout(hide_notices, 2000);
  }
}


function hide_notices() {
  $('.notice').fadeOut('slow');
}
