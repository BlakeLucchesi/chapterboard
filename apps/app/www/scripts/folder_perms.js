Kohana.behaviors.folder_permissions = function() {
  // Update allowed groups on page load.
  update_allowed_groups($('#parent_id').val());
  // Update allowed groups on parent_id change.
  $('#parent_id').change(function() {
    update_allowed_groups($(this).val());
  });
}

function update_allowed_groups(selected) {
  // Allow all permissions for top level folders.
  if (selected == 0) {
    $('input[type=checkbox]').attr('disabled', '');
  }
  // Inherit permissions from the parent.
  else {
    $('input[type=checkbox]').attr('checked', false).attr('disabled', 'disabled');
    $.each(Kohana.settings.permissions[selected], function(index, value) {
      $('input[name=groups['+index+']]').attr('checked', true).attr('disabled', '');
    });      
  }
}