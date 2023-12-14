/**
 *
 */

let validateEmail = await cacheBustImport('./modules/functions/validateEmail.js');
// let checkIbmEmailAddress = await cacheBustImport('./modules/functions/checkIbmEmailAddress.js');
let checkOceanEmailAddress = await cacheBustImport('./modules/functions/checkOceanEmailAddress.js');
let checkKyndrylEmailAddress = await cacheBustImport('./modules/functions/checkKyndrylEmailAddress.js');
let inArrayCaseInsensitive = await cacheBustImport('./modules/functions/inArrayCaseInsensitive.js');

let initialiseStartEndDate = await cacheBustImport('./modules/functions/initialiseStartEndDate_Vendor.js');
let initialiseOtherDates = await cacheBustImport('./modules/functions/initialiseOtherDates_Vendor.js');
let initialiseFormSelect2 = await cacheBustImport('./modules/functions/initialiseFormSelect2_Vendor.js');

let saveVendorBoarding = await cacheBustImport('./modules/functions/saveVendorBoarding.js');

let knownExternalEmails = await cacheBustImport('./modules/dataSources/knownExternalEmails.js');
let knownIBMEmails = await cacheBustImport('./modules/dataSources/knownIBMEmails.js');
// let knownKyndrylEmails = await cacheBustImport('./modules/dataSources/knownKyndrylEmails.js');

let entry = await cacheBustImport('./modules/forms/onboardEntry.js');

class vendorOnboardEntry extends entry {

  static formId = 'boardingFormNotIbmer';
  static resourceEmailInputId = 'resource_email';
  static saveButtonId = 'saveVendorBoarding';
  static resetButtonId = 'resetVendorBoarding';
  static initiatePesButtonId = 'initiateVendorPes';

  static saveBoarding = saveVendorBoarding;

  table;
  responseObj;

  constructor() {
    console.log('+++ Function +++ vendorOnboardEntry.constructor');

    super(vendorOnboardEntry);

    this.listenForEmailChange();
    this.listenForEmailFocusOut();
    this.listenForEmployeeTypeRadioBtn();

    this.listenForResetForm();

    console.log('--- Function --- vendorOnboardEntry.constructor');
  }

  initialiseForm() {
    initialiseStartEndDate();
    initialiseOtherDates();
    initialiseFormSelect2();
  }

  listenForEmailChange() {
    $(document).on("change", "#" + vendorOnboardEntry.formId + " #" + vendorOnboardEntry.resourceEmailInputId, async function () {
      var newEmail = $(this).val();
      var trimmedEmail = newEmail.trim();
      if (trimmedEmail !== "") {

        // validate email address
        if (validateEmail(trimmedEmail)) {

          let knownExternalEmail = await knownExternalEmails.getEmails();
          let knownIBMEmail = await knownIBMEmails.getEmails();
          // let knownKyndrylEmail = await knownKyndrylEmails.getEmails();

          var allreadyExternalExists = inArrayCaseInsensitive(trimmedEmail, knownExternalEmail) >= 0;
          var allreadyIBMExists = inArrayCaseInsensitive(trimmedEmail, knownIBMEmail) >= 0;
          // var allreadyKyndrylExists = inArrayCaseInsensitive(trimmedEmail, knownKyndrylEmail) >= 0;

          // var ibmEmailAddress = checkIbmEmailAddress(trimmedEmail);
          var oceanEmailAddress = checkOceanEmailAddress(trimmedEmail);
          var kyndrylEmailAddress = checkKyndrylEmailAddress(trimmedEmail);

          if (allreadyExternalExists || allreadyIBMExists) {
            // comes back with Position in array(true) or false is it's NOT in the array.
            $("#" + vendorOnboardEntry.saveButtonId).attr("disabled", true);
            $("#" + vendorOnboardEntry.resourceEmailInputId).css("background-color", "LightPink");
            // } else if (ibmEmailAddress) {
            //     $("#" + vendorOnboardEntry.saveButtonId).attr("disabled", true);
            //     $("#" + vendorOnboardEntry.resourceEmailInputId).css("background-color", "Red");
          } else if (oceanEmailAddress) {
            $("#" + vendorOnboardEntry.saveButtonId).attr("disabled", true);
            $("#" + vendorOnboardEntry.resourceEmailInputId).css("background-color", "Red");
          } else if (kyndrylEmailAddress) {
            $("#" + vendorOnboardEntry.saveButtonId).attr("disabled", true);
            $("#" + vendorOnboardEntry.resourceEmailInputId).css("background-color", "Red");
          } else {
            $("#" + vendorOnboardEntry.saveButtonId).attr("disabled", false);
            $("#" + vendorOnboardEntry.resourceEmailInputId).css("background-color", "LightGreen");
          }
        } else {
          // can not proceed
          $("#" + vendorOnboardEntry.saveButtonId).attr("disabled", true);
          $("#" + vendorOnboardEntry.resourceEmailInputId).css("background-color", "Red");
        }
      } else {
        // no need to check
        $("#" + vendorOnboardEntry.saveButtonId).attr("disabled", true);
        $("#" + vendorOnboardEntry.resourceEmailInputId).val("");
        $("#" + vendorOnboardEntry.resourceEmailInputId).css("background-color", "white");
      }
    });
  }

