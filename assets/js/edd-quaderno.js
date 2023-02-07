jQuery(document).ready(function($) {
  var $form = $('#edd_purchase_form');

  // Update taxes on checkout page
  $(document.body).off('change', '#edd_cc_address input[name=card_zip]');
  $form.on('change', '#edd_tax_id, #billing_country, #card_state, #card_zip, #card_city', function(e) {
    recalculate_taxes();
    return true;
  });

  function recalculate_taxes() {
    if( '1' != edd_global_vars.taxes_enabled ) return; // Taxes not enabled

    var postData = {
      action: 'edd_recalculate_taxes',
      nonce: jQuery('#edd-checkout-address-fields-nonce').val(),
      edd_tax_id: $form.find('#edd_tax_id').val(),
      card_city: $form.find('#card_city').val(),
      card_zip: $form.find('#card_zip').val(),
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
        $('#edd_checkout_cart_form').replaceWith(tax_response.html);
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
