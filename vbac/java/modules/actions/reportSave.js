/*
 *
 *
 *
 */

let action = await cacheBustImport('./modules/actions/action.js');

class reportSave extends action {

    constructor(parent) {
        super(parent);
        this.listenForReportSave();
    }

    listenForReportSave() {
        var $this = this;
        $(document).on("click", "#reportSave", function (e) {
            var settings = $this.table.columns().visible().join(", ");
            $("#saveReportModal").modal("show");
            var searchBar = [];
            $("#personTable tfoot th").each(function () {
                var inputField = $(this).children()[0];
                var placeHolder = $(inputField).attr("placeholder");
                var searchValue = $(inputField).val();
                var searchObject = { placeHolder: placeHolder, value: searchValue };
                searchBar.push(searchObject);
            });
            var settingsJson = { settings: settings, searchBar: searchBar };
            $("#reportSettings").val(JSON.stringify(settingsJson));
        });
    }
}

export { reportSave as default };