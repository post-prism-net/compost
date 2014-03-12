// global debug switch
debugmode = true; 

// debuglog
debuglog = function( log ) {
    if( debugmode && typeof console != 'undefined' ) console.log( log );
}

// requestAnimationFrame polyfill
requestAnimationFramePolyfill = 
      window.requestAnimationFrame ||
      window.webkitRequestAnimationFrame ||
      window.mozRequestAnimationFrame ||
      window.msRequestAnimationFrame ||
      window.oRequestAnimationFrame ||
      function( callback ) { window.setTimeout( callback, 1000/60 ) }

// global vars
config = new Array();

jQuery( function( $ ) {

    // app
    var site = ( function() {

        var init = function() {
            debuglog( 'site.init()' );
            bindEventHandlers();
            win.init();
            tools.init();
            meta.init();
            modal.init();
        }

        var bindEventHandlers = function() {

        }

        // module window
        var win = ( function() {

            var winEl;
            var resizeDelay;
            var state;

            var init = function() {
                debuglog( 'site.win.init()' );
            
                winEl = $( window );
                bindEventHandlers();
            }

            var bindEventHandlers = function() {

                // throttle resize event
                winEl.on( 'resize', function() {
                    
                    if( resizeDelay ) { 
                        clearTimeout( resizeDelay );
                    }

                    resizeDelay = setTimeout( resize, 1000 );
                } );

            }

            var resize = function() {
                debuglog( 'site.win.resize()' );

                var _state = state || false;
                state = Modernizr.mq( config['mediaquery'] );
                
                debuglog( 'state: ' + state );

                if( state != _state ) {
                    window.location.reload();
                }
            }

            return {
                init: function() { init(); }
            }

        } )();


        // module tools
        var tools = ( function() {

            var init = function() {
                debuglog( 'site.tools.init()' );
                bindEventHandlers();
            }

            var bindEventHandlers = function() {

                $( document ).on( 'click', '.tools .share', function( e ) {

                    var url = $( this )
                        .closest( 'li')
                        .find( 'img' )
                        .first()
                        .attr( 'src' );

                    e.preventDefault();
                    modal.nu( 'share link: <br>' + url );

                } )

            }

            return {
                init: function() { init(); }
            }

        } )();

        // module meta
        var meta = ( function() {

            var metaEl;

            var init = function() {
                debuglog( 'site.meta.init()' );
                metaEl = $( '.metalist' );
                bindEventHandlers();
                setHealthBar();
            }

            var bindEventHandlers = function() {


            }

            var setHealthBar = function() { 
                debuglog( 'site.meta.setHealthBar()' );

                metaEl
                    .find( '.health .chart' ).each( function() { 

                        var value = $( this ).attr( 'data-health' );

                        debuglog( value );

                        $( this )
                            .find( '.bar' )
                            .css( {
                                'width': value
                            } );

                    } );

            }

            return {
                init: function() { init(); }
            }

        } )();  


        // module modal 
        var modal = ( function() {

            var modalEl;

            var init = function() {
                debuglog( 'site.modal.init()' );
                modalEl = $( '.messagelist' );
                bindEventHandlers();
            }

            var bindEventHandlers = function() {

                $( document ).on( 'click', '.messagelist li .close',  function( e ) {
                    e.preventDefault();

                    var item = $( this ).closest( 'li' );
                    hideItem( item );

                    setTimeout( function() {
                        removeItem( item );
                    }, 1000 );

                } );

            }

            var buildList = function() {
                debuglog( 'site.modal.buildList()' );

                modalEl = $( '<ul class="messagelist"></ul>' );
                modalEl.appendTo( $( 'body' ) );

            }

            var removeList = function() {
                debuglog( 'site.modal.removeList()' );

                modalEl.remove();
            }

            var addItem = function( message, clss ) {
                debuglog( 'site.modal.addItem( ' + message + ' )' );

                if( !clss ) var clss = '';

                var item = $( '<li class="hidden ' + clss + '"><a href="#" class="close">close</a></li>' );
                item
                    .append( message )
                    .appendTo( modalEl );

                return item;

            }

            var removeItem = function( item ) {
                debuglog( 'site.modal.removeItem()' );

                item.remove();
            } 

            var showItem = function( item ) {
                debuglog( 'site.modal.showItem()' );

                setTimeout( function() {
                    item.removeClass( 'hidden' );
                }, 1 );
            }

            var hideItem = function( item ) {
                debuglog( 'site.modal.hideItem()' );

                setTimeout( function() {
                    item.addClass( 'hidden' );
                }, 1 );
            }

            var nu = function( message, clss ) {

                if( modalEl.length < 1  ) {
                    buildList();
                }

                var item = addItem( message, clss );
                showItem( item );
                               
            }

            var unitTest = function() {
                debuglog( 'site.modal.unitTest()' );



                item = addItem( 'First message' );
                showItem( item );

                setTimeout( function() {

                    item = addItem( 'Second message' );
                    showItem( item );

                }, 2000 );

            }

            return {
                init: function() { init(); },
                buildList: function() { buildList(); },
                removeList: function() { removeList(); },
                addItem: function( item, clss ) { addItem( item, clss ); },
                removeItem: function( item ) { removeItem( item ); },
                showItem: function( item ) { showItem( item ); },
                hideItem: function( item ) { hideItem( item ); },
                nu: function( message, clss ) { nu( message, clss ); },
                unitTest: function() { unitTest(); }
            }

        } )();


        // module 
        var module = ( function() {

            var init = function() {
                debuglog( 'site.module.init()' );
                bindEventHandlers();
            }

            var bindEventHandlers = function() {


            }

            return {
                init: function() { init(); }
            }

        } )();

        return {
            init: function() { init(); }
        }

    } )(); // end of site


    // document ready
    $( document ).ready( function () {

        site.init();

        $( 'html' ).removeClass( 'no-js' );

    } );

} );