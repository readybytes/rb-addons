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
$year = date('Y');

?>
	
	<div class="well ">
	
		<div class="row-fluid">
		
			<span class="payment-errors hide"></span>
			
			<div class="span12">
			    
			    <label>
			      <span class="required-label"><?php echo JText::_('Card Number');?></span>
			    </label>
		        
		        <input type="text" placeholder="xxxx-xxxx-xxxx-xxxx" size="20" data-input-type="number" class="input-xlarge" name="payment_data[card_number]" required="true" />
			</div>
	
	        <div class="span12">
			    <label>
			      <span class="required-label"><?php echo JText::_('Expiration (MM/YYYY)');?></span>
			     </label>

	            <select name="payment_data[expiration_month]" class="input-mini" required="true">
<!--						<option value="" selected="selected">MM </option>-->
						<option value="01" ><?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_STRIPE_JANUARY'); ?> </option>
						<option value="02" ><?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_STRIPE_FEBRUARY'); ?></option>
						<option value="03" ><?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_STRIPE_MARCH'); ?></option>
						<option value="04" ><?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_STRIPE_APRIL'); ?></option>
						<option value="05" ><?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_STRIPE_MAY'); ?></option>
						<option value="06" ><?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_STRIPE_JUNE'); ?></option>
						<option value="07" ><?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_STRIPE_JULY'); ?></option>
						<option value="08" ><?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_STRIPE_AUGUST'); ?></option>
						<option value="09" ><?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_STRIPE_SEPTEMBER'); ?></option>
						<option value="10" ><?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_STRIPE_OCTOBER'); ?></option>
						<option value="11" ><?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_STRIPE_NOVEMBER'); ?></option>
						<option value="12" ><?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_STRIPE_DECEMBER'); ?></option>
					</select>
					
		    	<span> / </span>
	            <select name="payment_data[expiration_year]" class="input-mini" required="true">
<!--					<option value="" selected="selected">YYYY </option>-->
					<?php for ( $i = 0; $i < 20 ; $i++ ):?>
						<option value="<?php  echo $year ?>" > <?php echo $year++; ?> </option>
					<?php endfor; ?>
				</select>
	            
	        </div>
		
			<div class="span12">
				<label>
			      <span class="required-label"> <?php echo JText::_('Security Code');?></span>
			    </label>
		        
		        <div class="input-append">
		        	<input type="text" size="4" name="payment_data[card_code]" required="true" class="input-small" />
		            <span class="add-on"><i class="fa fa-question"></i></span>
		        </div>
		            
			</div>
		</div>
	</div>
