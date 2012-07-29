Kohana.behaviors.finances = function() {
  
  // Format amount input values after changing.
  $('input.amount-input').change(function() {
    $(this).val(parseMoney($(this).val()));
  })
  .focus(function() {
    if ($(this).val() == '0.00') {
      $(this).val('');
    }
  })
  .blur(function() {
    if ($(this).val() == '') {
      $(this).val('0.00');
    }
  });
  
  
  // Handle the campaign payment select options
  $('#payment-amount input[type=radio]', '#campaign-page').change(function() {
    if ($(this).val() === '-1') {
      $('#amount').focus();
      update_total_campaign_amount('0');
    }
    else {
      $('#amount').val('');
      update_total_campaign_amount($(this).attr('amount'));
    }
  });
  $('#payment-amount input.amount', '#campaign-page').change(function() {
    $(this).val(parseMoney($(this).val()));
    update_total_campaign_amount($(this).val());
  });
  

  // Total the amounts for payment on the payment form.
  $('input:text', '#payment-table').change(function() {
    // Format value
    $(this).val(parseMoney($(this).val()));
    
    // Sum amounts and display total.
    var sum = 0;
    $('input:text', '#payment-table').each(function() {
      sum += parseFloat($(this).val());
    });
    $('#pay-total').html('$' + sum.toFixed(2));
  });

  // Show/Hide the echeck/credit fields.
  if ($(':radio:checked', '#payment-method').val() == 'echeck') {
    $('#echeck').show();
    $('#credit-card').hide();
  }
  else {
    $('#echeck').hide();
    $('#credit-card').show();
  }
  $(':radio', '#payment-method').change(function() {
    $('#echeck, #credit-card').toggle();
  });

  // Make sure that when making a payment, the form isn't submitted twice.
  $('#process-input input').click(function() {
    $('#process-input').addClass('processing');
  });

  // Format the amount/percent entry on automatic late fees.
  if ($('#finance-charge-form').size() > 0) {
    late_fee_format();
    $(':radio', '#late-fee-format').change(late_fee_format);
  }
}

function update_total_campaign_amount(value) {
  $('#payment-total-amount').html(parseMoney(value));
}
function late_fee_format() {
  // Set no late fee as default.
  if ($(':radio:checked', '#late-fee-format').val() == undefined) {
    $(':radio[value=]', '#late-fee-format').attr('checked', true);
  }
  
  // Grab value and show/hide fields based on value.
  var value = $(':radio:checked', '#late-fee-format').val();
  if (value == '') {
    $('#late-fee-amount').hide();
  }
  else {
    $('#late-fee-amount').show();
  }
  if (value == 'percent') {
    $('.dollar-symbol', '#late-fee-format').hide();
    $('.percent-symbol', '#late-fee-format').show();
    $('label', '#late-fee-amount').html('Percent');
  }
  else if (value == 'amount') {
    $('.dollar-symbol', '#late-fee-format').show();
    $('.percent-symbol', '#late-fee-format').hide();
    $('label', '#late-fee-amount').html('Amount');
  }
}