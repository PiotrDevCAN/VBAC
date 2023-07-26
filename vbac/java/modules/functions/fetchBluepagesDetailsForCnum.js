/**
 *
 */

let toTitleCase = await cacheBustImport('./modules/functions/toTitleCase.js');
let convertOceanToKyndryl = await cacheBustImport('./modules/functions/convertOceanToKyndryl.js');

function fetchBluepagesDetailsForCnum(cnum) {
    if (typeof cnum !== "undefined") {
        if (cnum.length == 9) {
            var urlOptions = "preferredidentity&jobresponsibilities&notesemail&uid&givenname&sn&ismanager&employeetype&co&ibmloc";
            $.ajax({
                // url: "https://bluepages.ibm.com/BpHttpApisv3/slaphapi?ibmperson/(uid=" + cnum + ").search/byjson?" + urlOptions ,
                // url: "https://bluepages.ibm.com/BpHttpApisv3/wsapi?byCnum=" + cnum,
                url:
                    "api/bluepages.php?ibmperson/(uid=" +
                    cnum +
                    ").search/byjson?" +
                    urlOptions,
                type: "GET",
                success: function (result) {
                    var personDetailsObj = JSON.parse(result);
                    var attributes = personDetailsObj.search.entry[0].attribute;
                    var a = 0;
                    for (a = 0; a < attributes.length; a++) {
                        var object = attributes[a];
                        var value = object.value;
                        var name = object.name;
                        var regex = /[.]/;
                        switch (name) {
                            case "preferredidentity":
                                var intranet = document.getElementById("person_intranet");
                                var kyndrylIntranet = document.getElementById("person_kyn_intranet");
                                if (typeof intranet !== "undefined") {
                                    intranet.value = value;
                                    if (typeof kyndrylIntranet !== "undefined") {
                                        var kynValue = convertOceanToKyndryl(value.toString());
                                        kyndrylIntranet.value = kynValue;
                                    }
                                }
                                break;
                            case "jobresponsibilities":
                                var bio = document.getElementById("person_bio");
                                if (typeof bio !== "undefined") {
                                    bio.value = value;
                                }
                                break;
                            case "notesemail":
                                var Step1 = value[0].replace("CN=", "");
                                var Step2 = Step1.replace("OU=", "");
                                var Step3 = Step2.replace("O=", "");
                                var Split = Step3.split("@");
                                var notesId = Split[0];
                                var notesIdElem = document.getElementById("person_notesid");
                                if (
                                    typeof notesIdElem !== "undefined" &&
                                    notesIdElem.value == ""
                                ) {
                                    notesIdElem.value = notesId;
                                }
                                break;
                            case "uid":
                                var uid = document.getElementById("person_uid");
                                if (typeof uid !== "undefined") {
                                    uid.value = value;
                                }
                                break;
                            case "givenname":
                                var i = 0;
                                var firstName = value[i];
                                while (regex.test(firstName) && i < value.length) {
                                    var firstNameNext = value[++i];
                                    if (typeof firstNameNext !== "undefined") {
                                        firstName = firstNameNext;
                                    }
                                }

                                // get rid off dot from end of name
                                if (firstName[firstName.length - 1] === ".") {
                                    firstName = firstName.slice(0, -1);
                                }

                                var capitalizedName = toTitleCase(firstName);
                                var fname = document.getElementById("person_first_name");
                                if (typeof fname !== "undefined") {
                                    fname.value = capitalizedName;
                                }
                                break;
                            case "sn":
                                var lname = document.getElementById("person_last_name");
                                var lastName = value[0];
                                if (typeof lname !== "undefined") {
                                    lname.value = lastName;
                                }
                                break;
                            case "ismanager":
                                var isMgr = document.getElementById("person_is_mgr");
                                if (typeof isMgr !== "undefined") {
                                    if (value == "Y" || value == "Yes") {
                                        isMgr.value = "Yes";
                                    } else {
                                        isMgr.value = "No";
                                    }
                                }
                                // isMgr.value = value ;};
                                break;
                            case "employeetype":
                                var employeeeType = document.getElementById("person_employee_type");
                                if (typeof employeeeType !== "undefined") {
                                    employeeeType.value = value;
                                }
                                break;
                            case "co":
                                var country = document.getElementById("person_country");
                                if (typeof country !== "undefined") {
                                    country.value = value;
                                }
                                break;
                            case "ibmloc":
                                var location = document.getElementById("person_ibm_location");
                                if (typeof location !== "undefined") {
                                    location.value = value;
                                }
                                break;
                            default:
                            // console.log(name + ":" + value);
                        }
                    }
                },
                error: function (xhr, status) {
                    // handle errors
                    console.log("error");
                    console.log(xhr);
                    console.log(status);
                }
            });
        }
    }
}

export { fetchBluepagesDetailsForCnum as default };