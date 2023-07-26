/*
 *
 *
 *
 */

let personLiteTable = await cacheBustImport('./modules/tables/personLite.js');

let actions = await cacheBustImport('./modules/actions/person/personPortalLiteActions.js');

let amendPESLevelBox = await cacheBustImport('./modules/boxes/amendPESLevelBox.js');
let amendPESStatusBox = await cacheBustImport('./modules/boxes/amendPESStatusBox.js');
let restartPesBox = await cacheBustImport('./modules/boxes/restartPesBox.js');

let editRegularPersonBox = await cacheBustImport('./modules/boxes/person/editRegularPersonBox.js');
let editVendorPersonBox = await cacheBustImport('./modules/boxes/person/editVendorPersonBox.js');
let editEmailAddressBox = await cacheBustImport('./modules/boxes/person/editEmailAddressBox.js');

let pesInitiateFromPortalBox = await cacheBustImport('./modules/boxes/person/pesInitiateFromPortalBox.js');
let setFmFlagBox = await cacheBustImport('./modules/boxes/person/setFmFlagBox.js');
let stopOffboardingBox = await cacheBustImport('./modules/boxes/person/stopOffboardingBox.js');
let deoffBoardingBox = await cacheBustImport('./modules/boxes/person/deoffBoardingBox.js');
let offboardedBox = await cacheBustImport('./modules/boxes/person/completeOffboardingBox.js');
let offboardingBox = await cacheBustImport('./modules/boxes/person/initiateOffboardingBox.js');
let clearCtidBox = await cacheBustImport('./modules/boxes/person/clearCtidBox.js');

let setPmoStatusBox = await cacheBustImport('./modules/boxes/person/setPmoStatusBox.js');
let sendPesEmailBox = await cacheBustImport('./modules/boxes/person/sendPesEmailBox.js');
let togglePesTrackerStatusDetailsBox = await cacheBustImport('./modules/boxes/person/togglePesTrackerStatusDetailsBox.js');
let pesStopBox = await cacheBustImport('./modules/boxes/person/pesStopBox.js');
let pesProgressingBox = await cacheBustImport('./modules/boxes/person/pesProgressingBox.js');

let editAgileNumberBox = await cacheBustImport('./modules/boxes/person/editAgileNumberBox.js');
let clearSquadNumberBox = await cacheBustImport('./modules/boxes/person/clearSquadNumberBox.js');

class personPortalLite {

    table;
    tableObj;

    constructor() {

        $('[data-toggle="tooltip"]').tooltip();
        $('[data-toggle="popover"]').popover();

        // pass table to actions
        // const Actions = new actions(this);
    }
}

const PersonPortalLite = new personPortalLite();

const PersonLiteTable = new personLiteTable(document.tableType);
PersonPortalLite.table = PersonLiteTable.table;
PersonPortalLite.tableObj = PersonLiteTable;

// pass table to actions
const Actions = new actions(PersonPortalLite);

const RestartPesBox = new restartPesBox(PersonPortalLite);
const AmendPesLevelBox = new amendPESLevelBox(PersonPortalLite);
const AmendPesStatusBox = new amendPESStatusBox(PersonPortalLite);

const EditRegularEntryBox = new editRegularPersonBox(PersonPortalLite);
const EditVendorEntryBox = new editVendorPersonBox(PersonPortalLite);
const EditEmailAddressBox = await new editEmailAddressBox(PersonPortalLite);

const PesInitiateFromPortalBox = new pesInitiateFromPortalBox(PersonPortalLite);
const SetFmFlagBox = new setFmFlagBox(PersonPortalLite);

const StopOffboardingBox = new stopOffboardingBox(PersonPortalLite);
const DeoffBoardingBox = new deoffBoardingBox(PersonPortalLite);
const OffboardedBox = new offboardedBox(PersonPortalLite);
const OffboardingBox = new offboardingBox(PersonPortalLite);
const ClearCtidBox = new clearCtidBox(PersonPortalLite);

const SetPmoStatusBox = new setPmoStatusBox(PersonPortalLite);
const SendPesEmailBox = new sendPesEmailBox(PersonPortalLite);
const TogglePesTrackerStatusDetailsBox = new togglePesTrackerStatusDetailsBox(PersonPortalLite);
const PesStopBox = new pesStopBox(PersonPortalLite);
const PesProgressingBox = new pesProgressingBox(PersonPortalLite);

const EditAgileNumberBox = new editAgileNumberBox(PersonPortalLite);
const ClearSquadNumberBox = new clearSquadNumberBox(PersonPortalLite);

export { PersonPortalLite as default };