/*
 *
 *
 *
 */

class assetRequest {

  constructor() {
    if (document.myCnum) {
      $('[data-toggle="tooltip"]').tooltip();
      $('.toggle').bootstrapToggle();
      $('#requestees').select2({
        width: '100%',
        placeholder: 'Request For:',
        allowClear: true
      });
      $('#approvingManager').select2({
        width: '100%',
        placeholder: 'Approving Manager:',
        allowClear: true
      });
      $('.locationFor').select2({
        width: '100%',
        placeholder: 'Approved Location',
        allowClear: true,
        // ajax: {
        //   url: '/ajax/select2Locations.php',
        //   dataType: 'json'
        // }
      });
      this.listenForSelectRequestee();
      this.listenForEnteringCtid();
      this.listenForSelectLocation();
      this.listenForSelectAsset();
      this.listenForSaveAssetRequest();
      this.listenForToggleReturnRequest();
      this.listenForClosingSaveFeedbackModal();
      this.listenForAddPrereq();
      this.listenForIgnorePrereq();
      this.listenForClosingPrereqModal();
      this.listenForInvalidSelect();
      this.countCharsInTextarea();
    }

    $('#assetHelp').on("click", function () {
      $('#assetHelpModal').modal('show');
    });
  }

  enableOnlyReturns() {
    $('*[data-return="no"]').attr("disabled", true);
    alert("User is flagged as 'offboarding' therefore the only requests that can be made are to return assets");
  }

  enableRenewals() {
    $('*[data-renewable="yes"]').attr("disabled", false);
  }

  listenForSelectRequestee() {
    var $this = this;
    $(document).on("select2:select", "#requestees", function (e) {
      console.log("fired listenForSelectRequestee");
      var data = e.params.data;
      var cnum_id = data.id.trim();
      var ctid_id = cnum2ctid[cnum_id];
      var ctbFlag = cnum2ctbflag[cnum_id];

      var revalidationStatus = $(e.params.data.element).data(
        "revalidationstatus"
      );
      $("#revalidationStatus").val(revalidationStatus);

      if (!ctid_id) {
        console.log("prompt for CT ID");
        $(".locationFor").val("").trigger("change");
        $("#requesteeName").val(data.text);
        $("#ctbflag").val(ctbFlag);
        $("#obtainCtid").modal("show");
      } else {
        console.log("DONT prompt for CT ID");
        $this.recordCtidOnForm(data.text, ctid_id, ctbFlag);
      }
      console.log("now ajax get location for cnum");

      $.ajax({
        url: "ajax/checkForOpenRequests.php",
        data: {
          cnum: cnum_id,
          workerId: ''
        },
        type: "POST",
        success: function (result) {
          var resultObj = JSON.parse(result);
          console.log(resultObj);
          var assetTitles = resultObj.assetTitles;
          console.log(assetTitles);
          console.log(typeof assetTitles);
          for (var asset in assetTitles) {
            if (assetTitles.hasOwnProperty(asset)) {
              $("[data-asset='" + asset + "']").addClass(
                "subjectToOpenRequest"
              );
              console.log(asset);
              console.log($("[data-asset='" + asset + "']"));
            }
          }
          console.log($(".subjectToOpenRequest"));
        },
        complete: function (xhr, status) {
          $.ajax({
            url: "ajax/getLbgLocationForCnum.php",
            type: "GET",
            data: {
              cnum: cnum_id,
              workerId: ''
            },
            success: function (result) {
              console.log("did we get a location?");
              var resultObj = JSON.parse(result);
              var lbgLocation = resultObj.lbgLocation;
              var fmCnum = resultObj.fmCnum;
              $(".locationFor").attr("disabled", false);
              if (lbgLocation) {
                console.log("yes, we got a location");
                $(".locationFor")
                  .val(lbgLocation)
                  .trigger("change")
                  .trigger({
                    type: "select2:select",
                    params: {
                      data: {
                        id: lbgLocation,
                        text: lbgLocation,
                      },
                    },
                  });
                $("#approvingManager").val(fmCnum).trigger("change");
              } else {
                console.log("no, we did not get a location");
                $(".locationFor").val("").trigger("change");
              }
            },
          });
        },
      });
      $("#approvingManager option[value='" + cnum_id + "']").remove();
    });
  }

