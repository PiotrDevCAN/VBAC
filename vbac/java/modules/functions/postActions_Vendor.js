
let LBGLocation = await cacheBustImport('./modules/functions/selects/LBGLocation.js');

function postActions() {
    
    LBGLocation('person_LBGresource_LBG_LOCATION_LOCATION', 'resource_originalLBG_LOCATION');
}

export { postActions as default };