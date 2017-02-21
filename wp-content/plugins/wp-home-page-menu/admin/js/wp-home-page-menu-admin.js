/**
 * Dismisses plugin notices
 *
 */
jQuery(document).ready(function(){
   jQuery('.notice.is-dismissible.wp-home-page-menu .notice-dismiss').on( 'click', function() {

        jQuery.ajax({
            url: wp_home_page_menu.ajax_url,
            data: {
                action: 'wp_home_page_menu_notice_dismiss'
            }
        });

    });
});