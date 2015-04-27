<?php 
/**
* @copyright	Copyright (C) 2009 - 2015 Ready Bytes Software Labs Pvt. Ltd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* @package 		Joomla.Plugin
* @subpackage	Rb_EcommerceProcessor.CCbill
* @contact		support@readybytes.in
*/
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
?>

<div class="well ">
		<div class="row-fluid">
			<div class="text-center">
				<?php
					//@TODO:: dont use hardcoded path
					echo Rb_Html::image('plugins/rb_ecommerceprocessor/ccbill/processors/ccbill/layouts/ccbill.png', 'CCBill'); 
				?>	
			</div>
		</div>
</div>

<?php $applicableCurrency = array('USD'=>'840','GBP'=>'826','EUR'=>'978','CAD' =>'124','AUD'=>'036','JPY'=>'392');?>
<?php if(!array_key_exists($displayData['currencyCode'], $applicableCurrency )):
    echo JText::_("PLG_RB_ECOMMERCEPROCESSOR_CCBILL_CURRENCY_NOT_SUPPORTED");
    return ;
endif;
?>
			
		<input 	name="clientAccnum"
				id="rb_ecommerce_processor_ccbill_clientAccnum"
				type="hidden"
				value="<?php echo  $displayData['clientAccnum']; ?>" />
				
	  	
		<input 	name="clientSubacc"
				id="rb_ecommerce_processor_ccbill_clientSubacc"
				type="hidden"
				value="<?php echo  $displayData['clientSubacc']; ?>" />
					
		<input 	name="formName"
				id="rb_ecommerce_processor_ccbill_formName"
				type="hidden"
				value="<?php echo  $displayData['formName']; ?>" />
					
		<input 	name="formPrice"
				id="rb_ecommerce_processor_ccbill_formPrice"
				type="hidden"
				value="<?php echo  $displayData['formPrice']; ?>" />
					
		<input 	name="formPeriod"
				id="rb_ecommerce_processor_ccbill_formPeriod"
				type="hidden"
				value="<?php echo  $displayData['formPeriod']; ?>" />
					
		<input 	name="currencyCode"
				id="rb_ecommerce_processor_ccbill_currencyCode"
				type="hidden"
				value="<?php echo  $applicableCurrency[$displayData['currencyCode']]; ?>" />
					
		<input 	name="formDigest"
				id="rb_ecommerce_processor_ccbill_formDigest"
				type="hidden"
				value="<?php echo  $displayData['formDigest']; ?>" />
		
		<input 	name="invoice_number"
				id="rb_ecommerce_processor_ccbill_invoice_number"
				type="hidden"
				value="<?php echo  $displayData['invoice_number']; ?>" />
				
		<?php  if ($displayData['type'] == 'recurring') : ?>	
			
		<input 	name="formRecurringPrice"
				id="rb_ecommerce_processor_ccbill_formRecurringPrice"
				type="hidden"
				value="<?php echo  $displayData['formRecurringPrice']; ?>" />
					
		<input 	name="formRecurringPeriod"
				id="rb_ecommerce_processor_ccbill_formRecurringPeriod"
				type="hidden"
				value="<?php echo  $displayData['formRecurringPeriod']; ?>" />
					
		<input 	name="formRebills"
				id="rb_ecommerce_processor_ccbill_formRebills"
				type="hidden"
				value="<?php echo  $displayData['formRebills']; ?>" />
		<?php endif; ?>
					