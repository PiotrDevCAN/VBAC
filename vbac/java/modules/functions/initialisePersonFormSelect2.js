function initialisePersonFormSelect2() {
    $("#work_stream").select2();
    $("#work_stream").trigger("change");
    $("#person_preboarded").select2();
    $("#LBG_LOCATION").select2();
    $("#FM_CNUM").select2();
    $("#lob").select2();
    $("#skill_set_id").select2();
    $("#pesLevel").select2();
}

export { initialisePersonFormSelect2 as default };