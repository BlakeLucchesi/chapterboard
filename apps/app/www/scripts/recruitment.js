Kohana.behaviors.recruitment = function() {
  // Sorting
  $('#recruitment-dashboard .sorting a').click(function() {
    $('#recruitment-dashboard .sorting a').removeClass('active');
    $(this).addClass('active');
    $('#recruits .recruit.sortable').tsort({attr: $(this).attr('sort'), order: $(this).attr('order')});
    $('#recruits').hide().fadeIn('slow');
    return false;
  });
}