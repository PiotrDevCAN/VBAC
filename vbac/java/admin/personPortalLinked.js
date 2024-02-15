/*
 *
 *
 *
 */

let personLinkedTable = await cacheBustImport('./modules/tables/personLinked.js');

let actions = await cacheBustImport('./modules/actions/person/personPortalLinkedActions.js');

let amendPESLevelBox = await cacheBustImport('./modules/boxes/PES/amendPESLevelBox.js');
let amendPESStatusBox = await cacheBustImport('./modules/boxes/PES/amendPESStatusBox.js');
let restartPesBox = await cacheBustImport('./modules/boxes/PES/restartPesBox.js');

let editRegularPersonBox = await cacheBustImport('./modules/boxes/person/editRegularPersonBox.js');
let editVendorPersonBox = await cacheBustImport('./modules/boxes/person/editVendorPersonBox.js');
let editEmailAddressBox = await cacheBustImport('./modules/boxes/person/editEmailAddressBox.js');

let pesInitiateFromPortalBox = await cacheBustImport('./modules/boxes/PES/pesInitiateFromPortalBox.js');
let setFmFlagBox = await cacheBustImport('./modules/boxes/person/setFmFlagBox.js');
let stopOffboardingBox = await cacheBustImport('./modules/boxes/person/stopOffboardingBox.js');
let deoffBoardingBox = await cacheBustImport('./modules/boxes/person/deoffBoardingBox.js');
let offboardedBox = await cacheBustImport('./modules/boxes/person/completeOffboardingBox.js');
let offboardingBox = await cacheBustImport('./modules/boxes/person/initiateOffboardingBox.js');
let editCtidBox = await cacheBustImport('./modules/boxes/person/editCtidBox.js');
let clearCtidBox = await cacheBustImport('./modules/boxes/person/clearCtidBox.js');

let setPmoStatusBox = await cacheBustImport('./modules/boxes/person/setPmoStatusBox.js');
let sendPesEmailBox = await cacheBustImport('./modules/boxes/person/sendPesEmailBox.js');
let togglePesTrackerStatusDetailsBox = await cacheBustImport('./modules/boxes/person/togglePesTrackerStatusDetailsBox.js');
let pesStopBox = await cacheBustImport('./modules/boxes/PES/pesStopBox.js');
let pesProgressingBox = await cacheBustImport('./modules/boxes/PES/pesProgressingBox.js');

let editAgileNumberBox = await cacheBustImport('./modules/boxes/person/editAgileNumberBox.js');
let clearSquadNumberBox = await cacheBustImport('./modules/boxes/person/clearSquadNumberBox.js');

class personPortalLinked {

    table;
    tableObj;

    constructor() {

        $('[data-toggle="tooltip"]').tooltip();
        $('[data-toggle="popover"]').popover();

        // pass table to actions
        // const Actions = new actions(this);
    }
}

const PersonPortalLinked = new personPortalLinked();

const PersonLinkedTable = new personLinkedTable(document.tableType);
PersonPortalLinked.table = PersonLinkedTable.table;
PersonPortalLinked.tableObj = PersonLinkedTable;

// pass table to actions
const Actions = new actions(PersonPortalLinked);

const RestartPesBox = new restartPesBox(PersonPortalLinked);
const AmendPesLevelBox = new amendPESLevelBox(PersonPortalLinked);
const AmendPesStatusBox = new amendPESStatusBox(PersonPortalLinked);

const EditRegularEntryBox = new editRegularPersonBox(PersonPortalLinked);
const EditVendorEntryBox = new editVendorPersonBox(PersonPortalLinked);
const EditEmailAddressBox = await new editEmailAddressBox(PersonPortalLinked);

const PesInitiateFromPortalBox = new pesInitiateFromPortalBox(PersonPortalLinked);
const SetFmFlagBox = new setFmFlagBox(PersonPortalLinked);

const StopOffboardingBox = new stopOffboardingBox(PersonPortalLinked);
const DeoffBoardingBox = new deoffBoardingBox(PersonPortalLinked);
const OffboardedBox = new offboardedBox(PersonPortalLinked);
const OffboardingBox = new offboardingBox(PersonPortalLinked);
const EditCtidBox = new editCtidBox(PersonPortalLinked);
const ClearCtidBox = new clearCtidBox(PersonPortalLinked);

const SetPmoStatusBox = new setPmoStatusBox(PersonPortalLinked);
const SendPesEmailBox = new sendPesEmailBox(PersonPortalLinked);
const TogglePesTrackerStatusDetailsBox = new togglePesTrackerStatusDetailsBox(PersonPortalLinked);
const PesStopBox = new pesStopBox(PersonPortalLinked);
const PesProgressingBox = new pesProgressingBox(PersonPortalLinked);

const EditAgileNumberBox = new editAgileNumberBox(PersonPortalLinked);
const ClearSquadNumberBox = new clearSquadNumberBox(PersonPortalLinked);

export { PersonPortalLinked as default };