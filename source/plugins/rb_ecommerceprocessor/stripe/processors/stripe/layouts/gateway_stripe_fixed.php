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
			
			<div class="control-group">
				<div class="control-label">
				    <label for="rb-processor-stripe-card-number">
				     	<?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_PAYPALPRO_FORM_PAYPALPRO_CARD_NUMBER_LABEL');?>
				    </label>
			    </div>
		        <div class="controls">
		        	<input type="text"  size="20" id="rb-processor-stripe-card-number" class="input-block-level validate-rb-credit-card" name="payment_data[card_number]" required="true" autocomplete="off"/>
		        </div>
		        <span for="rb-processor-stripe-card-number" class="rb-error hide"><?php echo  JText::_('PLG_RB_ECOMMERCEPROCESSOR_STRIPE_ERROR_CRDEIT_CARD_NOT_VALID'); ?></span>
		        
			</div>
	
	       <div class="control-group">
				<div class="control-label">
				    <label for="rb-processor-stripe-card-expiry-year">
				      	<?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_STRIPE_FORM_STRIPE_EXPIRATION_MONTH_LABEL').'/'.JText::_("PLG_RB_ECOMMERCEPROCESSOR_STRIPE_FORM_STRIPE_EXPIRATION_YEAR_LABEL");?>
				     </label>
				</div>
	
				<div class="controls">
		            <select name="payment_data[expiration_month]" class="input-small validate-rb-exp-date "  
		            		id="rb-processor-stripe-card-expiry-month" 
		            		data-rb-validate-error="#rb-processor-stripe-card-expiry-error"
		            		data-rb-validate="#rb-processor-stripe-card-expiry-year"
		            		data-rb-validate-type="month"
		            		>
<!--						<option value="" selected="selected">MM </option>-->
							<option value="01" ><?php echo JText::_('JANUARY'); 	?></option>
							<option value="02" ><?php echo JText::_('FEBRUARY'); 	?></option>
							<option value="03" ><?php echo JText::_('MARCH'); 		?></option>
							<option value="04" ><?php echo JText::_('APRIL'); 		?></option>
							<option value="05" ><?php echo JText::_('MAY'); 		?></option>
							<option value="06" ><?php echo JText::_('JUNE'); 		?></option>
							<option value="07" ><?php echo JText::_('JULY'); 		?></option>
							<option value="08" ><?php echo JText::_('AUGUST'); 		?></option>
							<option value="09" ><?php echo JText::_('SEPTEMBER'); 	?></option>
							<option value="10" ><?php echo JText::_('OCTOBER');		?></option>
							<option value="11" ><?php echo JText::_('NOVEMBER');	?></option>
							<option value="12" ><?php echo JText::_('DECEMBER'); 	?></option>
						</select>
						
			    	<span> / </span>
		            <select name="payment_data[expiration_year]" class="input-small validate-rb-exp-date" 
		            		id="rb-processor-stripe-card-expiry-year" 
		            		data-rb-validate-error="#rb-processor-stripe-card-expiry-error"
		            		data-rb-validate="#rb-processor-stripe-card-expiry-month"
		            		data-rb-validate-type="year"
		            		>
	<!--					<option value="" selected="selected">YYYY </option>-->
						<?php for ( $i = 0; $i < 20 ; $i++ ):?>
							<option value="<?php  echo $year ?>" > <?php echo $year++; ?> </option>
						<?php endfor; ?>
					</select>
	            </div>
	            
	            <span id="rb-processor-stripe-card-expiry-error" class="rb-error hide"><?php echo  JText::_('PLG_RB_ECOMMERCEPROCESSOR_STRIPE_ERROR_EXPIRY_DATE_NOT_VALID'); ?> </span>	
	            
	        </div>
		
			 <div class="control-group">
				<div class="control-label">
					<label for="rb-processor-stripe-cvc-number">
				      	 <?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_STRIPE_FORM_STRIPE_CARD_CODE_LABEL');?>
				    </label>
		        </div>
		        <div class="controls">
		        	<input type="text" size="4" name="payment_data[card_code]" class="input-small validate-rb-cvc-length" id="rb-processor-stripe-cvc-number" data-rb-validate='#rb-processor-stripe-card-number'  required="true"  autocomplete="off"/>
		            <span class="add-on">
		            	<?php 
			            	//@TODO:: dont use hardcoded path
							echo Rb_Html::image('/plugins/rb_ecommerceprocessor/stripe/processors/stripe/layouts/cvc-code-icon.png', 'CVC Code', Array('style' =>"height:20px", 'title' => 'CVC Code'));
						?>
		            </span>
		        </div>
		        <span for="rb-processor-stripe-cvc-number" class="rb-error hide"><?php echo  JText::_('PLG_RB_ECOMMERCEPROCESSOR_STRIPE_ERROR_CVC_NOT_VALID'); ?></span>    
			</div>
		</div>
	</div>
