( function( $ ) {



  /**
   * Main container object.
   */
  function WC_MNM_Filter( container ) {

    var filter = this;

    this.$form     = container.$mnm_form;
    this.$productWrap = this.$form.find( '.mnm_child_products' );
    this.$products = this.$productWrap.find( '.mnm_item' );
    this.$filter   = this.$form.find( '.mnm_filter_button_group' );
    this.$showAll  = this.$filter.find( 'button[data-filter="*"]' );
    this.$buttons  = this.$filter.find( 'button[data-filter!="*"]' );
    this.taxonomy  = this.$filter.data( 'taxonomy' );
    this.$error    = $( '<div style="display:none" class="mnm_filter_error"></div>' );

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

      /**
       * Insert error notice.
       */
      this.$error.html( WC_MNM_FILTER_PARAMS.i18n_no_matches ).hide().insertAfter( this.$filter );

    };


    /**
     * Remove empty buttons before display.
     */
    this.hide_empty_buttons = function() {

      var count_visible = 0;

      $( '.mnm_filter_button_group button' ).each( function () {
        
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

    };


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

      if ( ! WC_MNM_FILTER_PARAMS.multi_terms ) {
        filter.$buttons.removeClass( 'selected' );
      }

      $(this).toggleClass( 'selected' );

      // Get all selected terms.
      var $selected = filter.$buttons.filter( '.selected' );
       
      // If "show all" (or no selection) remove classes and show all products.
      if( '*' === $(this).data( 'filter' ) || $selected.length === 0 ) {

        filter.$buttons.removeClass( 'selected' );
        filter.$showAll.addClass( 'selected' );
        filter.$productWrap.show();
        filter.$products.show();
        filter.$error.hide();

      } else {

        var findClass = [];

        filter.$showAll.removeClass( 'selected' );

        $selected.each( function() {
          findClass.push( filter.classTerm + $(this).data( 'filter' ) );
        });

        var $matches = filter.$products.filter( findClass.join('') );

        if( $matches.length ) {
          filter.$productWrap.show();
          filter.$error.hide();
        } else {
          filter.$productWrap.hide();
          filter.$error.show();
        }

        filter.$products.hide();
        $matches.show();

      }

      // Fix grid layout classes.
      if( filter.$form.hasClass( 'layout_grid' ) ) {

        var columns = WC_MNM_FILTER_PARAMS.columns;

        // Restore first/last loop classes
        filter.$products.removeClass( 'first' ).removeClass( 'last' );

        filter.$products.filter( ':visible' ).each( function (i) {
          if ( 0 === i || 0 === (i+1) % columns ) {
            $(this).addClass( 'first' );
          }
          if ( 0 === (i+1) % columns ) {
            $(this).addClass( 'last' );
          }
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

    };

    // Launch.
    this.initialize();

  } // End WC_MNM_Filter.

  /*-----------------------------------------------------------------*/
  /*  Initialization.                                                */
  /*-----------------------------------------------------------------*/

  $( 'body' ).on( 'wc-mnm-initializing', function( e, container ) {
    new WC_MNM_Filter( container );
  });

} ) ( jQuery );