  listenForSelectLocation() {
    var $this = this;
    $(document).on("select2:select", ".locationFor", function (e) {
      console.log("fired listenForSelectLocation");

      console.log("is form valid NOW listenForSelectLocation ?");
      var form = document.getElementById("assetRequestForm");
      var formValid = form.checkValidity();
      console.log(formValid);

      $("#requestableAssetDetailsDiv").show();
      console.log($(".requestableAsset"));

      $(".requestableAsset").not(":first").prop("checked", false); // uncheck all the ticks
      $(".justificationDiv").hide(); // Close all the Justification boxes.

      var data = e.params.data;
      var location = data.text.trim();
      if (location.includes("UK")) {
        $this.checkAssetsForShore("on");
      } else {
        $this.checkAssetsForShore("off");
      }

      var revalidationStatus = $("#revalidationStatus").val();
      var offboarding = revalidationStatus.substr(0, 11) == "offboarding";
      if (offboarding) {
        $this.enableOnlyReturns();
        $this.enableRenewals();
      }

      console.log("is form valid NOW NOW listenForSelectLocation ?");
      var form = document.getElementById("assetRequestForm");
      var formValid = form.checkValidity();
      console.log(formValid);

      console.log("finished listenForSelectLocation");
    });
  }

  listenForSelectAsset() {
    var $this = this;
    $(document).on("click", ".requestableAsset", function (e) {
      var id = this.id;
      var asset = $(this).data("asset");
      console.log(asset);
      if (asset.substring(0, 5) == "Other") {
        alert("Please Note : You CANNOT raise requests for AD Groups in vBAC, they must be submitted through the Proforma");
      }

      var justificationDivId = id.replace("-asset-", "-justification-div-");
      $("#" + justificationDivId).toggle();

      var justificationState = $(this).is(":checked") ? true : false;
      $(this)
        .closest(".selectableThing")
        .find(".justification")
        .attr("required", justificationState);
      console.log($(this).closest(".selectable").find(".justification"));

      var assetMissingPrereq = $this.checkForAssetMissingPrereq();
      console.log("do we have an assetmissingprereq?");
      console.log(assetMissingPrereq);
      if (assetMissingPrereq) {
        $this.promptForMissingPrereq(assetMissingPrereq);
      }
    });
  }

  listenForEnteringCtid() {
    var $this = this;
    $(document).on('hidden.bs.modal', '#obtainCtid', function (e) {
      var requestee = $("#requesteeName").val();
      var ctid = $("#requesteeCtid").val().trim();
      var ctbflag = $("#ctbflag").val().trim();

      if (ctid && ctid.length == 7 && !isNaN(ctid)) {
        console.log("record" + ctid);
        $this.recordCtidOnForm(requestee, ctid, ctbflag);
        $.ajax({
          url: "ajax/saveCtid.php",
          type: "POST",
          data: {
            notesid: requestee,
            ctid: ctid
          },
          success: function (result) {
            console.log("we have saved their CT ID");
            console.log(result);
            console.log("record required");
            $this.recordCtidOnForm(requestee, ctid, ctbflag);
          },
        });
      } else if ((ctid && ctid.length != 7) || isNaN(ctid)) {
        alert("CT ID must be 7 digits in length. Please re-enter valid CT ID");
        $("#obtainCtid").modal("show");
      } else {
        var doubleCheck = confirm(
          "STOP : Please confirm the indivual DOES NOT ALREADY HAVE a CT ID"
        );
        if (!doubleCheck) {
          $("#obtainCtid").modal("show");
        }
        console.log("they did not provide a CT ID");
        $this.recordCtidOnForm(requestee, "Required", ctbflag);
      }
    });
  }

