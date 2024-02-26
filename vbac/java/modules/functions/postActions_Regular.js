
let LBGLocation = await cacheBustImport('./modules/functions/selects/LBGLocation.js');
let agileTribe = await cacheBustImport('./modules/functions/selects/agileTribe.js');
let agileSquad = await cacheBustImport('./modules/functions/selects/agileSquad.js');

let agileTribeToSquadSelect = await cacheBustImport('./modules/functions/agileTribeToSquadSelect.js');

function postActions() {

    LBGLocation('person_LBG_LOCATION', 'person_originalLBG_LOCATION');
    agileTribe('person_TRIBE_NUMBER', 'person_originalTRIBE_NUMBER');
    agileSquad('person_SQUAD_NUMBER', 'person_originalSQUAD_NUMBER');

    agileTribeToSquadSelect('person_TRIBE_NUMBER', 'person_SQUAD_NUMBER');
}

export { postActions as default };