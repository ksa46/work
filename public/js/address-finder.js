var accountKey = 'hr11-gh82-fj44-gw59';
var preferredLanguage = '';
var	filter   = '';
var userName = '';

$j("#post_code").click(function() {
    $j("#postcode-finder-return").css("display", "none");
});

$j('#post-code-find-button').click(function() {
    //var scriptTag = document.getElementById("PCAa73f9bc2b60d4e4cbd595512478a3291");
	$j('#address1').removeClass('validation-failed');
	$j('.post-code-error').html('');
	var scriptId = 'PCAa73f9bc2b60d4e4cbd595512478a3291';
    var scriptTag = $j('#'+scriptId).val();
    var headTag = $j("head");
	var searchTerm = $j.trim($j('#area_code').val());
	if (searchTerm == '') {
		$j('.post-code-error').html('Please enter your post code or company name');
		$j('.post-code-error').css('visibility', 'visible');
	    $j("#postcode-finder-return").css("display", "none"); 
		return false;
	}
    var strUrl = "";

	// Build the url
	strUrl = "http://services.postcodeanywhere.co.uk/PostcodeAnywhere/Interactive/Find/v1.10/json.ws?";
	strUrl += "&Key=" + encodeURI(accountKey);
	strUrl += "&SearchTerm=" + encodeURI(searchTerm);
	strUrl += "&PreferredLanguage="
			+ encodeURI(preferredLanguage);
	strUrl += "&Filter=" + encodeURI(filter);
	strUrl += "&UserName=" + encodeURI(userName);
	strUrl += "&CallbackFunction=postcodeFindResponse";

	if (scriptTag) {
		try {
			headTag.remove(scriptTag);
		} catch (e) {
			// Ignore
		}
	}
	scriptTag = document.createElement("script");
	scriptTag.src = strUrl
	scriptTag.type = "text/javascript";
	scriptTag.id = scriptId;
	headTag.append(scriptTag);
});
function postcodeFindResponse(response) {
	// Test for an error
	if (response.length == 1 && typeof (response[0].Error) != 'undefined') {
		// Show the error message
		$j('.post-code-error').html(response[0].Description);
		$j('.post-code-error').css('visibility', 'visible');
	    $j("#postcode-finder-return").css("display", "none"); 
	} else {
		// Check if there were any items found
		if (response.length == 0) {
			$j('.post-code-error').html('Sorry, no matching post code found.Please enter your address manually');
			$j('.post-code-error').css('visibility', 'visible');
		} else {
			document.getElementById('postcode-finder-return').style.display = '';
			document.getElementById('postcode-finder-return').options.length = 0;
			document.getElementById('postcode-finder-return').options.add(new Option("Select address" , 0));
					//+ response[i].Place, response[i].Id));
			for ( var i = 0; i < response.length; i++)
				document.getElementById('postcode-finder-return').options
						.add(new Option(response[i].StreetAddress + ", "
								+ response[i].Place, response[i].Id));
		}
	}
}
$j('#postcode-finder-return').change(function() {
	var scriptId = 'PCA6d35cfc188f1451f9cfdf1b5d751a716';
    var scriptTag = $j('#'+scriptId).val();
    var headTag = $j("head");
    var strUrl = "";
    var searchId = $j('#postcode-finder-return').val();

    //Build the url
    strUrl = "http://services.postcodeanywhere.co.uk/PostcodeAnywhere/Interactive/RetrieveById/v1.10/json.ws?";
    strUrl += "&Key=" + encodeURI(accountKey);
    strUrl += "&Id=" + encodeURI(searchId);
    strUrl += "&PreferredLanguage=" + encodeURI(preferredLanguage);
    strUrl += "&UserName=" + encodeURI(userName);
    strUrl += "&CallbackFunction=idFindResponse";

    //Make the request
    if (scriptTag) {
          try {
        	  headTag.removeChild(scriptTag);
          }
          catch (e) { }
    }
    scriptTag = document.createElement("script");
    scriptTag.src = strUrl
    scriptTag.type = "text/javascript";
    scriptTag.id = scriptId;
    headTag.append(scriptTag);
});

function idFindResponse(response) {
    //Test for an error
    if (response.length==1 && typeof(response[0].Error) != 'undefined')
       {
          //Show the error message
          $j('.post-code-error').html(response[0].Description);
          $j('.post-code-error').css('visibility', 'visible');
	      $j("#postcode-finder-return").css("display", "none"); 
       }
    else
       {
          //Check if there were any items found
          if (response.length==0)
             {
                $j('.post-code-error').html('Sorry, no matching items found.Please enter your address manually');
                $j('.post-code-error').css('visibility', 'visible');
			    $j("#postcode-finder-return").css("display", "none"); 
             }
          else
             {
        	  	  var addressId    = response[0].Udprn;
        	      var houseNumber  = response[0].Company;
			      var addressLine1 = response[0].Line1;
			   
			      var addressLine2 = response[0].Line2 ;
			      if (addressLine2 != '' && response[0].Line3 != '') {
				     addressLine2 = addressLine2 +  ' , ' +response[0].Line3;
			      }
			      var postTown     = response[0].PostTown;
			      var county       = response[0].County
			      var postCode     = response[0].Postcode; 

			     //$j('#address_id').val(addressId);
			      $j('#address1').val(addressLine1);
			      $j('#address2').val(addressLine2);
			      $j('#city').val(postTown);
			      $j('#region').val(county);
			      $j('#post_code').val(postCode);
			      $j("#postcode-finder-return").css("display", "none"); 
             }
       }
}