/*
 *
 *
 *
 */

let sleep = await cacheBustImport('./modules/functions/sleep.js');

let delegateTable = await cacheBustImport('./modules/tables/delegate.js');
let editDelegateBox = await cacheBustImport('./modules/boxes/editDelegateBox.js');

class delegate {

  table;
  tableObj;

  constructor() {
    console.log("+++ Function +++ delegate.init");

    $('.select2').select2({
      'placeholder': 'Select your delegate'

    });
    this.listenForSaveDelegate();

    console.log("--- Function --- delegate.init");
  }

  listenForSaveDelegate() {
    var $this = this;
    $(document).on("click", "#saveDelegate", function () {
      $("#saveDelegate").addClass("spinning");
      var cnum = $("#delegate").val();
      var requestorCnum = $("#requestorCnum").val();
      var requestorEmail = $("#requestorEmail").val();
      $.ajax({
        url: "ajax/saveDelegate.php",
        type: "POST",
        data: {
          cnum: cnum,
          requestorCnum: requestorCnum,
          requestorEmail: requestorEmail,
        },
        success: function (result) {
          var resultObj = JSON.parse(result);
          console.log(resultObj);
          $("#resultHere").html("Delegate Saved");
          $this.table.ajax.reload();
          var promise = sleep(4000);
          promise.then(function (result) {
            console.log("slept");
            $("#resultHere").html("");
            $("#saveDelegate").removeClass("spinning");
          });
        },
      });
    });
  }
}

const Delegate = new delegate();

const DelegateTable = new delegateTable();
Delegate.table = DelegateTable.table;
Delegate.tableObj = DelegateTable;

const EditDelegateBox = new editDelegateBox(Delegate);

export { Delegate as default };