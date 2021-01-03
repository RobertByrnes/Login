<?php
/* Smarty version 3.1.36, created on 2021-01-03 18:43:50
  from 'C:\wamp64\www\repositories\login\templates\activationform.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.36',
  'unifunc' => 'content_5ff21066262af8_00527960',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '53b061f845b239a64bc0046e5240c9a8b474e919' => 
    array (
      0 => 'C:\\wamp64\\www\\repositories\\login\\templates\\activationform.tpl',
      1 => 1609699428,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5ff21066262af8_00527960 (Smarty_Internal_Template $_smarty_tpl) {
?><div class="row">
    <div class="col-lg-4 offset-lg-4 col-md-4 offset-md-4 col-sm-12 mt-5">
        <div class="card card-chart">
            <div class="card-header">
                <i class="fas fa-user-circle"></i></i>&nbsp;&nbsp;<strong class="login-title">Activate Your Account</strong>
            </div>
            <div class="card-body justify-content-center">
				<div class="form-group">
					<input type="text" name="username" id="useractivation" tabindex="3" class="form-control" placeholder="Email" value="" autocomplete="off">
				</div>
				<div class="form-group">
					<input type="text" name="code" id="activationcode" tabindex="4" class="form-control" placeholder="Activation code" autocomplete="off">
				</div>
				<div class="form-group">
					<div class="row">
						<div class="col-sm-6 col-sm-offset-3">
							<input type="button" name="activate-submit" id="activate-submit" tabindex="4" class="btn btn-register text-muted font-weight-bold" value="Send">
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div><?php }
}
