/*
 *
 *
 *
 */

let buttonCommon = await cacheBustImport('./modules/functions/buttonCommon.js');

class delegate {

  table;

  constructor() {
    this.initialiseMyDelegatesDataTable();
  }

  initialiseMyDelegatesDataTable() {
    var requestorCnum = $("#requestorCnum").val();
    // DataTable
    this.table = $("#myDelegatesTable").DataTable({
      ajax: {
        url: "ajax/populateMyDelegates.php",
        type: "POST",
        data: { requestorCnum: requestorCnum },
      },
      autoWidth: true,
      processing: true,
      responsive: true,
      language: {
        emptyTable: "No Delegates Found",
      },
      dom: "Blfrtip",
      buttons: [
        $.extend(true, {}, buttonCommon, {
          extend: "excelHtml5",
          exportOptions: {
            orthogonal: "sort",
            stripHtml: true,
            stripNewLines: false,
          },
          customize: function (xlsx) {
            var sheet = xlsx.xl.worksheets["sheet1.xml"];
          },
        }),
        $.extend(true, {}, buttonCommon, {
          extend: "csvHtml5",
          exportOptions: {
            orthogonal: "sort",
            stripHtml: true,
            stripNewLines: false,
          },
        }),
      ],
    });
  }
}

export { delegate as default };