function initialiseProposedLeavingDate() {
    $("#proposed_leaving_date").attr("disabled", false);
    $("#proposed_leaving_date").datepicker({
        dateFormat: "dd M yy",
        altField: "#proposed_leaving_date_db2",
        altFormat: "yy-mm-dd",
    });
}

export { initialiseProposedLeavingDate as default };