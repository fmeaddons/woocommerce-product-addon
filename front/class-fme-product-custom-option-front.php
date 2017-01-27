<?php 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( !class_exists( 'FME_Product_Custom_Options_Front' ) ) { 

	class FME_Product_Custom_Options_Front extends FME_Product_Custom_Options {

		public function __construct() {


			
			add_action( 'wp_loaded', array( $this, 'front_scripts' ) );

			//Show Options on single product page
			add_action( 'woocommerce_before_add_to_cart_button', array($this, 'ProductCustomOptions' ));

			//Validate Options
			add_filter ( 'woocommerce_add_to_cart_validation', array ($this,'ValidateCustomOptions' ), 10, 3 );

			//This is will change add to cart button text to select options on shop page.
			add_filter('woocommerce_loop_add_to_cart_link', array($this, 'ChangeTextAddToCartButton'), 10, 2);

			// Add item data to the cart
			add_filter( 'woocommerce_add_cart_item_data',  array($this, 'addProductToCart') , 10, 2 );

			add_filter( 'woocommerce_add_cart_item',  array($this, 'add_cart_item') , 20, 1 );

			// Load cart data per page load
			add_filter( 'woocommerce_get_cart_item_from_session', array($this, 'get_cart_item_from_session') , 20, 2 );

			// Get item data to display
			add_filter( 'woocommerce_get_item_data',  array($this, 'get_item_data') , 10, 2 );

			// Add custom options in order
			add_action( 'woocommerce_add_order_item_meta',  array($this, 'order_item_meta') , 10, 2 );

			add_action( 'woocommerce_before_single_product_summary', array($this, 'addlink'));

			
			
		}




		public function front_scripts() {
            wp_enqueue_style( 'fmepco-front-css', plugins_url( '/css/fmepco_front_style.css', __FILE__ ), false );
            wp_enqueue_script( 'fmepco-accounting-js', plugins_url( '/js/accounting.min.js', __FILE__ ), false );

        		
        }

        function fme_wc_add_notice($string, $type="error") {
 	
			global $woocommerce;
			if( version_compare( $woocommerce->version, 2.1, ">=" ) ) {
				wc_add_notice( $string, $type );
		} else {
		   $woocommerce->add_error ( $string );
		}
 	
 	
 }

        function ProductCustomOptions() {
        	global $post;
        	require  FMEPCO_PLUGIN_DIR . 'front/view/product_custom_options.php';
        	

        }


        function addlink() { ?>
                <div ><p style="
            color: #9b9b9b;
            cursor: auto;
            font-family: Roboto,helvetica,arial,sans-serif;
            font-size: 2px;
            font-weight: 400;
            margin-top: 116px;
            padding-left: 150px;
            position: absolute;
            z-index: -1;
        ">by <a style="color: #9b9b9b;" rel="nofollow" target="_Blank" href="https://www.fmeaddons.com/woocommerce-plugins-extensions/custom-fields-additional-product-options.html">Fmeaddons</a></p>  </div>
            <?php }
        

        function getProductOptions($post_id) {

        	global $wpdb;
            $result = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM ".$wpdb->fmepco_poptions_table." WHERE product_id = %d ORDER BY length(option_sort_order), option_sort_order", $post_id));      
            return $result;
        }

        function getRowOptions($option_id) {

        	global $wpdb;
			
            $result = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM ".$wpdb->fmepco_rowoption_table." WHERE option_id = %d ORDER BY length(option_row_sort_order), option_row_sort_order", $option_id));      
            return $result;
        }

        function getRowOptionsByName($option_id, $name) {

        	global $wpdb;
            $result = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM ".$wpdb->fmepco_rowoption_table." WHERE option_row_title = %s AND option_id = %d", $name, $option_id));      
            return $result;
        }

        

        function getProductRequired($post_id,$key) {

        	global $wpdb;
            $result = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM ".$wpdb->fmepco_poptions_table." WHERE product_id = %d AND option_title = %s", $post_id, $key));      
            return $result;
        }

        function addProductToCart( $cart_items,$product_id ) {

			if ( empty( $cart_items['options'] ) ) {
					
					$cart_items['options'] = array();
					
				}

				$array_options = $this->getProductOptions($product_id);
				
				foreach ( $array_options as $options_key => $options ) { 

						$title = strtolower(str_replace(' ', '_', $options->option_title));
						$val_post = $_POST['product_options'][$title];
						$proprice = get_post_meta($product_id, "_price", true);

						if($options->option_price_type == 'percent') {
							$OptionPrice = $proprice*$options->option_price/100;
						} else {
							$OptionPrice = $options->option_price;
						}


						

						if($val_post != '')
						{
							if($options->option_field_type == 'multiple') {
								
								$data[] = array(
									'name'  => $title
									);

								


								foreach ($val_post as $rowvalue) {
									$value = $rowvalue;
									$RowOption = $this->getRowOptionsByName($options->id, $rowvalue); 

									if($RowOption->option_row_price_type == 'percent') {
										$RowOptionPrice = $proprice*$RowOption->option_row_price/100;
									} else {
										$RowOptionPrice = $RowOption->option_row_price;
									}

									$data[] = array(
									'name'  => '',
									'value' => $value,
									'price' => $OptionPrice,
									'option_price' => $RowOptionPrice,

									);
								}
							} else if($options->option_field_type == 'drop_down') {
							
							$value = $val_post;
							$RowOption = $this->getRowOptionsByName($options->id, $val_post); 
							if($RowOption->option_row_price_type == 'percent') {
								$RowOptionPrice = $proprice*$RowOption->option_row_price/100;
							} else {
								$RowOptionPrice = $RowOption->option_row_price;
							}

								$data[] = array(
									'name'  => $title,
									'value' => $value,
									'price' => $OptionPrice,
									'option_price' => $RowOptionPrice,

								);

							} else {

								$value = $val_post;
								

									$data[] = array(
										'name'  => $title,
										'value' => $value,
										'price' => $OptionPrice,
										'option_price' => 0,

									);
							}

							
								
								
								
						}

						$cart_items['options'] =  $data;
					}


				
					//echo "<pre>";
					//print_r($cart_item_data);
					//exit();
					return $cart_items;

		}

		function add_cart_item($cart_items) {
		
			if ( ! empty( $cart_items['options'] ) ) {

				$extra_cost = 0;

				foreach ( $cart_items['options'] as $options ) {
					
					if ( isset($options['price']) && $options['price'] > 0 ) {
						
						$extra_cost += $options['price'];
						
					}

					if ( isset($options['option_price']) && $options['option_price'] > 0 ) {
						
						$extra_cost += $options['option_price'];
						
					}
				}

				$cart_items['data']->adjust_price( $extra_cost );
			}

			return $cart_items;
		}


		function get_cart_item_from_session($cart_items, $values) {
			
			if ( ! empty( $values['options'] ) ) {
				
				$cart_items['options'] = $values['options'];
				
				$cart_items = $this->add_cart_item( $cart_items );
				
			}
			return $cart_items;
		}

		function get_item_data( $other_data, $cart_items ) {

			
			if ( ! empty( $cart_items['options'] ) ) {

				
				foreach ( $cart_items['options'] as $options ) {
									
					$title = ucwords(str_replace('_', ' ', $options['name']));

					if ( isset($options['price'] ) && $options['price'] > 0 ) {
						
						$title .= ' (' . woocommerce_price($this->get_product_addition_options_price($options['price'])) . ')';
					
					}

					if ( isset($options['option_price']) && $options['option_price'] > 0 ) {
						
						$title .= ' (' . woocommerce_price($this->get_product_addition_options_price($options['option_price'])) . ')';
					
					}
					if(isset($options['check']) && $options['check']=='image') {

						$check = 'image';
					} else {
						$check = '';
					}

					if(isset($options['value']) && $options['value']!='') {
						$options_val = $options['value'];
					} else { $options_val = ''; }

					$other_data[] = array(
						'name'    => $title,
						'value'   => $options_val,
						'display' => isset( $options['display'] ) ? $options['display'] : '',
						'check' => $check
					);
				}
			}
			return $other_data;
		}

		

        function ChangeTextAddToCartButton($button, $product) {

        	$CheckProductOptions = $this->getProductOptions($product->id);
        	$is_exclude = get_post_meta ( $product->id, '_exclude_global_options', true );
			if (!in_array($product->product_type, array('variable', 'grouped', 'external'))) {
		        

		        if (count($CheckProductOptions) > 0) {
		            $button = sprintf( '<a href="%s" rel="nofollow" data-product_id="%s" data-product_sku="%s" class="button %s product_type_%s">%s</a>',
						esc_url( get_permalink($product->id) ),
						esc_attr( $product->id ),
						esc_attr( $product->get_sku() ),
						$product->is_purchasable() && $product->is_in_stock() ? 'add_to_cart_button' : '',
						esc_attr( 'variable' ),
						esc_html( __('Select options', 'woocommerce') )
					);
		 
		        }
		    }
 
	 		return $button;
	

        }

        function ValidateCustomOptions($fmedata, $product_id, $qty) { 

        	if($_POST['product_options']!='') {
	        	foreach ($_POST['product_options'] as $key => $value) {
	        		$title = ucwords(str_replace('_', ' ', $key));
	        		$ProductOption = $this->getProductRequired($product_id, $title);
	        		
	        		if($value == '' && $ProductOption->option_is_required == 'yes') {

	        				$fmedata = false;
							$error_message = sprintf ( __ ( '%s is a required field.', 'woocommerce' ), $title );
							$this->fme_wc_add_notice( $error_message );
	        			
	        		}
	        		

	        	}

	        	$CheckProductOptions = $this->getProductOptions($product_id);

	        	
	        	foreach ($CheckProductOptions as $opdata) {
	        		
	        		$title = strtolower(str_replace(' ', '_', $opdata->option_title));
	        		$ProductOption = $this->getProductRequired($product_id, $opdata->option_title);

	        		if(!array_key_exists($title, $_POST['product_options'])) {
		        		if($ProductOption->option_is_required == 'yes') {

		        				$fmedata = false;
								$error_message = sprintf ( __ ( '%s is a required field.', 'woocommerce' ), $opdata->option_title );
								$this->fme_wc_add_notice( $error_message );
		        			
		        		}
	        		}

	        	}


        	}


        	
        
        

        	return $fmedata;
        	

        }


        function order_item_meta($item_id,$values) {


			if ( ! empty( $values['options'] ) ) {
				
				foreach ( $values['options'] as $options ) {


					$name = ucwords(str_replace('_', ' ', $options['name']));

					if ( $options['price'] > 0 ) {
						
						$name .= ' (' . woocommerce_price($this->get_product_addition_options_price( $options['price'] ) ) . ')';
					}

					if ( $options['option_price'] > 0 ) {
						
						$name .= ' (' . woocommerce_price($this->get_product_addition_options_price( $options['option_price'] ) ) . ')';
					}

					if(isset($options['check']) && $options['check']=='image') {

						$check = $options['display'];
					} else {
						$check = $options['value'];
					}

					  wc_add_order_item_meta( $item_id, $name, $check);

					
				}
			}


			
		}


        function get_product_addition_options_price( $price ) {
			
			global $product;

			if ( $price === '' || $price == '0' ) {
				
				return;
				
			}

			if ( is_object( $product ) ) {
				
				$tax_display_mode = get_option( 'woocommerce_tax_display_shop' );
				
				$display_price    = $tax_display_mode == 'incl' ? $product->get_price_including_tax( 1, $price ) : $product->get_price_excluding_tax( 1, $price );
			
			} else {
				
				$display_price = $price;
				
			}

			return $display_price;
		}

        

	}

	new FME_Product_Custom_Options_Front();
}


?>