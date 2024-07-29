(function($){

    InstallResponsiveTheme = {

        /**
         * Init
         */
        init: function() {
            this._bind();
        },

        /**
         * Binds events for the Responsive Theme Installation.
         *
         * @since 2.0.3
         *
         * @access private
         * @method _bind
         */
        _bind: function()
        {
            $( document ).on( 'click', '.responsive-sites-theme-not-installed', InstallResponsiveTheme._install_and_activate );
            $( document ).on( 'click', '.responsive-sites-theme-installed-but-inactive', InstallResponsiveTheme._activateTheme );
            $( document ).on('wp-theme-install-success' , InstallResponsiveTheme._activateTheme);
            $( document ).on('click', '.responsive-notice .notice-dismiss', InstallResponsiveTheme._dismissNotice );
            $( document ).on('click', '#rst_welcome_banner_close_icon', InstallResponsiveTheme._dismissWelcomeNotice );


        },


        _dismissWelcomeNotice: function(event){
            event.preventDefault();      
            jQuery.ajax({
                url: ajaxurl,
                type: 'post',
                data: {
                    action: 'responsive_ready_sites_welcome_banner_dismiss_notice',
                    '_ajax_nonce' : ResponsiveInstallThemeVars._ajax_nonce,
                },
                success: function(data) {
                    $('#responsive-welcome_banner-section').remove();
                }
            });
        },

        /**
         * Activate Theme
         *
         * @since 2.0.3
         */
        _activateTheme: function( event, response ) {
            event.preventDefault();

            $('#responsive-theme-activation a').addClass('processing');

            if( response ) {
                $('#responsive-theme-activation a').text( ResponsiveInstallThemeVars.installed );
            } else {
                $('#responsive-theme-activation a').text( ResponsiveInstallThemeVars.activating );
            }

            // WordPress adds "Activate" button after waiting for 1000ms. So we will run our activation after that.
            setTimeout( function() {

                $.ajax({
                    url: ResponsiveInstallThemeVars.ajaxurl,
                    type: 'POST',
                    data: {
                        'action' : 'responsive-ready-sites-activate-theme',
                        '_ajax_nonce' : ResponsiveInstallThemeVars._ajax_nonce,
                    },
                })
                    .done(function (result) {
                        if( result.success ) {
                            $('#responsive-theme-activation a').text( ResponsiveInstallThemeVars.activated );

                            setTimeout(function() {
                                location.reload();
                            }, 1000);
                        }

                    });

            }, 3000 );

        },

        /**
         * Install and activate
         *
         * @since 2.0.3
         *
         * @param  {object} event Current event.
         * @return void
         */
        _install_and_activate: function(event ) {
            event.preventDefault();
            var theme_slug = $(this).data('theme-slug') || '';
            var btn = $( event.target );

            if ( btn.hasClass( 'processing' ) ) {
                return;
            }

            btn.text( ResponsiveInstallThemeVars.installing ).addClass('processing');

            if ( wp.updates.shouldRequestFilesystemCredentials && ! wp.updates.ajaxLocked ) {
                wp.updates.requestFilesystemCredentials( event );
            }

            wp.updates.installTheme( {
                slug: theme_slug
            });
        },

        /**
         * Dismiss notice if user has clicked on dismiss
         *
         * @since 2.0.3
         *
         */
        _dismissNotice: function( event ) {
            event.preventDefault();

            var notice_id = $( this ).parents('.responsive-notice').attr( 'id' ) || '';

            if( '' === notice_id ) {
                return;
            }

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action            : 'responsive-notice-dismiss',
                    notice_id         : notice_id,
                    _ajax_nonce       : ResponsiveInstallThemeVars._ajax_nonce,
                },
            });

        },

    };

    /**
     * Initialize
     */
    $(function(){
        InstallResponsiveTheme.init();
    });

})(jQuery);