  saveAssetRequestRecords() {
    console.log("would save all the records now");
    var allDisabledFields = $("#assetRequestForm input:disabled");
    $(allDisabledFields).attr("disabled", false);
    var formData = $("#assetRequestForm").serialize();
    $(allDisabledFields).attr("disabled", true);
    console.log(formData);
    $.ajax({
      url: "ajax/createAssetRequestRecords.php",
      data: formData,
      type: "POST",
      success: function (result) {
        var resultObj = JSON.parse(result);
        console.log(resultObj);

        if (resultObj.result == "success") {
          var assetRequests = resultObj.requests;
          $("#saveFeedbackModal .modal-body").html(
            "<h3>Requests Created</h3>" + assetRequests
          );
        } else {
          var errorMessages = resultObj.messages;
          console.log(resultObj);
          console.log(resultObj.messages);
          $("#saveFeedbackModal .modal-body").html(
            "<h3>An error has occured</h3>" + errorMessages
          );
        }
        $("#saveFeedbackModal").modal("show");
      }
    });
  }

  listenForSaveAssetRequest() {
    var $this = this;
    $(document).on("click", "#saveAssetRequest", function () {

      $(".has-error").removeClass("has-error");

      console.log("they want to save");
      $("#saveAssetRequest").addClass("spinning");
      $("#saveAssetRequest").attr("disabled", true);

      var anyRequireOrderIt = $(".requestableAsset:checked").filter(
        '*[data-orderitreq="Yes"]'
      );
      if (anyRequireOrderIt.length > 0) {
        $("#orderItNumber").attr("required", true);
        if (typeof $("#orderItNumber").val() == "undefined") {
          alert("You have selected assets that require an LBG number be supplied");
        }
      } else {
        $("#orderItNumber").attr("required", false);
      }

      console.log($("#orderItNumber"));

      var form = document.getElementById("assetRequestForm");
      alert('checkValidity 1');
      var formValid = form.checkValidity();

      console.log(formValid);

      if (formValid) {
        $this.saveAssetRequestRecords();
      } else {
        $('#messageModalBody').html("<p>Form is not valid, please correct</p>");
        $('#messageModal').modal('show');
        $("#saveAssetRequest").removeClass("spinning");
        $("#saveAssetRequest").attr("disabled", false);
      }
    });
  }

  checkForAssetMissingPrereq() {
    var prereqElement = false;
    var selectedAssetsToInspect = $(".requestableAsset:checked").not(
      '*[data-ignore="Yes"]'
    );
    var prereqsRequiredFor = [];
    for (var i = 0; i < selectedAssetsToInspect.length; i++) {
      var selectedAsset = selectedAssetsToInspect[i];
      var asset = $(selectedAsset).data("asset");
      var preReq = $(selectedAsset).data("prereq");
      /*
       * Basically. Get all the checked requestable assets
       * then filter for the asset named 'preReq'.
       *
       * If the list we get back is empty - then we know the pre-req is not amongst the checked assets, so prompt the user how they
       * want to handle it.
       *
       */
      var isPreReqAmongstTheChecked = $(".requestableAsset:checked").filter(
        '*[data-asset="' + preReq + '"]'
      );
      if (isPreReqAmongstTheChecked.length == 0) {
        /*
         * Seek the pre-req amongst all the requestableAssets that we've not been told to ignore.
         * If it turns up - return it from this Function and stop looking.
         * If it doesn't then carry one, we've been told to ignore it.
         */
        var prereqElement = $(".requestableAsset:enabled")
          .not('*[data-ignore="Yes"]')
          .filter('*[data-asset="' + preReq + '"]')[0];
        if (prereqElement) {
          prereqsRequiredFor.push(selectedAsset);
        }
      }
    }

    // If we found a prereqElement, (ie a pre-req that isn't checked - then return the Owning selectedAsset;
    return prereqsRequiredFor.length > 0 ? prereqsRequiredFor[0] : false;
  }

  promptForMissingPrereq(element) {
    var asset = $(element).data("asset");
    var preReq = $(element).data("prereq");
    var requestee = $("#requesteeName").val();
    $("#requestedAssetTitle").html(asset);
    $("#prereqAssetTitle").html(preReq);
    $("#requesteeNotesid").html(requestee);
    $("#missingPrereqModal").modal("show");
  }

