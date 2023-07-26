/*
 *
 *
 *
 */

let action = await cacheBustImport('./modules/actions/action.js');

class mapVarbToOrderIt extends action {
    
    varbRequestTable;

    constructor(parent) {
        super(parent);
        this.listenForMapVarbButton();
        this.listenForDeVarbButton();
        this.listenForMapVarbModalShown();
        this.listenForSaveMapping();
    }

    listenForMapVarbButton() {
        var $this = this;
        $(document).on("click", "#mapVarbToOrderIt", function (e) {
            $("#mapVarbToOrderIt").addClass("spinning");
            $("#mapVarbToOrderIt").attr("disabled", true);
            $.ajax({
                url: "ajax/prepareForMapVarbToOrderIT.php",
                type: "GET",
                success: function (result) {
                    var resultObj = JSON.parse(result);
                    $("#mapVarbToOrderItModal .modal-body").html(resultObj.form);
                    $("#mapVarbToOrderItModal").modal("show");
                    $("#mapVarbToOrderIt").removeClass("spinning");
                    $("#mapVarbToOrderIt").attr("disabled", false);
                },
            });
        });
    }

    listenForDeVarbButton() {
        var $this = this;
        $(document).on("click", "#deVarb", function (e) {
            $("#deVarb").addClass("spinning");
            $("#deVarb").attr("disabled", true);
            if (
                !confirm(
                    "This will remove SELECTED requests from the VARB, please confirm that is what you want to do"
                )
            ) {
                $("#deVarb").removeClass("spinning");
                $("#deVarb").attr("disabled", false);
                return false;
            }
            var formData = $("#mapVarbToOrderItForm").serialize();
            $.ajax({
                url: "ajax/deVarb.php",
                type: "POST",
                data: formData,
                success: function (result) {
                    var resultObj = JSON.parse(result);
                    $("#deVarb").removeClass("spinning");
                    $("#deVarb").attr("disabled", false);
                    $("#mapVarbToOrderItModal").modal("hide");
                    $this.table.ajax.reload();
                    $this.parent.parent.countRequestsForPortal();
                },
            });
        });
    }

    listenForMapVarbModalShown() {
        var $this = this;
        $(document).on('shown.bs.modal', '#mapVarbToOrderItModal', function (e) {
            $("#unmappedVarb").select2({
                placeholder: "Select VARB Reference",
            });
            $("#unmappedRef").select2({
                placeholder: "Select Request Reference",
            });
            $this.populateRequestTableForVarb();
            $this.listenForVarbSelectedForMapping();
            $this.listenForRefSelectedForMapping();
        });
    }

    populateRequestTableForVarb() {
        this.varbRequestTable = $("#requestsWithinVarb").DataTable({
            ajax: {
                url: "ajax/populateRequestTableForVarb.php",
                type: "POST",
                data: function (d) {
                    var varb = $("#unmappedVarb").find(":selected").val();
                    var ref = $("#unmappedRef").find(":selected").val();
                    var varbObject = { varb: varb, ref: ref };
                    return varbObject;
                },
            },
            columns: [
                { data: "INCLUDED", defaultContent: "", width: "5%" },
                { data: "REFERENCE", defaultContent: "", width: "5%" },
                { data: "ORDERIT_NUMBER", defaultContent: "", width: "15%" },
                { data: "PERSON", defaultContent: "", width: "25%" },
                { data: "ASSET", defaultContent: "", width: "25%" },
                { data: "COMMENT", defaultContent: "", width: "25%" },
            ],

            autoWidth: false,
            deferRender: true,
            processing: true,
            responsive: true,
            pageLength: 20,
            order: [[1, "asc"]],
            language: {
                emptyTable: "Please select VARB or Request Reference",
            },
            dom: "Bfrtip",
            //		      colReorder: true,
            buttons: ["csvHtml5"],
        });
    }

    listenForVarbSelectedForMapping() {
        var $this = this;
        console.log("setup listener");
        $("#unmappedVarb")
            .off("select2:select.varb")
            .on("select2:select.varb", function (e) {
                $this.varbRequestTable.ajax.reload();
                $("#deVarb").attr("disabled", false);
            });
    }

    listenForRefSelectedForMapping() {
        var $this = this;
        console.log("setup listener");
        $("#unmappedRef")
            .off("select2:select.varb")
            .on("select2:select.varb", function (e) {
                $this.varbRequestTable.ajax.reload();
                $("#deVarb").attr("disabled", false);
            });
    }

    listenForSaveMapping() {
        var $this = this;
        $(document).on("click", "#saveMapVarbToOrderIT", function (e) {
            $("#saveMapVarbToOrderIT").addClass("spinning");
            var form = document.getElementById("mapVarbToOrderItForm");
            var formValid = form.checkValidity();
            if (formValid) {
                var formData = $("#mapVarbToOrderItForm").serialize();
                $.ajax({
                    url: "ajax/saveVarbToOrderItMapping.php",
                    type: "POST",
                    data: formData,
                    success: function (result) {
                        console.log(result);
                        var resultObj = JSON.parse(result);
                        $("#saveMapVarbToOrderIT").removeClass("spinning");
                    },
                });
            } else {
                $('#messageModalBody').html("<p>Form is not valid, please correct</p>");
                $('#messageModal').modal('show');
                $("#saveMapVarbToOrderIT").removeClass("spinning");
            }
            $this.table.ajax.reload();
            $this.parent.parent.countRequestsForPortal();
        });
    }
}

export { mapVarbToOrderIt as default };