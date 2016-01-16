jQuery(document).ready(function($) {
  var $form = $('#edd_purchase_form');
  var eu_countries = ['AT', 'BE', 'BG', 'HR', 'CY', 'CZ', 'DK', 'EE', 'FI', 'FR', 'DE', 'GR', 'HU', 'IE', 'IT', 'LV', 'LT', 'LU', 'MT', 'NL', 'PL', 'PT', 'RO', 'SK', 'SI', 'ES', 'SE', 'GB'];

  // Show VAT Number and Tax ID fields
  $form.on('change', '#billing_country', function(e) {
	  if ( $(this).val() == $('#edd_shop_country').val() || $.inArray($(this).val(), eu_countries) == -1 ) {
	    $('#edd_vat_number').parent().hide();
	    $('#edd_vat_number').val('');
	  } else {
	    $('#edd_vat_number').parent().show();
	  }
	  
    $('#edd_tax_id').parent().toggle($(this).val() == 'ES');
    return true;
  });
  $('#billing_country').trigger('change');

  // Recalculate taxes on checkout page load
  if ($form.length > 0) recalculate_taxes();

  // Update taxes on checkout page
  $form.on('change', '#edd_vat_number, #billing_country, .card-zip, #card_state', function(e) {
    recalculate_taxes();
    return true;
  });

  function recalculate_taxes() {
    if( '1' != edd_global_vars.taxes_enabled ) return; // Taxes not enabled

    var postData = {
      action: 'edd_recalculate_taxes',
      edd_vat_number: $form.find('#edd_vat_number').val(),
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
