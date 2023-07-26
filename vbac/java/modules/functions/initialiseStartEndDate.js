function initialiseStartEndDate() {
    $("#start_date").datepicker({
        dateFormat: "dd M yy",
        altField: "#start_date_db2",
        altFormat: "yy-mm-dd",
        maxDate: +100,
        onSelect: function (selectedDate) {
            $("#end_date").datepicker("option", "minDate", selectedDate);
        },
    });

    var startDate = $("#start_date").datepicker("getDate");

    $("#end_date").datepicker({
        dateFormat: "dd M yy",
        altField: "#end_date_db2",
        altFormat: "yy-mm-dd",
        minDate: startDate,
    });
}

export { initialiseStartEndDate as default };