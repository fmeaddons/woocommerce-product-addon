<?php 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
require_once FMEPCO_PLUGIN_DIR . 'front/class-fme-product-custom-option-front.php';
$custom_options = new FME_Product_Custom_Options_Front();



$ProductOptions = $custom_options->getProductOptions($post->ID);

$currency = get_woocommerce_currency();
$string = get_woocommerce_currency_symbol( $currency );
$proprice = get_post_meta($post->ID, "_price", true);
$product_image =get_the_post_thumbnail($post->ID);


?>
<div class="custom_options">

	

	<!-- Product Options Start-->

	<?php if($ProductOptions!='') { ?>
	<?php foreach ($ProductOptions as $global_option) { ?>
	<?php 
		$title = strtolower(str_replace(' ', '_', $global_option->option_title));

		if(isset($_POST['product_options'][$title]) && $_POST['product_options'][$title]!='') {
			$val_post = $_POST['product_options'][$title];
		} else { $val_post = ''; }
	?>

	<div class="fmecustomgroup">	
		<label>
			<?php echo stripslashes($global_option->option_title); ?> 
			<?php if($global_option->option_is_required == 'yes') { ?>
				<span class="required">*</span>
			<?php } ?>
			:-
			<?php if($global_option->option_price != '') { ?>
				<?php if($global_option->option_price_type == 'percent') { ?>
					<span class="price">(
						<?php
							echo wc_price($proprice*$global_option->option_price/100, array(
							    'ex_tax_label'       => false,
							    'currency'           => '',
							    'decimal_separator'  => wc_get_price_decimal_separator(),
							    'thousand_separator' => wc_get_price_thousand_separator(),
							    'decimals'           => wc_get_price_decimals(),
							    'price_format'       => get_woocommerce_price_format()
							) );

						
						?>
					)</span>
				<?php } else { ?>
					<span class="price">(
						<?php 


							echo wc_price($global_option->option_price, array(
							    'ex_tax_label'       => false,
							    'currency'           => '',
							    'decimal_separator'  => wc_get_price_decimal_separator(),
							    'thousand_separator' => wc_get_price_thousand_separator(),
							    'decimals'           => wc_get_price_decimals(),
							    'price_format'       => get_woocommerce_price_format()
							) );



						 ?>
					)</span>
				<?php } ?>
			<?php } ?>
		</label>
		<?php if($global_option->option_field_type == 'field') { ?>
			<input data-price="<?php if($global_option->option_price_type == 'percent') { echo $proprice*$global_option->option_price/100; } else { echo $global_option->option_price; }  ?>" class="fmeop fmeinput" type="text" maxlength="<?php echo $global_option->option_maxchars; ?>" value="<?php if( ! empty($val_post) ){ echo $val_post; } ?>" name="product_options[<?php echo strtolower(str_replace(' ', '_', $global_option->option_title)); ?>]">
		<?php } else if($global_option->option_field_type == 'area') { ?>
			<textarea data-price="<?php if($global_option->option_price_type == 'percent') { echo $proprice*$global_option->option_price/100; } else { echo $global_option->option_price; }  ?>" class="fmeop fmeinput" maxlength="<?php echo $global_option->option_maxchars; ?>" name="product_options[<?php echo strtolower(str_replace(' ', '_', $global_option->option_title)); ?>]"><?php if( ! empty($val_post) ){ echo $val_post; } ?></textarea>
		<?php } else if($global_option->option_field_type == 'drop_down') { ?> 
			<select type="select" class="fma fmeop fmeinput" name="product_options[<?php echo strtolower(str_replace(' ', '_', $global_option->option_title)); ?>]">
				<?php $RowOptions = $this->getRowOptions($global_option->id) ?>
				<?php foreach($RowOptions as $option_row) { ?>
					<option <?php selected($val_post,$option_row->option_row_title); ?> data-price="<?php if($option_row->option_row_price_type == 'percent') { echo $proprice*$option_row->option_row_price/100; } else { echo $option_row->option_row_price; }  ?>" value="<?php echo $option_row->option_row_title; ?>">
					<?php echo $option_row->option_row_title; ?>   
					<?php if($option_row->option_row_price != '') { ?>
						<?php if($option_row->option_row_price_type == 'percent') { ?>
							-  <span class="price">(
								<?php 


							echo wc_price($proprice*$option_row->option_row_price/100, array(
							    'ex_tax_label'       => false,
							    'currency'           => '',
							    'decimal_separator'  => wc_get_price_decimal_separator(),
							    'thousand_separator' => wc_get_price_thousand_separator(),
							    'decimals'           => wc_get_price_decimals(),
							    'price_format'       => get_woocommerce_price_format()
							) );


							 ?>
							)</span>
						<?php } else { ?>
							-  <span class="price">(
								<?php 



								echo wc_price($option_row->option_row_price, array(
								    'ex_tax_label'       => false,
								    'currency'           => '',
								    'decimal_separator'  => wc_get_price_decimal_separator(),
								    'thousand_separator' => wc_get_price_thousand_separator(),
								    'decimals'           => wc_get_price_decimals(),
								    'price_format'       => get_woocommerce_price_format()
								) );


								 ?>
							)</span>
						<?php } ?>
					<?php } ?>
					</option>
				<?php } ?>
			</select>
		<?php } else if($global_option->option_field_type == 'multiple') { ?> 
			<select type="mselect" multiple = "multiple" class="fmm fmeop fmeinput multi" name="product_options[<?php echo strtolower(str_replace(' ', '_', $global_option->option_title)); ?>][]">
				<?php $RowOptions = $this->getRowOptions($global_option->id) ?>
				<?php foreach($RowOptions as $option_row) { ?>
					<?php 
						$title = strtolower(str_replace(' ', '_', $global_option->option_title));

						if(isset($_POST['product_options'][$title]) && $_POST['product_options'][$title]!='') {
							$val_post2 = $_POST['product_options'][$title];
						} else { $val_post2 = ''; } 

					?>
					<option <?php if($val_post2!='') { foreach ($val_post2 as $valp) { selected($valp,$option_row->option_row_title); } } ?> data-price="<?php if($option_row->option_row_price_type == 'percent') { echo $proprice*$option_row->option_row_price/100; } else { echo $option_row->option_row_price; }  ?>" value="<?php echo $option_row->option_row_title; ?>">
					<?php echo $option_row->option_row_title; ?>   
					<?php if($option_row->option_row_price != '') { ?>
						<?php if($option_row->option_row_price_type == 'percent') { ?>
							-  <span class="price">(
								<?php 


								echo wc_price($proprice*$option_row->option_row_price/100, array(
								    'ex_tax_label'       => false,
								    'currency'           => '',
								    'decimal_separator'  => wc_get_price_decimal_separator(),
								    'thousand_separator' => wc_get_price_thousand_separator(),
								    'decimals'           => wc_get_price_decimals(),
								    'price_format'       => get_woocommerce_price_format()
								) );

								 ?>
							)</span>
						<?php } else { ?>
							-  <span class="price">(
								<?php 

								echo wc_price($option_row->option_row_price, array(
								    'ex_tax_label'       => false,
								    'currency'           => '',
								    'decimal_separator'  => wc_get_price_decimal_separator(),
								    'thousand_separator' => wc_get_price_thousand_separator(),
								    'decimals'           => wc_get_price_decimals(),
								    'price_format'       => get_woocommerce_price_format()
								) );

								 ?>
							)</span>
						<?php } ?>
					<?php } ?>
					</option>
				<?php } ?>
			</select>
		<?php } ?>
		
	</div>
	<?php } ?>
	<?php } ?>

	<!-- Product Options End-->



