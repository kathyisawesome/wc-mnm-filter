( function( $ ) {



  /**
   * Main container object.
   */
  function WC_MNM_Filter( $form ) {

    var filter = this;

    this.$form   = $form;
    this.$products = $form.find( '.mnm_child_products' );
    this.$filter = $form.find( '.mnm_filter_button_group' );
    this.taxonomy = this.$filter.data( 'taxonomy' );

    // @todo- When MNM supports custom data attrs, use that instead of classes.
    this.classTerm = '.' + this.taxonomy + '-';

    /**
     * Object initialization.
     */
    this.initialize = function() {

      /**
       * Remove empty buttons.
       */
      this.hide_empty_buttons();

      /**
       * Bind event handlers.
       */
      this.bind_event_handlers();


      
      this.$filter.show();

    };


    /**
     * Remove empty buttons before display.
     */

    this.hide_empty_buttons = function() {

      $( '.mnm-filter-button-group button' ).each( function (i) {
        
        var filterValue = $(this).attr( 'data-filter' );

        if( '*' != filterValue && ! filter.$products.find( filter.classTerm + filterValue ).length ) {
          $(this).hide();
        }

      });

    }


    /**
     * Events.
     */

    this.bind_event_handlers = function() {
      this.$filter.on( 'click', 'button', this.filter );
    };

    /**
     * Filter the products.
     */

    this.filter = function(e) {

      e.preventDefault();

      // Remove all existing classes.
      filter.$filter.find( 'button' ).removeClass( 'selected' );

      $(this).addClass( 'selected' );
      
      var filterValue = $(this).data( 'filter' );
      
      if( '*' == filterValue ) {
        filter.$products.find( '.mnm_item' ).show();
      } else {
        filter.$products.find( '.mnm_item' ).hide();
        filter.$products.find( filter.classTerm + filterValue ).show();
      }

      // Fix grid layout classes.
      if( filter.$form.hasClass( 'layout_grid' ) ) {

        var columns = WC_MNM_FILTER_PARAMS.columns;

        // Restore first/last loop classes
        filter.$products.find( '.mnm_item' ).removeClass( 'first' ).removeClass( 'last' );
        filter.$products.find( '.mnm_item:visible' ).each( function (i) {
        if ( i== 0 || (i+1) % columns == 0 ) $(this).addClass( 'first' );
          if ( (i+1) % columns == 0 ) $(this).addClass( 'last' );
        });

      }

    };

    // Launch.
    this.initialize();

  } // End WC_MNM_Filter.

  /*-----------------------------------------------------------------*/
  /*  Initialization.                                                */
  /*-----------------------------------------------------------------*/

  jQuery( document ).ready( function($) {
  
    /*
     * Initialize form script.
     */
    $( '.mnm_form' ).each( function() {
      new WC_MNM_Filter( $(this) );
    } );

    /**
     * QuickView compatibility.
     */
    $( 'body' ).on( 'quick-view-displayed', function() {

      $( '.mnm_form' ).each( function() {
        new WC_MNM_Filter( $(this) );
      } );

    } );

  } );

} ) ( jQuery );



