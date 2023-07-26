function initialiseStartEndDateRegular() {
    $("#person_start_date").datepicker({
        dateFormat: "dd M yy",
        altField: "#person_start_date_db2",
        altFormat: "yy-mm-dd",
        maxDate: +100,
        onSelect: function (selectedDate) {
            $("#person_end_date").datepicker("option", "minDate", selectedDate);
        },
    });

    var startDate = $("#person_start_date").datepicker("getDate");

    $("#person_end_date").datepicker({
        dateFormat: "dd M yy",
        altField: "#person_end_date_db2",
        altFormat: "yy-mm-dd",
        minDate: startDate,
    });
}

export { initialiseStartEndDateRegular as default };