</div>

<?php 
				
			$product = get_product( $post->ID );
				
			if ( is_object( $product ) ) {
				
				$tax_display_mode = get_option( 'woocommerce_tax_display_shop' );
				
				$d_price    = $tax_display_mode == 'incl' ? $product->get_price_including_tax() : $product->get_price_excluding_tax();
			
			} else {
				
				$d_price    = '';
				
			}

?>
<div class="price_total">
	<div id="product_options_total" product-type="<?php echo $product->product_type;  ?>" product-price="<?php echo $d_price; ?>"></div>
</div>

<script type="text/javascript">
	jQuery( document ).ready( function($) {
	
	$(this).on( 'change', 'input:text, select, textarea, input.qty', function() {

		ProductCustomOptions();
		
	});
	
	ProductCustomOptions();
	
	function ProductCustomOptions() {
	
		var option_total = 0;
		
		var product_price = $('#product_options_total').attr( 'product-price' );
		
		var product_total_price = 0;
		
		var final_total = 0;
		
		$('.fmeop').each( function() {
			
			var option_price = 0;
			if($(this).attr('type') == 'select') {

				option_price = $("option:selected", this).attr('data-price');

			} else if($(this).attr('type') == 'mselect') {
					
					var sum = option_price;
				    $( "option:selected", this ).each(function() {
				      str = parseFloat($( this ).attr('data-price'));
				      sum = str+sum;
				    });
				    option_price = sum;

			} else {
			
				option_price = $(this).attr('data-price');
			}
			var value_entered =  $(this).val();
			
			if(value_entered != '' || option_price == 0)
			{
				option_total = parseFloat( option_total ) + parseFloat( option_price );
			}
			
		});
		
		
		var qty = $('.qty').val();
		
		if ( option_total > 0 && qty > 0 ) {
			
			option_total = parseFloat( option_total * qty );

			var price_form = "<?php echo get_option( 'woocommerce_currency_pos' ); ?>";
			var op_price = '';
			
			if(price_form == 'left') {
				op_price = accounting.formatMoney(option_total, { symbol: "<?php echo $string; ?>",  format: "%s%v" }, "<?php echo wc_get_price_decimals(); ?>", "<?php echo wc_get_price_thousand_separator(); ?>", "<?php echo wc_get_price_decimal_separator(); ?>"); // €4.999,99
			} else if(price_form == 'left_space') {
				op_price = accounting.formatMoney(option_total, { symbol: "<?php echo $string; ?>",  format: "%s %v" }, "<?php echo wc_get_price_decimals(); ?>", "<?php echo wc_get_price_thousand_separator(); ?>", "<?php echo wc_get_price_decimal_separator(); ?>"); // €4.999,99
			} else if(price_form == 'right') {
				op_price = accounting.formatMoney(option_total, { symbol: "<?php echo $string; ?>",  format: "%v%s" }, "<?php echo wc_get_price_decimals(); ?>", "<?php echo wc_get_price_thousand_separator(); ?>", "<?php echo wc_get_price_decimal_separator(); ?>"); // €4.999,99
			} else if(price_form == 'right_space') {
				op_price = accounting.formatMoney(option_total, { symbol: "<?php echo $string; ?>",  format: "%v %s" }, "<?php echo wc_get_price_decimals(); ?>", "<?php echo wc_get_price_thousand_separator(); ?>", "<?php echo wc_get_price_decimal_separator(); ?>"); // €4.999,99
			}
			

			if ( product_price ) {

				product_total_price = parseFloat( product_price * qty );
				

			}
			
			final_total = option_total + product_total_price;

			var fi_price = '';
			
			if(price_form == 'left') {
				fi_price = accounting.formatMoney(final_total, { symbol: "<?php echo $string; ?>",  format: "%s%v" }, "<?php echo wc_get_price_decimals(); ?>", "<?php echo wc_get_price_thousand_separator(); ?>", "<?php echo wc_get_price_decimal_separator(); ?>"); // €4.999,99
			} else if(price_form == 'left_space') {
				fi_price = accounting.formatMoney(final_total, { symbol: "<?php echo $string; ?>",  format: "%s %v" }, "<?php echo wc_get_price_decimals(); ?>", "<?php echo wc_get_price_thousand_separator(); ?>", "<?php echo wc_get_price_decimal_separator(); ?>"); // €4.999,99
			} else if(price_form == 'right') {
				fi_price = accounting.formatMoney(final_total, { symbol: "<?php echo $string; ?>",  format: "%v%s" }, "<?php echo wc_get_price_decimals(); ?>", "<?php echo wc_get_price_thousand_separator(); ?>", "<?php echo wc_get_price_decimal_separator(); ?>"); // €4.999,99
			} else if(price_form == 'right_space') {
				fi_price = accounting.formatMoney(final_total, { symbol: "<?php echo $string; ?>",  format: "%v %s" }, "<?php echo wc_get_price_decimals(); ?>", "<?php echo wc_get_price_thousand_separator(); ?>", "<?php echo wc_get_price_decimal_separator(); ?>"); // €4.999,99
			}

			html = '';
			
			
				html = html + '<div class="tprice"><div class="leftprice"><?php echo _e("Options Total:","fmepco") ?></div><div class="rightprice optionprice">'+op_price+'</div></div>';
			
			
			if ( final_total ) {
				
				
					html = html + '<div class="tprice"><div class="leftprice"><?php echo _e("Final Total:","fmepco") ?></div><div class="rightprice finalprice">'+fi_price+'</div></div>';
				

			}

			html = html + '</dl>';

			$('#product_options_total').html( html );
				
		} else {
			
			$('#product_options_total').html( '' );
		}
	}	
		
});



</script>

<script>
    var URL = "<?php echo FMEPCO_URL; ?>";
</script>

