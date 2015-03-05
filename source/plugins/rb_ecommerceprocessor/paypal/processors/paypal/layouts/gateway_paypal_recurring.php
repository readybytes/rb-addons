<?php 
/**
* @copyright	Copyright (C) 2009 - 2012 Ready Bytes Software Labs Pvt. Ltd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* @package 		Joomla.Plugin
* @subpackage	Rb_EcommerceProcessor.Paypal
* @contact		support@readybytes.in
*/
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
 
/**
 * Populated data :  
 * $displayData
 * 
 * 
 * $displayData['return'] 
 * $displayData['cancel_return']
 * $displayData['notify_url']
 * $displayData['business']
 * $displayData['no_note']
 * $displayData['invoice']
 * $displayData['item_name']
 * $displayData['item_number']
 * $displayData['currency_code']
 * $displayData['is_trial_one']
 * $displayData['is_trial_two']
 * $displayData['cmd']
 * 
 * ############################## Recurring specific
 * $displayData['a1']
 * $displayData['p1']
 * $displayData['t1']
 * $displayData['a2']
 * $displayData['p2']
 * $displayData['t2']
 * $displayData['a3']
 * $displayData['p3']
 * $displayData['t3']
 * $displayData['srt']
 * 
 * ################################# Fixed Specific
 * $displayData['amount']
 * 
 */
?>


	
	<div class="well ">
		<div class="row-fluid">
			<div class="text-center">
				<?php
					//@TODO:: dont use hardcoded path
					echo Rb_Html::image('/plugins/rb_ecommerceprocessor/paypal/processors/paypal/layouts/paypallogo.png', 'Paypal'); 
				?>	
			</div>
		</div>
	</div>
	
		<input 	name="invoice"
				id="rb_ecommerce_processor_paypal_invoice"
				type="hidden"
				value="<?php echo  $displayData['invoice']; ?>" />
		
		<input 	name="item_name" 
				id="rb_ecommerce_processor_paypal_item_name"
				type="hidden"
				value="<?php echo $displayData['item_name'] ; ?>"/>
				
		<input 	name="item_number" 
				id="rb_ecommerce_processor_paypal_item_number"
				type="hidden"
				value="<?php echo $displayData['item_number'] ; ?>"/>
				
		<input 	name="return"
				id="rb_ecommerce_processor_paypal_return"
				type="hidden"
				value="<?php echo  $displayData['return'] ; ?>"/>
				
		<input 	name="cancel_return" 
				id="rb_ecommerce_processor_paypal_cancel_return"
				type="hidden"
				value="<?php echo $displayData['cancel_return'] ; ?>"/>
				
		<input 	name="notify_url" 
				id="rb_ecommerce_processor_paypal_notify_url"
				type="hidden"
				value="<?php echo $displayData['notify_url'] ; ?>"/>
		
		<input 	name="cmd" 
				id="rb_ecommerce_processor_paypal_cmd"
				type="hidden"
				value="<?php echo $displayData['cmd'] ; ?>"/>
				
		<input 	name="business" 
				id="rb_ecommerce_processor_paypal_business"
				type="hidden"
				value="<?php echo $displayData['business'] ; ?>"/>
								
		<input 	name="currency_code" 
				id="rb_ecommerce_processor_paypal_currency_code"
				type="hidden"
				value="<?php echo  $displayData['currency_code'] ; ?>"/>
								
		<input 	name="no_note" 
				id="rb_ecommerce_processor_paypal_no_note"
				type="hidden"
				value="<?php echo  $displayData['no_note'];?>"/>
				
	
	
				
		<input 	name="a3" 
				id="rb_ecommerce_processor_paypal_a3"
				type="hidden"
				value="<?php echo  $displayData['a3'];?>"/>

		<input 	name="p3" 
				id="rb_ecommerce_processor_paypal_p3"
				type="hidden"
				value="<?php echo  $displayData['p3'];?>"/>		
				
		<input 	name="t3" 
				id="rb_ecommerce_processor_paypal_t3"
				type="hidden"
				value="<?php echo  $displayData['t3'];?>"/>		

		
		<input 	name="srt" 
				id="rb_ecommerce_processor_paypal_srt"
				type="hidden"
				value="<?php echo  $displayData['srt'];?>"/>
				
				
		<?php  if ($displayData['is_trial_one']) : ?>
		
		<input 	name="a1" 
				id="rb_ecommerce_processor_paypal_a1"
				type="hidden"
				value="<?php echo  $displayData['a1'];?>"/>

		<input 	name="p1" 
				id="rb_ecommerce_processor_paypal_p1"
				type="hidden"
				value="<?php echo  $displayData['p1'];?>"/>		
				
		<input 	name="t1" 
				id="rb_ecommerce_processor_paypal_t1"
				type="hidden"
				value="<?php echo  $displayData['t1'];?>"/>
		
		<?php endif; ?>
		
		<?php  if ($displayData['is_trial_two']) : ?>		
		
		<input 	name="a2" 
				id="rb_ecommerce_processor_paypal_a2"
				type="hidden"
				value="<?php echo  $displayData['a2'];?>"/>

		<input 	name="p2" 
				id="rb_ecommerce_processor_paypal_p2"
				type="hidden"
				value="<?php echo  $displayData['p2'];?>"/>		
				
		<input 	name="t2" 
				id="rb_ecommerce_processor_paypal_t2"
				type="hidden"
				value="<?php echo  $displayData['t3'];?>"/>
				
		<?php endif; ?>
							

		<input 	name="src" 
				id="rb_ecommerce_processor_paypal_src"
				type="hidden"
				value="1"/>

		<input 	name="sra" 
				id="rb_ecommerce_processor_paypal_sra"
				type="hidden"
				value="1"/>		
		
				
		<!--	METHOD in which data to be post from paypal 0-get,1,2-post -->
		<input 	name="rm" 
				id="rb_ecommerce_processor_paypal_rm"
				type="hidden"
				value="2"/>
				