  listenForAddPrereq() {
    $(document).on("click", "#addPreReq", function () {
      console.log("they want to add the prereq");
      var preReqTitle = $("#prereqAssetTitle").html();
      var preReqElement = $(".requestableAsset").filter(
        '*[data-asset="' + preReqTitle + '"]'
      );
      $(preReqElement).trigger("click");
      $("#missingPrereqModal").modal("hide");
    });
  }

  listenForIgnorePrereq() {
    $(document).on("click", "#ignorePreReq", function () {
      console.log("they want to ignore the prereq");
      var preReqTitle = $("#prereqAssetTitle").html();
      var preReqElement = $(".requestableAsset").filter(
        '*[data-asset="' + preReqTitle + '"]'
      )[0];
      $(preReqElement).attr("data-ignore", "Yes");
      $("#missingPrereqModal").modal("hide");
    });
  }

  listenForClosingPrereqModal() {
    var $this = this;
    $(document).on('hidden.bs.modal', '#missingPrereqModal', function (e) {
      var assetMissingPrereq = $this.checkForAssetMissingPrereq();
      if (assetMissingPrereq) {
        $this.promptForMissingPrereq(assetMissingPrereq);
      }
    });
  }

  listenForClosingSaveFeedbackModal() {
    $(document).on('hidden.bs.modal', '#saveFeedbackModal', function (e) {
      $(".greyablePage").addClass("overlay");
      location.reload();
    });
  }

  listenForToggleReturnRequest() {
    var saveCtidState = "notRecorded";
    var saveCtidTick = "notRecorded";
    $(document).on("change", "#returnRequest", function () {
      var ctidAsset = $('*[data-asset="CT ID"]');
      console.log("Toggling between a return and a request");
      var isReturn = $("#returnRequest:checked").length > 0;

      console.log(isReturn);
      console.log(saveCtidState);

      if (isReturn) {
        var reallyReturn = confirm(
          'You have indicated you wish to RETURN an asset, is this correct ? If this is NOT what you intend to do, please click "Cancel" for this prompt and re-click the Toggle so it reads "Request New"'
        );
        if (!reallyReturn) {
          console.log("they dont want to return it");
          return;
        }
      }

      console.log("they really want to return it");

      if (isReturn && saveCtidState == "notRecorded") {
        saveCtidState = $(ctidAsset).attr("disabled");
        saveCtidTick = $(ctidAsset).attr("checked");
        $(ctidAsset).attr("disabled", false);
      } else if (isReturn) {
        $(ctidAsset).attr("disabled", false);
      } else {
        $(ctidAsset).attr("disabled", saveCtidState);
        $(ctidAsset).attr("checked", saveCtidTick);
      }
    });
  }

  listenForInvalidSelect() {
    var $this = this;
    $("#assetRequestForm select").on("invalid", function (event) {
      console.log(this);
      $(this).parent().addClass('has-error');
    });
  }

  recordCtidOnForm(email_address, ctid, ctbflag) {
    console.log("recordCtidOnForm");
    var ctb = ctbflag ? ctbflag : "unknown";
    var lastCtidInput = $("#allCtidHereDiv > :input").not(".select2").last();
    $(lastCtidInput).val(ctid);

    var lastRequest = $("#requestDetailsDiv > .panel").last();
    $(lastRequest)
      .find(".panel-title")
      .html("Request For : " + email_address + "(" + ctb + ")");
    var ctidAsset = $('*[data-asset="CT ID"]');
    var alreadyDisabled = $(ctidAsset).attr("disabled");
    console.log("already disabled:");
    console.log(alreadyDisabled);
    if (alreadyDisabled == "disabled") {
      $("#obtainCtid").off("hidden.bs.modal");
      $("#obtainCtid").modal("hide");
      this.listenForEnteringCtid();
      return false;
    }
    if (ctid == "Required") {
      $(ctidAsset).attr("disabled", true).prop("checked", true);
    } else {
      $(ctidAsset).attr("disabled", true).prop("checked", false);
    }
    $(ctidAsset)
      .next("label")
      .text("CT ID (" + ctid + ")");
  }

