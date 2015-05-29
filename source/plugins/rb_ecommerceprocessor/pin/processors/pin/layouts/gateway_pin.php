<?php
/**
 * @copyright	Copyright (C) 2009 - 2015 Ready Bytes Software Labs Pvt. Ltd. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * @package		Rb_EcommerceProcessor
 * @subpackage	Pin
 * @contact		support@readybytes.in
 */
if(defined('_JEXEC')===false) die();
$year = date('Y');
?>
<div class="well ">
	
	<div class="row-fluid">
	
		<span class="payment-errors hide"></span>
		
		 <div class="control-group">
				<div class="control-label">
				    <label for="rb-processor-pin-card-number">
				      	<?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_PIN_CARD_NUMBER_LABEL');?>
				    </label>
			    </div>
		        <div class="controls">
		        	<input type="text"  size="20" id="rb-processor-pin-card-number" class="input-block-level validate-rb-credit-card" name="payment_data[card_number]" required="true" autocomplete="off" value=""/>
		        </div>
		        <span for="rb-processor-pin-card-number" class="rb-error hide"><?php echo  JText::_('PLG_RB_ECOMMERCEPROCESSOR_PIN_ERROR_CREDIT_CARD_NOT_VALID'); ?></span>
		</div>      
			
      	    <div class="control-group">
				<div class="control-label">
				    <label for="rb-processor-pin-card-expiry-year">
				      	<?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_PIN_EXPIRATION_MONTH_LABEL').'/'.JText::_("PLG_RB_ECOMMERCEPROCESSOR_PIN_EXPIRATION_YEAR_LABEL");?></span>
				    </label>
				 </div>
					<div class="controls">
			            <select name="payment_data[expiration_month]" class="input-small validate-rb-exp-date "  
			            		id="rb-processor-pin-card-expiry-month" 
			            		data-rb-validate-error="#rb-processor-pin-card-expiry-error"
			            		data-rb-validate="#rb-processor-pin-card-expiry-year"
			            		data-rb-validate-type="month">
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
			            		id="rb-processor-pin-card-expiry-year" 
			            		data-rb-validate-error="#rb-processor-pin-card-expiry-error"
			            		data-rb-validate="#rb-processor-pin-card-expiry-month"
			            		data-rb-validate-type="year"
			            		>
							<?php for ( $i = 0; $i < 20 ; $i++ ):?>
								<option value="<?php  echo $year ?>" > <?php echo $year++; ?> </option>
							<?php endfor; ?>
						</select>
					</div>
					
			<span id="rb-processor-pin-card-expiry-error" class="rb-error hide"><?php echo  JText::_('PLG_RB_ECOMMERCEPROCESSOR_PIN_ERROR_EXPIRY_DATE_NOT_VALID'); ?> </span>	
					
	        </div>
	       
       <div class="control-group">
				<div class="control-label">
					<label for="rb-processor-pin-cvc-number">
				      	<?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_PIN_CVC_CODE_LABEL');?>
				    </label>
		        </div>
		        <div class="controls">
		        	<input type="text" size="4" name="payment_data[card_code]" class="input-small validate-rb-cvc-length" id="rb-processor-pin-cvc-number" data-rb-validate='#rb-processor-pin-card-number'  required="true" class="input-small"  autocomplete="off" value=""/>
		            <span class="add-on">
		            	<?php 
			            	//@TODO:: dont use hardcoded path
							echo Rb_Html::image('plugins/rb_ecommerceprocessor/pin/processors/pin/layouts/cvc-code-icon.png', 'CVC Code', Array('style' =>"height:20px", 'title' => 'CVC Code'));
						?>
		            </span>
		        </div>
		        <span for="rb-processor-pin-cvc-number" class="rb-error hide"><?php echo  JText::_('PLG_RB_ECOMMERCEPROCESSOR_PIN_ERROR_CVC_NOT_VALID'); ?></span>        
		</div>
		
		
		 <div class="control-group">
				<div class="control-label">
		          	<label for="rb-processor-pin-card-name">
					      <span class="required-label"><?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_PIN_CARD_NAME_LABEL')?></span>
					</label>
				</div>
	          <div class="controls">
	          		<input type="text" class="input-block-level required" name="payment_data[card_name]" id="rb-processor-pin-card-name" value="" placeHolder="<?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_PIN_CARD_NAME_HELP')?>"></input>
	          </div>
	      	  <span for="rb-processor-pin-card-name" class="rb-error hide"><?php echo  JText::_('PLG_RB_ECOMMERCEPROCESSOR_PIN_ERROR_VALIDATION_REQUIRED'); ?></span>    
	          
        </div>
        
		<div class="control-group">
				<div class="control-label">
		          	<label for="rb-processor-pin-address">
					     <?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_PIN_ADDRESS_LABEL')?>
					</label>
				</div>
	            <div class="controls">
	          
	          		<textarea  class="input-block-level required " name="payment_data[address]" id="rb-processor-pin-address" value=""></textarea>
	          </div>
	     	 <span for="rb-processor-pin-address" class="rb-error hide"><?php echo  JText::_('PLG_RB_ECOMMERCEPROCESSOR_PIN_ERROR_VALIDATION_REQUIRED'); ?></span>  
	     
         </div>
        
        <div class="control-group">
				<div class="control-label">
		          	<label for="rb-processor-pin-country">
					      <?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_PIN_COUNTRY_LABEL')?>
					</label>
				</div>
	            <div class="controls">
	  				<?php echo PaycartHtmlCountry::getList("payment_data[country]", 'AU', "rb-processor-pin-country", array('class' => 'input-block-level required'),'isocode2'); ?>
	            </div>
	         
	         <span for="rb-processor-pin-country" class="rb-error hide"><?php echo  JText::_('PLG_RB_ECOMMERCEPROCESSOR_PIN_ERROR_VALIDATION_REQUIRED'); ?></span>  
        </div>
        
         <div class="control-group">
				<div class="control-label">
		          	<label for="rb-processor-pin-state">
					      <?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_PIN_STATE_LABEL')?>
					</label>
				</div>
	            <div class="controls">
	          		<input type="text" class="input-block-level required " name="payment_data[state]" id="rb-processor-pin-state" value=""></input>
	            </div>
                 
                <span for="rb-processor-pin-state" class="rb-error hide"><?php echo  JText::_('PLG_RB_ECOMMERCEPROCESSOR_PIN_ERROR_VALIDATION_REQUIRED'); ?></span>  
         </div>
        
         <div class="control-group">
			  <div class="control-label">
	          	<label for="rb-processor-pin-city">
				      <?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_PIN_CITY_LABEL')?>
				</label>
			  </div>
	          <div class="controls">
	          	<input type="text" class="input-block-level required " name="payment_data[city]" id="rb-processor-pin-city" value=""></input>
	          </div>
         
              <span for="rb-processor-pin-city" class="rb-error hide"><?php echo  JText::_('PLG_RB_ECOMMERCEPROCESSOR_PIN_ERROR_VALIDATION_REQUIRED'); ?></span>
        </div>
        
        <div class="control-group">
				<div class="control-label">
		          	<label for="rb-processor-pin-zip">
					      <?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_PIN_ZIP_LABEL')?>
					</label>
				</div>
	            <div class="controls">
	          		<input type="text" class="input-block-level required " name="payment_data[zip]" id="rb-processor-pin-zip" value=""></input>
	            </div>
                 
                <span for="rb-processor-pin-zip" class="rb-error hide"><?php echo  JText::_('PLG_RB_ECOMMERCEPROCESSOR_PIN_ERROR_VALIDATION_REQUIRED'); ?></span>  
         </div>
    </div>
</div>
<?php 