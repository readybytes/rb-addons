<?php
/**
* @copyright	Copyright (C) 2009 - 2015 Ready Bytes Software Labs Pvt. Ltd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* @package		PayPlans
* @subpackage	Frontend
* @contact 		support@readybytes.in
*/

if(defined('_JEXEC')===false) die();

JHtml::_('behavior.formvalidation');
$year = date('Y');
?>

<div class="well ">
	
	<div class="row-fluid">
	
		<span class="payment-errors hide"></span>
		
		<fieldset>
			<legend><?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_AUTHORIZENET_FORM_AUTHORIZENET_FIELDSET_CARD_DETAILS');?></legend>
			<div class="control-group">
	        	<div class="control-label">
				    <label class="required" for="rb-processor-authorizeaim-card-number" title="<?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_AUTHORIZENET_FORM_AUTHORIZENET_CARD_NUMBER_DESC');?>">
				    	<?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_AUTHORIZENET_FORM_AUTHORIZENET_CARD_NUMBER_LABEL');?>
				    </label>
				</div>
				
	          	<div class="controls">
			        <input type="text"  size="20" id="rb-processor-authorizeaim-card-number" class="input-block-level validate-rb-credit-card" name="payment_data[card_number]" required="true" autocomplete="off"/>
	        	</div>
	        	<span for="rb-processor-authorizeaim-card-number" class="rb-error hide"><?php echo  JText::_('PLG_RB_ECOMMERCEPROCESSOR_AUTHORIZENET_ERROR_CREDIT_CARD_NOT_VALID'); ?></span>
	        </div>  
	        
	 	    <div class="control-group">
	 	    	<div class="control-label">
				    <label class="required" for="rb-processor-authorizeaim-card-expiry-month" title="<?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_AUTHORIZENET_FORM_AUTHORIZENET_EXPIRATION_DATE_DESC');?>">
				      <?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_AUTHORIZENET_FORM_AUTHORIZENET_EXPIRATION_MONTH_LABEL').' / '.JText::_("PLG_RB_ECOMMERCEPROCESSOR_AUTHORIZENET_FORM_AUTHORIZENET_EXPIRATION_YEAR_LABEL");?>
				     </label>
				</div>
				
				<div  class="controls">
			            <select name="payment_data[expiration_month]" class="input-small validate-rb-exp-date "  
			            		id="rb-processor-authorizeaim-card-expiry-month" 
			            		data-rb-validate-error="#rb-processor-authorizeaim-card-expiry-error"
			            		data-rb-validate="#rb-processor-authorizeaim-card-expiry-year"
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
			            		id="rb-processor-authorizeaim-card-expiry-year" 
			            		data-rb-validate-error="#rb-processor-authorizeaim-card-expiry-error"
			            		data-rb-validate="#rb-processor-authorizeaim-card-expiry-month"
			            		data-rb-validate-type="year">
	
							<?php for ( $i = 0; $i < 20 ; $i++ ):?>
								<option value="<?php  echo $year ?>" > <?php echo $year++; ?> </option>
							<?php endfor; ?>
						</select>
				</div>
					 <span id="rb-processor-authorizeaim-card-expiry-error" class="rb-error hide"><?php echo  JText::_('PLG_RB_ECOMMERCEPROCESSOR_AUTHORIZENET_ERROR_EXPIRY_DATE_NOT_VALID'); ?></span>
						
		     </div>
	        
	        	        
	       	<div class="control-group">
				<div class="control-label">
					<label for="rb-processor-authorizeaim-cvc-number" class="required" title="<?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_AUTHORIZENET_FORM_AUTHORIZENET_CARD_CODE_DESC');?>">
			       		<?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_AUTHORIZENET_FORM_AUTHORIZENET_CARD_CODE_LABEL');?>
			   		</label>
			    </div>
		        
		        <div class="controls">
		        	<input type="text" size="4" name="payment_data[card_code]" class="required input-small validate-rb-cvc-length" id="rb-processor-authorizeaim-cvc-number" data-rb-validate='#rb-processor-authorizeaim-card-number'  required="true"  autocomplete="off"/>
		            <span class="add-on">
		            	<?php 
			            	//@TODO:: dont use hardcoded path
							echo Rb_Html::image('plugins/rb_ecommerceprocessor/authorizenet/processors/authorize/layouts/cvc-code-icon.png', 'CVC Code', Array('style' =>"height:20px", 'title' => 'CVC Code'));
						?>
		            </span>
		        </div>
		        <span for="rb-processor-authorizeaim-cvc-number" class="rb-error hide"><?php echo  JText::_('PLG_RB_ECOMMERCEPROCESSOR_AUTHORIZEAIM_ERROR_CVC_NOT_VALID'); ?></span>
			            
			</div>
   		</fieldset>
   
   		<fieldset>
	   		<legend><?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_AUTHORIZENET_FORM_AUTHORIZENET_FIELDSET_CUSTOMER_DETAILS');?></legend>
	        <div class="control-group">
	        	<div class="control-label">
		          	<label for="rb-processor-authorizeaim-first_name" title="<?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_AUTHORIZENET_FORM_AUTHORIZENET_FIRST_NAME_DESC');?>">
					     <?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_AUTHORIZENET_FORM_AUTHORIZENET_FIRST_NAME_LABEL')?>
					</label>
				</div>
		        <div class="controls">
		          	<input type="text" class="input-block-level" name="payment_data[first_name]" id="rb-processor-authorizeaim-first_name" value=""></input>
		        </div> 	        
	          	<span for="rb-processor-authorizeaim-first_name" class="rb-error hide"><?php echo  JText::_('PLG_RB_ECOMMERCEPROCESSOR_AUTHORIZENET_ERROR_VALIDATION_REQUIRED'); ?></span>     
	        </div>
	        
	         <div class="control-group">
		      	   <div class="control-label">
		          		<label for="rb-processor-authorizeaim-last_name" title="<?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_AUTHORIZENET_FORM_AUTHORIZENET_LAST_NAME_DESC');?>">
					      <?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_AUTHORIZENET_FORM_AUTHORIZENET_LAST_NAME_LABEL')?>
						</label>
					</div>
		            <div class="controls">
		          		<input type="text" class="input-block-level"  id="rb-processor-authorizeaim-last_name" name="payment_data[last_name]" value=""></input>
		           </div> 
		          <span for="rb-processor-authorizeaim-last_name" class="rb-error hide"><?php echo  JText::_('PLG_RB_ECOMMERCEPROCESSOR_AUTHORIZENET_ERROR_VALIDATION_REQUIRED'); ?></span>        
	        </div>
	        
	         <div class="control-group">
	         	<div class="control-label">
		          	 <label for="rb-processor-authorizeaim-company" title="<?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_AUTHORIZENET_FORM_AUTHORIZENET_COMPANY_DESC');?>">
					    <?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_AUTHORIZENET_FORM_AUTHORIZENET_COMPANY_LABEL')?>
					 </label>
				 </div>
		         <div class="controls">
		          	<input type="text" class="input-block-level" name="payment_data[company]" id="rb-processor-authorizeaim-company" value=""></input>
		         </div>
		          
		         <span for="rb-processor-authorizeaim-company" class="rb-error hide"><?php echo  JText::_('PLG_RB_ECOMMERCEPROCESSOR_AUTHORIZENET_ERROR_VALIDATION_REQUIRED'); ?></span>  
		          
	        </div>
	        
			<div class="control-group">
				<div class="control-label">
		          <label for="rb-processor-authorizeaim-email" title="<?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_AUTHORIZENET_FORM_AUTHORIZENET_EMAIL_DESC');?>">
					    <?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_AUTHORIZENET_FORM_AUTHORIZENET_EMAIL_LABEL');?>
				  </label>
				</div>
		        <div class="controls">
		          		<input type="email" class="input-block-level  validate-email" name="payment_data[email]" id="rb-processor-authorizeaim-email" value=""></input>
		        </div>
		        <span for="rb-processor-authorizeaim-email" class="rb-error hide"><?php echo  JText::_('PLG_RB_ECOMMERCEPROCESSOR_AUTHORIZENET_ERROR_EMAIL_VALIDATION_REQUIRED'); ?></span>   
	        </div>
	        
	        <div class="control-group" >
		          <div class="control-label">
	            	<label for="rb-processor-authorizeaim-mobile" title="<?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_AUTHORIZENET_FORM_AUTHORIZENET_MOBILE_DESC');?>">
				    	<?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_AUTHORIZENET_FORM_AUTHORIZENET_MOBILE_LABEL');?>
			 	    </label>
			 	  </div>
		          <div class="controls">
		          	<input type="text" class="input-block-level" name="payment_data[mobile]" id="rb-processor-authorizeaim-mobile" value=""></input>
		          </div>
		         <span for="rb-processor-authorizeaim-mobile" class="rb-error hide"><?php echo  JText::_('PLG_RB_ECOMMERCEPROCESSOR_AUTHORIZENET_ERROR_VALIDATION_REQUIRED'); ?></span>  
	        </div>
	        
	         <div  class="control-group" >
	        	<div class="control-label">
		          	<label for="rb-processor-authorizeaim-address" title="<?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_AUTHORIZENET_FORM_AUTHORIZENET_ADDRESS_DESC');?>">
					    <?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_AUTHORIZENET_FORM_AUTHORIZENET_ADDRESS_LABEL')?>
					</label>
				</div>
	            <div class="controls">
	          		<textarea class="input-block-level" name="payment_data[address]" id="rb-processor-authorizeaim-address" value=""></textarea>
	            </div>
		         
		        <span for="rb-processor-authorizeaim-address" class="rb-error hide"><?php echo  JText::_('PLG_RB_ECOMMERCEPROCESSOR_AUTHORIZENET_ERROR_VALIDATION_REQUIRED'); ?></span>  
	        </div>

			<div class="control-group" >
             	<div class="control-label">
		          	<label for="rb-processor-authorizeaim-country" title="<?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_AUTHORIZENET_FORM_AUTHORIZENET_COUNTRY_DESC');?>">
					  	<?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_AUTHORIZENET_FORM_AUTHORIZENET_COUNTRY_LABEL')?>
				  	</label>
				</div>
		        <div class="controls">
		  			<?php echo Rb_EcommerceHtmlCountries::getList("payment_data[country]", '', "rb-processor-authorizeaim-country", array('class' => 'input-block-level required')); ?>
		        </div>
	         	<span for="rb-processor-authorizeaim-country" class="rb-error hide"><?php echo  JText::_('PLG_RB_ECOMMERCEPROCESSOR_AUTHORIZENET_ERROR_VALIDATION_REQUIRED'); ?></span>
        	</div>

			<div class="control-group" >
             	<div class="control-label">
		          	<label for="rb-processor-authorizeaim-state" title="<?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_AUTHORIZENET_FORM_AUTHORIZENET_STATE_DESC');?>">
					      <span class="required-label"><?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_AUTHORIZENET_FORM_AUTHORIZENET_STATE_LABEL')?></span>
					</label>
				</div>
			    <div class="controls">
			    	<input type="text" class="input-block-level" id="rb-processor-authorizeaim-state" name="payment_data[state]" value=""></input>
			    </div>
		        <span for="rb-processor-authorizeaim-state" class="rb-error hide"><?php echo  JText::_('PLG_RB_ECOMMERCEPROCESSOR_AUTHORIZENET_ERROR_VALIDATION_REQUIRED'); ?></span>    
	        </div>
	        
	        <div class="control-group" >
	             	<div class="control-label">
			          	<label for="rb-processor-authorizeaim-city" title="<?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_AUTHORIZENET_FORM_AUTHORIZENET_CITY_DESC');?>">
						    <?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_AUTHORIZENET_FORM_AUTHORIZENET_CITY_LABEL')?>
						</label>
					</div>
		            <div class="controls">
		           		<input type="text" class="input-block-level" name="payment_data[city]" id="rb-processor-authorizeaim-city" value=""></input>
		          	</div>
		         <span for="rb-processor-authorizeaim-city" class="rb-error hide"><?php echo  JText::_('PLG_RB_ECOMMERCEPROCESSOR_AUTHORIZENET_ERROR_VALIDATION_REQUIRED'); ?></span>       
	        </div>
	        
	        <div class="control-group" >
	             <div class="control-label">
		          	<label for="rb-processor-authorizeaim-zip" title="<?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_AUTHORIZENET_FORM_AUTHORIZENET_ZIP_DESC');?>">
					   <?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_AUTHORIZENET_FORM_AUTHORIZENET_ZIP_LABEL')?>
					</label>
				 </div>
		         <div class="controls">
		         	<input type="text" class="input-block-level" id="rb-processor-authorizeaim-zip" name="payment_data[zip]" value=""></input>
		         </div>
		         <span for="rb-processor-authorizeaim-zip" class="rb-error hide"><?php echo  JText::_('PLG_RB_ECOMMERCEPROCESSOR_AUTHORIZENET_ERROR_VALIDATION_REQUIRED'); ?></span>  
	        </div>	        
 		</fieldset>
    </div>
</div>

<?php 
