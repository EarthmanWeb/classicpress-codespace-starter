jQuery(document).ready(function($) {
    $( document ).on( 'click' , '.rst-start-auth' , _startAppAuth );
	$( document ).on( 'click' , '.rst-delete-auth' , _deleteAppAuth );
	$( document ).on( 'click' , '.raddons-upgrade-the-plan' , _addonsUpgradePlan );

    $('#responsive-theme-setting-wl-tab').on('click', function() {
        $('#responsive-theme-setting-wl-tab span, #responsive-theme-setting-wl-tab p').addClass('responsive-theme-setting-active-tab');
        $('#responsive-setting-item-app-connection-tab span, #responsive-setting-item-app-connection-tab p').removeClass('responsive-theme-setting-active-tab');
        $('#responsive-theme-setting-app-connection-section').hide();
        $('#responsive-theme-raddons-setting-wl-section').show();
    });

    $('#responsive-setting-item-app-connection-tab').on('click', function() {
        $('#responsive-theme-setting-app-connection-section').show();
        $('#responsive-theme-raddons-setting-wl-section').hide();
    });

    window.addEventListener("message", function(event) {
        if (event.isTrusted && event.origin === responsiveAddonsGettingStarted.ccAppURL) {
            if( event.data === 'success' ) {
                location.reload();
            } else {
                _storeAuth(event.data);
            }
        }
    });

    $('.responsive-addons-templates-tab').click(function() {
        $('.responsive-addons-settings-tab-content').hide();
        $('.responsive-addons-templates-tab-content').show();
        $('.responsive-addons-templates-tab').addClass('responsive-addons-active-tab');
        $('.responsive-addons-settings-tab').removeClass('responsive-addons-active-tab');
    });
    $('.responsive-addons-settings-tab, .rst-go-to-templates').click(function() {
        $('.responsive-addons-templates-tab-content').hide();
        $('.responsive-addons-settings-tab-content').show();
        $('.responsive-addons-settings-tab').addClass('responsive-addons-active-tab');
        $('.responsive-addons-templates-tab').removeClass('responsive-addons-active-tab');
    });

    function _startAppAuth (event)  {
        event.preventDefault();
        $(this).addClass( 'disable' );
        let is_new_user = this.classList.contains('rst-start-auth-new');
        $_this = '';
        if (is_new_user) {
            $_this = this;
            $('.rst-start-auth-new #loader').css( 'display', 'inline-block' );
        } else {
            $_this = this;
            $('.rst-start-auth-exist #loader').css( 'display', 'inline-block' );
        }
        $.ajax(
            {
                url  : responsiveAddonsGettingStarted.ajaxurl,
                type : 'POST',
                data : {
                    action      : 'cyberchimps_app_start_auth',
                    _ajax_nonce : responsiveAddonsGettingStarted._ajax_nonce,
                    is_new_user : is_new_user,
                },
            }
        )
            .done(
                function ( response ) {

                    // Get the width and height of the viewport
                    let viewportWidth = window.innerWidth;
                    let viewportHeight = window.innerHeight;

                    // Set the dimensions of the popup
                    let popupWidth = 1260;
                    let popupHeight = 740;

                    // Calculate the position to center the popup
                    let leftPosition = (viewportWidth - popupWidth) / 2;
                    let topPosition = (viewportHeight - popupHeight) / 2;
                    // Open the popup window at the calculated position
                    let e = window.open(
                    response.data.url,
                    "_blank",
                    "location=no,width=" + popupWidth + ",height=" + popupHeight + ",left=" + leftPosition + ",top=" + topPosition + ",scrollbars=0"
                    );

                    if (null === e) {
                    } else {
                        $($_this).removeClass( 'disable' );
                        $($_this).find('#loader').css( 'display', 'none' );
                        e.focus();
                    }
                }
            );
          
    }

   function _storeAuth(data) {
        $.ajax({
            type: 'POST',
            url: responsiveAddonsGettingStarted.ajaxurl,
            data: {
                action: 'cyberchimps_app_store_auth',
                _ajax_nonce : responsiveAddonsGettingStarted._ajax_nonce,
                response: data.response,
                origin: data.origin,
            },
            success: function(response) {
                location.reload();
            },
            error: function(error) {
            }
        });

    }

    function _deleteAppAuth() {
        $(this).addClass( 'disable' );
        $('#loader').css( 'display', 'inline-block' );
        $.ajax(
            {
                url  : responsiveAddonsGettingStarted.ajaxurl,
                type : 'POST',
                data : {
                    action      : 'cyberchimps_app_delete_auth',
                    _ajax_nonce : responsiveAddonsGettingStarted._ajax_nonce,
                },
            }
        ).done(
            function ( response ) {
                $(this).removeClass( 'disable' );
                $('#loader').css( 'display', 'none' );
                location.reload();
            }
        );
    }

    function _addonsUpgradePlan (event)  {
        event.preventDefault();
        $.ajax(
            {
                url  : responsiveAddonsGettingStarted.ajaxurl,
                type : 'POST',
                data : {
                    action      : 'cyberchimps_app_upgrade_user_plan',
                    _ajax_nonce : responsiveAddonsGettingStarted._ajax_nonce,
                },
            }
        )
            .done(
                function ( response ) {

                    // Get the width and height of the viewport
                    let viewportWidth = window.innerWidth;
                    let viewportHeight = window.innerHeight;

                    // Set the dimensions of the popup
                    let popupWidth = 1260;
                    let popupHeight = 740;

                    // Calculate the position to center the popup
                    let leftPosition = (viewportWidth - popupWidth) / 2;
                    let topPosition = (viewportHeight - popupHeight) / 2;
                    // Open the popup window at the calculated position
                    let e = window.open(
                    response.data.url,
                    "_blank",
                    "location=no,width=" + popupWidth + ",height=" + popupHeight + ",left=" + leftPosition + ",top=" + topPosition + ",scrollbars=0"
                    );
                    if( null == e ) {
                        console.log( 'error while opening the popup window' );
                    }
                }
            );
    }
});
