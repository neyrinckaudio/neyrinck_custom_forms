<?php


// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);


add_shortcode('NCF_PRODUCT_DOWNLOAD_FORM', 'product_download_form');
add_shortcode('NCF_THANK_YOU_FOR_DOWNLOADING_SPILL', 'thank_you_for_downloading_spill');
add_shortcode('NCF_ECHO_LATEST_VCP_DOWNLOAD_LINKS', 'echo_latest_vcp_download_links');
add_shortcode('NCF_SUPPORT_FORM', 'support_form');
add_shortcode('NCF_CONTACT_FORM', 'contact_form');
add_shortcode('NCF_NEWSLETTER_SUBSCRIPTION_FORM', 'newsletter_subscription_form');


function newsletter_subscription_form(){

	ob_start();
	include('subscription_form_reCaptcha.php');
	$form = ob_get_contents();
	ob_clean();
	ob_end_flush();

	return $form;

}



function contact_form(){
	ob_start();
	include('contact-form.php');
	$form = ob_get_contents();
	ob_clean();
	ob_end_flush();

	return $form;

}

function support_form(){
	ob_start();
	include('support-form.php');
	$form = ob_get_contents();
	ob_clean();
	ob_end_flush();

	return $form;

}

function product_download_form($atts){

	$product = $atts['product'];
	$downloadProduct =  $product;
	ob_start();
	switch ($product){
		case "Spill":
			include('download-redirect-thank-you-page.php');
			break;
		default:
			include('download-form.php');
	}

	$form = ob_get_contents();
	ob_clean();
	ob_end_flush();

	return $form;

}

function thank_you_for_downloading_spill(){
	ob_start();
	include('scripts/sms_spill.php');
	$form = ob_get_contents();
	ob_clean();
	ob_end_flush();

	return $form;
}

function echo_latest_vcp_download_links(){
	ob_start();
	include('scripts/vcp-download-links.php');
	$form = ob_get_contents();
	ob_clean();
	ob_end_flush();

	return $form;


}




?>