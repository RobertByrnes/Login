<?php
/* Smarty version 3.1.34-dev-7, created on 2021-01-04 20:19:42
  from 'C:\wamp64\www\Repositories\login\templates\password.change.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.34-dev-7',
  'unifunc' => 'content_5ff3785edff208_33596638',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '4cea299067489196f2f54aa62ab07ef2c9a2e33f' => 
    array (
      0 => 'C:\\wamp64\\www\\Repositories\\login\\templates\\password.change.tpl',
      1 => 1609791578,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5ff3785edff208_33596638 (Smarty_Internal_Template $_smarty_tpl) {
?><div class="row">
    <div class="col-lg-4 offset-lg-4 col-md-4 offset-md-4 col-sm-12 mt-5">
        <div class="card card-chart">
            <div class="card-header">
                <i class="fas fa-key"></i></i>&nbsp;&nbsp;<strong class="login-title">Change Your Password</strong>
            </div>
            <div class="card-body justify-content-center">
				<form method="post" action="?activity=change.password">
					<div class="row form-group justify-content-center">
						<input type="text" name="email" id="email" tabindex="3" class="form-control" placeholder="Enter email" value="" autocomplete="off">
					</div>
					<div class="row form-group justify-content-center">
						<input type="text" name="password1" id="password1" tabindex="4" class="form-control" placeholder="Enter current password" autocomplete="off">
					</div>
					<div class="row form-group justify-content-center">
						<input type="text" name="password2" id="password2" tabindex="4" class="form-control" placeholder="Re-type your current password" autocomplete="off">
					</div>
					<div class="row form-group justify-content-center">
						<input type="text" name="password3" id="password3" tabindex="4" class="form-control" placeholder="Enter new password" autocomplete="off">
					</div>
					<div class="row form-group justify-content-center">
						<div class="row">
							<div class="col-sm-6 col-sm-offset-3">
								<input type="button" name="change-password" id="change-password" tabindex="4" class="btn btn-register text-muted font-weight-bold" value="Change Password">
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div><?php }
}
