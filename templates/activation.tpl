<div class="row">
    <div class="col-lg-4 offset-lg-4 col-md-4 offset-md-4 col-sm-12 mt-5">
        <div class="card card-chart">
            <div class="card-header">
                <i class="fas fa-user-circle"></i></i>&nbsp;&nbsp;<strong class="login-title">Activate Your Account</strong>
            </div>
            <div class="card-body justify-content-center">
				<form method="post" action="?activity=activate">
					<div class="row form-group justify-content-center">
						<input type="text" name="email" id="useractivation" tabindex="3" class="form-control" placeholder="Enter email address and click activate." value="" autocomplete="off">
					</div>
					<div class="row form-group justify-content-center">
						<input type="text" name="authCode" id="activationcode" tabindex="4" class="form-control"
							{if isset($templateData.authCode)} value="{$templateData.authCode}"{/if}
							placeholder="Activation code" autocomplete="off">
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
</div>