  listenForEmailFocusOut() {
    $(document).on("focusout", "#" + vendorOnboardEntry.formId + " #" + vendorOnboardEntry.resourceEmailInputId, async function () {
      // var newEmail = $("#" + vendorOnboardEntry.resourceEmailInputId).val();
      var newEmail = $(this).val();
      var trimmedEmail = newEmail.trim();
      if (trimmedEmail !== "") {

        // validate email address
        if (validateEmail(trimmedEmail)) {

          let knownExternalEmail = await knownExternalEmails.getEmails();
          let knownIBMEmail = await knownIBMEmails.getEmails();
          // let knownKyndrylEmail = await knownKyndrylEmails.getEmails();

          var allreadyExternalExists = inArrayCaseInsensitive(trimmedEmail, knownExternalEmail) >= 0;
          var allreadyIBMExists = inArrayCaseInsensitive(trimmedEmail, knownIBMEmail) >= 0;
          // var allreadyKyndrylExists = inArrayCaseInsensitive(trimmedEmail, knownKyndrylEmail) >= 0;

          // var ibmEmailAddress = checkIbmEmailAddress(trimmedEmail);
          var oceanEmailAddress = checkOceanEmailAddress(trimmedEmail);
          var kyndrylEmailAddress = checkKyndrylEmailAddress(trimmedEmail);

          if (allreadyExternalExists || allreadyIBMExists) {
            // comes back with Position in array(true) or false is it's NOT in the array.
            $('#messageModalBody').html("<p>Email address already defined to VBAC</p>");
            $('#messageModal').modal('show');
            return false;
            // } else if (ibmEmailAddress) {
            //     $('#messageModalBody').html("<p>Kyndryl employees should NOT BE Pre-Boarded. Please board as an IBMer</p>");
            //     $('#messageModal').modal('show');
          } else if (oceanEmailAddress) {
            $('#messageModalBody').html("<p>Ocean IDs should NOT BE Pre-Boarded. Please board as a regular employee</p>");
            $('#messageModal').modal('show');
          } else if (kyndrylEmailAddress) {
            $('#messageModalBody').html("<p>Kyndryls should NOT BE Pre-Boarded. Please board as a regular employee</p>");
            $('#messageModal').modal('show');
          } else {

          }
        } else {
          // can not proceed
          $('#messageModalBody').html("<p>Provided email address in invalid</p>");
          $('#messageModal').modal('show');
        }
      } else {
        // no need to check
      }
    });
  }

  listenForEmployeeTypeRadioBtn() {
    $(document).on("click", "input[name=EMPLOYEE_TYPE]", function (e) {
      var selectedEmployeeType = $("input[name=EMPLOYEE_TYPE]:checked");
      var employeeType = selectedEmployeeType.val();
      var type = selectedEmployeeType.data("type");

      $("#resource_employee_type").val(employeeType);

      switch (employeeType) {
        case 'preboarder':
          switch (type) {
            case 'ibmer':
            default:
              $("#" + vendorOnboardEntry.resourceEmailInputId)
                .val("").trigger("change");
              break;
          }
          $("#resource_open_seat").val("");
          break;
        case 'vendor':
          switch (type) {
            case 'other':
              $("#" + vendorOnboardEntry.resourceEmailInputId)
                .val("").trigger("change");
              break;
            default:
              $("#" + vendorOnboardEntry.resourceEmailInputId)
                .val("")
                .attr("disabled", true)
                .attr("required", false)
                .attr("placeholder", "Not required - GDPR")
                .css("background-color", "#eeeeee");
              break;
          }
          var Type = type[0].toUpperCase() + type.slice(1).toLowerCase();
          $("#resource_open_seat").val(Type);
          break;
        default:
          break;
      }
    });
  }

  listenForResetForm() {
    var $this = this;
    $(document).on('reset', '#' + vendorOnboardEntry.formId, function (event) {
      console.log("reset clicked " + vendorOnboardEntry.formId);
      // event.preventDefault();

      // fields in none IBMer
      // $("#resource_first_name").val("").trigger("change");
      // $("#resource_last_name").val("").trigger("change");
      // $("#" + vendorOnboardEntry.resourceEmailInputId).val("").trigger("change");

      $("#" + vendorOnboardEntry.resourceEmailInputId).val("").trigger("change");

      // if ($("#resource_country").data("select2")) {
      //     $("#resource_country").select2("destroy");
      // }
      // $("#resource_country").select2();

    });
  }
}

const VendorOnboardEntry = new vendorOnboardEntry();

export { VendorOnboardEntry as default };