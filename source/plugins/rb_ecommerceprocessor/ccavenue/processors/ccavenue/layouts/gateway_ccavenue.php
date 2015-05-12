<?php
/**
* @copyright	Copyright (C) 2009 - 2015 Ready Bytes Software Labs Pvt. Ltd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* @package		ccavenue
* @subpackage	Frontend
* @contact 		support@readybytes.in
*/

if(defined('_JEXEC')===false) die();

JHtml::_('behavior.formvalidation');
?>

<div class="well ">
	
	<div class="row-fluid">
		<div class="text-center">
			<?php
				echo Rb_Html::image('plugins/rb_ecommerceprocessor/ccavenue/processors/ccavenue/layouts/ccavenue.png', ''); 
			?>	
		</div>
	
		<?php 
		$encrypted_data = $displayData['encRequest'];
		$access_code    = $displayData['access_code'];
		?>

		<input type=hidden name=encRequest value=<?php echo $encrypted_data; ?> >
		<input type=hidden name=access_code value=<?php echo $access_code; ?> >
				
 </div>
</div>
 