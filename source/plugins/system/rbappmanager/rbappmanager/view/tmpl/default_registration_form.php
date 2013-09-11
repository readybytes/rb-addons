<?php

/**
* @copyright	Copyright (C) 2009 - 2012 Ready Bytes Software Labs Pvt. Ltd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* @package 		PAYINVOICE
* @subpackage	Back-end
* @contact		team@readybytes.in
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

?>
<div class="row-fluid">
	<div class="span11 offset1">
	<form class="form-horizontal">
	
		<?php if(isset($registration_error)):?>
			<div class="alert alert-error">
				<?php echo $registration_error;?>
    		</div>
		<?php endif;?>
		
		<fieldset>		
			<!-- Text input-->
			<div class="control-group">
				<label class="control-label"><?php echo Rb_Text::_('PLG_SYSTEM_RBAPPMANAGER_REGISTRATION_FORM_WINDOW_EMAIL');?></label>
				<div class="controls">
			    	<input id="rbappmanager-registration-email" name="rbappmanager_registration_email" class="required" type="text" value="">			    	
				</div>
			</div>
			
			<!-- Text input-->
			<div class="control-group">
				<label class="control-label"><?php echo Rb_Text::_('PLG_SYSTEM_RBAPPMANAGER_REGISTRATION_FORM_WINDOW_PASSWORD');?></label>
				<div class="controls">
			    	<input id="rbappmanager-registration-password" name="rbappmanager_registration_password" class="required" type="password">		    	
				</div>
			</div>
			
		</fieldset>
	</form>	
	</div>
</div>
<?php 