<?php
/**
* @copyright	Copyright (C) 2009 - 2015 Ready Bytes Software Labs Pvt. Ltd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* @package		RB Ecommerce Package
* @subpackage	Frontend
* @contact 		support@readybytes.in
*/

if(defined('_JEXEC')===false) die();

JHtml::_('behavior.formvalidation');
$year 		= date('Y');
$form_data	= $displayData;
?>
<div class="well ">
	<div class="row-fluid">
			<div class="text-center">
				<?php
					//@TODO:: dont use hardcoded path
					echo Rb_Html::image('plugins/rb_ecommerceprocessor/payumoney/processors/payumoney/layouts/logo.png', 'PayUMoney'); 
				?>	
			</div>
		</div>
	<div class="row-fluid">
		<span class="payment-errors hide"></span>
		<br/>
   		<fieldset>
	   	   <legend><?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_PAYUMONEY_FORM_PAYUMONEY_FIELDSET_CUSTOMER_DETAILS');?></legend>
	       <div class="control-group" >
		          <div class="control-label">
	            	<label for="phone" title="<?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_PAYUMONEY_FORM_PAYUMONEY_MOBILE_DESC');?>">
				    	<?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_PAYUMONEY_FORM_PAYUMONEY_MOBILE_LABEL');?>
			 	    </label>
			 	  </div>
		          <div class="controls">
		          	<input type="text" class="input-block-level required validate-rb-regex-pattern generateHash" name="phone" data-validate-pattern="^(?=.*\d.*\d.*\d)[0-9+\(\)#\.\s\/ext-]+$" id="phone" value="<?php echo $form_data['phone'];?>" <?php if(!empty($form_data['phone'])){ echo "readonly";}?>></input>
		          </div>
		         <span for="phone" class="rb-error hide"><?php echo  JText::_('PLG_RB_ECOMMERCEPROCESSOR_PAYUMONEY_ERROR_VALIDATION_REQUIRED'); ?></span>  
	        </div>
	        
	        <input type="hidden" name="key" value="<?php echo $form_data['key'];?>" />
	        <input type="hidden" name="txnid" value="<?php echo $form_data['txnid'];?>" />
	        <input type="hidden" name="amount" value="<?php echo $form_data['amount'];?>" />
	        <input type="hidden" name="firstname" value="<?php echo $form_data['firstname'];?>" />
	        <input type="hidden" name="email" value="<?php echo $form_data['email'];?>" />
	        <input type="hidden" name="productinfo" value="<?php echo htmlspecialchars($form_data['productinfo']);?>" />
	        <input type="hidden" name="surl" value="<?php echo $form_data['surl'];?>" />
	        <input type="hidden" name="furl" value="<?php echo $form_data['furl'];?>" />
	        <input type="hidden" name="curl" value="<?php echo $form_data['curl'];?>" />
	        <input type="hidden" name="hash" value="<?php echo $form_data['hash'];?>" />
	        <input type="hidden" name="udf1" value="<?php echo $form_data['udf1'];?>" />
	        <input type="hidden" name="service_provider" value="<?php echo $form_data['service_provider'];?>" />
 		</fieldset>
    </div>
</div>

<?php 
