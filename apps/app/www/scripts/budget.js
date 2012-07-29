Kohana.behaviors.budget = function() {

  $('input[name^=category], input[name=starting_balance], input[name=uncharged_dues]', '#budget').change(function() {
    $(this).moneyFormat();
    update_net();
    $(this).parents('tr').addClass('changed');
  });
  
  $('input[name^=category]', '#income').change(function() {
    var available = $(this).valNumber();
    var actual = $(this).parent().next().htmlNumber();
    var result = $(this).parent().next().next();
    if (actual > available) {
      result.html(formatCurrency(0));
    }
    else {
      result.html(formatCurrency(available-actual));
    }


  });
  
  $('input[name^=category]', '#expenses').change(function() {
    var available = $(this).valNumber();
    var actual = $(this).parent().next().htmlNumber();
    var result = $(this).parent().next().next();
    result.html(formatCurrency(available-actual));
    if (available - actual > 0) {
      result.removeClass('red');
    }
    else {
      result.addClass('red');
    }
  });
}

// Updates the available or outstanding amount.
function update_row() {
  
}

// Updates the net profit / loss after changes are made.
function update_net() {
  var collections = 0;
  var income = 0;
  var expenses = 0;
  
  // we store expected dues in a hidden html element so we don't continue to add uncharged_dues to
  // the total value.
  collections += $('#expected-dues').htmlNumber(); 
  collections += $('input[name=uncharged_dues]').valNumber();
  $('strong', '#expected-collections').html(formatCurrency(collections));
  
  $('input[name^=category]', '#income').each(function() {
    income += $(this).valNumber();
  });
  income += $('input[name=starting_balance]').valNumber();
  $('strong', '#expected-income').html(formatCurrency(income));

  $('input[name^=category]', '#expenses').each(function() {
    expenses += $(this).valNumber();
  })
  $('strong', '#expected-expenses').html(formatCurrency(expenses));
  
  var total = collections + income - expenses;
  $('#expected-profit-loss').html(formatCurrency(collections + income - expenses));
  if (total < 0) {
    $('#expected-profit-loss').addClass('red');
  }
  else {
    $('#expected-profit-loss').removeClass('red');
  }
}


// function catch_enter(e) {
//   if (e.keyCode){
//     code = e.keyCode;
//   }
//   else if (e.which) {
//     code = e.which;
//   }
//   if (code == 13) {
//     $('a#add-income').click();
//     return false;
//   }
// }

/** jQuery wrapper for formatting money amounts. **/
(function($) {
  $.fn.moneyFormat = function() {
     var num = this.val().toString().replace(/\$|\,/g,'');
     if (isNaN(num))
       num = "0";
     sign = (num == (num = Math.abs(num)));
     num = Math.floor(num*100+0.50000000001);
     cents = num%100;
     num = Math.floor(num/100).toString();
     if (cents<10)
       cents = "0" + cents;
     for (var i = 0; i < Math.floor((num.length-(1+i))/3); i++)
       num = num.substring(0,num.length-(4*i+3))+','+num.substring(num.length-(4*i+3));
    var amount = (((sign)?'':'-') + num + '.' + cents);
    return this.val(amount);
  }
  $.fn.valNumber = function() {
    return Number(this.val().replace(/\$|,/g,''));
  }
  $.fn.htmlNumber = function() {
    return Number(this.html().replace(/\$|,/g,''));
  }
})(jQuery);

/**
 * Helper Function to Format Currency
 **/
function formatCurrency(num) {
  num = num.toString().replace(/\$|\,/g,'');
  if(isNaN(num))
    num = "0";
  sign = (num == (num = Math.abs(num)));
  num = Math.floor(num*100+0.50000000001);
  cents = num%100;
  num = Math.floor(num/100).toString();
  if(cents<10)
    cents = "0" + cents;
  for (var i = 0; i < Math.floor((num.length-(1+i))/3); i++)
    num = num.substring(0,num.length-(4*i+3))+','+num.substring(num.length-(4*i+3));
 return (((sign)?'':'-') + '$' + num + '.' + cents);
 }