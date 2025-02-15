<?php



use itdq\JavaScript;
use vbac\personRecord;

$sql = " SELECT CNUM, FIRST_NAME FROM " . $GLOBALS['Db2Schema'] . ".PERSON WHERE FIRST_NAME is null and trim(REVALIDATION_STATUS) = '" . personRecord::REVALIDATED_FOUND . "' ";
$rs = sqlsrv_query($GLOBALS['conn'], $sql);
$firstNames = array();
while ($row = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC)){
    $firstNames[trim($row['CNUM'])] = trim($row['FIRST_NAME']);
}

echo count($firstNames);

JavaScript::buildArrayOfObjectsFromArrayOfRows($firstNames, 'firstNames', 'firstNamesObj');

?>

<script type="text/javascript">

$(document).ready(function(){
	for (var cnum in firstNamesObj) {
	    if (firstNamesObj.hasOwnProperty(cnum)) {
	        callBp(cnum);
	    }
	}

	
});


function callBp(cnum){
    console.log(cnum);

//  var urlOptions = "preferredidentity&preferredfirstname&hrfirstname&sn&hrfamilyname";
  var urlOptions = "givenname";
  if(cnum.length == 9){
      $.ajax({
        url: "api/bluepages.php?ibmperson/(uid=" + cnum + ").search/byjson?" + urlOptions ,
          type: 'GET',
        success: function(result){
        //  console.log(result);
          var personDetailsObj = JSON.parse(result);
          var attributes = personDetailsObj.search.entry[0].attribute;
          for(a=0;a<attributes.length;a++){
            var object = attributes[a];
            var value = object.value;
            var name = object.name;

            var regex = /[.]/;

            switch(name){
//             case 'preferredfirstname':
//             case 'hrfirstname':
            case 'givenname':    
	            var i=0;
                var firstName = value[i];
                while(regex.test(firstName) && i < value.length){
					firstName = value[++i];
                }
                capitalizedName = toTitleCase(firstName);
                console.log('First name for ' + cnum + ' is:' + capitalizedName);      
                $.ajax({
                	url: "ajax/setFirstNameForCnum.php" ,
                    type: 'POST',
                    data: {cnum:cnum,
                    	   firstname: capitalizedName },
                    success: function(result){
                          console.log(result);
                    }
               });
               break;
            default:
            }
          }
        },
          error: function (xhr, status) {
              // handle errors
            console.log('error');
            console.log(xhr);
            console.log(status);
          }
      });
  };

};


function toTitleCase(str) {
    return str.replace(/\w\S*/g, function(txt){
        return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();
    });
}





</script>