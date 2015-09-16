<?php

/**
* @copyright	Copyright (C) 2009 - 2012 Ready Bytes Software Labs Pvt. Ltd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* @package 		Joomla.Plugin
* @subpackage	Rb_EcommerceProcessor.BLUEPAY
* @contact		team@readybytes.in
* @author		Garima Agal
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
$year = date('Y');

$parts = explode(" ", $displayData->shipping_address->to);
$displayData->shipping_address->to
?>
	
	<div class="well ">
		<div class="row-fluid">
			<span class="payment-errors hide"></span>
			
			<div class="control-group">
				<div class="control-label">
				    <label for="rb-processor-bluepay-card-number" class="required">
				     	<?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_BLUEPAY_FORM_BLUEPAY_CARD_NUMBER_LABEL');?>
				    </label>
			    </div>
		        <div class="controls">
		        	<input type="text"  size="20" id="rb-processor-bluepay-card-number" class="input-block-level validate-rb-credit-card" name="payment_data[card_number]" required="true" autocomplete="off"/>
		        </div>
		        <span for="rb-processor-bluepay-card-number" class="rb-error hide"><?php echo  JText::_('PLG_RB_ECOMMERCEPROCESSOR_BLUEPAY_ERROR_CREDIT_CARD_NOT_VALID'); ?></span>
		        
			</div>
	
	       <div class="control-group">
				<div class="control-label">
				    <label class="required" for="rb-processor-bluepay-card-expiry-year">
				      	<?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_BLUEPAY_FORM_BLUEPAY_EXPIRATION_MONTH_LABEL').'/'.JText::_("PLG_RB_ECOMMERCEPROCESSOR_BLUEPAY_FORM_BLUEPAY_EXPIRATION_YEAR_LABEL");?>
				     </label>
				</div>
	
				<div class="controls">
		            <select name="payment_data[expiration_month]" class="input-small validate-rb-exp-date "  
		            		id="rb-processor-bluepay-card-expiry-month" 
		            		data-rb-validate-error="#rb-processor-bluepay-card-expiry-error"
		            		data-rb-validate="#rb-processor-bluepay-card-expiry-year"
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
		            		id="rb-processor-bluepay-card-expiry-year" 
		            		data-rb-validate-error="#rb-processor-bluepay-card-expiry-error"
		            		data-rb-validate="#rb-processor-bluepay-card-expiry-month"
		            		data-rb-validate-type="year"
		            		>
	<!--					<option value="" selected="selected">YYYY </option>-->
						<?php for ( $i = 0; $i < 20 ; $i++ ):?>
							<option value="<?php  echo $year ?>" > <?php echo $year++; ?> </option>
						<?php endfor; ?>
					</select>
	            </div>
	            
	            <span id="rb-processor-bluepay-card-expiry-error" class="rb-error hide"><?php echo  JText::_('PLG_RB_ECOMMERCEPROCESSOR_BLUEPAY_ERROR_EXPIRY_DATE_NOT_VALID'); ?> </span>	
	            
	        </div>
		
			 <div class="control-group">
				<div class="control-label">
					<label class="required" for="rb-processor-bluepay-cvc-number">
				      	 <?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_BLUEPAY_FORM_BLUEPAY_CARD_CODE_LABEL');?>
				    </label>
		        </div>
		        <div class="controls">
		        	<input type="text" size="4" name="payment_data[card_code]" class="input-small validate-rb-cvc-length" id="rb-processor-bluepay-cvc-number" data-rb-validate='#rb-processor-bluepay-card-number'  required="true"  autocomplete="off"/>
		            <span class="add-on">
		            	<?php 
			            	//@TODO:: dont use hardcoded path
							echo Rb_Html::image('plugins/rb_ecommerceprocessor/bluepay/processors/bluepay/layouts/cvc-code-icon.png', 'CVC Code', Array('style' =>"height:20px", 'title' => 'CVC Code'));
						?>
		            </span>
		        </div>
		        <span for="rb-processor-bluepay-cvc-number" class="rb-error hide"><?php echo  JText::_('PLG_RB_ECOMMERCEPROCESSOR_BLUEPAY_ERROR_CVC_NOT_VALID'); ?></span>    
			</div>
			
			<div class="control-group">
        	<div class="control-label">
	          	<label for="rb-processor-bluepay-first_name" class="required">
				     <?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_BLUEPAY_FORM_BLUEPAY_FIRST_NAME_LABEL')?>
				</label>
			</div>
	        <div class="controls">
	          	<input type="text" class="input-block-level" name="payment_data[first_name]" id="rb-processor-bluepay-first_name" value="<?php echo array_shift( $parts);?>"></input>
	        </div> 	        
          	<span for="rb-processor-bluepay-first_name" class="rb-error hide"><?php echo  JText::_('PLG_RB_ECOMMERCEPROCESSOR_BLUEPAY_ERROR_VALIDATION_REQUIRED'); ?></span>     
        </div>
        
         <div class="control-group">
	      	   <div class="control-label">
	          		<label for="rb-processor-bluepay-last_name" class="required">
				      <?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_BLUEPAY_FORM_BLUEPAY_LAST_NAME_LABEL')?>
					</label>
				</div>
	            <div class="controls">
	          		<input type="text" class="input-block-level"  id="rb-processor-bluepay-last_name" name="payment_data[last_name]" value="<?php echo array_pop($parts);?>"></input>
	           </div> 
	          <span for="rb-processor-bluepay-last_name" class="rb-error hide"><?php echo  JText::_('PLG_RB_ECOMMERCEPROCESSOR_BLUEPAY_ERROR_VALIDATION_REQUIRED'); ?></span>        
        </div>
        
           <div  class="control-group" >
        	<div class="control-label">
	          	<label for="rb-processor-bluepay-address" class="required">
				    <?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_BLUEPAY_FORM_BLUEPAY_ADDRESS_LABEL')?>
				</label>
			</div>
            <div class="controls">
          		<textarea class="input-block-level" name="payment_data[address]" id="rb-processor-bluepay-address"><?php echo $displayData->shipping_address->address;?></textarea>
            </div>
	         
	        <span for="rb-processor-bluepay-address" class="rb-error hide"><?php echo  JText::_('PLG_RB_ECOMMERCEPROCESSOR_BLUEPAY_ERROR_VALIDATION_REQUIRED'); ?></span>  
        </div>
        
        <div class="control-group" >
             	<div class="control-label">
		          <label for="rb-processor-bluepay-country" class="required">
					  <?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_BLUEPAY_FORM_BLUEPAY_COUNTRY_LABEL')?>
				  </label>
				</div>
		          <div class="controls">
		          <?php $defaultCountry =  $displayData->shipping_address->country->isocode3;
		  			 echo PaycartHtmlCountry::getList("payment_data[country]", $defaultCountry, "rb-processor-bluepay-country", array('class' => 'input-block-level required')); ?>
		          </div>
	         	<span for="rb-processor-bluepay-country" class="rb-error hide"><?php echo  JText::_('PLG_RB_ECOMMERCEPROCESSOR_BLUEPAY_ERROR_VALIDATION_REQUIRED'); ?></span>
        	</div>
             
          <div class="control-group" >
             	<div class="control-label">
		          	<label for="rb-processor-bluepay-state" class="required">
					      <span class="required-label"><?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_BLUEPAY_FORM_BLUEPAY_STATE_LABEL')?></span>
					</label>
				</div>
	          <div class="controls">
	          	<input type="text" class="input-block-level" id="rb-processor-bluepay-state" name="payment_data[state]" value="<?php echo $displayData->shipping_address->state;?>"></input>
	          </div>
	        <span for="rb-processor-bluepay-state" class="rb-error hide"><?php echo  JText::_('PLG_RB_ECOMMERCEPROCESSOR_BLUEPAY_ERROR_VALIDATION_REQUIRED'); ?></span>  
	         
        </div>
        
         <div class="control-group" >
             	<div class="control-label">
		          	<label for="rb-processor-bluepay-city" class="required">
					    <?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_BLUEPAY_FORM_BLUEPAY_CITY_LABEL')?>
					</label>
				</div>
	            <div class="controls">
	           		<input type="text" class="input-block-level" name="payment_data[city]" id="rb-processor-bluepay-city" value="<?php echo $displayData->shipping_address->city;?>"></input>
	          	</div>
	         <span for="rb-processor-bluepay-city" class="rb-error hide"><?php echo  JText::_('PLG_RB_ECOMMERCEPROCESSOR_BLUEPAY_ERROR_VALIDATION_REQUIRED'); ?></span>       
        </div>
        
		 <div class="control-group" >
             <div class="control-label">
	          	<label for="rb-processor-bluepay-zip" class="required">
				   <?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_BLUEPAY_FORM_BLUEPAY_ZIP_LABEL')?>
				</label>
			 </div>
	          <div class="controls">
	          	  <input type="text" class="input-block-level" id="rb-processor-bluepay-zip" name="payment_data[zip]" value="<?php echo $displayData->shipping_address->zipcode;?>"></input>
	          </div>
	           <span for="rb-processor-bluepay-zip" class="rb-error hide"><?php echo  JText::_('PLG_RB_ECOMMERCEPROCESSOR_BLUEPAY_ERROR_VALIDATION_REQUIRED'); ?></span>  
        </div>
        
        <div class="control-group">
			<div class="control-label">
	          <label for="rb-processor-bluepay-email" class="required">
				    <?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_BLUEPAY_FORM_BLUEPAY_EMAIL_LABEL')?>
			  </label>
			</div>
	        <div class="controls">
	          		<input type="email" class="input-block-level  validate-email" name="payment_data[email]" id="rb-processor-bluepay-email" value="<?php echo $displayData->email;?>"></input>
	        </div>
	        <span for="rb-processor-bluepay-email" class="rb-error hide"><?php echo  JText::_('PLG_RB_ECOMMERCEPROCESSOR_BLUEPAY_ERROR_EMAIL_VALIDATION_REQUIRED'); ?></span>   
        </div>
        
         <div class="control-group" >
	          <div class="control-label">
            	<label for="rb-processor-bluepay-phone" class="required">
			    	<?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_BLUEPAY_FORM_BLUEPAY_PHONE_LABEL')?>
		 	    </label>
		 	  </div>
	          <div class="controls">
	          	<input type="text" class="input-block-level" name="payment_data[phone]" id="rb-processor-bluepay-phone" value="<?php echo $displayData->shipping_address->phone_number;?>"></input>
	          </div>
	         <span for="rb-processor-bluepay-phone" class="rb-error hide"><?php echo  JText::_('PLG_RB_ECOMMERCEPROCESSOR_BLUEPAY_ERROR_VALIDATION_REQUIRED'); ?></span>  
        </div>
		</div>
	</div>