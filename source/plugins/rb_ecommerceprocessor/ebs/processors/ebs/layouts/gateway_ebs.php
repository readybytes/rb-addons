<?php
/**
* @copyright	Copyright (C) 2009 - 2015 Ready Bytes Software Labs Pvt. Ltd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* @package		PayPlans
* @subpackage	Frontend
* @contact 		paycart@readybytes.in
*/

if(defined('_JEXEC')===false) die();

?>

<script type="text/javascript">

(function($){
// START : 	
// Scoping code for easy and non-conflicting access to $.
// Should be first line, write code below this line.	

		$(document).ready(function() {
			// code to show and hide extra information at dashboard page.
			$(".rb-same-as-above").click(function(){
				
				var data = {};
				data['ship_name']	 	 = 'name';
				data['ship_address']   	 = 'address';
				data['ship_city']	 	 = 'city';
				data['ship_state']	 	 = 'state';
				data['ship_postal_code'] = 'postal_code';
				data['ship_phone']		 = 'phone';
				
				if($(this).is(':checked')){
					for(id in data){
						$("input[name='"+id+"']").val($("input[name='"+data[id]+"']").val());
					}
					$("select#rb-processor-ebs-ship-country").val($("select#rb-processor-ebs-country option:selected").val());
				}
				else{
					for(id in data){
						$("input[name='"+id+"']").val('');
					}
					$("select#rb-processor-ebs-ship-country").val('');
				}
				
			});
		});
			
// ENDING :
// Scoping code for easy and non-conflicting access to $.
// Should be last line, write code above this line.
})(paycart.jQuery);
</script>

