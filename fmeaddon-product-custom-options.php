<?php 

/*
 * Plugin Name:       Product Custom Options(Free)
 * Plugin URI:        https://www.fmeaddons.com/woocommerce-plugins-extensions/custom-fields-additional-product-options.html
 * Description:       FME Addon Product Custom Options module provide facility to add custom options for the product.
 * Version:           1.0.1
 * Author:            FME Addons
 * Developed By:  	  Raja Usman Mehmood
 * Author URI:        http://fmeaddons.com/
 * Support:	      http://support.fmeaddons.com/
 * Text Domain:       fmepco
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Check if WooCommerce is active
 * if wooCommerce is not active FME Tabs module will not work.
 **/
if ( !in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

	echo 'This plugin required woocommerce installed!';
}

if ( !class_exists( 'FME_Product_Custom_Options' ) ) { 

	class FME_Product_Custom_Options {

		public $module_settings = array();
		public $module_default_settings = array();

		function __construct() {

			$this->module_constants();
			$this->module_tables();

			if ( is_admin() ) {
				require_once( FMEPCO_PLUGIN_DIR . 'admin/class-fme-product-custom-options-admin.php' );
				register_activation_hook( __FILE__, array( $this, 'install_module' ) );
				
			} else {
				require_once( FMEPCO_PLUGIN_DIR . 'front/class-fme-product-custom-option-front.php' );
			}

			
		}

		

		public function module_constants() {
            
            if ( !defined( 'FMEPCO_URL' ) )
                define( 'FMEPCO_URL', plugin_dir_url( __FILE__ ) );

            if ( !defined( 'FMEPCO_BASENAME' ) )
                define( 'FMEPCO_BASENAME', plugin_basename( __FILE__ ) );

            if ( ! defined( 'FMEPCO_PLUGIN_DIR' ) )
                define( 'FMEPCO_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
        }

        public function install_module() {
        	$this->module_tables();
        	$this->create_module_data();
        }

        private function module_tables() {
            
			global $wpdb;
		
			$wpdb->fmepco_temp_table = $wpdb->prefix . 'fmepco_temp_table';
			$wpdb->fmepco_poptions_table = $wpdb->prefix . 'fmepco_poptions_table';
			$wpdb->fmepco_rowoption_table = $wpdb->prefix . 'fmepco_rowoption_table';
		}


		public function create_module_data() {

			//$this->set_module_default_settings();
            $this->create_tables();
            
            
        }



        public function create_tables() {
            
			global $wpdb;
			
			$charset_collate = '';
		
			if ( !empty( $wpdb->charset ) )
				$charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
			if ( !empty( $wpdb->collate ) )
				$charset_collate .= " COLLATE $wpdb->collate";	
				
			if ( $wpdb->get_var( "SHOW TABLES LIKE '$wpdb->fmepco_temp_table'" ) != $wpdb->fmepco_temp_table ) {
				$sql = "CREATE TABLE " . $wpdb->fmepco_temp_table . " (
									 id int(25) NOT NULL auto_increment,
									 field_id varchar(255) NULL,
									 field_type varchar(255) NULL, 
									 field varchar(255) NULL,
									 
									 PRIMARY KEY (id)
									 ) $charset_collate;";
		
			
			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
				dbDelta( $sql );
			}


			if ( $wpdb->get_var( "SHOW TABLES LIKE '$wpdb->fmepco_poptions_table'" ) != $wpdb->fmepco_poptions_table ) {
				$sql1 = "CREATE TABLE " . $wpdb->fmepco_poptions_table . " (
									 id int(25) NOT NULL auto_increment,
									 product_id varchar(255) NOT NULL,
									 option_title varchar(500) NULL,
									 option_field_type varchar(500) NULL,
									 option_is_required varchar(500) NULL,
									 option_sort_order varchar(500) NULL,
									 option_price varchar(500) NULL,
									 option_price_type varchar(500) NULL,
									 option_maxchars varchar(500) NULL,
									 option_allowed_file_extensions varchar(500) NULL,
									 option_type varchar(500) NULL,
									 option_global_productids varchar(500) NULL,
									 
									 PRIMARY KEY (id)
									 ) $charset_collate;";
			
			
			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
				dbDelta( $sql1 );
				$wpdb->query("ALTER TABLE ".$wpdb->fmepco_poptions_table." AUTO_INCREMENT=1001 ");
			}

			if ( $wpdb->get_var( "SHOW TABLES LIKE '$wpdb->fmepco_rowoption_table'" ) != $wpdb->fmepco_rowoption_table ) {
				$sql1 = "CREATE TABLE " . $wpdb->fmepco_rowoption_table . " (
									 id int(25) NOT NULL auto_increment,
									 option_id varchar(255) NOT NULL,
									 option_row_title varchar(500) NULL,
									 option_row_sort_order varchar(500) NULL,
									 option_row_price varchar(500) NULL,
									 option_row_price_type varchar(500) NULL,
									 option_image varchar(500) NULL,
									 option_pro_image varchar(500) NULL,
									 
									 PRIMARY KEY (id)
									 ) $charset_collate;";

		
			
			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
				dbDelta( $sql1 );
				$wpdb->query( $wpdb->prepare( "ALTER TABLE ".$wpdb->fmepco_rowoption_table." AUTO_INCREMENT=1001 ", ARRAY_A ) );
			}


			



		}

		


		public function set_module_default_settings() {
            
			$module_settings = get_option( 'fmera_settings' );
			if ( !$module_settings ) {
                update_option( 'fmera_settings', $this->module_default_settings );
			}
		}

		public function get_module_default_settings() {
            
            $module_default_settings = array (
                'profile_title'  => __( 'Profile Info', 'fmera' ),
                'account_title'  => __( 'Account Info', 'fmera' ),
                
            ); 
            
            return $module_default_settings;
		}

		public function get_module_settings() {
            
            $module_settings = get_option( 'fmera_settings' );

            //print_r($module_settings);

            if ( !$module_settings ) {
                update_option( 'fmera_settings', $this->module_default_settings );
                $module_settings = $this->module_default_settings;
            }

            return $module_settings;
        } 

        


	}

	$fmepco = new FME_Product_Custom_Options();


}

?>
