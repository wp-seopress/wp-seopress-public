<?php

namespace WPGodWpseopress\Handler;

use WPGodWpseopress\Services\GodAbstractService;
use WPGodWpseopress\Helpers\GodTypeDevelopment;
use WPGodWpseopress\Models\HooksInterface;
use WPGodWpseopress\WPGod;

/**
 * 
 * GodAdminNoticeHandler
 * 
 * @author Thomas DENEULIN <contact@wp-god.com>
 * @version 1.0.0
 * @since 1.0.0
 */
class GodAdminNoticeHandler extends GodAbstractService implements HooksInterface {

    public function __construct($services = array(), $helpers = array(), $preventUser = false){

        parent::__construct($services, $helpers);

        $this->preventUser = $preventUser;
        
    }


    public function hooks(){
        if($this->preventUser){
            add_action( 'admin_notices', array( $this, 'preventMonitoring' ) );
            add_action( 'admin_init', array( $this, 'checkForMonitoring' ) );
        }
    }


    public function checkForMonitoring() {



        if ( ! current_user_can( 'manage_options' ) && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {
            return;
        }
        

        if(isset($_GET["wpgod_seopress_action"]) && 
           in_array($_GET["wpgod_seopress_action"], array("opt_in", "opt_out"))){

            $hide_notice = get_option( 'wpgod_seopress_tracking_notice' );

            if( $hide_notice ) {
                return;
            }

            update_option( 'wpgod_seopress_tracking_notice', 1);

            switch ($_GET["wpgod_seopress_action"]) {
                case 'opt_in':
                    update_option("wpgod_seopress_allow_tracking", 1);
                    break;
                
                case "opt_out":
                    update_option("wpgod_seopress_allow_tracking", 0);
                    break;
            }
        }
    }


    public function preventMonitoring(){
        $hide_notice = get_option( 'wpgod_seopress_tracking_notice' );

        if( $hide_notice ) {
            return;
        }

        if( get_option( 'wpgod_seopress_allow_tracking', false ) ) {
            return;
        }

        if( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        if(
            stristr( network_site_url( '/' ), 'dev'       ) !== false ||
            stristr( network_site_url( '/' ), 'localhost' ) !== false ||
            stristr( network_site_url( '/' ), ':8888'     ) !== false 
        ) {
            update_option( 'wpgod_seopress_tracking_notice', '1' );
        } else {
            $optinUrl  = add_query_arg( 'wpgod_seopress_action', "opt_in" );
            $optoutUrl = add_query_arg( 'wpgod_seopress_action', "opt_out" );

            ?>
            <div class="updated">
                <p>
                    Allow to track plugin SEOPress usage ? . No sensitive data is tracked.
                    &nbsp;<a href="<?php echo esc_url( $optinUrl ) ?>" class="button-secondary">Allow</a>
                    &nbsp;<a href="<?php echo esc_url( $optoutUrl ) ?>" class="button-secondary">Do not allow</a>
                </p>
            </div>

            <?php
        }
    }


}









