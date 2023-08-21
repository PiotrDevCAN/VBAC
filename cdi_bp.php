<?php
use itdq\BluePages;

$people = array();
$allCnums = array();
$details = array();

$sql = " Select distinct cnum, first_name, last_name, employee_type from vbac.person ";
$rs = sqlsrv_query($GLOBALS['conn'], $sql);

while (($row=sqlsrv_fetch_array($rs))==true ){
    $people[$row['CNUM']] = $row ;
    $allCnums[] = $row['CNUM'];
}

// echo "<pre>";
// print_r($people);
// print_r($allCnums);
// echo "</pre>";

$bpParms = "preferredidentity&jobresponsibilities&notesemail&uid&preferredfirstname&hrfirstname&sn&hrfamilyname&ismanager&phonemailnumber&employeetype&co&ibmloc";

$bpDefinition = array();
$allEmployeeTypes = array();


// $allCnums = array("00064M744","00076Q744","00107S744","00235V744","0076A2744","00817V744","01506R744");


echo "<pre>";

$details = BluePages::getDetailsFromCnumSlapMulti($allCnums,$bpParms);

echo "<hr/>";
$cnum="";
foreach ($details->search->entry as $bpEntry){
    foreach($bpEntry as $individualAttributes){
        if(is_array($individualAttributes)){
            foreach ($individualAttributes as $object){

                switch($object->name){
                    case "preferredfirstname":
                    case "hrfirstname":
                        $bpDefinition[$cnum]['first name'] = $object->value[0];
                        $bpDefinition[$cnum][$object->name] = $object->value[0];
                        break;
                    case "sn":
                    case "hrfamilyname":
                        $bpDefinition[$cnum]['surname'] = $object->value[0];
                        $bpDefinition[$cnum][$object->name] = $object->value[0];
                        break;
                    default:
                        break;
                }

                if($object->name=='employeetype'){
                    $allEmployeeTypes[$object->value[0]] = $object->value[0];
                }
            }
        } else {
            echo "<b>" . $individualAttributes . "</b>";
            $cnum = substr($individualAttributes,4,9);
        }

    }
    echo "<hr/>";
}

foreach ($bpDefinition as $cnum => $attributes){
    echo "<br/><b>$cnum</b>";
    $preferredfirstname = isset($attributes['preferredfirstname']) ? $attributes['preferredfirstname'] : "Not provided";
    echo "<br/>preferredfirstname: " . $preferredfirstname;

    $hrfirstname =   isset($attributes['hrfirstname']) ?$attributes['hrfirstname'] : "Not provided";
    echo "<br/>hrfirstname: " . $hrfirstname;

    $sn =  isset($attributes['sn']) ?$attributes['sn'] : "Not provided";
    echo "<br/>sn: " . $sn;

    $hrfamilyname = isset($attributes['hrfamilyname']) ?$attributes['hrfamilyname'] : "Not provided";
    echo "<br/>hrfamilyname: " .  $hrfamilyname;

    echo "<br/>First Name: " . $attributes['first name'] . " Surname:" . $attributes['surname'];
    echo "<br/>Combined: $cnum : <b>" . $attributes['first name'] . " " . $attributes['surname'] . "</b>";
 }

echo "<br/>";
print_r($allEmployeeTypes);


echo "</pre>";