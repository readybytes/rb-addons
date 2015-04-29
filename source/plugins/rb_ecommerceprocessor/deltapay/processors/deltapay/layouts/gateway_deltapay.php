<?php
/**
* @copyright	Copyright (C) 2009 - 2012 Ready Bytes Software Labs Pvt. Ltd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* @package 		Joomla.Plugin
* @subpackage	Rb_EcommerceProcessor.Stripe
* @contact		support@readybytes.in
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
$form = $displayData;
?>
	
<?php if($form['currency'] != 'EUR'):?>
	<div class="alert alert-error"><?php echo JText::_('PLG_RB_ECOMMERCEPROCESSOR_DELTAPAY_CURRENCY_NOT_SUPPORTED')?></div>
	<br>
<?php endif;?>
<div class="well ">
	<div class="row-fluid">
		<div class="text-center">
			<?php
				echo Rb_Html::image('plugins/rb_ecommerceprocessor/deltapay/processors/deltapay/layouts/logo.png', ''); 
			?>	
		</div>
	</div>
</div>

<input name="Guid1" id="rb_ecommerce_processor_deltapay_guid1" type="hidden" value="<?php echo  $displayData['Guid1']; ?>" />
<?php 
