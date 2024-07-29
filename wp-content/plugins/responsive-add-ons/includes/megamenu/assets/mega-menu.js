var $ = jQuery.noConflict(),
    $window = $(window);

$(document).on('ready', function () {
    "use strict";
    // Mega menu
    var windowWidth = $(window).width();
    if (windowWidth >= 768) {
        responsiveMegaMenu();
    }
});


function responsiveMegaMenu() {
    "use strict"
    renderMegaMenuWidth();

    if ($(window).width() < 767) {
        $('.megamenu').css({ 'display': 'block' })
        $('#site-navigation .megamenu-parent').find('.megamenu .children,.megamenu .sub-menu').css({ 'width': 'auto' });
    } else {
        $('.megamenu').css({ 'display': 'flex' })
        $("ul.megamenu > li").addClass("focus");
    }
}

function renderMegaMenuWidth() {
    
    let class_prefix = 'responsive-megamenu-width-';

    let $parent = $('#site-navigation .responsive-megamenu-parent')
    $parent.each(function() {
        var idValue     = $(this).attr('id');
        let menuIDClass = '.' + idValue
        $(menuIDClass).hover(function() {
            // Content Width.
            if( $(menuIDClass + '.responsive-megamenu-parent').hasClass(class_prefix + 'content') ) {

                let $siteHeader = $('.site-header > .container > .row'),
                $menuWidth        = $siteHeader.width(),
                $menuPosition     = $siteHeader.offset(),
                $menuItemPosition = $(this).offset(),
                $positionLeft     = $menuItemPosition.left - $menuPosition.left;


                $(this).find('.responsive-megamenu').css({ 'left': '-' + $positionLeft + 'px', 'width': $menuWidth });
                let $megaSubMenuOffset = $(this).find('.responsive-megamenu').offset();

                let $tabWidth = $(this).find('.responsive-megamenu > li').width();

                $(this).find('.responsive-megamenu .children, .responsive-megamenu .sub-menu').offset({ top: $megaSubMenuOffset.top });

                $(this).find('.responsive-megamenu .children, .responsive-megamenu .sub-menu').css({ 'width': $tabWidth });

            }
            // Menu Container.
            if( $(menuIDClass + '.responsive-megamenu-parent').hasClass(class_prefix + 'menu-container') ) {

                let $siteHeader = $('.site-header > .container #header-menu'),
                $menuWidth        = $siteHeader.width(),
                $menuPosition     = $siteHeader.offset(),
                $menuItemPosition = $(this).offset(),
                $positionLeft     = $menuItemPosition.left - $menuPosition.left;

                $(this).find('.responsive-megamenu').css({ 'left': '-' + $positionLeft + 'px', 'width': $menuWidth });

                $(this).find('.responsive-megamenu .children,.responsive-megamenu .sub-menu').css({ 'width': 'auto'});

            }
            // Full Width.
            if( $(menuIDClass + '.responsive-megamenu-parent').hasClass(class_prefix + 'full') ) {

                let $containerRow = $('.site-header > .container > .row')
                let $siteHeader   = $('#masthead.site-header'),
                $menuWidth        = $siteHeader.width(),
                $menuPosition     = $siteHeader.offset(),
                $menuItemPosition = $(this).offset(),
                $positionLeft     = $menuItemPosition.left - $menuPosition.left;

                $(this).find('.responsive-full-megamenu-wrapper').css({ 'left': '-' + $positionLeft + 'px', 'width': $menuWidth });

                let $tabWidth = $(this).find('.responsive-megamenu > li').width();

                $(this).find('.responsive-megamenu.sub-menu').css({ 'position': 'unset' });
                $(this).find('.responsive-megamenu.sub-menu').css({ 'width': $containerRow.width() });
                $(this).find('.responsive-megamenu.sub-menu').css({ 'margin': '0 auto' });
                $(this).find('.responsive-megamenu .children,.responsive-megamenu .sub-menu').css({ 'width': $tabWidth });

            }
            // Full Stretch Width.
            if( $(menuIDClass + '.responsive-megamenu-parent').hasClass(class_prefix + 'full-stretched') ) {

                let $siteHeader = $('#masthead.site-header'),
                $menuWidth        = $siteHeader.width(),
                $menuPosition     = $siteHeader.offset(),
                $menuItemPosition = $(this).offset(),
                $positionLeft     = $menuItemPosition.left - $menuPosition.left;

                $(this).find('.responsive-megamenu').css({ 'left': '-' + $positionLeft + 'px', 'width': $menuWidth });

                let $tabWidth = $(this).find('.responsive-megamenu > li').width();

                $(this).find('.responsive-megamenu .children,.responsive-megamenu .sub-menu').css({ 'width': $tabWidth });

            }
            // Custom Width.
            if( $(menuIDClass + '.responsive-megamenu-parent').hasClass(class_prefix + 'custom') ) {

                let $siteHeader = $('.site-header > .container #header-menu'),
                $menuWidth      = $siteHeader.width()

                let customWidth = $('#site-navigation ' + menuIDClass + '.responsive-megamenu-parent').data('custom-width');
                customWidth     = ( undefined !== customWidth ) ? customWidth : 600;

                let renderedWidth = customWidth - $menuWidth;
                renderedWidth = Math.abs( renderedWidth );

                $(this).find('.responsive-megamenu').css({ 'left': '-' + renderedWidth + 'px', 'width': customWidth });

                let $tabWidth = $(this).find('.responsive-megamenu > li').width();

                $(this).find('.responsive-megamenu .children,.responsive-megamenu .sub-menu').css({ 'width': $tabWidth });

            }
        }, function(){
            if( $(menuIDClass + '.responsive-megamenu-parent').hasClass(class_prefix + 'full') ) {
                $(this).find('.responsive-megamenu.sub-menu').css({ 'position': 'absolute' });
            }
            $(this).find('.responsive-megamenu').css({ 'left': '-99999em' });
        })
    });

}