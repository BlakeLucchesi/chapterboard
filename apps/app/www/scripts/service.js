Kohana.behaviors.service = function() {
  
  toggleCustomServiceRange($('select', '#service-year-form').val() === 'custom');
  
  $('select', '#service-year-form').change(function() {
    toggleCustomServiceRange($(this).val() === 'custom');
  });

  $('#event-toggle').click(function() {
    if ( ! $(this).hasClass('create-event')) {
      $('#event-create').slideDown();
      $('select#event_id').attr('disabled', 'disabled').val('');
      $(this).addClass('create-event').html('- Select Existing Event');
    }
    else {
      $('#event-create').slideUp();
      $('input#title, input#date').val('');
      $('select#event_id').attr('disabled', '');
      $(this).removeClass('create-event').html('+ Create New Event');
    }
    return false;
  });
}

function toggleCustomServiceRange(visible) {
  $('#service-year-custom-range').toggle(visible);
}