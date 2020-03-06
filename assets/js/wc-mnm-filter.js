( function( $ ) {



  /**
   * Main container object.
   */
  function WC_MNM_Filter( $form ) {

    var filter = this;

    this.$form     = $form;
    this.$productWrap = $form.find( '.mnm_child_products' );
    this.$products = this.$productWrap.find( '.mnm_item' );
    this.$filter   = $form.find( '.mnm_filter_button_group' );
    this.taxonomy  = this.$filter.data( 'taxonomy' );

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

      /**
       * Display filter.
       */  
      this.maybe_display();

    };


    /**
     * Remove empty buttons before display.
     */
    this.hide_empty_buttons = function() {

      var count_visible = 0;

      $( '.mnm_filter_button_group button' ).each( function (i) {
        
        var filterValue = $(this).data( 'filter' );

        // Skip the "show all" button.
        if( '*' === filterValue ) {
          return true;
        }

        if( ! filter.$products.filter( filter.classTerm + filterValue ).length ) {
          $(this).hide();
        } else {
          count_visible++;
        }

      });

      filter.$filter.data( 'visible', count_visible );

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
      var $matches = filter.$products.filter( filter.classTerm + filterValue );
      
      if( '*' === filterValue || ! $matches.length ) {
        filter.$products.show();
      } else {
        filter.$products.hide();
        $matches.show();
      }

      // Fix grid layout classes.
      if( filter.$form.hasClass( 'layout_grid' ) ) {

        var columns = WC_MNM_FILTER_PARAMS.columns;

        // Restore first/last loop classes
        filter.$products.removeClass( 'first' ).removeClass( 'last' );

        filter.$products.filter( ':visible' ).each( function (i) {
        if ( i== 0 || (i+1) % columns == 0 ) $(this).addClass( 'first' );
          if ( (i+1) % columns == 0 ) $(this).addClass( 'last' );
        });

      }

    };

    /**
     * Remove empty buttons before display.
     */
    this.maybe_display = function() {

      // Only show "show all" if other buttons exist.
      if( filter.$filter.data( 'visible' ) ) {
        filter.$filter.show();
      }

    }

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



