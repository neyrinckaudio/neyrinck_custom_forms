function disableBtn(){
  hideBtn();
  cleanUpErrorMsg(); 
}

function hideBtn(){
  jQuery("#second_submit").hide();
}

function cleanUpErrorMsg(){
  jQuery("#emailError").remove();
  jQuery("#addError").remove();
  jQuery("#iLokError").remove();
  jQuery("#fnameError").remove();
  jQuery("#lnameError").remove();
}

function validate_iLok_account(){
    var ilok_id = jQuery('#item_ilok_user_id').val();
    jQuery.ajax({
        type: "POST",
        data: { ilok : ilok_id},
        url : "/scripts/pace/ilok_validatiton.php",
        error : function(error){
          console.log(error);
          // alert("Error connecting to PACE.");
        },
        success: function(response)
          {   
            if (response == "Not found."){
              alert("This iLok User ID is not valid. Please enter a valid iLok User ID.");
            } 
          }
        
    });
  }
  


  var el = document.getElementById('submit_status');
  if (el){
    
    var status = jQuery(el).val();
    if ( status == 'error') { 
        jQuery("#first_submit").removeClass("download_btn");
        jQuery("#first_submit").addClass("download_btn_error");
        jQuery("#second_submit").removeClass("download_btn");
        jQuery("#second_submit").addClass("download_btn_error");
    } 
  }


 
var el = document.getElementById('submit_email_status');
if (el){
  var status = jQuery(el).val();
  if ( status == 'error') { 
      jQuery("#email").append("<span style='font-size: 0.8em; color: red';>Your email address was invalid.</span>");
      
  }
}

var el = document.getElementById('submit_email_match_status');
if (el){
  var status = jQuery(el).val();
  if ( status == 'error') { 
      jQuery("#email2").append("<span style='font-size: 0.8em; color: red';>Your email address does not match.</span>");
      
  }
}

var el = document.getElementById('submit_ilok_id_status');
if (el){
  var status = jQuery(el).val();
  if ( status == 'error') { 
      jQuery("#ilokId").append("<span style='font-size: 0.8em; color: red';>Please enter your iLok User ID.</span>");
      
  }
}

var el = document.getElementById('submit_firstname_status');
if (el){
  var status = jQuery(el).val();
  if ( status == 'error') { 
      jQuery("#firstname").append("<span style='font-size: 0.8em; color: red';>Please enter your first name.</span>");
      
  }
}

var el = document.getElementById('submit_lastname_status');
if (el){
  var status = jQuery(el).val();
  if ( status == 'error') { 
      jQuery("#lastname").append("<span style='font-size: 0.8em; color: red';>Please enter your last name.</span>");
      
  }
}



