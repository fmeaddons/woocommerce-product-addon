<?php 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


if ( !class_exists( 'FME_Product_Custom_Options_Admin' ) ) { 

	class FME_Product_Custom_Options_Admin extends FME_Product_Custom_Options {

		public function __construct() {

			add_action( 'wp_loaded', array( $this, 'admin_init' ) );
			//$this->module_settings = $this->get_module_settings();
			
			add_action( 'add_meta_boxes', array($this, 'fme_product_custom_options_box' ));
			add_action('save_post', array($this, 'fme_save_product_meta'), 1, 2);
			add_action('wp_ajax_addoptionTempData', array($this, 'addoptionTemData'));
			add_action('wp_ajax_deloptionTempData', array($this, 'deloptionTempData'));
			add_action('wp_ajax_addrowTempData', array($this, 'addrowTemData'));
			add_action('wp_ajax_addmultirowTempData', array($this, 'addmultirowTemData'));
			add_action('wp_ajax_delrowTempData', array($this, 'delrowTempData'));

			
			
			
		}

		public function admin_init() {
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );	
		}

		public function admin_scripts() {	
           
        	wp_enqueue_style( 'fmepco-admin-css', plugins_url( '/css/fmepco_style.css', __FILE__ ), false );
        	
        }

        

		

		function fme_product_custom_options_box() {
    		add_meta_box( 'product_custom_optios', 'Product Custom Options', array($this, 'product_custom_options_call'), 'product', 'normal', 'high' );

		}

		function fme_save_product_meta($post_id, $post) { 

			  
			if ( !current_user_can( apply_filters( 'fmepco_capability', 'manage_options' ) ) )
			die( '-1' );



			global $wpdb;
			if(isset($_POST['editpro']) && $_POST['editpro'] == 'yes') {
				$wpdb->query( $wpdb->prepare( "DELETE FROM ".$wpdb->fmepco_poptions_table." WHERE product_id = %d", $post->ID) );

			}
			//echo "<pre>";
			//print_r($_POST['product_option']);exit();
			if(count($_POST['product_option'])!=0) {
			foreach ($_POST['product_option'] as $product_option) {

				if(isset($product_option['option_id']) && $product_option['option_id']!='') {
					$opt_id = $product_option['option_id'];
				} else { $opt_id = 0; }

				if(isset($_POST['editpro']) && $_POST['editpro'] == 'yes') {
					$wpdb->query( $wpdb->prepare( "DELETE FROM ".$wpdb->fmepco_rowoption_table." WHERE option_id = %d", intval($opt_id)) );
				}

				

				if(isset($product_option['option_type']) && $product_option['option_type'] == 'field') {

					if($product_option['option_title'] != '') {
						$wpdb->query($wpdb->prepare( 
			            "
			            INSERT INTO $wpdb->fmepco_poptions_table
			            (product_id,option_title,option_field_type,option_is_required,option_sort_order,option_price,option_price_type,option_maxchars)
			            VALUES (%s,%s,%s,%s,%s,%s,%s,%s)
			            ",
			            $post->ID,
			            sanitize_text_field($product_option['option_title']),
			            sanitize_text_field($product_option['option_type']),
			            sanitize_text_field($product_option['option_is_required']),
			            sanitize_text_field($product_option['option_sort_order']),
			            sanitize_text_field($product_option['text_option_price']),
			            sanitize_text_field($product_option['text_option_price_type']),
			            sanitize_text_field($product_option['text_option_maxchars'])
			            ) );
					}
				} else if(isset($product_option['option_type']) && $product_option['option_type'] == 'area') {

					if(sanitize_text_field($product_option['option_title']) != '') {
						$wpdb->query($wpdb->prepare( 
			            "
			            INSERT INTO $wpdb->fmepco_poptions_table
			            (product_id,option_title,option_field_type,option_is_required,option_sort_order,option_price,option_price_type,option_maxchars)
			            VALUES (%s,%s,%s,%s,%s,%s,%s,%s)
			            ",
			            $post->ID,
			            sanitize_text_field($product_option['option_title']),
			            sanitize_text_field($product_option['option_type']),
			            sanitize_text_field($product_option['option_is_required']),
			            sanitize_text_field($product_option['option_sort_order']),
			            sanitize_text_field($product_option['area_option_price']),
			            sanitize_text_field($product_option['area_option_price_type']),
			            sanitize_text_field( $product_option['area_option_maxchars'])
			            ) );
					}
				} else if(isset($product_option['option_type']) && $product_option['option_type'] == 'drop_down') {

					if(sanitize_text_field($product_option['option_title']) != '') {
						$wpdb->query($wpdb->prepare( 
			            "
			            INSERT INTO $wpdb->fmepco_poptions_table
			            (product_id,option_title,option_field_type,option_is_required,option_sort_order)
			            VALUES (%s,%s,%s,%s,%s)
			            ",
			            $post->ID,
			            sanitize_text_field($product_option['option_title']),
			            sanitize_text_field($product_option['option_type']),
			            sanitize_text_field($product_option['option_is_required']),
			            sanitize_text_field($product_option['option_sort_order'])
			            ));
			            $lastid = $wpdb->insert_id;

						foreach ($product_option['row_value'] as $row_value) {
							
							if(sanitize_text_field($row_value['drop_option_row_title']) != '') {
								$wpdb->query($wpdb->prepare( 
					            "
					            INSERT INTO $wpdb->fmepco_rowoption_table
					            (option_id,option_row_title,option_row_price,option_row_price_type,option_row_sort_order)
					            VALUES (%s,%s,%s,%s,%s)
					            ",
					            $lastid,
					            sanitize_text_field($row_value['drop_option_row_title']),
					            sanitize_text_field($row_value['drop_option_row_price']),
					            sanitize_text_field($row_value['drop_option_row_price_type']),
					            sanitize_text_field($row_value['drop_option_row_sort_order'])
					            ));
							}
							
						}
					}
					

				} else if(isset($product_option['option_type']) && $product_option['option_type'] == 'multiple') {

					if(sanitize_text_field($product_option['option_title']) != '') {
						$wpdb->query($wpdb->prepare( 
			            "
			            INSERT INTO $wpdb->fmepco_poptions_table
			            (product_id,option_title,option_field_type,option_is_required,option_sort_order)
			            VALUES (%s,%s,%s,%s,%s)
			            ",
			            $post->ID,
			            sanitize_text_field($product_option['option_title']),
			            sanitize_text_field($product_option['option_type']),
			            sanitize_text_field($product_option['option_is_required']),
			            sanitize_text_field($product_option['option_sort_order'])
			            ));
			            $lastid = $wpdb->insert_id;

						foreach ($product_option['row_value'] as $row_value) {
							
							if(sanitize_text_field($row_value['multi_option_row_title']) != '') {
								$wpdb->query($wpdb->prepare( 
					            "
					            INSERT INTO $wpdb->fmepco_rowoption_table
					            (option_id,option_row_title,option_row_price,option_row_price_type,option_row_sort_order)
					            VALUES (%s,%s,%s,%s,%s)
					            ",
					            $lastid,
					            sanitize_text_field($row_value['multi_option_row_title']),
					            sanitize_text_field($row_value['multi_option_row_price']),
					            sanitize_text_field($row_value['multi_option_row_price_type']),
					            sanitize_text_field($row_value['multi_option_row_sort_order'])
					            ));
							}
						}
					}

					

				}  

			} 
			
			}
			
	    	
		}

		function product_custom_options_call( $post ) { 

			global $wpdb;
			$wpdb->query("TRUNCATE TABLE ".$wpdb->fmepco_temp_table);

			require  FMEPCO_PLUGIN_DIR . 'admin/view/product_level_form.php';
		}

		function getTempFields($id) { 
			global $wpdb;
            $result = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM ".$wpdb->fmepco_temp_table." WHERE field_type = %s AND  id = %d", 'option', $id));      
            return $result;
		}

		function getrowTempFields($id,$field_id) {
			global $wpdb;

            $result = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM ".$wpdb->fmepco_temp_table." WHERE field_type = %s AND field_id = %d AND id = %d", 'row', $field_id, $id));      
            return $result;
		}

		function getProductOptions($post) { 

			global $wpdb;
			
            $result = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM ".$wpdb->fmepco_poptions_table." WHERE product_id = %d",$post));      
            return $result;
		}

		function getProductOptionRows($option_id) { 

			global $wpdb;
			
            $result = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM ".$wpdb->fmepco_rowoption_table." WHERE option_id = %d",$option_id));      
            return $result;
		}

		function addForm($id) { ?>

			<?php 

		   		$tempField = $this->getTempFields($id);
		   		if(count($tempField)!=0) {
		   		
		   			
		   	?>

			<div class="addFormFields" id="field<?php echo $tempField->id; ?>">
				<input onClick="delFields('<?php echo $tempField->id; ?>')" type="button" class="btnDel button btn-danger button-large" value="<?php echo _e('Delete Option','fmepco'); ?>">
				<div class="topFields">
					<table class="datatable">
						<thead>
						    <tr>
						    	<th class="datath1"><label><b><?php echo _e('Title:','fmepco'); ?></b></label></th>
						    	<th class="datath2"><label><b><?php echo _e('Input Type:','fmepco'); ?></b></label></th>
						    	<th class="datath3"><label><b><?php echo _e('Is Required:','fmepco'); ?></b></label></th>
						    	<th class="datath4"><label><b>Sort Order:</b></label></th>
						    	</tr>
						</thead>
						<tbody>
							<tr>
								<td class="datath1"><input class="inputs" type="text" name="product_option[<?php echo $tempField->id; ?>][option_title]" id="title" /></td>
								<td class="datath2">
						    		<select class="select_type inputs" name="product_option[<?php echo $tempField->id; ?>][option_type]" id="type" onChange="showFields('<?php echo $tempField->id; ?>',this.value)">
						    			<option value=""><?php echo _e('-- Please select --','fmepco'); ?></option>
						    			<optgroup label="Text">
						    				<option value="field"><?php echo _e('Field','fmepco'); ?></option>
						    				<option value="area"><?php echo _e('Area','fmepco'); ?></option>
						    			</optgroup>
						    			
						    			<optgroup label="Select">
						    				<option value="drop_down"><?php echo _e('Drop-down','fmepco'); ?></option>
			            					<option value="multiple"><?php echo _e('Multiple Select','fmepco'); ?></option>
			            				</optgroup>
					            		
			            			</select>
						        </td>
								<td class="datath3">
									<select class="select_is_required inputs" name="product_option[<?php echo $tempField->id; ?>][option_is_required]" id="is_required">
				                		<option value="yes"><?php echo _e('Yes','fmepco'); ?></option>
				                		<option value="no"><?php echo _e('No','fmepco'); ?></option>
				                	</select>
								</td>
								<td class="datath4"><input class="inputs" type="text" name="product_option[<?php echo $tempField->id; ?>][option_sort_order]" id="sort_order<?php echo $tempField->id; ?>" onChange="SortOrderonlyNumber(this.id);" /></td>
							</tr>
							
						</tbody>
					</table>
				</div>

				<div class="bottom_fields">
					<div id="textField<?php echo $tempField->id; ?>" style="display:none;">
						<table class="datatable">
							<thead>
							    <tr>
							    	<th class="datath1"><label><b><?php echo _e('Price:','fmepco'); ?></b></label></th>
							    	<th class="datath2"><label><b><?php echo _e('Price Type:','fmepco'); ?></b></label></th>
							    	<th class="datath3"><label><b><?php echo _e('Max Characters:','fmepco'); ?></b></label></th>
							    </tr>
							</thead>
							<tbody>
								<tr>
									<td class="datath1"><input class="inputs" type="text" name="product_option[<?php echo $tempField->id; ?>][text_option_price]" id="price<?php echo $tempField->id; ?>" onChange="PriceOnly(this.id);" /></td>
									<td class="datath2">
										<select class="select_is_required inputs" name="product_option[<?php echo $tempField->id; ?>][text_option_price_type]" id="fieldprice_type">
					                		<option value="fixed"><?php echo _e('Fixed','fmepco'); ?></option>
					                		<option value="percent"><?php echo _e('Percent','fmepco'); ?></option>
					                	</select>
									</td>
									<td class="datath3"><input class="inputs" type="text" name="product_option[<?php echo $tempField->id; ?>][text_option_maxchars]" id="maxchars<?php echo $tempField->id; ?>" onChange="MaxCharsOnlyNumber(this.id);" /></td>
								</tr>
							</tbody>
						</table>
					</div>
					<div id="textArea<?php echo $tempField->id; ?>" style="display:none;">
						<table class="datatable">
							<thead>
							    <tr>
							    	<th class="datath1"><label><b><?php echo _e('Price:','fmepco'); ?></b></label></th>
							    	<th class="datath2"><label><b><?php echo _e('Price Type:','fmepco'); ?></b></label></th>
							    	<th class="datath3"><label><b><?php echo _e('Max Characters:','fmepco'); ?></b></label></th>
							    </tr>
							</thead>
							<tbody>
								<tr>
									<td class="datath1"><input class="inputs" type="text" name="product_option[<?php echo $tempField->id; ?>][area_option_price]" id="price<?php echo $tempField->id; ?>" onChange="PriceOnly(this.id);" /></td>
									<td class="datath2">
										<select class="select_is_required inputs" name="product_option[<?php echo $tempField->id; ?>][area_option_price_type]" id="areaprice_type">
					                		<option value="fixed"><?php echo _e('Fixed','fmepco'); ?></option>
					                		<option value="percent"><?php echo _e('Percent','fmepco'); ?></option>
					                	</select>
									</td>
									<td class="datath3"><input class="inputs" type="text" name="product_option[<?php echo $tempField->id; ?>][area_option_maxchars]" id="maxchars<?php echo $tempField->id; ?>" onChange="SMaxCharsonlyNumber(this.id);" /></td>
								</tr>
							</tbody>
						</table>
					</div>
					
					<div id="dropdown<?php echo $tempField->id; ?>" style="display:none;">
						<div class="rowfield_success<?php echo $tempField->id; ?>"></div>
						<table class="datatable" id="POITable<?php echo $tempField->id; ?>">
							<thead>
							    <tr>
							    	<th class="datath1"><label><b><?php echo _e('Title:','fmepco'); ?></b></label></th>
							    	<th class="datath2"><label><b><?php echo _e('Price:','fmepco'); ?></b></label></th>
							    	<th class="datath3"><label><b><?php echo _e('Price Type:','fmepco'); ?></b></label></th>
							    	<th class="datath4"><label><b><?php echo _e('Sort Order:','fmepco'); ?></b></label></th>
							    	<th></th>
							    </tr>
							</thead>
							<tbody>

								
								<tr>
									<td class="<?php echo $tempField->id; ?>droprowdata" colspan="4"></td>
								</tr>
							</tbody>
							<tfoot>
								<tr class="addButton">
							   		<td colspan="5"><input onClick="addNewDropRow(<?php echo $tempField->id; ?>)" type="button" id="btnAdd" class="button button-default" value="<?php echo _e('Add New Row','fmepco'); ?>"></td> 
							   	</tr>
							</tfoot>
							<tbody>
								
							</tbody>
						</table>
					</div>
					<div id="multiselect<?php echo $tempField->id; ?>" style="display:none;">
						<div class="rowfield_success<?php echo $tempField->id; ?>"></div>
						<table class="datatable" id="MultiTable<?php echo $tempField->id; ?>">
							<thead>
							    <tr>
							    	<th class="datath1"><label><b><?php echo _e('Title:','fmepco'); ?></b></label></th>
							    	<th class="datath2"><label><b><?php echo _e('Price:','fmepco'); ?></b></label></th>
							    	<th class="datath3"><label><b><?php echo _e('Price Type:','fmepco'); ?></b></label></th>
							    	<th class="datath4"><label><b><?php echo _e('Sort Order:','fmepco'); ?></b></label></th>
							    	<th></th>
							    </tr>
							</thead>
							<tbody>
								
								<tr>
									<td class="<?php echo $tempField->id; ?>multirowdata" colspan="4"></td>
								</tr>
							</tbody>
							<tfoot>
								<tr class="addButton">
							   		<td colspan="5"><input onClick="addNewMultiRow(<?php echo $tempField->id; ?>)" type="button" id="btnAdd" class="button button-default" value="<?php echo _e('Add New Row','fmepco'); ?>"></td> 
							   	</tr>
							</tfoot>
							<tbody>
								
							</tbody>
						</table>
					</div>
					
					
				</div>

			</div>
			<?php }  ?>

		<?php }

		function addForm2($id,$field_id) { ?>

			<?php 

		   		$tempField = $this->getrowTempFields($id,$field_id);
		   		if(count($tempField)!=0) {
		   		
		   			
		   	?>

		   		<tr id="tr<?php echo $tempField->id; ?>_<?php echo $tempField->field_id; ?>">
					<td class="datath1"><input class="inputs" type="text" name="product_option[<?php echo $tempField->field_id; ?>][row_value][<?php echo $tempField->id; ?>][drop_option_row_title]" id="dropdowntitle" /></td>
					<td class="datath2">
			    		<input class="inputs" type="text" name="product_option[<?php echo $tempField->field_id; ?>][row_value][<?php echo $tempField->id; ?>][drop_option_row_price]" id="price<?php echo $tempField->field_id; ?>__<?php echo $tempField->id; ?>" onChange="PriceOnly(this.id);" />
			        </td>
					<td class="datath3">
						<select class="select_is_required inputs" name="product_option[<?php echo $tempField->field_id; ?>][row_value][<?php echo $tempField->id; ?>][drop_option_row_price_type]" id="dropdownprice_type">
	                		<option value="fixed"><?php echo _e('Fixed','fmepco'); ?></option>
	                		<option value="percent"><?php echo _e('Percent','fmepco'); ?></option>
	                	</select>
					</td>
					<td class="datath4"><input class="inputs" type="text" name="product_option[<?php echo $tempField->field_id; ?>][row_value][<?php echo $tempField->id; ?>][drop_option_row_sort_order]" id="sort_order<?php echo $tempField->field_id; ?>__<?php echo $tempField->id; ?>" onChange="SortOrderonlyNumber(this.id);" /></td>
					<td><a href="javascript:void(0)" onClick="deleteDropRow('<?php echo $tempField->id; ?>','<?php echo $tempField->field_id; ?>')">Remove</a></td>
				</tr>

		   	<?php } }


		function addMultiForm($id,$field_id) { ?>

			<?php 

		   		$tempField = $this->getrowTempFields($id,$field_id);
		   		if(count($tempField)!=0) {
		   		
		   			
		   	?>

		   		<tr id="tr<?php echo $tempField->id; ?>_<?php echo $tempField->field_id; ?>">
					<td class="datath1"><input class="inputs" type="text" name="product_option[<?php echo $tempField->field_id; ?>][row_value][<?php echo $tempField->id; ?>][multi_option_row_title]" id="dropdowntitle" /></td>
					<td class="datath2">
			    		<input class="inputs" type="text" name="product_option[<?php echo $tempField->field_id; ?>][row_value][<?php echo $tempField->id; ?>][multi_option_row_price]" id="price<?php echo $tempField->field_id; ?>__<?php echo $tempField->id; ?>" onChange="PriceOnly(this.id);" />
			        </td>
					<td class="datath3">
						<select class="select_is_required inputs" name="product_option[<?php echo $tempField->field_id; ?>][row_value][<?php echo $tempField->id; ?>][multi_option_row_price_type]" id="dropdownprice_type">
	                		<option value="fixed"><?php echo _e('Fixed','fmepco'); ?></option>
	                		<option value="percent"><?php echo _e('Percent','fmepco'); ?></option>
	                	</select>
					</td>
					<td class="datath4"><input class="inputs" type="text" name="product_option[<?php echo $tempField->field_id; ?>][row_value][<?php echo $tempField->id; ?>][multi_option_row_sort_order]" id="sort_order<?php echo $tempField->field_id; ?>__<?php echo $tempField->id; ?>" onChange="SortOrderonlyNumber(this.id);" /></td>
					<td><a href="javascript:void(0)" onClick="deleteDropRow('<?php echo $tempField->id; ?>','<?php echo $tempField->field_id; ?>')">Remove</a></td>
				</tr>

		   	<?php } }


		 


		


		function addoptionTemData() {
			global $wpdb;
			$wpdb->query($wpdb->prepare( 
	            "
	            INSERT INTO $wpdb->fmepco_temp_table
	            (field_type,field)
	            VALUES (%s,%s)
	            ",
	            'option',
	            'textfield'
	            ) );
			$lastid = $wpdb->insert_id;
			$this->addForm($lastid);
			die();
			return true;

			
			

		}

		function addrowTemData() {
			global $wpdb;
			$wpdb->query($wpdb->prepare( 
	            "
	            INSERT INTO $wpdb->fmepco_temp_table
	            (field_id,field_type,field)
	            VALUES (%s,%s,%s)
	            ",
	            intval($_POST['field_id']),
	            'row',
	            'textfield'
	            ) );
			$lastid = $wpdb->insert_id;
			$this->addForm2($lastid,intval($_POST['field_id']));
			die();
			return true;

		}


		function addmultirowTemData() {
			global $wpdb;
			$wpdb->query($wpdb->prepare( 
	            "
	            INSERT INTO $wpdb->fmepco_temp_table
	            (field_id,field_type,field)
	            VALUES (%s,%s,%s)
	            ",
	            intval($_POST['field_id']),
	            'row',
	            'textfield'
	            ) );
			$lastid = $wpdb->insert_id;
			$this->addMultiForm($lastid,intval($_POST['field_id']));
			die();
			return true;

		}

		


		

		function deloptionTempData() {

			$field_id = intval($_POST['field_id']);
			global $wpdb;
			$wpdb->query( $wpdb->prepare( "DELETE FROM ".$wpdb->fmepco_temp_table . " WHERE id = %d", $field_id ) );
			die();
			return true;

		}

		function delrowTempData() {

			$field_id = intval($_POST['field_id']);
			$id = intval($_POST['id']);
			global $wpdb;
			$wpdb->query( $wpdb->prepare( "DELETE FROM ".$wpdb->fmepco_temp_table . " WHERE id = %d AND field_id = %d", $id, $field_id ) );
			die();
			return true;

		}


		

		


		
	}

	new FME_Product_Custom_Options_Admin();
}