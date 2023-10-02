/**
 *
 */

let toTitleCase = await cacheBustImport('./modules/functions/toTitleCase.js');

function fetchWorkerAPIDetailsForCnum(cnum) {
    if (typeof cnum !== "undefined") {
        if (cnum.length == 9) {
            $.ajax({
                url: "api/workerAPI.php?cnum=" + cnum,
                type: "GET",
                dataType: "json",
                success: function (data) {
                    var personDetailsObj = data.data.results;
                    var attributes = personDetailsObj[0];
                    var regex = /[.]/;
                    for (let name in attributes) {
                        // console.log(name + ' ' + attributes[name]);
                        var value = attributes[name];

                        // http://localhost:8082/api/workerAPI.php?ibmperson/(uid=128673866).search/byjson?
                        /*
                        preferredidentity
                        &jobresponsibilities
                        &notesemail
                        &uid
                        &givenname
                        &sn
                        &ismanager
                        &employeetype
                        &co
                        &ibmloc
                        */

                        // "isActive": true,
                        // "workerID": 5072493,
                        // "cnum": "128673866",
                        // "email": "Neil.Islam@kyndryl.com",
                        // "firstName": "Neil",
                        // "lastName": "Islam",
                        // "displayName": "Neil Islam",
                        // "businessTitle": "Senior Lead, Data Science",
                        // "usageLocation": "GB",
                        // "countryName": "United Kingdom",
                        // "mobilePhone": null,
                        // "workPhone": "",
                        // "faxNumber": "kyn-cio-iam-azad-FnF-R0",
                        // "employeeType": "H",
                        // "division": "02",
                        // "orgCode": "PU",
                        // "isManager": true,
                        // "workLoc": "NHB",
                        // "workplaceIndicator": "H",
                        // "costCenter": "3620250856",
                        // "matrixManagerEmail": "Nicola.Reynolds@kyndryl.com",
                        // "managerEmail": "Nicola.Reynolds@kyndryl.com",

                        switch (name) {
                            case "businessTitle":
                                var bio = document.getElementById("person_bio");
                                if (typeof bio !== "undefined") {
                                    bio.value = value;
                                }
                                break;
                            case "cnum":
                                var uid = document.getElementById("person_uid");
                                if (typeof uid !== "undefined") {
                                    uid.value = value;
                                }
                                break;
                            case "firstName":
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
                            case "lastName":
                                var lname = document.getElementById("person_last_name");
                                var lastName = value[0];
                                if (typeof lname !== "undefined") {
                                    lname.value = lastName;
                                }
                                break;
                            case "isManager":
                                var isMgr = document.getElementById("person_is_mgr");
                                if (typeof isMgr !== "undefined") {
                                    if (value == "Y" || value == "Yes" || value == true) {
                                        isMgr.value = "Yes";
                                    } else {
                                        isMgr.value = "No";
                                    }
                                }
                                break;
                            case "employeeType":
                                var employeeeType = document.getElementById("person_employee_type");
                                if (typeof employeeeType !== "undefined") {
                                    employeeeType.value = value;
                                }
                                break;
                            case "countryName":
                                var country = document.getElementById("person_country");
                                if (typeof country !== "undefined") {
                                    country.value = value;
                                }
                                break;
                            case "workLoc":
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

export { fetchWorkerAPIDetailsForCnum as default };