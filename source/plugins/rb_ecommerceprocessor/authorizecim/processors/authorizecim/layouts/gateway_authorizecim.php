<?php
/**
* @copyright	Copyright (C) 2009 - 2012 Ready Bytes Software Labs Pvt. Ltd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* @package		PayPlans
* @subpackage	Frontend
* @contact 		payplans@readybytes.in
*/

if(defined('_JEXEC')===false) die();

JHtml::_('behavior.formvalidation');
$year = date('Y');
?>

<div class="well ">
	
	<div class="row-fluid">
	
		<span class="payment-errors hide"></span>
		
		<div class="control-group">
        	<div class="control-label">
			    <label class="required" for="rb-processor-authorizecim-card-number"><?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_AUTHORIZECIM_FORM_AUTHORIZECIM_CARD_NUMBER_LABEL');?></label>
			</div>
			
          	<div class="controls">
		        <input type="text"  size="20" id="rb-processor-authorizecim-card-number" class="input-block-level validate-rb-credit-card" name="payment_data[card_number]" required="true" autocomplete="off"/>
        	</div>
        	<span for="rb-processor-authorizecim-card-number" class="rb-error hide"><?php echo  JText::_('PLG_RB_ECOMMERCEPROCESSOR_AUTHORIZECIM_ERROR_CREDIT_CARD_NOT_VALID'); ?></span>
        </div>  
        
 	    <div class="control-group">
 	    	<div class="control-label">
			    <label class="required" for="rb-processor-authorizecim-card-expiry-month">
			      <?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_AUTHORIZECIM_FORM_AUTHORIZECIM_EXPIRATION_MONTH_LABEL').'/'.JText::_("PLG_RB_ECOMMERCEPROCESSOR_AUTHORIZECIM_FORM_AUTHORIZECIM_EXPIRATION_YEAR_LABEL");?>
			     </label>
			</div>
			
			<div  class="controls">
		            <select name="payment_data[expiration_month]" class="input-small validate-rb-exp-date "  
		            		id="rb-processor-authorizecim-card-expiry-month" 
		            		data-rb-validate-error="#rb-processor-authorizecim-card-expiry-error"
		            		data-rb-validate="#rb-processor-authorizecim-card-expiry-year"
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
		            		id="rb-processor-authorizecim-card-expiry-year" 
		            		data-rb-validate-error="#rb-processor-authorizecim-card-expiry-error"
		            		data-rb-validate="#rb-processor-authorizecim-card-expiry-month"
		            		data-rb-validate-type="year">

						<?php for ( $i = 0; $i < 20 ; $i++ ):?>
							<option value="<?php  echo $year ?>" > <?php echo $year++; ?> </option>
						<?php endfor; ?>
					</select>
			</div>
				 <span id="rb-processor-authorizecim-card-expiry-error" class="rb-error hide"><?php echo  JText::_('PLG_RB_ECOMMERCEPROCESSOR_AUTHORIZECIM_ERROR_EXPIRY_DATE_NOT_VALID'); ?></span>
					
	     </div>
        
        	        
       	<div class="control-group">
			<div class="control-label">
				<label for="rb-processor-authorizecim-cvc-number" class="required">
		       		<?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_AUTHORIZECIM_FORM_AUTHORIZECIM_CARD_CODE_LABEL');?>
		   		</label>
		    </div>
	        
	        <div class="controls">
	        	<input type="text" size="4" name="payment_data[card_code]" class="required input-small validate-rb-cvc-length" id="rb-processor-authorizecim-cvc-number" data-rb-validate='#rb-processor-authorizecim-card-number'  required="true"  autocomplete="off"/>
	            <span class="add-on">
	            	<?php 
		            	//@TODO:: dont use hardcoded path
						echo Rb_Html::image('plugins/rb_ecommerceprocessor/authorizecim/processors/authorizecim/layouts/cvc-code-icon.png', 'CVC Code', Array('style' =>"height:20px", 'title' => 'CVC Code'));
					?>
	            </span>
	        </div>
	        <span for="rb-processor-authorizecim-cvc-number" class="rb-error hide"><?php echo  JText::_('PLG_RB_ECOMMERCEPROCESSOR_AUTHORIZECIM_ERROR_CVC_NOT_VALID'); ?></span>
		            
		</div>
   
   
        <div class="control-group">
        	<div class="control-label">
	          	<label for="rb-processor-authorizecim-first_name">
				     <?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_AUTHORIZECIM_FORM_AUTHORIZECIM_FIRST_NAME_LABEL')?>
				</label>
			</div>
	        <div class="controls">
	          	<input type="text" class="input-block-level" name="payment_data[first_name]" id="rb-processor-authorizecim-first_name" value=""></input>
	        </div> 	        
          	<span for="rb-processor-authorizecim-first_name" class="rb-error hide"><?php echo  JText::_('PLG_RB_ECOMMERCEPROCESSOR_AUTHORIZECIM_ERROR_VALIDATION_REQUIRED'); ?></span>     
        </div>
        
         <div class="control-group">
	      	   <div class="control-label">
	          		<label for="rb-processor-authorizecim-last_name">
				      <?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_AUTHORIZECIM_FORM_AUTHORIZECIM_LAST_NAME_LABEL')?>
					</label>
				</div>
	            <div class="controls">
	          		<input type="text" class="input-block-level"  id="rb-processor-authorizecim-last_name" name="payment_data[last_name]" value=""></input>
	           </div> 
	          <span for="rb-processor-authorizecim-last_name" class="rb-error hide"><?php echo  JText::_('PLG_RB_ECOMMERCEPROCESSOR_AUTHORIZECIM_ERROR_VALIDATION_REQUIRED'); ?></span>        
        </div>
        
         <div class="control-group">
         	<div class="control-label">
	          	 <label for="rb-processor-authorizecim-company">
				    <?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_AUTHORIZECIM_FORM_AUTHORIZECIM_COMPANY_LABEL')?>
				 </label>
			 </div>
	         <div class="controls">
	          	<input type="text" class="input-block-level" name="payment_data[company]" id="rb-processor-authorizecim-company" value=""></input>
	         </div>
	          
	         <span for="rb-processor-authorizecim-company" class="rb-error hide"><?php echo  JText::_('PLG_RB_ECOMMERCEPROCESSOR_AUTHORIZECIM_ERROR_VALIDATION_REQUIRED'); ?></span>  
	          
        </div>
        
		<div class="control-group">
			<div class="control-label">
	          <label for="rb-processor-authorizecim-email">
				    <?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_AUTHORIZECIM_FORM_AUTHORIZECIM_EMAIL_LABEL')?>
			  </label>
			</div>
	        <div class="controls">
	          		<input type="email" class="input-block-level  validate-email" name="payment_data[email]" id="rb-processor-authorizecim-email" value=""></input>
	        </div>
	        <span for="rb-processor-authorizecim-email" class="rb-error hide"><?php echo  JText::_('PLG_RB_ECOMMERCEPROCESSOR_AUTHORIZECIM_ERROR_EMAIL_VALIDATION_REQUIRED'); ?></span>   
        </div>
        
        <div class="control-group" >
	          <div class="control-label">
            	<label for="rb-processor-authorizecim-mobile">
			    	<?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_AUTHORIZECIM_FORM_AUTHORIZECIM_MOBILE_LABEL')?>
		 	    </label>
		 	  </div>
	          <div class="controls">
	          	<input type="text" class="input-block-level" name="payment_data[mobile]" id="rb-processor-authorizecim-mobile" value=""></input>
	          </div>
	         <span for="rb-processor-authorizecim-mobile" class="rb-error hide"><?php echo  JText::_('PLG_RB_ECOMMERCEPROCESSOR_AUTHORIZECIM_ERROR_VALIDATION_REQUIRED'); ?></span>  
        </div>
        
         <div  class="control-group" >
        	<div class="control-label">
	          	<label for="rb-processor-authorizecim-address">
				    <?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_AUTHORIZECIM_FORM_AUTHORIZECIM_ADDRESS_LABEL')?>
				</label>
			</div>
            <div class="controls">
          		<textarea class="input-block-level" name="payment_data[address]" id="rb-processor-authorizecim-address" value=""></textarea>
            </div>
	         
	        <span for="rb-processor-authorizecim-address" class="rb-error hide"><?php echo  JText::_('PLG_RB_ECOMMERCEPROCESSOR_AUTHORIZECIM_ERROR_VALIDATION_REQUIRED'); ?></span>  
        </div>
        
             <div class="control-group" >
             	<div class="control-label">
		          <label for="rb-processor-authorizecim-country">
					  <?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_AUTHORIZECIM_FORM_AUTHORIZECIM_COUNTRY_LABEL')?>
				  </label>
				</div>
		          <div class="controls">
		  			<?php echo PaycartHtmlCountry::getList("payment_data[country]", '', "rb-processor-authorizecim-country", array('class' => 'input-block-level required')); ?>
		          </div>
	         	<span for="rb-processor-authorizecim-country" class="rb-error hide"><?php echo  JText::_('PLG_RB_ECOMMERCEPROCESSOR_AUTHORIZECIM_ERROR_VALIDATION_REQUIRED'); ?></span>
        	</div>
             
          <div class="control-group" >
             	<div class="control-label">
		          	<label for="rb-processor-authorizecim-state">
					      <span class="required-label"><?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_AUTHORIZECIM_FORM_AUTHORIZECIM_STATE_LABEL')?></span>
					</label>
				</div>
	          <div class="controls">
	          	<input type="text" class="input-block-level" id="rb-processor-authorizecim-state" name="payment_data[state]" value=""></input>
	          </div>
	        <span for="rb-processor-authorizecim-state" class="rb-error hide"><?php echo  JText::_('PLG_RB_ECOMMERCEPROCESSOR_AUTHORIZECIM_ERROR_VALIDATION_REQUIRED'); ?></span>  
	         
        </div>
        
         <div class="control-group" >
             	<div class="control-label">
		          	<label for="rb-processor-authorizecim-city">
					    <?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_AUTHORIZECIM_FORM_AUTHORIZECIM_CITY_LABEL')?>
					</label>
				</div>
	            <div class="controls">
	           		<input type="text" class="input-block-level" name="payment_data[city]" id="rb-processor-authorizecim-city" value=""></input>
	          	</div>
	         <span for="rb-processor-authorizecim-city" class="rb-error hide"><?php echo  JText::_('PLG_RB_ECOMMERCEPROCESSOR_AUTHORIZECIM_ERROR_VALIDATION_REQUIRED'); ?></span>       
        </div>
        
		 <div class="control-group" >
             <div class="control-label">
	          	<label for="rb-processor-authorizecim-zip">
				   <?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_AUTHORIZECIM_FORM_AUTHORIZECIM_ZIP_LABEL')?>
				</label>
			 </div>
	          <div class="controls">
	          	  <input type="text" class="input-block-level" id="rb-processor-authorizecim-zip" name="payment_data[zip]" value=""></input>
	          </div>
	           <span for="rb-processor-authorizecim-zip" class="rb-error hide"><?php echo  JText::_('PLG_RB_ECOMMERCEPROCESSOR_AUTHORIZECIM_ERROR_VALIDATION_REQUIRED'); ?></span>  
        </div>
 
    </div>
</div>

<?php 
