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
	<form name="activate_first" id="activate_first" method="POST" action="">
    <input type="hidden" id="g-recaptcha-response" name="g-recaptcha-response">
    <input type="hidden" name="action" value="validate_captcha">
		<p><label>iLok User ID:  &nbsp; &nbsp;</label><input name="item_account_id" id="item_account_id" value="" type="text" autocorrect="off" autocapitalize="none"></p>
		<p><input class="et_pb_promo_button et_pb_button" type="submit" name="submitAccountId" id="first_submit" value="Get Standard Trial License"><input class="et_pb_promo_button et_pb_button" type="submit" name="submitPlusAccountId" id="plus_submit" value="Get Plus Trial License"></p>
	</form>
<?php
?>
