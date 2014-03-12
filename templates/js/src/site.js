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

            var init = function() {
                debuglog( 'site.modal.init()' );
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

    } )();

    // document ready
    $( document ).ready( function () {

        site.init();

        $( 'html' ).removeClass( 'no-js' );

    } );

} );