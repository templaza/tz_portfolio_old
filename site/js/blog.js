(function ( $ ) {
    $.fn.tzSlideScroll = function(options){
        // This is the easiest way to have default options.
        var settings = $.extend({
            // These are the defaults.
            rows: '#ss-container > div.ss-row',
            links: '#ss-links > a',
            scollPageSpeed: 2000,
            scollPageEasing: 'easeInOutExpo',
            hasPerspective: false
        }, options );

        var $sidescroll	= (function() {

            // the row elements
            var $rows			= $(settings.rows),
            // we will cache the inviewport rows and the outside viewport rows
                $rowsViewport, $rowsOutViewport,
            // navigation menu links
                $links			= $(settings.links),
            // the window element
                $win			= $(window),
            // we will store the window sizes here
                winSize			= {},
            // used in the scroll setTimeout function
                anim			= false,
            // page scroll speed
                scollPageSpeed	= settings.scollPageSpeed ,
            // page scroll easing
                scollPageEasing = settings.scollPageEasing,
            // perspective?
                hasPerspective	= settings.hasPerspective,

                perspective		= hasPerspective && Modernizr.csstransforms3d,
            // initialize function
                init			= function() {

                    // get window sizes
                    getWinSize();
                    // initialize events
                    initEvents();
                    // define the inviewport selector
                    defineViewport();
                    // gets the elements that match the previous selector
                    setViewportRows();
                    // if perspective add css
                    if( perspective ) {
                        $rows.css({
                            '-webkit-perspective'			: 600,
                            '-webkit-perspective-origin'	: '50% 0%'
                        });
                    }
                    // show the pointers for the inviewport rows
                    $rowsViewport.find('a.ss-circle').addClass('ss-circle-deco');
                    // set positions for each row
                    placeRows();

                },
            // defines a selector that gathers the row elems that are initially visible.
            // the element is visible if its top is less than the window's height.
            // these elements will not be affected when scrolling the page.
                defineViewport	= function() {

                    $.extend( $.expr[':'], {

                        inviewport	: function ( el ) {
                            if ( $(el).offset().top < winSize.height ) {
                                return true;
                            }
                            return false;
                        }

                    });

                },
            // checks which rows are initially visible
                setViewportRows	= function() {

                    $rowsViewport 		= $rows.filter(':inviewport');
                    $rowsOutViewport	= $rows.not( $rowsViewport )

                },
            // get window sizes
                getWinSize		= function() {

                    winSize.width	= $win.width();
                    winSize.height	= $win.height();

                },
            // initialize some events
                initEvents		= function() {

                    // navigation menu links.
                    // scroll to the respective section.
                    $links.on( 'click.Scrolling', function( event ) {

                        // scroll to the element that has id = menu's href
                        $('html, body').stop().animate({
                            scrollTop: $( $(this).attr('href') ).offset().top - 20
                        }, scollPageSpeed, scollPageEasing );

                        return false;

                    });


                    $(window).on({
                        // on window resize we need to redefine which rows are initially visible (this ones we will not animate).
                        'resize.Scrolling' : function( event ) {

                            // get the window sizes again
                            getWinSize();
                            // redefine which rows are initially visible (:inviewport)
                            setViewportRows();
                            // remove pointers for every row
                            $rows.find('a.ss-circle').removeClass('ss-circle-deco');
                            // show inviewport rows and respective pointers
                            $rowsViewport.each( function() {

                                $(this).find('div.ss-left')
                                    .css({ left   : '0%' })
                                    .end()
                                    .find('div.ss-right')
                                    .css({ right  : '0%' })
                                    .end()
                                    .find('a.ss-circle')
                                    .addClass('ss-circle-deco');

                            });

                        },
                        // when scrolling the page change the position of each row
                        'scroll.Scrolling' : function( event ) {

                            // set a timeout to avoid that the
                            // placeRows function gets called on every scroll trigger
                            if( anim ) return false;
                            anim = true;

                            setTimeout( function() {

                                placeRows();
                                anim = false;

                            }, 10 );

                        }
                    });

                },
            // sets the position of the rows (left and right row elements).
            // Both of these elements will start with -50% for the left/right (not visible)
            // and this value should be 0% (final position) when the element is on the
            // center of the window.
                placeRows		= function() {

                    // how much we scrolled so far
                    var winscroll	= $win.scrollTop(),
                    // the y value for the center of the screen
                        winCenter	= winSize.height / 2 + winscroll;

                    // for every row that is not inviewport
                    $rowsOutViewport.each( function(i) {

                        var $row	= $(this),
                        // the left side element
                            $rowL	= $row.find('div.ss-left'),
                        // the right side element
                            $rowR	= $row.find('div.ss-right'),
                        // top value
                            rowT	= $row.offset().top;

                        // hide the row if it is under the viewport
                        if( rowT > winSize.height + winscroll ) {

                            if( perspective ) {

                                $rowL.css({
                                    '-webkit-transform'	: 'translate3d(-75%, 0, 0) rotateY(-90deg) translate3d(-75%, 0, 0)',
                                    'opacity'			: 0
                                });
                                $rowR.css({
                                    '-webkit-transform'	: 'translate3d(75%, 0, 0) rotateY(90deg) translate3d(75%, 0, 0)',
                                    'opacity'			: 0
                                });

                            }
                            else {

                                $rowL.css({ left 		: '-50%' });
                                $rowR.css({ right 		: '-50%' });

                            }

                        }
                        // if not, the row should become visible (0% of left/right) as it gets closer to the center of the screen.
                        else {

                            // row's height
                            var rowH	= $row.height(),
                            // the value on each scrolling step will be proporcional to the distance from the center of the screen to its height
                                factor 	= ( ( ( rowT + rowH / 2 ) - winCenter ) / ( winSize.height / 2 + rowH / 2 ) ),
                            // value for the left / right of each side of the row.
                            // 0% is the limit
                                val		= Math.max( factor * 50, 0 );

                            if( val <= 0 ) {

                                // when 0% is reached show the pointer for that row
                                if( !$row.data('pointer') ) {

                                    $row.data( 'pointer', true );
                                    $row.find('.ss-circle').addClass('ss-circle-deco');

                                }

                            }
                            else {

                                // the pointer should not be shown
                                if( $row.data('pointer') ) {

                                    $row.data( 'pointer', false );
                                    $row.find('.ss-circle').removeClass('ss-circle-deco');

                                }

                            }

                            // set calculated values
                            if( perspective ) {

                                var	t		= Math.max( factor * 75, 0 ),
                                    r		= Math.max( factor * 90, 0 ),
                                    o		= Math.min( Math.abs( factor - 1 ), 1 );

                                $rowL.css({
                                    '-webkit-transform'	: 'translate3d(-' + t + '%, 0, 0) rotateY(-' + r + 'deg) translate3d(-' + t + '%, 0, 0)',
                                    'opacity'			: o
                                });
                                $rowR.css({
                                    '-webkit-transform'	: 'translate3d(' + t + '%, 0, 0) rotateY(' + r + 'deg) translate3d(' + t + '%, 0, 0)',
                                    'opacity'			: o
                                });

                            }
                            else {

                                $rowL.css({ left 	: - val + '%' });
                                $rowR.css({ right 	: - val + '%' });

                            }

                        }

                    });

                };

            return { init : init };

        })();

      $sidescroll.init();
        return this;
    };
}(jQuery));