<?php
JHtml::_('behavior.formvalidation');
$year = date('Y');
?>
<div class="well ">
	
	<div class="row-fluid">
	
		<span class="payment-errors hide"></span>

		
        <div class="control-group">
	          <div class="control-label">
	          		 <label for="rb-processor-ebs-name"><?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_EBS_FORM_EBS_NAME_LABEL')?></label>
	       
	          </div>
	          <div class="controls">
	           		 <input type="text" class="input-block-level required "  id="rb-processor-ebs-name" name="name" value=""></input>
	          </div>
	          <span for="rb-processor-ebs-name" class="rb-error hide"><?php echo  JText::_('PLG_RB_ECOMMERCEPROCESSOR_EBS_ERROR_VALIDATION_REQUIRED'); ?></span> 
        </div>
        
        
        <div class="control-group">
	          <div class="control-label">
	          		<label  for="rb-processor-ebs-address"><?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_EBS_FORM_EBS_ADDRESS_LABEL')?></label>
	          </div>
	          <div class="controls">
	           	 <input type="text" class="input-block-level required "  id="rb-processor-ebs-address" name="address" value=""></input>
	          </div>
	          <span for="rb-processor-ebs-address" class="rb-error hide"><?php echo  JText::_('PLG_RB_ECOMMERCEPROCESSOR_EBS_ERROR_VALIDATION_REQUIRED'); ?></span> 
        </div>
        
        
         <div class="control-group">
	          <div class="control-label">
	          		<label  for="rb-processor-ebs-city"><?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_EBS_FORM_EBS_CITY_LABEL')?></label>
	          </div>
	          <div class="controls">
	          	  <input type="text" class="input-block-level required "  id="rb-processor-ebs-city" name="city" value=""></input>
	          </div>
	          <span for="rb-processor-ebs-city" class="rb-error hide"><?php echo  JText::_('PLG_RB_ECOMMERCEPROCESSOR_EBS_ERROR_VALIDATION_REQUIRED'); ?></span> 
        </div>
        
         <div class="control-group">
	          <div class="control-label">
	          		<label  for="rb-processor-ebs-state"><?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_EBS_FORM_EBS_STATE_LABEL')?></label>
	          </div>
	          <div class="controls">
	          	  <input type="text" class="input-block-level required "  id="rb-processor-ebs-state" name="state" value=""></input>
	          </div>
	          <span for="rb-processor-ebs-state" class="rb-error hide"><?php echo  JText::_('PLG_RB_ECOMMERCEPROCESSOR_EBS_ERROR_VALIDATION_REQUIRED'); ?></span> 
        </div>
        
             <div class="control-group" >
             	<div class="control-label">
		          <label for="rb-processor-ebs-country">
					  <?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_EBS_FORM_FORM_EBS_COUNTRY_LABEL')?>
				  </label>
				</div>
		          <div class="controls">
		  			<?php echo PaycartHtmlCountry::getList("country", '', "rb-processor-ebs-country", array('class' => 'input-block-level required')); ?>
		          </div>
	         	<span for="rb-processor-ebs-country" class="rb-error hide"><?php echo  JText::_('PLG_RB_ECOMMERCEPROCESSOR_EBS_ERROR_VALIDATION_REQUIRED'); ?></span>
        	</div>
 
        
         <div class="control-group">
	          <div class="control-label">
	          		<label  for="rb-processor-ebs-postal_code"><?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_EBS_FORM_EBS_POSTAL_CODE_LABEL')?></label>
	          </div>
	          <div class="controls">
	          	  <input type="text" class="input-block-level required "  id="rb-processor-ebs-postal_code" name="postal_code" value=""></input>
	          </div>
	          <span for="rb-processor-ebs-postal_code" class="rb-error hide"><?php echo  JText::_('PLG_RB_ECOMMERCEPROCESSOR_EBS_ERROR_VALIDATION_REQUIRED'); ?></span> 
        </div>
        
         <div class="control-group">
	          <div class="control-label">
	          		<label  for="rb-processor-ebs-phone"><?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_EBS_FORM_EBS_PHONE_LABEL')?></label>
	          </div>
	          <div class="controls">
	          	  <input type="text" class="input-block-level required "  id="rb-processor-ebs-phone" name="phone" value=""></input>
	          </div>
	          <span for="rb-processor-ebs-phone" class="rb-error hide"><?php echo  JText::_('PLG_RB_ECOMMERCEPROCESSOR_EBS_ERROR_VALIDATION_REQUIRED'); ?></span> 
        </div>
        
         <div class="control-group">
	          <div class="control-label">
	          		<label  for="rb-processor-ebs-email"><?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_EBS_FORM_EBS_EMAIL_LABEL')?></label>
	          </div>
	          <div class="controls">
	          	  <input type="email" class="input-block-level required validate-email"  id="rb-processor-ebs-email" name="email" value=""></input>
	          </div>
	          <span for="rb-processor-ebs-email" class="rb-error hide"><?php echo  JText::_('PLG_RB_ECOMMERCEPROCESSOR_AUTHORIZECIM_ERROR_EMAIL_VALIDATION_REQUIRED'); ?></span> 
        </div>
        
	 <!--  Shipping detail     -->
	
	<legend><?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_EBS_FORM_EBS_SHIPPING_DETAILS');?></legend>
	
	 <label class="checkbox">
			<input type="checkbox" class="rb-same-as-above"><?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_EBS_FORM_EBS_SAME_AS_ABOVE')?>
	 </label>
       
          <div class="control-group">
	          <div class="control-label">
	          		 <label  for="rb-processor-ebs-ship-name"><?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_EBS_FORM_EBS_SHIP_NAME_LABEL')?></label>
	       
	          </div>
	          <div class="controls">
	           		 <input type="text" class="input-block-level required "  id="rb-processor-ebs-ship-name" name="ship_name" value=""></input>
	          </div>
	          <span for="rb-processor-ebs-ship-name" class="rb-error hide"><?php echo  JText::_('PLG_RB_ECOMMERCEPROCESSOR_EBS_ERROR_VALIDATION_REQUIRED'); ?></span> 
        </div>
        
        
        <div class="control-group">
	          <div class="control-label">
	          		<label  for="rb-processor-ebs-ship-address"><?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_EBS_FORM_EBS_SHIP_ADDRESS_LABEL')?></label>
	          </div>
	          <div class="controls">
	           	 <input type="text" class="input-block-level required "  id="rb-processor-ebs-ship-address" name="ship_address" value=""></input>
	          </div>
	          <span for="rb-processor-ebs-ship-address" class="rb-error hide"><?php echo  JText::_('PLG_RB_ECOMMERCEPROCESSOR_EBS_ERROR_VALIDATION_REQUIRED'); ?></span> 
        </div>
        
        
         <div class="control-group">
	          <div class="control-label">
	          		<label  for="rb-processor-ebs-ship-city"><?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_EBS_FORM_EBS_SHIP_CITY_LABEL')?></label>
	          </div>
	          <div class="controls">
	          	  <input type="text" class="input-block-level required "  id="rb-processor-ebs-ship-city" name="ship_city" value=""></input>
	          </div>
	          <span for="rb-processor-ebs-ship-city" class="rb-error hide"><?php echo  JText::_('PLG_RB_ECOMMERCEPROCESSOR_EBS_ERROR_VALIDATION_REQUIRED'); ?></span> 
        </div>
        
         <div class="control-group">
	          <div class="control-label">
	          		<label  for="rb-processor-ebs-ship-state"><?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_EBS_FORM_EBS_SHIP_STATE_LABEL')?></label>
	          </div>
	          <div class="controls">
	          	  <input type="text" class="input-block-level required "  id="rb-processor-ebs-ship-state" name="ship_state" value=""></input>
	          </div>
	          <span for="rb-processor-ebs-ship-state" class="rb-error hide"><?php echo  JText::_('PLG_RB_ECOMMERCEPROCESSOR_EBS_ERROR_VALIDATION_REQUIRED'); ?></span> 
        </div>
        
             <div class="control-group" >
             	<div class="control-label">
		          <label for="rb-processor-ebs-ship-country">
					  <?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_EBS_FORM_EBS_SHIP_COUNTRY_LABEL')?>
				  </label>
				</div>
		          <div class="controls">
		  			<?php echo PaycartHtmlCountry::getList("ship_country", '', "rb-processor-ebs-ship-country", array('class' => 'input-block-level required')); ?>
		          </div>
	         	<span for="rb-processor-ebs-ship-country" class="rb-error hide"><?php echo  JText::_('PLG_RB_ECOMMERCEPROCESSOR_EBS_ERROR_VALIDATION_REQUIRED'); ?></span>
        	</div>
 
        
         <div class="control-group">
	          <div class="control-label">
	          		<label  for="rb-processor-ebs-ship-postal_code"><?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_EBS_FORM_EBS_SHIP_POSTAL_CODE_LABEL')?></label>
	          </div>
	          <div class="controls">
	          	  <input type="text" class="input-block-level required "  id="rb-processor-ebs-ship-postal_code" name="ship_postal_code" value=""></input>
	          </div>
	          <span for="rb-processor-ebs-ship-postal_code" class="rb-error hide"><?php echo  JText::_('PLG_RB_ECOMMERCEPROCESSOR_EBS_ERROR_VALIDATION_REQUIRED'); ?></span> 
        </div>
        
         <div class="control-group">
	          <div class="control-label">
	          		<label  for="rb-processor-ebs-ship-phone"><?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_EBS_FORM_EBS_SHIP_PHONE_LABEL')?></label>
	          </div>
	          <div class="controls">
	          	  <input type="text" class="input-block-level required "  id="rb-processor-ebs-ship-phone" name="ship_phone" value=""></input>
	          </div>
	          <span for="rb-processor-ebs-ship-phone" class="rb-error hide"><?php echo  JText::_('PLG_RB_ECOMMERCEPROCESSOR_EBS_ERROR_VALIDATION_REQUIRED'); ?></span> 
        </div>
   
      <input type="hidden" name="amount" value="<?php echo $displayData['payment_data']['amount'];?>" />
      <input type="hidden" name="mode" value="<?php echo $displayData['payment_data']['mode'];?>" />
      <input type="hidden" name="account_id" value="<?php echo $displayData['payment_data']['account_id'];?>" />
      <input type="hidden" name="reference_no" value="<?php echo $displayData['payment_data']['reference_no'];?>" />
      <input type="hidden" name="description" value="<?php echo $displayData['payment_data']['description'];?>" />
      <input type="hidden" name="return_url" value="<?php echo $displayData['payment_data']['return_url'];?>" />
      <input name="secure_hash" type="hidden" size="60" value="<?php echo $displayData['payment_data']['secure_hash'];?>" />
  
</div>
</div>
<?php 
