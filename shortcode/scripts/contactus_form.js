/*  This script is included in OH add script plugin.
 *  Works in conjunction with forms/download_forms.php
**/
var el = document.getElementById('submit_status');
if (el){
var status = $(el).val();
   if ( status == 'error') { 
        $("#download_btn").removeClass("download_btn");
        $("#download_btn").addClass("download_btn_error");
   }
        
}

var el = document.getElementById('submit_security_code_status');
if (el){
var status = $(el).val();
   if ( status == 'error') { 
        $("#code").append("<span style='font-size: 0.8em; color: red';>Please prove that you are a human.</span>");
   }
}



var el = document.getElementById('submit_email_status');
if (el){
var status = $(el).val();
   if ( status == 'error') { 
        $("#email").append("<span style='font-size: 0.8em; color: red';>Your email address was invalid.</span>");
   }
}

var el = document.getElementById('submit_email_match_status');
if (el){
  var status = $(el).val();
   if ( status == 'error') { 
        $("#email2").append("<span style='font-size: 0.8em; color: red';>Your email address does not match.</span>");
        
   }
}

var el = document.getElementById('submit_firstname_status');
if (el){
var status = $(el).val();
   if ( status == 'error') { 
        $("#firstname").append("<span style='font-size: 0.8em; color: red';>Please enter your first name.</span>");
        
   }
}
var el = document.getElementById('submit_lastname_status');
if (el){
var status = $(el).val();
   if ( status == 'error') { 
        $("#lastname").append("<span style='font-size: 0.8em; color: red';>Please enter your last name.</span>");
        
   }
}
var el = document.getElementById('submit_country_status');
if (el){
var status = $(el).val();
   if ( status == 'error') { 
        $("#country").append("<span style='font-size: 0.8em; color: red';>Please specify a country.</span>");
        
   }
}

var el = document.getElementById('submit_message_status');
if (el){
var status = $(el).val();
   if ( status == 'error') { 
        $("#message").append("<span style='font-size: 0.8em; color: red';>Your message was blank.</span>");
        
   }
}

var el = document.getElementById('submit_department_status');
if (el){
var status = $(el).val();
   if ( status == 'error') { 
        $("#departments").append("<span style='font-size: 0.8em; color: red';>Please select a contact.</span>");
   }
}

