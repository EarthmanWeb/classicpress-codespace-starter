/**
 * Update Typography Customizer settings live.
 *
 * @version 1.0.0
 */
// phpcs:ignoreFile
( function( $ ) {

	// Declare vars
	var api = wp.customize;

	/******** TYPOGRAPHY OPTIONS LOOP *********/
    if ( responsive.isProGreater && ! responsive.isThemeGreater ) {
        // console.log('log from pro');
        api( "page_title_typography[font-family]", function( $swipe ) {
            $swipe.bind( function( pair ) {
                if ( pair ) {
                    /** @type {string} */
                    var fontName = pair.split(",")[0];
                    fontName = fontName.replace(/'/g, '');
                    var idfirst = ( fontName.trim().toLowerCase().replace( " ", "-" ), "customizer-typography-page_title-font-family" );
                    var fontSize = fontName.replace( " ", "%20" );
                    fontSize = fontSize.replace( ",", "%2C" );
                    /** @type {string} */
                    fontSize = responsive.googleFontsUrl + "/css?family=" + fontName + ":" + responsive.googleFontsWeight;
                    if ( $( "#" + idfirst ).length ) {
                        $( "#" + idfirst ).attr( "href", fontSize );
                    } else {
                        $( "head" ).append( '<link id="' + idfirst + '" rel="stylesheet" type="text/css" href="' + fontSize + '">' );
                    }
                }
                jQuery( 'style.customizer-typography-page_title-font-family' ).remove();
                jQuery( 'head' ).append(
                    '<style class="customizer-typography-page_title-font-family">'
                    + '.page .post-title, #main-blog h1 { font-family:' + pair +' }'
                    + '</style>'
                );
    
            } );
        } ), api( "page_title_typography[font-weight]", function( $swipe ) {
            $swipe.bind( function( dataAndEvents ) {
                jQuery( 'style.customizer-typography-page_title-font-weight' ).remove();
                jQuery( 'head' ).append(
                    '<style class="customizer-typography-page_title-font-weight">'
                    + '.page .post-title, #main-blog h1{ font-weight:' + dataAndEvents +';}'
                    + '</style>'
                );
    
            } );
        } ), api( "page_title_typography[font-style]", function( $swipe ) {
            $swipe.bind( function( dataAndEvents ) {
                jQuery( 'style.customizer-typography-page_title-font-style' ).remove();
                jQuery( 'head' ).append(
                    '<style class="customizer-typography-page_title-font-style">'
                    + '.page .post-title, #main-blog h1{ font-style:' + dataAndEvents +';}'
                    + '</style>'
                );
    
            } );
        } ), api( "page_title_typography[font-size]", function( $swipe ) {
            $swipe.bind( function( dataAndEvents ) {
                jQuery( 'style.customizer-typography-page_title-font-size' ).remove();
                jQuery( 'head' ).append(
                    '<style class="customizer-typography-page_title-font-size">'
                    + '.page .post-title, #main-blog h1{ font-size:' + dataAndEvents +';}'
                    + '@media (max-width: 768px){.page .post-title, #main-blog h1{ font-size:' + api( "page_title_tablet_typography[font-size]" ).get() +';}}'
                    + '@media (max-width: 480px){.page .post-title, #main-blog h1{ font-size:' + api( "page_title_mobile_typography[font-size]" ).get() +';}}'
                    + '</style>'
                );
    
            } );
        } ), api( "page_title_tablet_typography[font-size]", function( $swipe ) {
            $swipe.bind( function( dataAndEvents ) {
                jQuery( 'style.customizer-typography-page_title-tablet-font-size' ).remove();
                jQuery( 'head' ).append(
                    '<style class="customizer-typography-page_title-tablet-font-size">'
                    + '@media (max-width: 768px){.page .post-title, #main-blog h1{ font-size:' + dataAndEvents +';}}'
                    + '@media (max-width: 480px){.page .post-title, #main-blog h1{ font-size:' + api( "page_title_mobile_typography[font-size]" ).get() +';}}'
                    + '</style>'
                );
    
            } );
        } ), api( "page_title_mobile_typography[font-size]", function( $swipe ) {
            $swipe.bind( function( dataAndEvents ) {
                jQuery( 'style.customizer-typography-page_title-mobile-font-size' ).remove();
                jQuery( 'head' ).append(
                    '<style class="customizer-typography-page_title-mobile-font-size">'
                    + '@media (max-width: 480px){.page .post-title, #main-blog h1{ font-size:' + dataAndEvents +';}}'
                    + '</style>'
                );
    
            } );
        } ), api( "page_title_typography[color]", function( $swipe ) {
            $swipe.bind( function( dataAndEvents ) {
                jQuery( 'style.customizer-typography-page_title-color' ).remove();
                jQuery( 'head' ).append(
                    '<style class="customizer-typography-page_title-color">'
                    + '.page .post-title, #main-blog h1{ color:' + dataAndEvents +';}'
                    + '</style>'
                );
    
            } );
        } ), api( "page_title_typography[line-height]", function( $swipe ) {
            $swipe.bind( function( dataAndEvents ) {
                jQuery( 'style.customizer-typography-page_title-line-height' ).remove();
                jQuery( 'head' ).append(
                    '<style class="customizer-typography-page_title-line-height">'
                    + '.page .post-title, #main-blog h1{ line-height:' + dataAndEvents +';}'
                    + '</style>'
                );
    
            } );
        } ), api( "page_title_tablet_typography[line-height]", function( $swipe ) {
            $swipe.bind( function( dataAndEvents ) {
                jQuery( 'style.customizer-typography-page_title-tablet-line-height' ).remove();
                jQuery( 'head' ).append(
                    '<style class="customizer-typography-page_title-tablet-line-height">'
                    + '@media (max-width: 768px){.page .post-title, #main-blog h1{ line-height:' + dataAndEvents +';}}'
                    + '</style>'
                );
    
            } );
        } ), api( "page_title_mobile_typography[line-height]", function( $swipe ) {
            $swipe.bind( function( dataAndEvents ) {
                jQuery( 'style.customizer-typography-page_title-mobile-line-height' ).remove();
                jQuery( 'head' ).append(
                    '<style class="customizer-typography-page_title-mobile-line-height">'
                    + '@media (max-width: 480px){.page .post-title, #main-blog h1{ line-height:' + dataAndEvents +';}}'
                    + '</style>'
                );
    
            } );
        } ), api( "page_title_typography[letter-spacing]", function( $swipe ) {
            $swipe.bind( function( dataAndEvents ) {
                jQuery( 'style.customizer-typography-page_title-letter-spacing' ).remove();
                jQuery( 'head' ).append(
                    '<style class="customizer-typography-page_title-letter-spacing">'
                    + '.page .post-title, #main-blog h1{ letter-spacing:' + dataAndEvents +'px;}'
                    + '</style>'
                );
    
            } );
        } ), api( "page_title_tablet_typography[letter-spacing]", function( $swipe ) {
            $swipe.bind( function( dataAndEvents ) {
                jQuery( 'style.customizer-typography-page_title-tablet-letter-spacing' ).remove();
                jQuery( 'head' ).append(
                    '<style class="customizer-typography-page_title-tablet-letter-spacing">'
                    + '@media (max-width: 768px){.page .post-title, #main-blog h1{ letter-spacing:' + dataAndEvents +'px;}}'
                    + '</style>'
                );
    
            } );
        } ), api( "page_title_mobile_typography[letter-spacing]", function( $swipe ) {
            $swipe.bind( function( dataAndEvents ) {
                jQuery( 'style.customizer-typography-page_title-mobile-letter-spacing' ).remove();
                jQuery( 'head' ).append(
                    '<style class="customizer-typography-page_title-mobile-letter-spacing">'
                    + '@media (max-width: 480px){.page .post-title, #main-blog h1{ letter-spacing:' + dataAndEvents +'px;}}'
                    + '</style>'
                );
    
            } );
        } ), api( "page_title_typography[text-transform]", function( $swipe ) {
            $swipe.bind( function( dataAndEvents ) {
                jQuery( 'style.customizer-typography-page_title-text-transform' ).remove();
                jQuery( 'head' ).append(
                    '<style class="customizer-typography-page_title-text-transform">'
                    + '.page .post-title, #main-blog h1{ text-transform:' + dataAndEvents +';}'
                    + '</style>'
                );
    
            } );
        } )
    }
    api( "blog_entry_title_typography[font-family]", function( $swipe ) {
        $swipe.bind( function( pair ) {
            if ( pair ) {
                /** @type {string} */
                var fontName = pair.split(",")[0];
                fontName = fontName.replace(/'/g, '');
                var idfirst = ( fontName.trim().toLowerCase().replace( " ", "-" ), "customizer-typography-blog_entry_title_typography-font-family" );
                var fontSize = fontName.replace( " ", "%20" );
                fontSize = fontSize.replace( ",", "%2C" );
                /** @type {string} */
                fontSize = responsive.googleFontsUrl + "/css?family=" + fontName + ":" + responsive.googleFontsWeight;
                if ( $( "#" + idfirst ).length ) {
                    $( "#" + idfirst ).attr( "href", fontSize );
                } else {
                    $( "head" ).append( '<link id="' + idfirst + '" rel="stylesheet" type="text/css" href="' + fontSize + '">' );
                }
            }
            jQuery( 'style.customizer-typography-blog_entry_title-font-family' ).remove();
            jQuery( 'head' ).append(
                '<style class="customizer-typography-blog_entry_title-font-family">'
                + '#primary .post .post-entry .entry-title a, .wp-block-latest-posts li a { font-family:' + pair +' }'
                + '</style>'
            );

        } );
    } ), api( "blog_entry_title_typography[font-weight]", function( $swipe ) {
        $swipe.bind( function( dataAndEvents ) {
            jQuery( 'style.customizer-typography-blog_entry_title-font-weight' ).remove();
            jQuery( 'head' ).append(
                '<style class="customizer-typography-blog_entry_title-font-weight">'
                + '#primary .post .post-entry .entry-title a, .wp-block-latest-posts li a{ font-weight:' + dataAndEvents +';}'
                + '</style>'
            );

        } );
    } ), api( "blog_entry_title_typography[font-style]", function( $swipe ) {
        $swipe.bind( function( dataAndEvents ) {
            jQuery( 'style.customizer-typography-blog_entry_title-font-style' ).remove();
            jQuery( 'head' ).append(
                '<style class="customizer-typography-blog_entry_title-font-style">'
                + '#primary .post .post-entry .entry-title a, .wp-block-latest-posts li a{ font-style:' + dataAndEvents +';}'
                + '</style>'
            );

        } );
    } ), api( "blog_entry_title_typography[font-size]", function( $swipe ) {
        $swipe.bind( function( dataAndEvents ) {
            jQuery( 'style.customizer-typography-blog_entry_title-font-size' ).remove();
            jQuery( 'head' ).append(
                '<style class="customizer-typography-blog_entry_title-font-size">'
                + '#primary .post .post-entry .entry-title a, .wp-block-latest-posts li a{ font-size:' + dataAndEvents +';}'
                + '@media (max-width: 768px){#primary .post .post-entry .entry-title a, .wp-block-latest-posts li a{ font-size:' + api( "blog_entry_title_tablet_typography[font-size]" ).get() +';}}'
                + '@media (max-width: 480px){#primary .post .post-entry .entry-title a, .wp-block-latest-posts li a{ font-size:' + api( "blog_entry_title_mobile_typography[font-size]" ).get() +';}}'
                + '</style>'
            );

        } );
    } ), api( "blog_entry_title_tablet_typography[font-size]", function( $swipe ) {
        $swipe.bind( function( dataAndEvents ) {
            jQuery( 'style.customizer-typography-blog_entry_title-tablet-font-size' ).remove();
            jQuery( 'head' ).append(
                '<style class="customizer-typography-blog_entry_title-tablet-font-size">'
                + '@media (max-width: 768px){#primary .post .post-entry .entry-title a, .wp-block-latest-posts li a{ font-size:' + dataAndEvents +';}}'
                + '@media (max-width: 480px){#primary .post .post-entry .entry-title a, .wp-block-latest-posts li a{ font-size:' + api( "blog_entry_title_mobile_typography[font-size]" ).get() +';}}'
                + '</style>'
            );

        } );
    } ), api( "blog_entry_title_mobile_typography[font-size]", function( $swipe ) {
        $swipe.bind( function( dataAndEvents ) {
            jQuery( 'style.customizer-typography-blog_entry_title-mobile-font-size' ).remove();
            jQuery( 'head' ).append(
                '<style class="customizer-typography-blog_entry_title-mobile-font-size">'
                + '@media (max-width: 480px){#primary .post .post-entry .entry-title a, .wp-block-latest-posts li a{ font-size:' + dataAndEvents +';}}'
                + '</style>'
            );

        } );
    } ), api( "blog_entry_title_typography[color]", function( $swipe ) {
        $swipe.bind( function( dataAndEvents ) {
            jQuery( 'style.customizer-typography-blog_entry_title-color' ).remove();
            jQuery( 'head' ).append(
                '<style class="customizer-typography-blog_entry_title-color">'
                + '#primary .post .post-entry .entry-title a, .wp-block-latest-posts li a{ color:' + dataAndEvents +';}'
                + '</style>'
            );

        } );
    } ), api( "blog_entry_title_typography[line-height]", function( $swipe ) {
        $swipe.bind( function( dataAndEvents ) {
            jQuery( 'style.customizer-typography-blog_entry_title-line-height' ).remove();
            jQuery( 'head' ).append(
                '<style class="customizer-typography-blog_entry_title-line-height">'
                + '#primary .post .post-entry .entry-title a, .wp-block-latest-posts li a{ line-height:' + dataAndEvents +';}'
                + '</style>'
            );

        } );
    } ), api( "blog_entry_title_tablet_typography[line-height]", function( $swipe ) {
        $swipe.bind( function( dataAndEvents ) {
            jQuery( 'style.customizer-typography-blog_entry_title-tablet-line-height' ).remove();
            jQuery( 'head' ).append(
                '<style class="customizer-typography-blog_entry_title-tablet-line-height">'
                + '@media (max-width: 768px){#primary .post .post-entry .entry-title a, .wp-block-latest-posts li a{ line-height:' + dataAndEvents +';}}'
                + '</style>'
            );

        } );
    } ), api( "blog_entry_title_mobile_typography[line-height]", function( $swipe ) {
        $swipe.bind( function( dataAndEvents ) {
            jQuery( 'style.customizer-typography-blog_entry_title-mobile-line-height' ).remove();
            jQuery( 'head' ).append(
                '<style class="customizer-typography-blog_entry_title-mobile-line-height">'
                + '@media (max-width: 480px){#primary .post .post-entry .entry-title a, .wp-block-latest-posts li a{ line-height:' + dataAndEvents +';}}'
                + '</style>'
            );

        } );
    } ), api( "blog_entry_title_typography[letter-spacing]", function( $swipe ) {
        $swipe.bind( function( dataAndEvents ) {
            jQuery( 'style.customizer-typography-blog_entry_title-letter-spacing' ).remove();
            jQuery( 'head' ).append(
                '<style class="customizer-typography-blog_entry_title-letter-spacing">'
                + '#primary .post .post-entry .entry-title a, .wp-block-latest-posts li a{ letter-spacing:' + dataAndEvents +'px;}'
                + '</style>'
            );

        } );
    } ), api( "blog_entry_title_tablet_typography[letter-spacing]", function( $swipe ) {
        $swipe.bind( function( dataAndEvents ) {
            jQuery( 'style.customizer-typography-blog_entry_title-tablet-letter-spacing' ).remove();
            jQuery( 'head' ).append(
                '<style class="customizer-typography-blog_entry_title-tablet-letter-spacing">'
                + '@media (max-width: 768px){#primary .post .post-entry .entry-title a, .wp-block-latest-posts li a{ letter-spacing:' + dataAndEvents +'px;}}'
                + '</style>'
            );

        } );
    } ), api( "blog_entry_title_mobile_typography[letter-spacing]", function( $swipe ) {
        $swipe.bind( function( dataAndEvents ) {
            jQuery( 'style.customizer-typography-blog_entry_title-mobile-letter-spacing' ).remove();
            jQuery( 'head' ).append(
                '<style class="customizer-typography-blog_entry_title-mobile-letter-spacing">'
                + '@media (max-width: 480px){#primary .post .post-entry .entry-title a, .wp-block-latest-posts li a{ letter-spacing:' + dataAndEvents +'px;}}'
                + '</style>'
            );

        } );
    } ), api( "blog_entry_title_typography[text-transform]", function( $swipe ) {
        $swipe.bind( function( dataAndEvents ) {
            jQuery( 'style.customizer-typography-blog_entry_title-text-transform' ).remove();
            jQuery( 'head' ).append(
                '<style class="customizer-typography-blog_entry_title-text-transform">'
                + '#primary .post .post-entry .entry-title a, .wp-block-latest-posts li a{ text-transform:' + dataAndEvents +';}'
                + '</style>'
            );

        } );
    } ),api( "blog_post_title_typography[font-family]", function( $swipe ) {
        $swipe.bind( function( pair ) {
            if ( pair ) {
                /** @type {string} */
                var fontName = pair.split(",")[0];
                fontName = fontName.replace(/'/g, '');
                var idfirst = ( fontName.trim().toLowerCase().replace( " ", "-" ), "customizer-typography-blog_post_title_typography-font-family" );
                var fontSize = fontName.replace( " ", "%20" );
                fontSize = fontSize.replace( ",", "%2C" );
                /** @type {string} */
                fontSize = responsive.googleFontsUrl + "/css?family=" + fontName + ":" + responsive.googleFontsWeight;
                if ( $( "#" + idfirst ).length ) {
                    $( "#" + idfirst ).attr( "href", fontSize );
                } else {
                    $( "head" ).append( '<link id="' + idfirst + '" rel="stylesheet" type="text/css" href="' + fontSize + '">' );
                }
            }
            jQuery( 'style.customizer-typography-blog_post_title-font-family' ).remove();
            jQuery( 'head' ).append(
                '<style class="customizer-typography-blog_post_title-font-family">'
                + '.single-post #primary .post .post-entry .entry-title { font-family:' + pair +' }'
                + '</style>'
            );
            
        } );
    } ), api( "blog_post_title_typography[font-weight]", function( $swipe ) {
        $swipe.bind( function( dataAndEvents ) {
            jQuery( 'style.customizer-typography-blog_post_title-font-weight' ).remove();
            jQuery( 'head' ).append(
                '<style class="customizer-typography-blog_post_title-font-weight">'
                + '.single-post #primary .post .post-entry .entry-title{ font-weight:' + dataAndEvents +';}'
                + '</style>'
            );

        } );
    } ), api( "blog_post_title_typography[font-style]", function( $swipe ) {
        $swipe.bind( function( dataAndEvents ) {
            jQuery( 'style.customizer-typography-blog_post_title-font-style' ).remove();
            jQuery( 'head' ).append(
                '<style class="customizer-typography-blog_post_title-font-style">'
                + '.single-post #primary .post .post-entry .entry-title{ font-style:' + dataAndEvents +';}'
                + '</style>'
            );

        } );
    } ), api( "blog_post_title_typography[font-size]", function( $swipe ) {
        $swipe.bind( function( dataAndEvents ) {
            jQuery( 'style.customizer-typography-blog_post_title-font-size' ).remove();
            jQuery( 'head' ).append(
                '<style class="customizer-typography-blog_post_title-font-size">'
                + '.single-post #primary .post .post-entry .entry-title{ font-size:' + dataAndEvents +';}'
                + '@media (max-width: 768px){.single-post #primary .post .post-entry .entry-title{ font-size:' + api( "blog_post_title_tablet_typography[font-size]" ).get() +';}}'
                + '@media (max-width: 480px){.single-post #primary .post .post-entry .entry-title{ font-size:' + api( "blog_post_title_mobile_typography[font-size]" ).get() +';}}'
                + '</style>'
            );

        } );
    } ), api( "blog_post_title_tablet_typography[font-size]", function( $swipe ) {
        $swipe.bind( function( dataAndEvents ) {
            jQuery( 'style.customizer-typography-blog_post_title-tablet-font-size' ).remove();
            jQuery( 'head' ).append(
                '<style class="customizer-typography-blog_post_title-tablet-font-size">'
                + '@media (max-width: 768px){.single-post #primary .post .post-entry .entry-title{ font-size:' + dataAndEvents +';}}'
                + '@media (max-width: 480px){.single-post #primary .post .post-entry .entry-title{ font-size:' + api( "blog_post_title_mobile_typography[font-size]" ).get() +';}}'
                + '</style>'
            );

        } );
    } ), api( "blog_post_title_mobile_typography[font-size]", function( $swipe ) {
        $swipe.bind( function( dataAndEvents ) {
            jQuery( 'style.customizer-typography-blog_post_title-mobile-font-size' ).remove();
            jQuery( 'head' ).append(
                '<style class="customizer-typography-blog_post_title-mobile-font-size">'
                + '@media (max-width: 480px){.single-post #primary .post .post-entry .entry-title{ font-size:' + dataAndEvents +';}}'
                + '</style>'
            );

        } );
    } ), api( "blog_post_title_typography[color]", function( $swipe ) {
        $swipe.bind( function( dataAndEvents ) {
            jQuery( 'style.customizer-typography-blog_post_title-color' ).remove();
            jQuery( 'head' ).append(
                '<style class="customizer-typography-blog_post_title-color">'
                + '.single-post #primary .post .post-entry .entry-title{ color:' + dataAndEvents +';}'
                + '</style>'
            );

        } );
    } ), api( "blog_post_title_typography[line-height]", function( $swipe ) {
        $swipe.bind( function( dataAndEvents ) {
            jQuery( 'style.customizer-typography-blog_post_title-line-height' ).remove();
            jQuery( 'head' ).append(
                '<style class="customizer-typography-blog_post_title-line-height">'
                + '.single-post #primary .post .post-entry .entry-title{ line-height:' + dataAndEvents +';}'
                + '</style>'
            );

        } );
    } ), api( "blog_post_title_tablet_typography[line-height]", function( $swipe ) {
        $swipe.bind( function( dataAndEvents ) {
            jQuery( 'style.customizer-typography-blog_post_title-tablet-line-height' ).remove();
            jQuery( 'head' ).append(
                '<style class="customizer-typography-blog_post_title-tablet-line-height">'
                + '@media (max-width: 768px){.single-post #primary .post .post-entry .entry-title{ line-height:' + dataAndEvents +';}}'
                + '</style>'
            );

        } );
    } ), api( "blog_post_title_mobile_typography[line-height]", function( $swipe ) {
        $swipe.bind( function( dataAndEvents ) {
            jQuery( 'style.customizer-typography-blog_post_title-mobile-line-height' ).remove();
            jQuery( 'head' ).append(
                '<style class="customizer-typography-blog_post_title-mobile-line-height">'
                + '@media (max-width: 480px){.single-post #primary .post .post-entry .entry-title{ line-height:' + dataAndEvents +';}}'
                + '</style>'
            );

        } );
    } ), api( "blog_post_title_typography[letter-spacing]", function( $swipe ) {
        $swipe.bind( function( dataAndEvents ) {
            jQuery( 'style.customizer-typography-blog_post_title-letter-spacing' ).remove();
            jQuery( 'head' ).append(
                '<style class="customizer-typography-blog_post_title-letter-spacing">'
                + '.single-post #primary .post .post-entry .entry-title{ letter-spacing:' + dataAndEvents +'px;}'
                + '</style>'
            );

        } );
    } ), api( "blog_post_title_tablet_typography[letter-spacing]", function( $swipe ) {
        $swipe.bind( function( dataAndEvents ) {
            jQuery( 'style.customizer-typography-blog_post_title-tablet-letter-spacing' ).remove();
            jQuery( 'head' ).append(
                '<style class="customizer-typography-blog_post_title-tablet-letter-spacing">'
                + '@media (max-width: 768px){.single-post #primary .post .post-entry .entry-title{ letter-spacing:' + dataAndEvents +'px;}}'
                + '</style>'
            );

        } );
    } ), api( "blog_post_title_mobile_typography[letter-spacing]", function( $swipe ) {
        $swipe.bind( function( dataAndEvents ) {
            jQuery( 'style.customizer-typography-blog_post_title-mobile-letter-spacing' ).remove();
            jQuery( 'head' ).append(
                '<style class="customizer-typography-blog_post_title-mobile-letter-spacing">'
                + '@media (max-width: 480px){.single-post #primary .post .post-entry .entry-title{ letter-spacing:' + dataAndEvents +'px;}}'
                + '</style>'
            );

        } );
    } ), api( "blog_post_title_typography[text-transform]", function( $swipe ) {
        $swipe.bind( function( dataAndEvents ) {
            jQuery( 'style.customizer-typography-blog_post_title-text-transform' ).remove();
            jQuery( 'head' ).append(
                '<style class="customizer-typography-blog_post_title-text-transform">'
                + '.single-post #primary .post .post-entry .entry-title{ text-transform:' + dataAndEvents +';}'
                + '</style>'
            );

        } );
    } ), api( "body_typography[font-size]", function( $swipe ) {
        $swipe.bind( function( newval ) {
            if ( newval === '' ) {
                newval = '16px';
            }
            var bodyFontDesktop = newval.replace( 'px', '' );
            var dateboxDesktopFontMY = bodyFontDesktop - 2.5;
            var dateboxDesktopFontDay = bodyFontDesktop * 2;
            var dateboxDesktopContainerWH = ( bodyFontDesktop * 2 ) + dateboxDesktopFontDay + 20;
            $( '.date-box-month, .date-box-year' ).css( 'font-size', dateboxDesktopFontMY+'px' );
            $( '.date-box-day' ).css( 'font-size', dateboxDesktopFontDay+'px' );
            $( '.responsive-date-box' ).css( 'width', dateboxDesktopContainerWH+'px' );
            $( '.responsive-date-box' ).css( 'height', dateboxDesktopContainerWH+'px' );
        } );
    } ), api( "body_tablet_typography[font-size]", function( $swipe ) {
        $swipe.bind( function( newval ) {
            if ( newval === '' ) {
                newval = '16px';
            }
            var bodyFontTablet = newval.replace( 'px', '' );
            var dateboxTabletFontMY = bodyFontTablet - 2.5;
            var dateboxTabletFontDay = bodyFontTablet * 2;
            var dateboxTabletContainerWH = ( bodyFontTablet * 2 ) + dateboxTabletFontDay + 20;
            $( '.date-box-month, .date-box-year' ).css( 'font-size', dateboxTabletFontMY+'px' );
            $( '.date-box-day' ).css( 'font-size', dateboxTabletFontDay+'px' );
            $( '.responsive-date-box' ).css( 'width', dateboxTabletContainerWH+'px' );
            $( '.responsive-date-box' ).css( 'height', dateboxTabletContainerWH+'px' );
        } );
    } ), api( "body_mobile_typography[font-size]", function( $swipe ) {
        $swipe.bind( function( newval ) {
            if ( newval === '' ) {
                newval = '16px';
            }
            var bodyFontMobile = newval.replace( 'px', '' );
            var dateboxMobileFontMY = bodyFontMobile - 2.5;
            var dateboxMobileFontDay = bodyFontMobile * 2;
            var dateboxMobileContainerWH = ( bodyFontMobile * 2 ) + dateboxMobileFontDay + 20;
            $( '.date-box-month, .date-box-year' ).css( 'font-size', dateboxMobileFontMY+'px' );
            $( '.date-box-day' ).css( 'font-size', dateboxMobileFontDay+'px' );
            $( '.responsive-date-box' ).css( 'width', dateboxMobileContainerWH+'px' );
            $( '.responsive-date-box' ).css( 'height', dateboxMobileContainerWH+'px' );
        } );
    } );

} )( jQuery );
