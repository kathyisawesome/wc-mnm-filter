( function( $ ) {

    // Hide buttons that don't have options.
    $('.mnm-filter-button-group button').each(function (i) {
        var filterValue = $(this).attr( 'data-filter');
        if( '*' != filterValue && ! $('.mnm_form .products').find( '.product_cat-' + filterValue ).length ) {
          $(this).hide();
        }
    });

    $('.mnm-filter-button-group').show();

  // filter items on button click
  $('.mnm-filter-button-group').on( 'click', 'button', function() {
    $('.mnm-filter-button-group button').removeClass('selected');
    $(this).addClass('selected');
    var filterValue = $(this).attr( 'data-filter');
    if( '*' == filterValue ) {
      $('.mnm_form .products .mnm_item').show();
    } else {
     $('.mnm_form .products .mnm_item').hide();
     $('.mnm_form .products').find( '.product_cat-' + filterValue ).show();
    }

    var columns = WC_MNM_FILTER_PARAMS.columns;

    // Restore first/last loop classes
    $('.mnm_form .products .mnm_item').removeClass('first').removeClass('last');
    $('.mnm_form .products .mnm_item:visible').each(function (i) {
        if ( i== 0 || (i+1) % columns == 0 ) $(this).addClass('first');
        if ( (i+1) % columns == 0 ) $(this).addClass('last');
    });


  });

} ) ( jQuery );


