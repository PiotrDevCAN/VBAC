/*
 *
 *
 *
 */

let action = await cacheBustImport('./modules/actions/action.js');

class setOrderItStatus extends action {

    requestsWithStatus;

    constructor(parent) {
        super(parent);
        this.listenForSetOitStatusButton();
        this.listenForSetOitStatusModalShown();
        this.listenForSaveOrderItStatus();
    }

    listenForSetOitStatusButton() {
        var $this = this;
        $(document).on("click", "#setOrderItStatus", function (e) {
            $("#setOrderItStatus").addClass("spinning");
            $("#setOrderItStatus").attr("disabled", true);
            $.ajax({
                url: "ajax/prepareForSetOrderItStatus.php",
                type: "GET",
                success: function (result) {
                    var resultObj = JSON.parse(result);
                    // $this.table.ajax.reload();
                    $("#setOitStatusModal .modal-body").html(resultObj.form);
                    $("#setOitStatusModal").modal("show");
                    $("#setOrderItStatus").removeClass("spinning");
                    $("#setOrderItStatus").attr("disabled", false);
                },
            });
        });
    }

    listenForSetOitStatusModalShown() {
        var $this = this;
        $(document).on('shown.bs.modal', '#setOitStatusModal', function (e) {
            $("#orderit").select2({
                placeholder: "Select LBG",
                allowClear: true,
            });

            $("#mappedVarb").select2({
                placeholder: "Select VARB",
                allowClear: true,
            });
            $("#mappedRef").select2({
                placeholder: "Select Request Reference",
                allowClear: true,
            });

            $this.populateRequestTableForOrderIt();
            $this.listenForOrderItSelected();
            $this.listenForMappedVarbSelected();
            $this.listenForMappedRefSelected();
        });
    }

    populateRequestTableForOrderIt() {
        this.requestsWithStatus = $("#requestsWithStatus").DataTable({
            ajax: {
                url: "ajax/populateRequestTableForOrderIt.php",
                type: "POST",
                data: function (d) {
                    var orderit = $("#orderit").find(":selected").val();
                    var varb = $("#mappedVarb").find(":selected").val();
                    var ref = $("#mappedRef").find(":selected").val();
                    var oitObject = { orderit: orderit, varb: varb, ref: ref };
                    return oitObject;
                },
            },

            columns: [
                { data: "REFERENCE", defaultContent: "", width: "5%" },
                { data: "PERSON", defaultContent: "", width: "5%" },
                { data: "ASSET", defaultContent: "", width: "15%" },
                { data: "STATUS", defaultContent: "", width: "15%" },
                { data: "ACTION", defaultContent: "", width: "20%" },
                { data: "PRIMARY_UID", defaultContent: "", width: "8%" },
                { data: "COMMENT", defaultContent: "", width: "25%" },
                { data: "ORDERIT_RESPONDED", defaultContent: "", width: "7%" },
            ],

            drawCallback: function (settings) {
                console.log($(".statusToggle"));
                $(".statusToggle").bootstrapToggle();
            },
            autoWidth: false,
            deferRender: true,
            processing: true,
            responsive: true,
            pageLength: 20,
            order: [[1, "asc"]],
            language: {
                emptyTable: "Please select LBG/Varb or Request Reference",
            },
            dom: "Bfrtip",
            //		      colReorder: true,
            buttons: ["csvHtml5"],
        });
    }

    listenForOrderItSelected() {
        var $this = this;
        console.log("setup listener for orderit selected");
        console.log($("#orderit"));
        $("#orderit").on("select2:select", function (e) {
            console.log("event triggered");
            $this.requestsWithStatus.ajax.reload();
        });
    }

    listenForMappedVarbSelected() {
        var $this = this;
        $("#mappedVarb").on("select2:select", function (e) {
            $this.requestsWithStatus.ajax.reload();
        });
    }

    listenForMappedRefSelected() {
        var $this = this;
        $("#mappedRef").on("select2:select", function (e) {
            $this.requestsWithStatus.ajax.reload();
        });
    }

    listenForSaveOrderItStatus() {
        var $this = this;
        $(document).on("click", "#saveOrderItStatus", function (e) {
            $("#saveOrderItStatus").addClass("spinning");
            var form = document.getElementById("setOrderItStatusForm");
            var formValid = form.checkValidity();
            if (formValid) {
                var formData = $("#setOrderItStatusForm").serialize();
                $.ajax({
                    url: "ajax/saveOrderItStatus.php",
                    type: "POST",
                    data: formData,
                    success: function (result) {
                        console.log(result);
                        var resultObj = JSON.parse(result);
                        $("#saveOrderItStatus").removeClass("spinning");
                        $this.table.ajax.reload();
                        $this.parent.parent.countRequestsForPortal();
                    },
                });
            } else {
                $('#messageModalBody').html("<p>Form is not valid, please correct</p>");
                $('#messageModal').modal('show');
                $("#saveOrderItStatus").removeClass("spinning");
            }
        });
    }
}

export { setOrderItStatus as default };