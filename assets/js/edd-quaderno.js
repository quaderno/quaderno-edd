jQuery(document).ready(function($) {
  var $form = $('#edd_purchase_form');
  
  // Recalculate taxes on checkout page load
  if ($form.length > 0) recalculate_taxes();

  // Update taxes on checkout page
  $form.on('change', '#tax-id, #company, .card-zip, #billing_country, #card_state', function(e) {
    e.preventDefault();
    recalculate_taxes();
    return false;
  });

  function recalculate_taxes() {
    if( '1' != edd_global_vars.taxes_enabled ) return; // Taxes not enabled

    var postData = {
      action: 'edd_recalculate_taxes',
      company: $form.find('#company').val(),
      tax_id: $form.find('#tax-id').val(),
      card_zip: $form.find('.card-zip').val(),
      billing_country: $form.find('#billing_country').val()
    };

    $.ajax({
      type: "POST",
      data: postData,
      dataType: "json",
      url: edd_global_vars.ajaxurl,
      xhrFields: {
        withCredentials: true
      },
      success: function (tax_response) {
        $('#edd_checkout_cart').replaceWith(tax_response.html);
        $('.edd_cart_amount').html(tax_response.total);
        var tax_data = new Object();
        tax_data.postdata = postData;
        tax_data.response = tax_response;
        $('body').trigger('edd_taxes_recalculated', [ tax_data ]);
      }
    }).fail(function (data) {
      if ( window.console && window.console.log ) {
        console.log( data );
      }
    });
  }
  
});
