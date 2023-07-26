/*
 *
 *
 *
 */

let spinner = await cacheBustImport('./modules/functions/spinner.js');

class pesTracker {

    table;

    constructor(records) {
        this.populatePesTracker(records);
    }

    populatePesTracker(records) {
        var buttons = $(".btnRecordSelection");

        $("#pesTrackerTableDiv").html(spinner);

        this.table = $.ajax({
            url: "ajax/populatePesTrackerTable.php",
            type: "POST",
            data: {
                records: records
            },

            // dataType: 'json',
            // dataSrc: function (json) {
            //   console.log('dataSrc');
            //   console.log(json);
            //   console.log($('#pesTrackerTable_processing').is(":visible"));

            //   //Make your callback here.
            //   if (json.error.length != 0) {
            //     $('#messageModalBody').html(json.error);
            //     $('#messageModal').modal('show');
            //   }
            //   console.log(json.data);
            //   return json.data;
            // },

            beforeSend: function (jqXHR, settings) {
                console.log('before send');
                console.log($('.dataTables_processing'));
                console.log($('#pesTrackerTable_processing').is(":visible"));

                $.each(xhrPool, function (idx, jqXHR) {
                    jqXHR.abort();  // basically, cancel any existing request, so this one is the only one running
                    xhrPool.splice(idx, 1);
                });
                xhrPool.push(jqXHR);
            },
            success: function (result) {
                var resultObj = JSON.parse(result);

                console.log(resultObj.success);
                console.log(resultObj.messages);
                if (resultObj.success) {
                    $("#pesTrackerTableDiv").html(resultObj.table);

                    $("#pesTrackerTable thead th").each(function () {
                        var title = $(this).text();
                        $(this).html(
                            title + '<input class="secondInput" type="hidden"  />'
                        );
                    });

                    $("#pesTrackerTable thead td").each(function () {
                        var title = $(this).text();
                        $(this).html(
                            '<input class="firstInput" type="text" size="10" placeholder="Search ' +
                            title +
                            '" />'
                        );
                    });
                } else {
                    $("#pesTrackerTableDiv").html(resultObj.messages);
                }
            },
        });

        // Apply the search
        $(document).on("keyup change", ".firstInput", function (e) {
            var searchFor = this.value;
            var col = $(this).parent().index();
            var searchCol = col + 1;
            if (searchFor.length >= 3) {
                $("#pesTrackerTable tbody tr").hide();
                $(
                    "#pesTrackerTable tbody td:nth-child(" +
                    searchCol +
                    "):contains(" +
                    searchFor +
                    ")	"
                )
                    .parent()
                    .show();
            } else {
                $("#pesTrackerTable tbody tr").show();
            }
        });
    }
}

export { pesTracker as default };