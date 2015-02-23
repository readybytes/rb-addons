<?php
/**
* @copyright	Copyright (C) 2009 - 2012 Ready Bytes Software Labs Pvt. Ltd. All rights reserved.
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
		<div class="control-group">
        	<div class="control-label">
			    <label for="rb-processor-mes-card-number"><?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_MES_FORM_MES_CARD_NUMBER_LABEL');?><span class="star">&nbsp;*</span></label>
			</div>
			
          	<div class="controls">
		        <input type="text"  size="20" id="rb-processor-mes-card-number" class="input-block-level validate-rb-credit-card" name="payment_data[card_number]" required="true" autocomplete="off"/>
        	</div>
        	<span for="rb-processor-mes-card-number" class="rb-error hide"><?php echo  JText::_('PLG_RB_ECOMMERCEPROCESSOR_MES_ERROR_CREDIT_CARD_NOT_VALID'); ?></span>
        </div>  
        
 	    <div class="control-group">
 	    	<div class="control-label">
			    <label for="rb-processor-mes-card-expiry-month rb-processor-mes-card-expiry-year">
			      <?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_MES_FORM_MES_EXPIRATION_MONTH_LABEL').'/'.JText::_("PLG_RB_ECOMMERCEPROCESSOR_MES_FORM_MES_EXPIRATION_YEAR_LABEL");?>
			     <span class="star">&nbsp;*</span>
			     </label>
			</div>
			
			<div  class="controls">
		            <select name="payment_data[exp_month]" class="input-small validate-rb-exp-date "  
		            		id="rb-processor-mes-card-expiry-month" 
		            		data-rb-validate-error="#rb-processor-mes-card-expiry-error"
		            		data-rb-validate="#rb-processor-mes-card-expiry-year"
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
		            <select name="payment_data[exp_year]" class="input-small validate-rb-exp-date" 
		            		id="rb-processor-mes-card-expiry-year" 
		            		data-rb-validate-error="#rb-processor-mes-card-expiry-error"
		            		data-rb-validate="#rb-processor-mes-card-expiry-month"
		            		data-rb-validate-type="year">

						<?php for ( $i = 0; $i < 20 ; $i++ ):?>
							<option value="<?php  echo $year ?>" > <?php echo $year++; ?> </option>
						<?php endfor; ?>
					</select>
			</div>
			<span id="rb-processor-mes-card-expiry-error" class="rb-error hide"><?php echo  JText::_('PLG_RB_ECOMMERCEPROCESSOR_MES_ERROR_EXPIRY_DATE_NOT_VALID'); ?></span>					
	    </div>
        
        	        
       	<div class="control-group">
			<div class="control-label">
				<label for="rb-processor-mes-cvn-number">
		       		<?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_MES_FORM_MES_CVN_NUMBER_LABEL');?>
		       		<span class="star">&nbsp;*</span>
		   		</label>
		    </div>
	        
	        <div class="controls">
	        	<input type="text" size="4" name="payment_data[cvv2]" class="required input-small validate-rb-cvc-length" id="rb-processor-mes-cvn-number" data-rb-validate='#rb-processor-mes-card-number'  required="true"  autocomplete="off"/>
	            <span class="add-on">
	            	<?php 
		            	//@TODO:: dont use hardcoded path
						echo Rb_Html::image('/plugins/rb_ecommerceprocessor/mes/processors/mes/layouts/cvc-code-icon.png', 'CVC Code', Array('style' =>"height:20px", 'title' => 'CVC Code'));
					?>
	            </span>
	        </div>
	        <span for="rb-processor-mes-cvn-number" class="rb-error hide"><?php echo  JText::_('PLG_RB_ECOMMERCEPROCESSOR_MES_ERROR_CVC_NOT_VALID'); ?></span>
		            
		</div>   
     
        
         <div  class="control-group" >
        	<div class="control-label">
	          	<label for="rb-processor-mes-address">
				    <?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_MES_FORM_MES_CARDHOLDER_STREET_ADDRESS_LABEL')?>
				    <span class="star">&nbsp;*</span>
				</label>
			</div>
            <div class="controls">
          		<textarea class="input-block-level required " name="payment_data[address]" id="rb-processor-mes-address" value=""></textarea>
            </div>
	         
	        <span for="rb-processor-mes-address" class="rb-error hide"><?php echo  JText::_('PLG_RB_ECOMMERCEPROCESSOR_MES_ERROR_VALIDATION_REQUIRED'); ?></span>  
		</div>    
        
		<div class="control-group" >
            <div class="control-label">
	    		<label for="rb-processor-mes-zip">
				   <?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_MES_FORM_MES_CARDHOLDER_ZIP_LABEL')?>
				   <span class="star">&nbsp;*</span>
				</label>
			</div>
	        <div class="controls">
	        	<input type="text" class="input-block-level required " id="rb-processor-mes-zip" name="payment_data[zip]" value=""></input>
	        </div>
	        <span for="rb-processor-mes-zip" class="rb-error hide"><?php echo  JText::_('PLG_RB_ECOMMERCEPROCESSOR_mes_ERROR_VALIDATION_REQUIRED'); ?></span>  
        </div> 
    </div>
</div>
<?php 