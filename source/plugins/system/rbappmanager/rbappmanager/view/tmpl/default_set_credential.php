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
	
		<?php if(isset($verification_error)):?>
			<div class="alert alert-error">
				<?php echo $verification_error;?>
    		</div>
		<?php endif;?>
		
		<fieldset>		
			<!-- Text input-->
			<div class="control-group">
				<label class="control-label"><?php echo Rb_Text::_('PLG_SYSTEM_RBAPPMANAGER_SET_CREDENTIAL_WINDOW_EMAIL');?></label>
				<div class="controls">
			    	<input id="rbappmanager-credential-email" name="rbappmanager_credential_email" class="required" type="text" value="<?php echo $credential['email'];?>">			    	
				</div>
			</div>
			
			<!-- Text input-->
			<div class="control-group">
				<label class="control-label"><?php echo Rb_Text::_('PLG_SYSTEM_RBAPPMANAGER_SET_CREDENTIAL_WINDOW_PASSWORD');?></label>
				<div class="controls">
			    	<input id="rbappmanager-credential-password" name="rbappmanager_credential_password" class="required" type="password">		    	
				</div>
			</div>
			
		</fieldset>
	</form>
	
	<div class="row-fluid">
		<a href="#" onclick="return rbappmanager.registration.form();">Register</a>
	</div>
	
	</div>
</div>
<?php 