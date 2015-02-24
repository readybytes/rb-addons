<?php
/**
 * @copyright	Copyright (C) 2009 - 2012 Ready Bytes Software Labs Pvt. Ltd. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * @package		PayPlans
 * @subpackage	Frontend
 * @contact 	payplans@readybytes.in


 */
if(defined('_JEXEC')===false) die();
$year = date('Y');
?>
<div class="well ">
	
	<div class="row-fluid">
	
		<span class="payment-errors hide"></span>
		
		 <div class="control-group">
				<div class="control-label">
				    <label for="rb-processor-paypalpro-card-number">
				      	<?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_PAYPALPRO_FORM_PAYPALPRO_CARD_NUMBER_LABEL');?>
				    </label>
			    </div>
		        <div class="controls">
		        	<input type="text"  size="20" id="rb-processor-paypalpro-card-number" class="input-block-level validate-rb-credit-card" name="payment_data[card_number]" required="true" autocomplete="off"/>
		        </div>
		        <span for="rb-processor-paypalpro-card-number" class="rb-error hide"><?php echo  JText::_('PLG_RB_ECOMMERCEPROCESSOR_PAYPALPRO_ERROR_CREDIT_CARD_NOT_VALID'); ?></span>
		</div>      
			
      	    <div class="control-group">
				<div class="control-label">
				    <label for="rb-processor-paypalpro-card-expiry-year">
				      	<?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_PAYPALPRO_FORM_PAYPALPRO_EXPIRATION_MONTH_LABEL').'/'.JText::_("PLG_RB_ECOMMERCEPROCESSOR_paypalpro_FORM_paypalpro_EXPIRATION_YEAR_LABEL");?></span>
				    </label>
				 </div>
					<div class="controls">
			            <select name="payment_data[expiration_month]" class="input-small validate-rb-exp-date "  
			            		id="rb-processor-paypalpro-card-expiry-month" 
			            		data-rb-validate-error="#rb-processor-paypalpro-card-expiry-error"
			            		data-rb-validate="#rb-processor-paypalpro-card-expiry-year"
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
			            		id="rb-processor-paypalpro-card-expiry-year" 
			            		data-rb-validate-error="#rb-processor-paypalpro-card-expiry-error"
			            		data-rb-validate="#rb-processor-paypalpro-card-expiry-month"
			            		data-rb-validate-type="year"
			            		>
		<!--					<option value="" selected="selected">YYYY </option>-->
							<?php for ( $i = 0; $i < 20 ; $i++ ):?>
								<option value="<?php  echo $year ?>" > <?php echo $year++; ?> </option>
							<?php endfor; ?>
						</select>
					</div>
					
			<span id="rb-processor-paypalpro-card-expiry-error" class="rb-error hide"><?php echo  JText::_('PLG_RB_ECOMMERCEPROCESSOR_PAYPALPRO_ERROR_EXPIRY_DATE_NOT_VALID'); ?> </span>	
					
	        </div>
	       
       <div class="control-group">
				<div class="control-label">
					<label for="rb-processor-paypalpro-cvc-number">
				      	<?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_PAYPALPRO_FORM_PAYPALPRO_CARD_CODE_LABEL');?>
				    </label>
		        </div>
		        <div class="controls">
		        	<input type="text" size="4" name="payment_data[card_code]" class="input-small validate-rb-cvc-length" id="rb-processor-paypalpro-cvc-number" data-rb-validate='#rb-processor-paypalpro-card-number'  required="true" class="input-small"  autocomplete="off"/>
		            <span class="add-on">
		            	<?php 
			            	//@TODO:: dont use hardcoded path
							echo Rb_Html::image('/plugins/rb_ecommerceprocessor/paypalpro/processors/paypalpro/layouts/cvc-code-icon.png', 'CVC Code', Array('style' =>"height:20px", 'title' => 'CVC Code'));
						?>
		            </span>
		        </div>
		        <span for="rb-processor-paypalpro-cvc-number" class="rb-error hide"><?php echo  JText::_('PLG_RB_ECOMMERCEPROCESSOR_PAYPALPRO_ERROR_CVC_NOT_VALID'); ?></span>        
		</div>
		
		
		 <div class="control-group">
				<div class="control-label">
		          	<label for="rb-processor-paypalpro-first_name">
					      <span class="required-label"><?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_PAYPALPRO_FORM_PAYPALPRO_FIRST_NAME_LABEL')?></span>
					</label>
				</div>
	          <div class="controls">
	          		<input type="text" class="input-block-level required " name="payment_data[first_name]" id="rb-processor-paypalpro-first_name" value=""></input>
	          </div>
	      	  <span for="rb-processor-paypalpro-first_name" class="rb-error hide"><?php echo  JText::_('PLG_RB_ECOMMERCEPROCESSOR_PAYPALPRO_ERROR_VALIDATION_REQUIRED'); ?></span>    
	          
        </div>
        
         <div class="control-group">
				<div class="control-label">
		          	<label for="rb-processor-paypalpro-last_name">
					      <span class="required-label"><?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_PAYPALPRO_FORM_PAYPALPRO_LAST_NAME_LABEL')?></span>
					</label>
				</div>
	          <div class="controls">
	          		<input type="text" class="input-block-level required " name="payment_data[last_name]" id="rb-processor-paypalpro-last_name" value=""></input>
	          </div>
	          <span for="rb-processor-paypalpro-last_name" class="rb-error hide"><?php echo  JText::_('PLG_RB_ECOMMERCEPROCESSOR_PAYPALPRO_ERROR_VALIDATION_REQUIRED'); ?></span>    
        </div>
        
		<div class="control-group">
				<div class="control-label">
		          	<label for="rb-processor-paypalpro-email">
					      <span class="required-label"><?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_PAYPALPRO_FORM_PAYPALPRO_EMAIL_LABEL')?></span>
					</label>
				</div>
	            <div class="controls">	
	          		<input type="email" class="input-block-level required validate-email" name="payment_data[email]" id="rb-processor-paypalpro-email" value=""></input>
	            </div>
	          <span for="rb-processor-paypalpro-email" class="rb-error hide"><?php echo  JText::_('PLG_RB_ECOMMERCEPROCESSOR_PAYPALPRO_ERROR_VALID_EMAIL_REQUIRED'); ?></span>    
        </div>
        
        <div class="control-group">
				<div class="control-label">
		          	<label for="rb-processor-paypalpro-mobile">
					      <?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_PAYPALPRO_FORM_PAYPALPRO_MOBILE_LABEL')?>
					</label>
				</div>
	          <div class="controls">
	          		<input type="text" class="input-block-level required " name="payment_data[mobile]" id="rb-processor-paypalpro-mobile" value=""></input>
	          </div>
	        	
	         <span for="rb-processor-paypalpro-mobile" class="rb-error hide"><?php echo  JText::_('PLG_RB_ECOMMERCEPROCESSOR_PAYPALPRO_ERROR_VALIDATION_REQUIRED'); ?></span>  
	         
        </div>
        
        <div class="control-group">
				<div class="control-label">
		          	<label for="rb-processor-paypalpro-address">
					     <?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_PAYPALPRO_FORM_PAYPALPRO_ADDRESS_LABEL')?>
					</label>
				</div>
	            <div class="controls">
	          
	          		<textarea  class="input-block-level required " name="payment_data[address]" id="rb-processor-paypalpro-address" value=""></textarea>
	          </div>
	     	 <span for="rb-processor-paypalpro-address" class="rb-error hide"><?php echo  JText::_('PLG_RB_ECOMMERCEPROCESSOR_PAYPALPRO_ERROR_VALIDATION_REQUIRED'); ?></span>  
	     
         </div>
        
        <div class="control-group">
				<div class="control-label">
		          	<label for="rb-processor-paypalpro-country">
					      <?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_PAYPALPRO_FORM_PAYPALPRO_COUNTRY_LABEL')?>
					</label>
				</div>
	            <div class="controls">
	  				<?php echo PaycartHtmlCountry::getList("payment_data[country]", '', "rb-processor-paypalpro-country", array('class' => 'input-block-level required')); ?>
	            </div>
	         
	         <span for="rb-processor-paypalpro-country" class="rb-error hide"><?php echo  JText::_('PLG_RB_ECOMMERCEPROCESSOR_PAYPALPRO_ERROR_VALIDATION_REQUIRED'); ?></span>  
        </div>
        
         <div class="control-group">
				<div class="control-label">
		          	<label for="rb-processor-paypalpro-state">
					      <?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_PAYPALPRO_FORM_PAYPALPRO_STATE_LABEL')?>
					</label>
				</div>
	            <div class="controls">
	          		<input type="text" class="input-block-level required " name="payment_data[state]" id="rb-processor-paypalpro-state" value=""></input>
	            </div>
                 
                <span for="rb-processor-paypalpro-state" class="rb-error hide"><?php echo  JText::_('PLG_RB_ECOMMERCEPROCESSOR_PAYPALPRO_ERROR_VALIDATION_REQUIRED'); ?></span>  
         </div>
        
           <div class="control-group">
				  <div class="control-label">
		          	<label for="rb-processor-paypalpro-city">
					      <?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_PAYPALPRO_FORM_PAYPALPRO_CITY_LABEL')?>
					</label>
				  </div>
		          <div class="controls">
		          	<input type="text" class="input-block-level required " name="payment_data[city]" id="rb-processor-paypalpro-city" value=""></input>
		          </div>
	         
	              <span for="rb-processor-paypalpro-city" class="rb-error hide"><?php echo  JText::_('PLG_RB_ECOMMERCEPROCESSOR_PAYPALPRO_ERROR_VALIDATION_REQUIRED'); ?></span>  
	          
        </div>
        
          <div class="control-group">
				<div class="control-label">
		          	<label for="rb-processor-paypalpro-zip">
					     <?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_PAYPALPRO_FORM_PAYPALPRO_ZIP_LABEL')?>
					</label>
				</div>
	          <div class="controls">    
	          	<input type="text" class="input-block-level required " name="payment_data[zip]" id="rb-processor-paypalpro-zip" value=""></input>
	          </div>
	          
	          <span for="rb-processor-paypalpro-zip" class="rb-error hide"><?php echo  JText::_('PLG_RB_ECOMMERCEPROCESSOR_PAYPALPRO_ERROR_VALIDATION_REQUIRED'); ?></span>  
        </div>
    
	</div>
</div>
<?php 
