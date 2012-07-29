$(document).ready(function() { 
  $('#name').focus();

  $('#name').autocomplete('/members/autocomplete', {
    selectFirst: true,
    minChars: 2
  });	
	
  $('#name').result(add_member);
	
});

function add_member(event, data, formatted) {
  $('#flash').append('<div class="message"><b>'+ data +'</b> has been added.</div>');
  setTimeout(function() { $('#flash .message:first-child').fadeOut('slow').remove(); }, 2500);
  $('#members').load(Kohana.groups_url, {name: $('#name').val()});
  $('#name').val('').focus();
}