  cloneRequestDetails(email_address, ctid) {
    console.log(email_address + ":" + ctid);
    var lastRequest = $("#requestDetailsDiv > .panel").last();
    console.log($(lastRequest));
    console.log(lastRequest);
    $(lastRequest).clone().appendTo("#requestDetailsDiv");
    $(lastRequest)
      .attr("id", "requestFor" + ctid)
      .data("ctid", ctid)
      .data("email", email_address);
    $(lastRequest)
      .find(".panel-title")
      .html("Request For : " + email_address);
    $(lastRequest).find("#locationFor");
  }

  initialiseLocationSelect2(ctid) {
    $("#locationFor" + ctid).select2({
      width: "100%",
      placeholder: "Approved Location",
      allowClear: true,
      ajax: {
        url: "/ajax/select2Locations.php",
        dataType: "json",
      },
    });
  }

  countCharsInTextarea() {
    $("textarea").keypress(function () {
      if (this.value.length + 1 > $(this).attr("max")) {
        $(this)
          .next("span")
          .html(
            "Justification too long, please keep to between" +
            $(this).attr("min") +
            " and " +
            $(this).attr("max") +
            " characters"
          );
        $(this)
          .next("span")
          .removeClass("bg-warning")
          .removeClass("bg-success")
          .removeClass("bg-danger")
          .addClass("bg-danger");
      } else {
        $(this)
          .next("span")
          .html(
            $(this).attr("max") -
            (this.value.length + 1) +
            " more chars allowed"
          );
        $(this)
          .next("span")
          .removeClass("bg-warning")
          .removeClass("bg-success")
          .removeClass("bg-danger")
          .addClass("bg-success");
      }
    });
  }

  checkAssetsForShore(shore) {
    if (shore == "on") {
      console.log("dissallow onshore=no");
      console.log($('*[data-onshore="No"]'));
      $('*[data-onshore="No"]')
        .not('*[data-asset="CT ID"]')
        .prop("checked", false)
        .attr("disabled", true);
      $('*[data-onshore="Yes"]')
        .not('*[data-asset="CT ID"]')
        .attr("disabled", false);
      $.each(
        $('*[data-onshore="No"]').not('*[data-asset="CT ID"]').next("label"),
        function (key, value) {
          var text = $(value).text();
          var alreadyAmended = text.includes("not available onshore");
          if (!alreadyAmended) {
            $(value).text(text + " - not available onshore");
          }
        }
      );
      $.each(
        $('*[data-onshore="Yes"]').not('*[data-asset="CT ID"]'),
        function (key, value) {
          var label = $(this).next("label");
          var assetTitle = $(this).data("asset");
          $(label).text(assetTitle);
        }
      );
    } else {
      console.log("dissallow offshore=no");
      console.log($('*[data-offshore="No"]'));
      $('*[data-offshore="No"]')
        .not('*[data-asset="CT ID"]')
        .prop("checked", false)
        .attr("disabled", true);
      $('*[data-offshore="Yes"]')
        .not('*[data-asset="CT ID"]')
        .attr("disabled", false);
      $.each(
        $('*[data-offshore="No"]').not('*[data-asset="CT ID"]').next("label"),
        function (key, value) {
          var text = $(value).text();
          var alreadyAmended = text.includes("not available offshore");
          if (!alreadyAmended) {
            $(value).text(text + " - not available offshore");
          }
        }
      );
    }
    /*
     * Now disable anything subject to an open request.
     */
    $(".subjectToOpenRequest").attr("disabled", true);
    $(".subjectToOpenRequest").prop("checked", false); // And untick it.

    $.each($(".subjectToOpenRequest"), function (index, value) {
      console.log(value);
      var asset = $(value).data("asset");
      alert(asset + " is currently subject to an open request so cannot be selected at this time.");
    });
  }
}

const AssetRequest = new assetRequest();

export { AssetRequest as default };