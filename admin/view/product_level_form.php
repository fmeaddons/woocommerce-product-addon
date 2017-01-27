<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
require_once FMEPCO_PLUGIN_DIR . 'admin/class-fme-product-custom-options-admin.php';
$custom_options = new FME_Product_Custom_Options_Admin();
$product_options = $custom_options->getProductOptions($post->ID);
$meta_value = get_post_meta( $post->ID, '_exclude_global_options', true );
//print_r($product_options);

//echo $post->ID;
?>
			
<div class="field_wrapper">

	<div class="field_success"></div>
	<div class="addButton">
		<input onClick="addFields()" type="button" id="btnAdd" class="button button-primary button-large" value="<?php echo _e('Add New Option','fmepco'); ?>"> 
	</div>
	<form id="featured_upload" method="post" action="" enctype="multipart/form-data">
	
		
		<?php 
			if(count($product_options)!= 0) {
			foreach ($product_options as $product_option) { 

			$product_option_rows = $custom_options->getProductOptionRows($product_option->id);
		?>
		
		<input type="hidden" value="yes" name="editpro" />
		<input type="hidden" value="<?php echo $product_option->id; ?>" name="product_option[<?php echo $product_option->id; ?>][option_id]" />
		<div class="addFormFields" id="field<?php echo $product_option->id; ?>">
				<input onClick="delFields('<?php echo $product_option->id; ?>')" type="button" class="btnDel button btn-danger button-large" value="<?php echo _e('Delete Option','fmepco'); ?>">
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
								<td class="datath1"><input class="inputs" type="text" value="<?php echo stripslashes($product_option->option_title); ?>" name="product_option[<?php echo $product_option->id; ?>][option_title]"  id="title" /></td>
								<td class="datath2">
						    		<select class="select_type inputs" name="product_option[<?php echo $product_option->id; ?>][option_type]" id="type" onChange="showFields('<?php echo $product_option->id; ?>',this.value)">
						    			<option value=""><?php echo _e('-- Please select --','fmepco'); ?></option>
						    			<optgroup label="Text">
						    				<option value="field" <?php selected('field',$product_option->option_field_type); ?>><?php echo _e('Field','fmepco'); ?></option>
						    				<option value="area" <?php selected('area',$product_option->option_field_type); ?>><?php echo _e('Area','fmepco'); ?></option>
						    			</optgroup>
						    			
						    			<optgroup label="Select">
						    				<option value="drop_down" <?php selected('drop_down',$product_option->option_field_type); ?>><?php echo _e('Drop-down','fmepco'); ?></option>
			            					<option value="multiple" <?php selected('multiple',$product_option->option_field_type); ?>><?php echo _e('Multiple Select','fmepco'); ?></option>
			            				</optgroup>
					            		
			            			</select>
						        </td>
								<td class="datath3">
									<select class="select_is_required inputs" name="product_option[<?php echo $product_option->id; ?>][option_is_required]" id="is_required">
				                		<option value="yes" <?php selected('yes',$product_option->option_is_required); ?>><?php echo _e('Yes','fmepco'); ?></option>
				                		<option value="no" <?php selected('no',$product_option->option_is_required); ?>><?php echo _e('No','fmepco'); ?></option>
				                	</select>
								</td>
								<td class="datath4"><input class="inputs" type="text" value="<?php echo $product_option->option_sort_order; ?>" name="product_option[<?php echo $product_option->id; ?>][option_sort_order]" id="sort_order<?php echo $product_option->id; ?>" onChange="SortOrderonlyNumber(this.id);" /></td>
							</tr>
						</tbody>
					</table>
				</div>

				<div class="bottom_fields">
					<div id="textField<?php echo $product_option->id; ?>" <?php if($product_option->option_field_type == 'field') { ?> style="display:block;" <?php } else { ?> style="display:none;" <?php } ?>>
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
									<td class="datath1"><input class="inputs" type="text" value="<?php echo $product_option->option_price; ?>" name="product_option[<?php echo $product_option->id; ?>][text_option_price]" id="price<?php echo $product_option->id ?>" onChange="PriceOnly(this.id);" /></td>
									<td class="datath2">
										<select class="select_is_required inputs" name="product_option[<?php echo $product_option->id; ?>][text_option_price_type]" id="fieldprice_type">
					                		<option value="fixed" <?php selected('fixed',$product_option->option_price_type); ?>><?php echo _e('Fixed','fmepco'); ?></option>
					                		<option value="percent" <?php selected('percent',$product_option->option_price_type); ?>><?php echo _e('Percent','fmepco'); ?></option>
					                	</select>
									</td>
									<td class="datath3"><input class="inputs" type="text" value="<?php echo $product_option->option_maxchars; ?>" name="product_option[<?php echo $product_option->id; ?>][text_option_maxchars]" id="maxchars<?php echo $product_option->id; ?>" onChange="MaxCharsonlyNumber(this.id);" /></td>
								</tr>
							</tbody>
						</table>
					</div>
					<div id="textArea<?php echo $product_option->id; ?>" <?php if($product_option->option_field_type == 'area') { ?> style="display:block;" <?php } else { ?> style="display:none;" <?php } ?>>
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
									<td class="datath1"><input class="inputs" type="text" value="<?php echo $product_option->option_price; ?>" name="product_option[<?php echo $product_option->id; ?>][area_option_price]" id="price<?php echo $product_option->id ?>" onChange="PriceOnly(this.id);" /></td>
									<td class="datath2">
										<select class="select_is_required inputs" name="product_option[<?php echo $product_option->id; ?>][area_option_price_type]" id="areaprice_type">
					                		<option value="fixed" <?php selected('fixed',$product_option->option_price_type); ?>><?php echo _e('Fixed','fmepco'); ?></option>
					                		<option value="percent" <?php selected('percent',$product_option->option_price_type); ?>><?php echo _e('Percent','fmepco'); ?></option>
					                	</select>
									</td>
									<td class="datath3"><input class="inputs" type="text" value="<?php echo $product_option->option_maxchars; ?>" name="product_option[<?php echo $product_option->id; ?>][area_option_maxchars]" id="maxchars<?php echo $product_option->id; ?>" onChange="MaxCharsonlyNumber(this.id);" /></td>
								</tr>
							</tbody>
						</table>
					</div>
					
					<div id="dropdown<?php echo $product_option->id; ?>" <?php if($product_option->option_field_type == 'drop_down') { ?> style="display:block;" <?php } else { ?> style="display:none;" <?php } ?>>
						<div class="rowfield_success<?php echo $product_option->id; ?>"></div>
						<table class="datatable" id="POITable<?php echo $product_option->id; ?>">
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

							<?php if($product_option->option_field_type == 'drop_down') { ?>
							<?php foreach ($product_option_rows as $product_option_row) { ?>
								
								<tr id="tr<?php echo $product_option_row->id; ?>_<?php echo $product_option_row->option_id; ?>">
									<td class="datath1"><input class="inputs" type="text" value="<?php echo stripslashes($product_option_row->option_row_title); ?>" name="product_option[<?php echo $product_option_row->option_id; ?>][row_value][<?php echo $product_option_row->id; ?>][drop_option_row_title]" id="dropdowntitle" /></td>
									<td class="datath2">
							    		<input class="inputs" type="text" value="<?php echo $product_option_row->option_row_price; ?>" name="product_option[<?php echo $product_option_row->option_id; ?>][row_value][<?php echo $product_option_row->id; ?>][drop_option_row_price]" id="price<?php echo $product_option_row->option_id; ?>__<?php echo $product_option_row->id; ?>" onChange="PriceOnly(this.id);" />
							        </td>
									<td class="datath3">
										<select class="select_is_required inputs" name="product_option[<?php echo $product_option_row->option_id; ?>][row_value][<?php echo $product_option_row->id; ?>][drop_option_row_price_type]" id="dropdownprice_type">
					                		<option value="fixed" <?php selected('fixed',$product_option_row->option_row_price_type); ?>><?php echo _e('Fixed','fmepco'); ?></option>
					                		<option value="percent" <?php selected('percent',$product_option_row->option_row_price_type); ?>><?php echo _e('Percent','fmepco'); ?></option>
					                	</select>
									</td>
									<td class="datath4"><input class="inputs" type="text" value="<?php echo $product_option_row->option_row_sort_order; ?>" name="product_option[<?php echo $product_option_row->option_id; ?>][row_value][<?php echo $product_option_row->id; ?>][drop_option_row_sort_order]" id="sort_order<?php echo $product_option_row->option_id; ?>__<?php echo $product_option_row->id; ?>" onChange="SortOrderonlyNumber(this.id);" /></td>
									<td><a href="javascript:void(0)" onClick="deleteDropRow('<?php echo $product_option_row->id; ?>','<?php echo $product_option_row->option_id; ?>')">Remove</a></td>
								</tr>

							<?php } } ?>

								<tr>
									<td class="<?php echo $product_option->id; ?>droprowdata" colspan="5">
										<?php $custom_options->addForm2(0,$product_option->id); ?>
									</td>
								</tr>

							</tbody>
							<tfoot>
								<tr class="addButton">
							   		<td colspan="5"><input onClick="addNewDropRow(<?php echo $product_option->id; ?>)" type="button" id="btnAdd" class="button button-default" value="<?php echo _e('Add New Row','fmepco'); ?>"></td> 
							   	</tr>
							</tfoot>
							<tbody>
								
							</tbody>
						</table>
					</div>
					<div id="multiselect<?php echo $product_option->id; ?>" <?php if($product_option->option_field_type == 'multiple') { ?> style="display:block;" <?php } else { ?> style="display:none;" <?php } ?>>
						<div class="rowfield_success<?php echo $product_option->id; ?>"></div>
						<table class="datatable" id="MultiTable<?php echo $product_option->id; ?>">
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
								<?php if($product_option->option_field_type == 'multiple') { ?>
								<?php foreach ($product_option_rows as $product_option_row) { ?>
								
									<tr id="tr<?php echo $product_option_row->id; ?>_<?php echo $product_option_row->option_id; ?>">
										<td class="datath1"><input class="inputs" type="text" value="<?php echo $product_option_row->option_row_title; ?>" name="product_option[<?php echo $product_option_row->option_id; ?>][row_value][<?php echo $product_option_row->id; ?>][multi_option_row_title]" id="dropdowntitle" /></td>
										<td class="datath2">
								    		<input class="inputs" type="text" value="<?php echo $product_option_row->option_row_price; ?>" name="product_option[<?php echo $product_option_row->option_id; ?>][row_value][<?php echo $product_option_row->id; ?>][multi_option_row_price]" id="price<?php echo $product_option_row->option_id; ?>__<?php echo $product_option_row->id; ?>" onChange="PriceOnly(this.id);" />
								        </td>
										<td class="datath3">
											<select class="select_is_required inputs" name="product_option[<?php echo $product_option_row->option_id; ?>][row_value][<?php echo $product_option_row->id; ?>][multi_option_row_price_type]" id="dropdownprice_type">
						                		<option value="fixed" <?php selected('fixed',$product_option_row->option_row_price_type); ?>><?php echo _e('Fixed','fmepco'); ?></option>
						                		<option value="percent" <?php selected('percent',$product_option_row->option_row_price_type); ?>><?php echo _e('Percent','fmepco'); ?></option>
						                	</select>
										</td>
										<td class="datath4"><input class="inputs" type="text" value="<?php echo $product_option_row->option_row_sort_order; ?>" name="product_option[<?php echo $product_option_row->option_id; ?>][row_value][<?php echo $product_option_row->id; ?>][multi_option_row_sort_order]" id="sort_order<?php echo $product_option_row->option_id; ?>__<?php echo $product_option_row->id; ?>" onChange="SortOrderonlyNumber(this.id);" /></td>
										<td><a href="javascript:void(0)" onClick="deleteDropRow('<?php echo $product_option_row->id; ?>','<?php echo $product_option_row->option_id; ?>')">Remove</a></td>
									</tr>
								
								<?php } } ?>

								<tr>
									<td class="<?php echo $product_option->id; ?>multirowdata" colspan="5">
										<?php $custom_options->addMultiForm(0,$product_option->id); ?>
									</td>
								</tr>
							</tbody>
							<tfoot>
								<tr class="addButton">
							   		<td colspan="5"><input onClick="addNewMultiRow(<?php echo $product_option->id; ?>)" type="button" id="btnAdd" class="button button-default" value="<?php echo _e('Add New Row','fmepco'); ?>"></td> 
							   	</tr>
							</tfoot>
							<tbody>
								
							</tbody>
						</table>
					</div>
					
				</div>

			</div>

		<?php } } ?>

		<div class="tt">
			<?php $custom_options->addForm(0); ?>
		</div>

	</form>

