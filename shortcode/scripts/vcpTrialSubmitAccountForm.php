<?php
?>
<script src="https://www.google.com/recaptcha/api.js?render=6LfduNcUAAAAAMCW0-tF_IDCz2gew_xTS1wYW2Mh"></script>
<script>
    grecaptcha.ready(function() {
    // do request for recaptcha token
    // response is promise with passed token
        grecaptcha.execute('6LfduNcUAAAAAMCW0-tF_IDCz2gew_xTS1wYW2Mh', {action:'vcptrial'})
                  .then(function(token) {
            // add token value to form
            document.getElementById('g-recaptcha-response').value = token;
        });
    });
</script>
<p>V-Control Pro operates when unlicensed, but features are limited. You can register for a trial license to enable all features for a limited amount of time. You must have an iLok user account to get a trial license. www.ilok.com.</p>
	<form name="activate_first" id="activate_first" method="POST" action="">
    <input type="hidden" id="g-recaptcha-response" name="g-recaptcha-response">
    <input type="hidden" name="action" value="validate_captcha">
		<p>Please enter your ilok User Id.</p>
		<p><label>iLok User ID:  &nbsp; &nbsp;</label><input name="item_account_id" id="item_account_id" value="" type="text" autocorrect="off" autocapitalize="none"></p>
		<p><input class="et_pb_promo_button et_pb_button" type="submit" name="submitAccountId" id="first_submit" value="Submit iLok Account Id"></p>
	</form>
<?php
?>
