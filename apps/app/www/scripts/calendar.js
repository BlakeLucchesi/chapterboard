Kohana.behaviors.calendar = function() {

  // Hide the submit button on the menu calendar select drop down.
  $('#calendar-select-form :submit').hide();
  $('#calendar_id').change(function() {
    $(this).parents('form').submit();
  });

  $(':submit', '#signup-form').hide();
  $('input[name=rsvp]', '#signup-form').change(function() {
    $('form', '#signup-form').submit();
  });

  // Show and hide time inputs based on all-day toggle.
  $('#all_day').change(function() {
    var visible = $(this).is(':checked') ? false : true;
    $('#start_time, #end_time').toggle(visible);
  });
  
  // When changing the start day, only automatically update the end
  // day when the start and end day are the same. Otherwise leave
  // end day alone.
  $('#start_day').data('oldVal', $('#start_day').val());
  $('#start_day').change(function() {
    if ($('#end_day').val() == $(this).data('oldVal')) {
      $('#end_day').val($(this).val());
    }
    $(this).data('oldVal', $(this).val());
  });
  
  // Repeating events popup and handling.
  $('#repeats').change(function() {
    if ($(this).is(':checked')) {
      $('#repeat-edit').click();
    }
    else {
      $('#repeat-edit').hide();
    }
  });
  
  // Store values in .data when we open tb.
  $('#repeats, #repeat-edit').click(function() {
    if ($('#repeats').is(':checked')) {
      $('#period').data('oldVal', $('#period').val());
      $('#period_option').data('oldVal', $('input[name=period_option]:checked').val());
      $('#until').data('oldVal', $('input[name=until]:checked').val());
      $('#until_occurrences').data('oldVal', $('#until_occurrences').val());
      $('#until_date').data('oldVal', $('#until_date').val());
    }
  });
  
  // Show month period option when monthly is shown.
  $('#period').change(function() {
    $('#period_option').toggle($(this).val() == 'monthly' ? true : false);
  });
  
  // Handle cancel event.
  $('#repeat-cancel').click(function() {
    // Reset free entry values.
    $('#period').val($('#period').data('oldVal'));
    $('#until_occurrences').val($('#until_occurrences').data('oldVal'));
    $('#until_date').val($('#until_date').data('oldVal'));
    
    // Reset radio values.
    $('input[name=period_option]').each(function(i, e) {
      if ($(e).attr('value') == $('#period_option').data('oldVal')) {
        $(e).attr('checked', true);
      }
      else {
        $(e).attr('checked', false);
      }
    });
    $('input[name=until]').each(function(i, e) {
      if ($(e).attr('value') == $('#until').data('oldVal')) {
        $(e).attr('checked', true);
      }
      else {
        $(e).attr('checked', false);
      }
    });
    show_hide_toggle();
    tb_remove();
  });
  
  // Handle save.
  $('#repeat-done').click(function() {
    show_hide_toggle();
    tb_remove();
  });
  
  // Run this on page load so that we setup form based on submitted values.
  show_hide_toggle();
}

var show_hide_toggle = function() {
  $('#start_time, #end_time').toggle($('#all_day').is(':checked') ? false : true); 
  $('#repeat-edit').toggle($('#repeats').is(':checked') ? true : false);
  $('#period_option').toggle($('#period').val() == 'monthly' ? true : false);
}