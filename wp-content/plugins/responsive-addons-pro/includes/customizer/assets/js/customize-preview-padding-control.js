/**
 * This file makes customizer preview of padding control faster
 */
// phpcs:ignoreFile
( function( $ ) {
    var api = wp.customize;

    function responsive_dynamic_padding(control, selector) {
        var mobile_menu_breakpoint = api( 'responsive_mobile_menu_breakpoint' ).get();
        if( 0 == api( 'responsive_disable_mobile_menu').get()) {
            mobile_menu_breakpoint = 0;
        }

        jQuery( 'style#responsive-'+control+'-padding' ).remove();
        var desktopPadding = 'padding-top:'+ api('responsive_'+control+'_top_padding').get()+'px; '+'padding-bottom:'+ api('responsive_'+control+'_bottom_padding').get()+'px; '+'padding-left:'+ api('responsive_'+control+'_left_padding').get()+'px; '+'padding-right:'+ api('responsive_'+control+'_right_padding').get()+'px;';
        var tabletPadding = 'padding-top:'+ api('responsive_'+control+'_tablet_top_padding').get()+'px; '+'padding-bottom:'+ api('responsive_'+control+'_tablet_bottom_padding').get()+'px; '+'padding-left:'+ api('responsive_'+control+'_tablet_left_padding').get()+'px; '+'padding-right:'+ api('responsive_'+control+'_tablet_right_padding').get()+'px;';
        var mobilePadding = 'padding-top:'+ api('responsive_'+control+'_mobile_top_padding').get()+'px; '+'padding-bottom:'+ api('responsive_'+control+'_mobile_bottom_padding').get()+'px; '+'padding-left:'+ api('responsive_'+control+'_mobile_left_padding').get()+'px; '+'padding-right:'+ api('responsive_'+control+'_mobile_right_padding').get()+'px;';
        jQuery( 'head' ).append(
            '<style id="responsive-'+control+'-padding">'
            + selector + '	{ ' + desktopPadding +' }'
            + '@media (max-width: ' + mobile_menu_breakpoint +'px) {' + selector + '	{ ' + tabletPadding + ' } }'
            + '@media (max-width: 544px) {' + selector + '	{ ' + mobilePadding + ' } }'
            + '</style>'
        );

    }
    // Dynamic width preview with responsive_addons_padding_control
    function responsive_dynamic_width(control, selector) {
        var mobile_menu_breakpoint = api( 'responsive_mobile_menu_breakpoint' ).get();
        if( 0 == api( 'responsive_disable_mobile_menu').get()) {
            mobile_menu_breakpoint = 0;
        }

        jQuery( 'style#responsive-'+control+'-padding' ).remove();
        var desktopPadding = 'border-top-width:'+ api('responsive_'+control+'_top_padding').get()+'px; '+'border-bottom-width:'+ api('responsive_'+control+'_bottom_padding').get()+'px; '+'border-left-width:'+ api('responsive_'+control+'_left_padding').get()+'px; '+'border-right-width:'+ api('responsive_'+control+'_right_padding').get()+'px;';
        var tabletPadding = 'border-top-width:'+ api('responsive_'+control+'_tablet_top_padding').get()+'px; '+'border-bottom-width:'+ api('responsive_'+control+'_tablet_bottom_padding').get()+'px; '+'border-left-width:'+ api('responsive_'+control+'_tablet_left_padding').get()+'px; '+'border-right-width:'+ api('responsive_'+control+'_tablet_right_padding').get()+'px;';
        var mobilePadding = 'border-top-width:'+ api('responsive_'+control+'_mobile_top_padding').get()+'px; '+'border-bottom-width:'+ api('responsive_'+control+'_mobile_bottom_padding').get()+'px; '+'border-left-width:'+ api('responsive_'+control+'_mobile_left_padding').get()+'px; '+'border-right-width:'+ api('responsive_'+control+'_mobile_right_padding').get()+'px;';
        jQuery( 'head' ).append(
            '<style id="responsive-'+control+'-padding">'
            + selector + '	{ ' + desktopPadding +' }'
            + '@media (max-width: ' + mobile_menu_breakpoint +'px) {' + selector + '	{ ' + tabletPadding + ' } }'
            + '@media (max-width: 544px) {' + selector + '	{ ' + mobilePadding + ' } }'
            + '</style>'
        );

    }

    // Sidebar Outside Padding
    api( 'responsive_sidebar_outside_container_top_padding', function( value ) {
        value.bind( function( newval ) {
            responsive_dynamic_padding('sidebar_outside_container', '#secondary.widget-area');
        } );
    } );
    api( 'responsive_sidebar_outside_container_left_padding', function( value ) {
        value.bind( function( newval ) {
            responsive_dynamic_padding('sidebar_outside_container', '#secondary.widget-area');
        } );
    } );
    api( 'responsive_sidebar_outside_container_bottom_padding', function( value ) {
        value.bind( function( newval ) {
            responsive_dynamic_padding('sidebar_outside_container', '#secondary.widget-area');
        } );
    } );
    api( 'responsive_sidebar_outside_container_right_padding', function( value ) {
        value.bind( function( newval ) {
            responsive_dynamic_padding('sidebar_outside_container', '#secondary.widget-area');
        } );
    } );

    api( 'responsive_sidebar_outside_container_tablet_top_padding', function( value ) {
        value.bind( function( newval ) {
            responsive_dynamic_padding('sidebar_outside_container', '#secondary.widget-area');
        } );
    } );

    api( 'responsive_sidebar_outside_container_tablet_bottom_padding', function( value ) {
        value.bind( function( newval ) {
            responsive_dynamic_padding('sidebar_outside_container', '#secondary.widget-area');
        } );
    } );

    api( 'responsive_sidebar_outside_container_tablet_left_padding', function( value ) {
        value.bind( function( newval ) {
            responsive_dynamic_padding('sidebar_outside_container', '#secondary.widget-area');
        } );
    } );

    api( 'responsive_sidebar_outside_container_tablet_right_padding', function( value ) {
        value.bind( function( newval ) {
            responsive_dynamic_padding('sidebar_outside_container', '#secondary.widget-area');
        } );
    } );

    api( 'responsive_sidebar_outside_container_mobile_top_padding', function( value ) {
        value.bind( function( newval ) {
            responsive_dynamic_padding('sidebar_outside_container', '#secondary.widget-area');
        } );
    } );

    api( 'responsive_sidebar_outside_container_mobile_bottom_padding', function( value ) {
        value.bind( function( newval ) {
            responsive_dynamic_padding('sidebar_outside_container', '#secondary.widget-area');
        } );
    } );

    api( 'responsive_sidebar_outside_container_mobile_left_padding', function( value ) {
        value.bind( function( newval ) {
            responsive_dynamic_padding('sidebar_outside_container', '#secondary.widget-area');
        } );
    } );

    api( 'responsive_sidebar_outside_container_mobile_right_padding', function( value ) {
        value.bind( function( newval ) {
            responsive_dynamic_padding('sidebar_outside_container', '#secondary.widget-area');
        } );
    } );

    // Sidebar Inside Padding
    api( 'responsive_sidebar_inside_container_top_padding', function( value ) {
        value.bind( function( newval ) {
            responsive_dynamic_padding('sidebar_inside_container', '#secondary.widget-area .widget-wrapper');
        } );
    } );
    api( 'responsive_sidebar_inside_container_left_padding', function( value ) {
        value.bind( function( newval ) {
            responsive_dynamic_padding('sidebar_inside_container', '#secondary.widget-area .widget-wrapper');
        } );
    } );
    api( 'responsive_sidebar_inside_container_bottom_padding', function( value ) {
        value.bind( function( newval ) {
            responsive_dynamic_padding('sidebar_inside_container', '#secondary.widget-area .widget-wrapper');
        } );
    } );
    api( 'responsive_sidebar_inside_container_right_padding', function( value ) {
        value.bind( function( newval ) {
            responsive_dynamic_padding('sidebar_inside_container', '#secondary.widget-area .widget-wrapper');
        } );
    } );

    api( 'responsive_sidebar_inside_container_tablet_top_padding', function( value ) {
        value.bind( function( newval ) {
            responsive_dynamic_padding('sidebar_inside_container', '#secondary.widget-area .widget-wrapper');
        } );
    } );

    api( 'responsive_sidebar_inside_container_tablet_bottom_padding', function( value ) {
        value.bind( function( newval ) {
            responsive_dynamic_padding('sidebar_inside_container', '#secondary.widget-area .widget-wrapper');
        } );
    } );

    api( 'responsive_sidebar_inside_container_tablet_left_padding', function( value ) {
        value.bind( function( newval ) {
            responsive_dynamic_padding('sidebar_inside_container', '#secondary.widget-area .widget-wrapper');
        } );
    } );

    api( 'responsive_sidebar_inside_container_tablet_right_padding', function( value ) {
        value.bind( function( newval ) {
            responsive_dynamic_padding('sidebar_inside_container', '#secondary.widget-area .widget-wrapper');
        } );
    } );

    api( 'responsive_sidebar_inside_container_mobile_top_padding', function( value ) {
        value.bind( function( newval ) {
            responsive_dynamic_padding('sidebar_inside_container', '#secondary.widget-area .widget-wrapper');
        } );
    } );

    api( 'responsive_sidebar_inside_container_mobile_bottom_padding', function( value ) {
        value.bind( function( newval ) {
            responsive_dynamic_padding('sidebar_inside_container', '#secondary.widget-area .widget-wrapper');
        } );
    } );

    api( 'responsive_sidebar_inside_container_mobile_left_padding', function( value ) {
        value.bind( function( newval ) {
            responsive_dynamic_padding('sidebar_inside_container', '#secondary.widget-area .widget-wrapper');
        } );
    } );

    api( 'responsive_sidebar_inside_container_mobile_right_padding', function( value ) {
        value.bind( function( newval ) {
            responsive_dynamic_padding('sidebar_inside_container', '#secondary.widget-area .widget-wrapper');
        } );
    } );

    // Container Outside Padding
    api( 'responsive_outside_container_top_padding', function( value ) {
        value.bind( function( newval ) {
            responsive_dynamic_padding('outside_container', '.responsive-site-style-content-boxed #primary.content-area, .responsive-site-style-boxed #primary.content-area');
        } );
    } );
    api( 'responsive_outside_container_left_padding', function( value ) {
        value.bind( function( newval ) {
            responsive_dynamic_padding('outside_container', '.responsive-site-style-content-boxed #primary.content-area, .responsive-site-style-boxed #primary.content-area');
        } );
    } );
    api( 'responsive_outside_container_bottom_padding', function( value ) {
        value.bind( function( newval ) {
            responsive_dynamic_padding('outside_container', '.responsive-site-style-content-boxed #primary.content-area, .responsive-site-style-boxed #primary.content-area');
        } );
    } );
    api( 'responsive_outside_container_right_padding', function( value ) {
        value.bind( function( newval ) {
            responsive_dynamic_padding('outside_container', '.responsive-site-style-content-boxed #primary.content-area, .responsive-site-style-boxed #primary.content-area');
        } );
    } );

    api( 'responsive_outside_container_tablet_top_padding', function( value ) {
        value.bind( function( newval ) {
            responsive_dynamic_padding('outside_container', '.responsive-site-style-content-boxed #primary.content-area, .responsive-site-style-boxed #primary.content-area');
        } );
    } );

    api( 'responsive_outside_container_tablet_bottom_padding', function( value ) {
        value.bind( function( newval ) {
            responsive_dynamic_padding('outside_container', '.responsive-site-style-content-boxed #primary.content-area, .responsive-site-style-boxed #primary.content-area');
        } );
    } );

    api( 'responsive_outside_container_tablet_left_padding', function( value ) {
        value.bind( function( newval ) {
            responsive_dynamic_padding('outside_container', '.responsive-site-style-content-boxed #primary.content-area, .responsive-site-style-boxed #primary.content-area');
        } );
    } );

    api( 'responsive_outside_container_tablet_right_padding', function( value ) {
        value.bind( function( newval ) {
            responsive_dynamic_padding('outside_container', '.responsive-site-style-content-boxed #primary.content-area, .responsive-site-style-boxed #primary.content-area');
        } );
    } );

    api( 'responsive_outside_container_mobile_top_padding', function( value ) {
        value.bind( function( newval ) {
            responsive_dynamic_padding('outside_container', '.responsive-site-style-content-boxed #primary.content-area, .responsive-site-style-boxed #primary.content-area');
        } );
    } );

    api( 'responsive_outside_container_mobile_bottom_padding', function( value ) {
        value.bind( function( newval ) {
            responsive_dynamic_padding('outside_container', '.responsive-site-style-content-boxed #primary.content-area, .responsive-site-style-boxed #primary.content-area');
        } );
    } );

    api( 'responsive_outside_container_mobile_left_padding', function( value ) {
        value.bind( function( newval ) {
            responsive_dynamic_padding('outside_container', '.responsive-site-style-content-boxed #primary.content-area, .responsive-site-style-boxed #primary.content-area');
        } );
    } );

    api( 'responsive_outside_container_mobile_right_padding', function( value ) {
        value.bind( function( newval ) {
            responsive_dynamic_padding('outside_container', '.responsive-site-style-content-boxed #primary.content-area, .responsive-site-style-boxed #primary.content-area');
        } );
    } );

    // Container Inside Padding
    api( 'responsive_blog_outside_container_top_padding', function( value ) {
        value.bind( function( newval ) {
            responsive_dynamic_padding('blog_outside_container', '.blog.responsive-site-style-content-boxed #primary.content-area, .blog.responsive-site-style-boxed #primary.content-area, .archive.responsive-site-style-content-boxed #primary.content-area, .archive.responsive-site-style-boxed #primary.content-area');
        } );
    } );
    api( 'responsive_blog_outside_container_left_padding', function( value ) {
        value.bind( function( newval ) {
            responsive_dynamic_padding('blog_outside_container', '.blog.responsive-site-style-content-boxed #primary.content-area, .blog.responsive-site-style-boxed #primary.content-area, .archive.responsive-site-style-content-boxed #primary.content-area, .archive.responsive-site-style-boxed #primary.content-area');
        } );
    } );
    api( 'responsive_blog_outside_container_bottom_padding', function( value ) {
        value.bind( function( newval ) {
            responsive_dynamic_padding('blog_outside_container', '.blog.responsive-site-style-content-boxed #primary.content-area, .blog.responsive-site-style-boxed #primary.content-area, .archive.responsive-site-style-content-boxed #primary.content-area, .archive.responsive-site-style-boxed #primary.content-area');
        } );
    } );
    api( 'responsive_blog_outside_container_right_padding', function( value ) {
        value.bind( function( newval ) {
            responsive_dynamic_padding('blog_outside_container', '.blog.responsive-site-style-content-boxed #primary.content-area, .blog.responsive-site-style-boxed #primary.content-area, .archive.responsive-site-style-content-boxed #primary.content-area, .archive.responsive-site-style-boxed #primary.content-area');
        } );
    } );

    api( 'responsive_blog_outside_container_tablet_top_padding', function( value ) {
        value.bind( function( newval ) {
            responsive_dynamic_padding('blog_outside_container', '.blog.responsive-site-style-content-boxed #primary.content-area, .blog.responsive-site-style-boxed #primary.content-area, .archive.responsive-site-style-content-boxed #primary.content-area, .archive.responsive-site-style-boxed #primary.content-area');
        } );
    } );

    api( 'responsive_blog_outside_container_tablet_bottom_padding', function( value ) {
        value.bind( function( newval ) {
            responsive_dynamic_padding('blog_outside_container', '.blog.responsive-site-style-content-boxed #primary.content-area, .blog.responsive-site-style-boxed #primary.content-area, .archive.responsive-site-style-content-boxed #primary.content-area, .archive.responsive-site-style-boxed #primary.content-area');
        } );
    } );

    api( 'responsive_blog_outside_container_tablet_left_padding', function( value ) {
        value.bind( function( newval ) {
            responsive_dynamic_padding('blog_outside_container', '.blog.responsive-site-style-content-boxed #primary.content-area, .blog.responsive-site-style-boxed #primary.content-area, .archive.responsive-site-style-content-boxed #primary.content-area, .archive.responsive-site-style-boxed #primary.content-area');
        } );
    } );

    api( 'responsive_blog_outside_container_tablet_right_padding', function( value ) {
        value.bind( function( newval ) {
            responsive_dynamic_padding('blog_outside_container', '.blog.responsive-site-style-content-boxed #primary.content-area, .blog.responsive-site-style-boxed #primary.content-area, .archive.responsive-site-style-content-boxed #primary.content-area, .archive.responsive-site-style-boxed #primary.content-area');
        } );
    } );

    api( 'responsive_blog_outside_container_mobile_top_padding', function( value ) {
        value.bind( function( newval ) {
            responsive_dynamic_padding('blog_outside_container', '.blog.responsive-site-style-content-boxed #primary.content-area, .blog.responsive-site-style-boxed #primary.content-area, .archive.responsive-site-style-content-boxed #primary.content-area, .archive.responsive-site-style-boxed #primary.content-area');
        } );
    } );

    api( 'responsive_blog_outside_container_mobile_bottom_padding', function( value ) {
        value.bind( function( newval ) {
            responsive_dynamic_padding('blog_outside_container', '.blog.responsive-site-style-content-boxed #primary.content-area, .blog.responsive-site-style-boxed #primary.content-area, .archive.responsive-site-style-content-boxed #primary.content-area, .archive.responsive-site-style-boxed #primary.content-area');
        } );
    } );

    api( 'responsive_blog_outside_container_mobile_left_padding', function( value ) {
        value.bind( function( newval ) {
            responsive_dynamic_padding('blog_outside_container', '.blog.responsive-site-style-content-boxed #primary.content-area, .blog.responsive-site-style-boxed #primary.content-area, .archive.responsive-site-style-content-boxed #primary.content-area, .archive.responsive-site-style-boxed #primary.content-area');
        } );
    } );

    api( 'responsive_blog_outside_container_mobile_right_padding', function( value ) {
        value.bind( function( newval ) {
            responsive_dynamic_padding('blog_outside_container', '.blog.responsive-site-style-content-boxed #primary.content-area, .blog.responsive-site-style-boxed #primary.content-area, .archive.responsive-site-style-content-boxed #primary.content-area, .archive.responsive-site-style-boxed #primary.content-area');
        } );
    } );

    //Blog Inside Padding
    api( 'responsive_blog_inside_container_top_padding', function( value ) {
        value.bind( function( newval ) {
            responsive_dynamic_padding('blog_inside_container', '.blog.responsive-site-style-content-boxed .site-content .hentry, .blog.responsive-site-style-boxed .site-content .hentry, .archive.responsive-site-style-content-boxed .site-content .hentry, .archive.responsive-site-style-boxed .site-content .hentry');
        } );
    } );
    api( 'responsive_blog_inside_container_left_padding', function( value ) {
        value.bind( function( newval ) {
            responsive_dynamic_padding('blog_inside_container', '.blog.responsive-site-style-content-boxed .site-content .hentry, .blog.responsive-site-style-boxed .site-content .hentry, .archive.responsive-site-style-content-boxed .site-content .hentry, .archive.responsive-site-style-boxed .site-content .hentry');
        } );
    } );
    api( 'responsive_blog_inside_container_bottom_padding', function( value ) {
        value.bind( function( newval ) {
            responsive_dynamic_padding('blog_inside_container', '.blog.responsive-site-style-content-boxed .site-content .hentry, .blog.responsive-site-style-boxed .site-content .hentry, .archive.responsive-site-style-content-boxed .site-content .hentry, .archive.responsive-site-style-boxed .site-content .hentry');
        } );
    } );
    api( 'responsive_blog_inside_container_right_padding', function( value ) {
        value.bind( function( newval ) {
            responsive_dynamic_padding('blog_inside_container', '.blog.responsive-site-style-content-boxed .site-content .hentry, .blog.responsive-site-style-boxed .site-content .hentry, .archive.responsive-site-style-content-boxed .site-content .hentry, .archive.responsive-site-style-boxed .site-content .hentry');
        } );
    } );

    api( 'responsive_blog_inside_container_tablet_top_padding', function( value ) {
        value.bind( function( newval ) {
            responsive_dynamic_padding('blog_inside_container', '.blog.responsive-site-style-content-boxed .site-content .hentry, .blog.responsive-site-style-boxed .site-content .hentry, .archive.responsive-site-style-content-boxed .site-content .hentry, .archive.responsive-site-style-boxed .site-content .hentry');
        } );
    } );

    api( 'responsive_blog_inside_container_tablet_bottom_padding', function( value ) {
        value.bind( function( newval ) {
            responsive_dynamic_padding('blog_inside_container', '.blog.responsive-site-style-content-boxed .site-content .hentry, .blog.responsive-site-style-boxed .site-content .hentry, .archive.responsive-site-style-content-boxed .site-content .hentry, .archive.responsive-site-style-boxed .site-content .hentry');
        } );
    } );

    api( 'responsive_blog_inside_container_tablet_left_padding', function( value ) {
        value.bind( function( newval ) {
            responsive_dynamic_padding('blog_inside_container', '.blog.responsive-site-style-content-boxed .site-content .hentry, .blog.responsive-site-style-boxed .site-content .hentry, .archive.responsive-site-style-content-boxed .site-content .hentry, .archive.responsive-site-style-boxed .site-content .hentry');
        } );
    } );

    api( 'responsive_blog_inside_container_tablet_right_padding', function( value ) {
        value.bind( function( newval ) {
            responsive_dynamic_padding('blog_inside_container', '.blog.responsive-site-style-content-boxed .site-content .hentry, .blog.responsive-site-style-boxed .site-content .hentry, .archive.responsive-site-style-content-boxed .site-content .hentry, .archive.responsive-site-style-boxed .site-content .hentry');
        } );
    } );

    api( 'responsive_blog_inside_container_mobile_top_padding', function( value ) {
        value.bind( function( newval ) {
            responsive_dynamic_padding('blog_inside_container', '.blog.responsive-site-style-content-boxed .site-content .hentry, .blog.responsive-site-style-boxed .site-content .hentry, .archive.responsive-site-style-content-boxed .site-content .hentry, .archive.responsive-site-style-boxed .site-content .hentry');
        } );
    } );

    api( 'responsive_blog_inside_container_mobile_bottom_padding', function( value ) {
        value.bind( function( newval ) {
            responsive_dynamic_padding('blog_inside_container', '.blog.responsive-site-style-content-boxed .site-content .hentry, .blog.responsive-site-style-boxed .site-content .hentry, .archive.responsive-site-style-content-boxed .site-content .hentry, .archive.responsive-site-style-boxed .site-content .hentry');
        } );
    } );

    api( 'responsive_blog_inside_container_mobile_left_padding', function( value ) {
        value.bind( function( newval ) {
            responsive_dynamic_padding('blog_inside_container', '.blog.responsive-site-style-content-boxed .site-content .hentry, .blog.responsive-site-style-boxed .site-content .hentry, .archive.responsive-site-style-content-boxed .site-content .hentry, .archive.responsive-site-style-boxed .site-content .hentry');
        } );
    } );

    api( 'responsive_blog_inside_container_mobile_right_padding', function( value ) {
        value.bind( function( newval ) {
            responsive_dynamic_padding('blog_inside_container', '.blog.responsive-site-style-content-boxed .site-content .hentry, .blog.responsive-site-style-boxed .site-content .hentry, .archive.responsive-site-style-content-boxed .site-content .hentry, .archive.responsive-site-style-boxed .site-content .hentry');
        } );
    } );

    // Single Blog Outside Padding
    api( 'responsive_single_blog_outside_container_top_padding', function( value ) {
        value.bind( function( newval ) {
            responsive_dynamic_padding('single_blog_outside_container', '.single.single-post.responsive-site-style-content-boxed #primary.content-area, .single.single-post.responsive-site-style-boxed #primary.content-area');
        } );
    } );
    api( 'responsive_single_blog_outside_container_left_padding', function( value ) {
        value.bind( function( newval ) {
            responsive_dynamic_padding('single_blog_outside_container', '.single.single-post.responsive-site-style-content-boxed #primary.content-area, .single.single-post.responsive-site-style-boxed #primary.content-area');
        } );
    } );
    api( 'responsive_single_blog_outside_container_bottom_padding', function( value ) {
        value.bind( function( newval ) {
            responsive_dynamic_padding('single_blog_outside_container', '.single.single-post.responsive-site-style-content-boxed #primary.content-area, .single.single-post.responsive-site-style-boxed #primary.content-area');
        } );
    } );
    api( 'responsive_single_blog_outside_container_right_padding', function( value ) {
        value.bind( function( newval ) {
            responsive_dynamic_padding('single_blog_outside_container', '.single.single-post.responsive-site-style-content-boxed #primary.content-area, .single.single-post.responsive-site-style-boxed #primary.content-area');
        } );
    } );

    api( 'responsive_single_blog_outside_container_tablet_top_padding', function( value ) {
        value.bind( function( newval ) {
            responsive_dynamic_padding('single_blog_outside_container', '.single.single-post.responsive-site-style-content-boxed #primary.content-area, .single.single-post.responsive-site-style-boxed #primary.content-area');
        } );
    } );

    api( 'responsive_single_blog_outside_container_tablet_bottom_padding', function( value ) {
        value.bind( function( newval ) {
            responsive_dynamic_padding('single_blog_outside_container', '.single.single-post.responsive-site-style-content-boxed #primary.content-area, .single.single-post.responsive-site-style-boxed #primary.content-area');
        } );
    } );

    api( 'responsive_single_blog_outside_container_tablet_left_padding', function( value ) {
        value.bind( function( newval ) {
            responsive_dynamic_padding('single_blog_outside_container', '.single.single-post.responsive-site-style-content-boxed #primary.content-area, .single.single-post.responsive-site-style-boxed #primary.content-area');
        } );
    } );

    api( 'responsive_single_blog_outside_container_tablet_right_padding', function( value ) {
        value.bind( function( newval ) {
            responsive_dynamic_padding('single_blog_outside_container', '.single.single-post.responsive-site-style-content-boxed #primary.content-area, .single.single-post.responsive-site-style-boxed #primary.content-area');
        } );
    } );

    api( 'responsive_single_blog_outside_container_mobile_top_padding', function( value ) {
        value.bind( function( newval ) {
            responsive_dynamic_padding('single_blog_outside_container', '.single.single-post.responsive-site-style-content-boxed #primary.content-area, .single.single-post.responsive-site-style-boxed #primary.content-area');
        } );
    } );

    api( 'responsive_single_blog_outside_container_mobile_bottom_padding', function( value ) {
        value.bind( function( newval ) {
            responsive_dynamic_padding('single_blog_outside_container', '.single.single-post.responsive-site-style-content-boxed #primary.content-area, .single.single-post.responsive-site-style-boxed #primary.content-area');
        } );
    } );

    api( 'responsive_single_blog_outside_container_mobile_left_padding', function( value ) {
        value.bind( function( newval ) {
            responsive_dynamic_padding('single_blog_outside_container', '.single.single-post.responsive-site-style-content-boxed #primary.content-area, .single.single-post.responsive-site-style-boxed #primary.content-area');
        } );
    } );

    api( 'responsive_single_blog_outside_container_mobile_right_padding', function( value ) {
        value.bind( function( newval ) {
            responsive_dynamic_padding('single_blog_outside_container', '.single.single-post.responsive-site-style-content-boxed #primary.content-area, .single.single-post.responsive-site-style-boxed #primary.content-area');
        } );
    } );

    // Single Blog Inside Padding
    api( 'responsive_single_blog_inside_container_top_padding', function( value ) {
        value.bind( function( newval ) {
            responsive_dynamic_padding('single_blog_inside_container', '.single.single-post.responsive-site-style-content-boxed .site-content .hentry, .single.single-post.responsive-site-style-boxed .site-content .hentry');
        } );
    } );
    api( 'responsive_single_blog_inside_container_left_padding', function( value ) {
        value.bind( function( newval ) {
            responsive_dynamic_padding('single_blog_inside_container', '.single.single-post.responsive-site-style-content-boxed .site-content .hentry, .single.single-post.responsive-site-style-boxed .site-content .hentry');
        } );
    } );
    api( 'responsive_single_blog_inside_container_bottom_padding', function( value ) {
        value.bind( function( newval ) {
            responsive_dynamic_padding('single_blog_inside_container', '.single.single-post.responsive-site-style-content-boxed .site-content .hentry, .single.single-post.responsive-site-style-boxed .site-content .hentry');
        } );
    } );
    api( 'responsive_single_blog_inside_container_right_padding', function( value ) {
        value.bind( function( newval ) {
            responsive_dynamic_padding('single_blog_inside_container', '.single.single-post.responsive-site-style-content-boxed .site-content .hentry, .single.single-post.responsive-site-style-boxed .site-content .hentry');
        } );
    } );

    api( 'responsive_single_blog_inside_container_tablet_top_padding', function( value ) {
        value.bind( function( newval ) {
            responsive_dynamic_padding('single_blog_inside_container', '.single.single-post.responsive-site-style-content-boxed .site-content .hentry, .single.single-post.responsive-site-style-boxed .site-content .hentry');
        } );
    } );

    api( 'responsive_single_blog_inside_container_tablet_bottom_padding', function( value ) {
        value.bind( function( newval ) {
            responsive_dynamic_padding('single_blog_inside_container', '.single.single-post.responsive-site-style-content-boxed .site-content .hentry, .single.single-post.responsive-site-style-boxed .site-content .hentry');
        } );
    } );

    api( 'responsive_single_blog_inside_container_tablet_left_padding', function( value ) {
        value.bind( function( newval ) {
            responsive_dynamic_padding('single_blog_inside_container', '.single.single-post.responsive-site-style-content-boxed .site-content .hentry, .single.single-post.responsive-site-style-boxed .site-content .hentry');
        } );
    } );

    api( 'responsive_single_blog_inside_container_tablet_right_padding', function( value ) {
        value.bind( function( newval ) {
            responsive_dynamic_padding('single_blog_inside_container', '.single.single-post.responsive-site-style-content-boxed .site-content .hentry, .single.single-post.responsive-site-style-boxed .site-content .hentry');
        } );
    } );

    api( 'responsive_single_blog_inside_container_mobile_top_padding', function( value ) {
        value.bind( function( newval ) {
            responsive_dynamic_padding('single_blog_inside_container', '.single.single-post.responsive-site-style-content-boxed .site-content .hentry, .single.single-post.responsive-site-style-boxed .site-content .hentry');
        } );
    } );

    api( 'responsive_single_blog_inside_container_mobile_bottom_padding', function( value ) {
        value.bind( function( newval ) {
            responsive_dynamic_padding('single_blog_inside_container', '.single.single-post.responsive-site-style-content-boxed .site-content .hentry, .single.single-post.responsive-site-style-boxed .site-content .hentry');
        } );
    } );

    api( 'responsive_single_blog_inside_container_mobile_left_padding', function( value ) {
        value.bind( function( newval ) {
            responsive_dynamic_padding('single_blog_inside_container', '.single.single-post.responsive-site-style-content-boxed .site-content .hentry, .single.single-post.responsive-site-style-boxed .site-content .hentry');
        } );
    } );

    api( 'responsive_single_blog_inside_container_mobile_right_padding', function( value ) {
        value.bind( function( newval ) {
            responsive_dynamic_padding('single_blog_inside_container', '.single.single-post.responsive-site-style-content-boxed .site-content .hentry, .single.single-post.responsive-site-style-boxed .site-content .hentry');
        } );
    } );

    // Mobile menu Border Width
    api( 'responsive_mobile_menu_border_mobile_top_padding', function( value ) {
        value.bind( function( newval ) {
            responsive_dynamic_width('mobile_menu_border', '.main-navigation.toggled .menu-toggle');
            responsive_dynamic_width('mobile_menu_border', '.main-navigation .menu-toggle');
        } );
    } );
    api( 'responsive_mobile_menu_border_mobile_bottom_padding', function( value ) {
        value.bind( function( newval ) {
            responsive_dynamic_width('mobile_menu_border', '.main-navigation.toggled .menu-toggle');
            responsive_dynamic_width('mobile_menu_border', '.main-navigation .menu-toggle');
        } );
    } );
    api( 'responsive_mobile_menu_border_mobile_left_padding', function( value ) {
        value.bind( function( newval ) {
            responsive_dynamic_width('mobile_menu_border', '.main-navigation.toggled .menu-toggle');
            responsive_dynamic_width('mobile_menu_border', '.main-navigation .menu-toggle');
        } );
    } );
    api( 'responsive_mobile_menu_border_mobile_right_padding', function( value ) {
        value.bind( function( newval ) {
            responsive_dynamic_width('mobile_menu_border', '.main-navigation.toggled .menu-toggle');
            responsive_dynamic_width('mobile_menu_border', '.main-navigation .menu-toggle');
        } );
    } );  
    
    // Native cart Popup
    // Popup padding.
    api( 'responsive_popup_top_padding', function( value ) {
        value.bind( function( newval ) {
            responsive_dynamic_padding('popup', '#woo-popup-wrap #woo-popup-inner');
        } );
    } ); 
    api( 'responsive_popup_right_padding', function( value ) {
        value.bind( function( newval ) {
            responsive_dynamic_padding('popup', '#woo-popup-wrap #woo-popup-inner');
        } );
    } ); 
    api( 'responsive_popup_bottom_padding', function( value ) {
        value.bind( function( newval ) {
            responsive_dynamic_padding('popup', '#woo-popup-wrap #woo-popup-inner');
        } );
    } ); 
    api( 'responsive_popup_left_padding', function( value ) {
        value.bind( function( newval ) {
            responsive_dynamic_padding('popup', '#woo-popup-wrap #woo-popup-inner');
        } );
    } ); 

    api( 'responsive_popup_tablet_top_padding', function( value ) {
        value.bind( function( newval ) {
            responsive_dynamic_padding('popup', '#woo-popup-wrap #woo-popup-inner');
        } );
    } ); 
    api( 'responsive_popup_tablet_right_padding', function( value ) {
        value.bind( function( newval ) {
            responsive_dynamic_padding('popup', '#woo-popup-wrap #woo-popup-inner');
        } );
    } ); 
    api( 'responsive_popup_tablet_bottom_padding', function( value ) {
        value.bind( function( newval ) {
            responsive_dynamic_padding('popup', '#woo-popup-wrap #woo-popup-inner');
        } );
    } ); 
    api( 'responsive_popup_tablet_left_padding', function( value ) {
        value.bind( function( newval ) {
            responsive_dynamic_padding('popup', '#woo-popup-wrap #woo-popup-inner');
        } );
    } );

    api( 'responsive_popup_mobile_top_padding', function( value ) {
        value.bind( function( newval ) {
            responsive_dynamic_padding('popup', '#woo-popup-wrap #woo-popup-inner');
        } );
    } ); 
    api( 'responsive_popup_mobile_right_padding', function( value ) {
        value.bind( function( newval ) {
            responsive_dynamic_padding('popup', '#woo-popup-wrap #woo-popup-inner');
        } );
    } ); 
    api( 'responsive_popup_mobile_bottom_padding', function( value ) {
        value.bind( function( newval ) {
            responsive_dynamic_padding('popup', '#woo-popup-wrap #woo-popup-inner');
        } );
    } ); 
    api( 'responsive_popup_mobile_left_padding', function( value ) {
        value.bind( function( newval ) {
            responsive_dynamic_padding('popup', '#woo-popup-wrap #woo-popup-inner');
        } );
    } );

    
} )( jQuery );
