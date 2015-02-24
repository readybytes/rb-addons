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
		
		    <div class="control-group" >
             	<div class="control-label"> 
		    		<label for="rb_ecommerce_processor_eway_cc_type">
		     	 		<?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_EWAY_FORM_CARD_TYPE_LABEL');?>
		    		</label>
		    	</div>

		        <div class="controls">
					<select name="cc_type" id ="rb_ecommerce_processor_eway_cc_type" class="input-block-level" >
								<option value="Visa"><?php echo JText::_("PLG_RB_ECOMMERCEPROCESSOR_EWAY_FORM_CARD_TYPE_VISA");?></option>
								<option value="MasterCard"><?php echo JText::_("PLG_RB_ECOMMERCEPROCESSOR_EWAY_FORM_CARD_TYPE_MASTERCARD");?></option>
	                            <option value="AmericanExpress"><?php echo JText::_("PLG_RB_ECOMMERCEPROCESSOR_EWAY_FORM_CARD_TYPE_AMERICANEXPRESS");?></option>
	                            <option value="DinersClub"><?php echo JText::_("PLG_RB_ECOMMERCEPROCESSOR_EWAY_FORM_CARD_TYPE_DINERSCLUB");?></option>
					</select>
				</div>
				<span for="rb_ecommerce_processor_eway_cc_type" class="rb-error hide"><?php echo  JText::_('PLG_RB_ECOMMERCEPROCESSOR_EWAY_ERROR_VALIDATION_REQUIRED'); ?></span>
						        
 		   </div>  
 		
 		
 		 <div class="control-group" >
             	<div class="control-label"> 
				    <label for="rb-processor-eway-card-name">
				     	<?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_EWAY_FORM_CARD_NAME_LABEL');?>
				    </label>
				  </div>
		        <div class="controls">
		       		 <input type="text"  size="20" class="input-block-level" name="payment_data[card_name]" required="true" id ="rb-processor-eway-card-name"/>
		        </div>
	        	<span for="rb-processor-eway-card-name" class="rb-error hide"><?php echo  JText::_('PLG_RB_ECOMMERCEPROCESSOR_EWAY_ERROR_VALIDATION_REQUIRED'); ?></span>
		</div> 
		
		 <div class="control-group" >
             	 <div class="control-label"> 
				    <label for="rb-processor-eway-card-number">
				      <?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_EWAY_FORM_CARD_NUMBER_LABEL');?>
				    </label>
				 </div>
		         <div class="controls">
		       		 <input type="text"  size="20" id="rb-processor-eway-card-number" class="input-block-level validate-rb-credit-card" name="payment_data[card_number]" required="true" autocomplete="off"/>
		       	 </div>	 
		        <span for="rb-processor-eway-card-number" class="rb-error hide"><?php echo  JText::_('PLG_RB_ECOMMERCEPROCESSOR_EWAY_ERROR_CREDIT_CARD_NOT_VALID'); ?></span>
		</div>      
			
        
      	     <div class="control-group" >
             	<div class="control-label"> 
			    	<label for="rb-processor-eway-card-expiry-month">
			      		<?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_EWAY_FORM_EXPIRATION_MONTH_LABEL').'/'.JText::_("PLG_RB_ECOMMERCEPROCESSOR_EWAY_FORM_EXPIRATION_YEAR_LABEL");?>
			    	</label>
			    </div>

           <div class="controls">	
           	 <select name="payment_data[expiration_month]" class="input-small validate-rb-exp-date "  
            		id="rb-processor-eway-card-expiry-month" 
            		data-rb-validate-error="#rb-processor-eway-card-expiry-error"
            		data-rb-validate="#rb-processor-eway-card-expiry-year"
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
            		id="rb-processor-eway-card-expiry-year" 
            		data-rb-validate-error="#rb-processor-eway-card-expiry-error"
            		data-rb-validate="#rb-processor-eway-card-expiry-month"
            		data-rb-validate-type="year"
            		>
				<?php for ( $i = 0; $i < 20 ; $i++ ):?>
					<option value="<?php  echo $year ?>" > <?php echo $year++; ?> </option>
				<?php endfor; ?>
			</select>					
		
		</div>
		<span id="rb-processor-eway-card-expiry-error" class="rb-error hide"><?php echo  JText::_('PLG_RB_ECOMMERCEPROCESSOR_EWAY_ERROR_EXPIRY_DATE_NOT_VALID'); ?> </span>	
        </div>
	        
	        
       	<div class="control-group" >
             	<div class="control-label"> 
					<label for="rb-processor-eway-cvc-number">
				      <?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_EWAY_FORM_CARD_CODE_LABEL');?>
				    </label>
		        </div>
		        <div class="controls">		        
		        	<input type="text" size="4" name="payment_data[card_code]" class="input-small validate-rb-cvc-length" id="rb-processor-eway-cvc-number" data-rb-validate='#rb-processor-eway-card-number'  required="true" autocomplete="off"/>
		            <span class="add-on">
		            	<?php 
			            	//@TODO:: dont use hardcoded path
							echo Rb_Html::image('/plugins/rb_ecommerceprocessor/eway/processors/eway/layouts/cvc-code-icon.png', 'CVC Code', Array('style' =>"height:20px", 'title' => 'CVC Code'));
						?>
		            </span>
		        </div>
		        <span for="rb-processor-eway-cvc-number" class="rb-error hide"><?php echo  JText::_('PLG_RB_ECOMMERCEPROCESSOR_EWAY_ERROR_CVC_NOT_VALID'); ?></span>
		            
		</div>
		
	
		 <div class="control-group" >
             	<div class="control-label"> 
		          	<label for="rb-processor-eway-name_title">
					    <?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_EWAY_FORM_TITLE_LABEL')?>
					</label>
				</div>
		          <div class="controls">
		          
		          	 <select name="name_title" id="rb-processor-eway-name_title" style="width:93px;">
		                <option value="Mr."><?php echo JText::_("PLG_RB_ECOMMERCEPROCESSOR_EWAY_FORM_TITLE_MR");?></option>
		               	<option value="Ms."><?php echo JText::_("PLG_RB_ECOMMERCEPROCESSOR_EWAY_FORM_TITLE_MS");?></option>
		                <option value="Mrs."><?php echo JText::_("PLG_RB_ECOMMERCEPROCESSOR_EWAY_FORM_TITLE_MRS");?></option>
		                <option value="Miss"><?php echo JText::_("PLG_RB_ECOMMERCEPROCESSOR_EWAY_FORM_TITLE_MISS");?></option>
		                <option value="Dr."><?php echo JText::_("PLG_RB_ECOMMERCEPROCESSOR_EWAY_FORM_TITLE_DR");?></option>
		                <option value="Sir."><?php echo JText::_("PLG_RB_ECOMMERCEPROCESSOR_EWAY_FORM_TITLE_SIR");?></option>
		                <option value="Prof."><?php echo JText::_("PLG_RB_ECOMMERCEPROCESSOR_EWAY_FORM_TITLE_PROF");?></option>		
		              </select>
		          </div>
          
          		<span for="rb-processor-eway-name_title" class="rb-error hide"><?php echo  JText::_('PLG_RB_ECOMMERCEPROCESSOR_EWAY_ERROR_VALIDATION_REQUIRED'); ?></span>  
        </div>
        
		

		   <div class="control-group" >
             	<div class="control-label"> 
          			<label for="rb-processor-eway-company">
			     		<?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_EWAY_FORM_COMPANY_NAME_LABEL')?>
					</label>
				</div>
		        <div class="controls">
		          	<input type="text" class="input-block-level required " name="payment_data[company]" id="rb-processor-eway-company" value=""></input>
		        </div>
          
         		 <span for="rb-processor-eway-company" class="rb-error hide"><?php echo  JText::_('PLG_RB_ECOMMERCEPROCESSOR_EWAY_ERROR_VALIDATION_REQUIRED'); ?></span>  
         </div>
        
          <div class="control-group" >
             	<div class="control-label"> 
          			<label for="rb-processor-eway-first_name">
			      		<?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_EWAY_FORM_FIRST_NAME_LABEL')?>
					</label>
				</div>
		        <div class="controls">  
		          	<input type="text" class="input-block-level required " name="payment_data[first_name]" id="rb-processor-eway-first_name" value=""></input>
		        </div>
          
           <span for="rb-processor-eway-first_name" class="rb-error hide"><?php echo  JText::_('PLG_RB_ECOMMERCEPROCESSOR_EWAY_ERROR_VALIDATION_REQUIRED'); ?></span>  
        </div>
        
         <div class="control-group" >
             	<div class="control-label"> 
          			<label for="rb-processor-eway-last_name">
			      		<?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_EWAY_FORM_LAST_NAME_LABEL')?>
					</label>
				</div>
		        <div class="controls">
		          	<input type="text" class="input-block-level required " name="payment_data[last_name]" id="rb-processor-eway-last_name" value=""></input>
		        </div>
          
          <span for="rb-processor-eway-last_name" class="rb-error hide"><?php echo  JText::_('PLG_RB_ECOMMERCEPROCESSOR_EWAY_ERROR_VALIDATION_REQUIRED'); ?></span>  
          
        </div>
        
		  <div class="control-group" >
             	 <div class="control-label"> 
          			<label for="rb-processor-eway-email">
			     		<?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_EWAY_FORM_EMAIL_LABEL')?>
					</label>
				 </div>
		          <div class="controls">
		          	<input type="email" class="input-block-level required " name="payment_data[email]" id="rb-processor-eway-email" value=""></input>
		          </div>
          
         <span for="rb-processor-eway-email" class="rb-error hide"><?php echo  JText::_('PLG_RB_ECOMMERCEPROCESSOR_EWAY_ERROR_EMAIL_VALIDATION_REQUIRED'); ?></span>  
          
        </div>
        
         <div class="control-group" >
             	 <div class="control-label"> 
          			<label for="rb-processor-eway-phone">
			     		<?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_EWAY_FORM_PHONE_LABEL')?>
					</label>
				</div>
		          <div class="controls">
		          	<input type="text" class="input-block-level required " name="payment_data[phone]" id="rb-processor-eway-phone" value=""></input>
		          </div>
          
          	<span for="rb-processor-eway-phone" class="rb-error hide"><?php echo  JText::_('PLG_RB_ECOMMERCEPROCESSOR_EWAY_ERROR_VALIDATION_REQUIRED'); ?></span>  
          
        </div>
        
         <div class="control-group" >
             	 <div class="control-label"> 
          			<label for="rb-processor-eway-mobile">
			      		<?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_EWAY_FORM_MOBILE_LABEL')?>
					</label>
				</div>
          	<div class="controls">
          		<input type="text" class="input-block-level required " name="payment_data[mobile]" id="rb-processor-eway-mobile" value=""></input>
          	</div>
          
          <span for="rb-processor-eway-mobile" class="rb-error hide"><?php echo  JText::_('PLG_RB_ECOMMERCEPROCESSOR_EWAY_ERROR_VALIDATION_REQUIRED'); ?></span>  
          
        </div>
        
        <div class="control-group" >
              <div class="control-label">
          		<label for="rb-processor-eway-fax">
		      		<?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_EWAY_FORM_FAX_LABEL')?>
				</label>
			 </div>
	          <div class="controls">
	          	<input type="text" class="input-block-level required " name="payment_data[fax]" id="rb-processor-eway-fax" value=""></input>
	          </div>
          
        	 <span for="rb-processor-eway-fax" class="rb-error hide"><?php echo  JText::_('PLG_RB_ECOMMERCEPROCESSOR_EWAY_ERROR_VALIDATION_REQUIRED'); ?></span>  
          
        </div>
        
        
         <div class="control-group" >
	              <div class="control-label">
		          	<label for="rb-processor-eway-address">
					      	<?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_EWAY_FORM_ADDRESS_LABEL')?>
					</label>
				  </div>
		          <div class="controls">
		          	<textarea  class="input-block-level required " name="payment_data[address]" id="rb-processor-eway-address" value=""></textarea>
		          </div>
          		  <span for="rb-processor-eway-address" class="rb-error hide"><?php echo  JText::_('PLG_RB_ECOMMERCEPROCESSOR_EWAY_ERROR_VALIDATION_REQUIRED'); ?></span>  
        </div>
        
		     
          <div class="control-group" >
	           <div class="control-label">
          			<label for="rb-processor-eway-country">
			    		<?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_EWAY_FORM_COUNTRY_LABEL')?>
					</label>
				</div>
	            <div class="controls">
		  			<?php echo PaycartHtmlCountry::getList("payment_data[country]", '', "rb-processor-eway-country", array('class' => 'input-block-level required')); ?>
		        </div>
          
          <span for="rb-processor-eway-country" class="rb-error hide"><?php echo  JText::_('PLG_RB_ECOMMERCEPROCESSOR_EWAY_ERROR_VALIDATION_REQUIRED'); ?></span>  
          
        </div>
        
        <div class="control-group" >
	           <div class="control-label">
		          	<label for="rb-processor-eway-state">
					      <?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_EWAY_FORM_STATE_LABEL')?>
					</label>
				</div>
		        <div class="controls">
		          	<input type="text" class="input-block-level required " name="payment_data[state]" id="rb-processor-eway-state" value=""></input>
		        </div>
          
          <span for="rb-processor-eway-state" class="rb-error hide"><?php echo  JText::_('PLG_RB_ECOMMERCEPROCESSOR_EWAY_ERROR_VALIDATION_REQUIRED'); ?></span>  
          
        </div>
        
        <div class="control-group" >
	           <div class="control-label">
          			<label for="rb-processor-eway-city">
			   			<?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_EWAY_FORM_CITY_LABEL')?>
			   		</label>
			   	</div>
	          <div class="controls">
	          	<input type="text" class="input-block-level required " name="payment_data[city]" id="rb-processor-eway-city" value=""></input>
	          </div>
          
         <span for="rb-processor-eway-city" class="rb-error hide"><?php echo  JText::_('PLG_RB_ECOMMERCEPROCESSOR_EWAY_ERROR_VALIDATION_REQUIRED'); ?></span>  
          
        </div>
  
        <div class="control-group" >
	           <div class="control-label">
          			<label for="rb-processor-eway-zip">
			      		<?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_EWAY_FORM_POST_CODE_LABEL')?>
					</label>
				</div>
          <div class="controls">
          
          	<input type="text" class="input-block-level required " name="payment_data[zip]" id="rb-processor-eway-zip" value=""></input>
          </div>
          
         <span for="rb-processor-eway-zip" class="rb-error hide"><?php echo  JText::_('PLG_RB_ECOMMERCEPROCESSOR_EWAY_ERROR_VALIDATION_REQUIRED'); ?></span>  
          
        </div>		
        
	</div>
</div>
<?php 
