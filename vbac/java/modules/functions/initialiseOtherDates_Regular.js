function initialiseOtherDatesRegular() {
    $("#person_pes_cleared_date").attr("disabled", false);
    $("#person_pes_cleared_date").datepicker({
        dateFormat: "dd M yy",
        altField: "#person_pes_cleared_date_db2",
        altFormat: "yy-mm-dd",
        disabled: true,
    });

    $("#person_pes_recheck_date").attr("disabled", false);
    $("#person_pes_recheck_date").datepicker({
        dateFormat: "dd M yy",
        altField: "#person_pes_recheck_date_db2",
        altFormat: "yy-mm-dd",
        disabled: true,
    });

    $("#person_proposed_leaving_date").attr("disabled", false);
    $("#person_proposed_leaving_date").datepicker({
        dateFormat: "dd M yy",
        altField: "#person_proposed_leaving_date_db2",
        altFormat: "yy-mm-dd",
        disabled: true,
    });
}

export { initialiseOtherDatesRegular as default };