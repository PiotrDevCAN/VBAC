function initialiseStartEndDateVendor() {
    $("#resource_start_date").datepicker({
        dateFormat: "dd M yy",
        altField: "#resource_start_date_db2",
        altFormat: "yy-mm-dd",
        maxDate: +100,
        onSelect: function (selectedDate) {
            $("#resource_end_date").datepicker("option", "minDate", selectedDate);
        },
    });

    var startDate = $("#resource_start_date").datepicker("getDate");

    $("#resource_end_date").datepicker({
        dateFormat: "dd M yy",
        altField: "#resource_end_date_db2",
        altFormat: "yy-mm-dd",
        minDate: startDate,
    });
}

export { initialiseStartEndDateVendor as default };