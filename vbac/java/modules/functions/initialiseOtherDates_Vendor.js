function initialiseOtherDatesVendor() {
    $("#resource_pes_cleared_date").attr("disabled", false);
    $("#resource_pes_cleared_date").datepicker({
        dateFormat: "dd M yy",
        altField: "#resource_pes_cleared_date_db2",
        altFormat: "yy-mm-dd",
        disabled: true,
    });

    $("#resource_pes_recheck_date").attr("disabled", false);
    $("#resource_pes_recheck_date").datepicker({
        dateFormat: "dd M yy",
        altField: "#resource_pes_recheck_date_db2",
        altFormat: "yy-mm-dd",
        disabled: true,
    });

    $("#resource_proposed_leaving_date").attr("disabled", false);
    $("#resource_proposed_leaving_date").datepicker({
        dateFormat: "dd M yy",
        altField: "#resource_proposed_leaving_date_db2",
        altFormat: "yy-mm-dd",
        disabled: true,
    });
}

export { initialiseOtherDatesVendor as default };