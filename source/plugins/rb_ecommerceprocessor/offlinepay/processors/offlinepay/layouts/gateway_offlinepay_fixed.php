<?php

/**
* @copyright	Copyright (C) 2009 - 2012 Ready Bytes Software Labs Pvt. Ltd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* @package 		Joomla.Plugin
* @subpackage	Rb_EcommerceProcessor.Stripe
* @contact		team@readybytes.in
* @author		mManishTrivedi
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

$bank_name 			= 	@$displayData['payment_data']['bank_name'];
$account_number 	= 	@$displayData['payment_data']['account_number'];
$invoice_number 	=	@$displayData['payment_data']['invoice_number'];
$amount				=	@$displayData['payment_data']['amount'];

?>
	
	<div class="well ">
	
		<div class="row-fluid">
		
			<span class="payment-errors hide"></span>
			
			<div class="span12">
			    
			    <label>
			      <span class="required-label"><?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_OFFLINE_FORM_OFFLINE_PAYMENT_MODE_LABEL');?></span>
			    </label>
		        
		        <select class="input-xlarge" name="payment_data[from]" required="true" >
		        
		        	<option value="cash" ><?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_OFFLINE_FORM_OFFLINE_PAYMENT_MODE_CASH'); ?> </option>
					<option value="cheque"><?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_OFFLINE_FORM_OFFLINE_PAYMENT_MODE_CHEQUE'); ?> </option>
					<option value="dd"><?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_OFFLINE_FORM_OFFLINE_PAYMENT_MODE_DEMAND_DRAFT'); ?> </option>
					
		        </select>
		        
			</div>
	
	        <div class="span12">
			    <label>
			      <span class="required-label"><?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_OFFLINE_FORM_DD_OR_CHEQUE_NUMBER_LABEL');?></span>
			     </label>

				<input type="text"  name="payment_data[id]" class="input-xlarge" />	            
	        </div>
		
		
			<div class="span12">
			    <label>
			      <span class="required-label"><?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_OFFLINE_FORM_BANK_NAME_LABEL');?></span>
			     </label>

				<input type="text"  name="payment_data[bank_name]" value="<?php echo $bank_name; ?>" class="input-xlarge" readonly="true" />	            
	        </div>
	        
	        
	        <div class="span12">
			    <label>
			      <span class="required-label"><?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_OFFLINE_ACCOUNT_NUMBER_LABEL');?></span>
			     </label>

				<input	type="text"  name="payment_data[account_number]" class="input-xlarge"
						value="<?php echo$account_number; ?>" readonly="true" 
				/>	
				            
	        </div>
	        
	        <input type="hidden"  name="payment_data[invoice_number]" class="input-xlarge" value="<?php echo $invoice_number; ?>" />
	        <input type="hidden"  name="payment_data[amount]" class="input-xlarge"  value="<?php echo $amount; ?>" />
	        
		
		</div>
	</div>