</div>
					 

<script type="text/javascript">


function SortOrderonlyNumber(id){
	    var DataVal = document.getElementById(id).value;
	    if(!DataVal.match(/^[1-9][0-9]*$/)) {
	    	 alert('Only Integer values are allowed in Sort Order field!');
	    	 document.getElementById(id).value = DataVal.replace(/[^0-9]/g,'');
	    	 ('#'+id).focus();

	    }

	    
	}

	function MaxCharsonlyNumber(id){
	    var DataVal = document.getElementById(id).value;
	    if(!DataVal.match(/^[1-9][0-9]*$/)) {
	    	 alert('Only Integer values are allowed in Max Characters field!');
	    	 document.getElementById(id).value = DataVal.replace(/[^0-9]/g,'');
	    	 ('#'+id).focus();

	    }

	    
	}

	function PriceOnly(id){
	    var DataVal = document.getElementById(id).value;
	    if(!DataVal.match(/^[1-9.-][0-9.-]*$/)) {
	    	 alert('Only Numaric values are allowed in Price field!');
	    	 document.getElementById(id).value = DataVal.replace(/[^0-9.-]/g,'');
	    	 ('#'+id).focus();

	    }

	    
	}
	
	


	jQuery(document).ready(function($) { 
		
		
	   $('#field').toggle(function(){ 
		   $('#field_div').removeClass('ui-state-default widget').addClass('ui-state-default widget open');
		   $("#bw").slideDown('slow');
		   
	   },function(){
		$('#bw').removeClass('ui-state-default widget open').addClass('ui-state-default widget');
		   $("#bw").slideUp('slow');
	   });

	});

	function addFields() {

		var ajaxurl = "<?php echo admin_url( 'admin-ajax.php'); ?>";

		jQuery.ajax({
			type: 'POST',
			url: ajaxurl,
			data: 'action=addoptionTempData',
			success: function(data) {
			   jQuery('.tt').append(data);
			}
		});
	}

	function addNewDropRow(field_id) {

		var ajaxurl = "<?php echo admin_url( 'admin-ajax.php'); ?>";

		jQuery.ajax({
			type: 'POST',
			url: ajaxurl,
			data: {"action": "addrowTempData", "field_id":field_id},
			success: function(data) {
			   jQuery('.'+field_id+'droprowdata').append(data);
			}
		});

	}


	function addNewMultiRow(field_id) {

		var ajaxurl = "<?php echo admin_url( 'admin-ajax.php'); ?>";

		jQuery.ajax({
			type: 'POST',
			url: ajaxurl,
			data: {"action": "addmultirowTempData", "field_id":field_id},
			success: function(data) {
			   jQuery('.'+field_id+'multirowdata').append(data);
			}
		});

	}


	


	function delFields(field_id) {

		var ajaxurl = "<?php echo admin_url( 'admin-ajax.php'); ?>";
		if(confirm("Are you sure to delete this option? This action can not be undone."))
		{
			jQuery.ajax({
			type: "POST",
			url: ajaxurl,
			data: {"action": "deloptionTempData", "field_id":field_id},
			success: function() {

				jQuery("#field"+field_id).fadeOut('slow');
				jQuery("#field"+field_id).remove();

				jQuery('.field_success').html("<div class='updated notice alert'>Option Deleted Sucessfully!</div>");
				window.scrollTo(0, 0);
				
				jQuery('.alert').delay(5000).fadeOut('slow');

			}
			});

		}
	return false;
	}


	function deleteDropRow(id, field_id) {

		var ajaxurl = "<?php echo admin_url( 'admin-ajax.php'); ?>";
		if(confirm("Are you sure to delete this row? This action can not be undone."))
		{
			jQuery.ajax({
			type: "POST",
			url: ajaxurl,
			data: {"action": "delrowTempData", "field_id":field_id, "id":id},
			success: function() {

				jQuery("#tr"+id+"_"+field_id).fadeOut('slow');
				jQuery("#tr"+id+"_"+field_id).remove();
				jQuery('.rowfield_success'+field_id).html("<div id='message' class='updated notice alert'>Row Deleted Sucessfully!</div>");
				jQuery('.rowfield_success'+field_id+" #message").delay(5000).fadeOut('slow');

			}
			});

		}
	return false;
	}





	function showFields(field_id, value) {

		if(value == 'field') {

			jQuery('#textArea'+field_id).slideUp('slow');
			jQuery('#dropdown'+field_id).slideUp('slow');
			jQuery('#multiselect'+field_id).slideUp('slow');

			
			jQuery('#textField'+field_id).slideDown('slow');

		} else if(value == 'area') {

			jQuery('#textField'+field_id).slideUp('slow');
			jQuery('#dropdown'+field_id).slideUp('slow');
			jQuery('#multiselect'+field_id).slideUp('slow');

			
			jQuery('#textArea'+field_id).slideDown('slow');

		}  else if(value == 'drop_down') {

			jQuery('#textField'+field_id).slideUp('slow');
			jQuery('#textArea'+field_id).slideUp('slow');
			jQuery('#multiselect'+field_id).slideUp('slow');

			
			jQuery('#dropdown'+field_id).slideDown('slow');

		} else if(value == 'multiple') {

			jQuery('#textField'+field_id).slideUp('slow');
			jQuery('#textArea'+field_id).slideUp('slow');
			jQuery('#dropdown'+field_id).slideUp('slow');

			
			jQuery('#multiselect'+field_id).slideDown('slow');

		}  else {

			jQuery('#textField'+field_id).slideUp('slow');
			jQuery('#textArea'+field_id).slideUp('slow');
			jQuery('#dropdown'+field_id).slideUp('slow');
			jQuery('#multiselect'+field_id).slideUp('slow');

		}

	}
	




</script>
