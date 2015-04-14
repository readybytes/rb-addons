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
$form = $displayData['form'];
$data = $displayData['data'];
?>
	
<div class="well ">
	<div class="row-fluid">
		<div class="text-center">
			<?php
				echo Rb_Html::image('/plugins/rb_ecommerceprocessor/payza/processors/payza/layouts/logo.png', ''); 
			?>	
		</div>
	</div>
</div>

<?php $fieldSets = $form->getFieldsets(); ?>
<?php foreach ($fieldSets as $name => $fieldSet) : ?>
<fieldset class="form-horizontal">
	<?php foreach ($form->getFieldset($name) as $field): ?>
		<?php echo $field->input; ?>	
	<?php endforeach;?>
</fieldset>
<?php endforeach;