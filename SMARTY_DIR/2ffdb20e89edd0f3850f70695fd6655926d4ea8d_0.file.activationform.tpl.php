<?php
/* Smarty version 3.1.34-dev-7, created on 2021-01-03 21:10:42
  from 'C:\wamp64\www\Repositories\login\templates\activationform.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.34-dev-7',
  'unifunc' => 'content_5ff232d2d448e1_11364271',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '2ffdb20e89edd0f3850f70695fd6655926d4ea8d' => 
    array (
      0 => 'C:\\wamp64\\www\\Repositories\\login\\templates\\activationform.tpl',
      1 => 1609708240,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5ff232d2d448e1_11364271 (Smarty_Internal_Template $_smarty_tpl) {
?><div class="row">
    <div class="col-lg-4 offset-lg-4 col-md-4 offset-md-4 col-sm-12 mt-5">
        <div class="card card-chart">
            <div class="card-header">
                <i class="fas fa-user-circle"></i></i>&nbsp;&nbsp;<strong class="login-title">Activate Your Account</strong>
            </div>
            <div class="card-body justify-content-center">
				<form method="post" action="?activity=activate">
					<div class="row form-group justify-content-center">
						<input type="text" name="email" id="useractivation" tabindex="3" class="form-control" placeholder="Email" value="" autocomplete="off">
					</div>
					<div class="row form-group justify-content-center">
						<input type="text" name="authCode" id="activationcode" tabindex="4" class="form-control" placeholder="Activation code" autocomplete="off">
					</div>
					<div class="row form-group justify-content-center">
						<div class="row">
							<div class="col-sm-6 col-sm-offset-3">
								<input type="button" name="activate-submit" id="activate-submit" tabindex="4" class="btn btn-register text-muted font-weight-bold" value="Activate">
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div><?php }
}
