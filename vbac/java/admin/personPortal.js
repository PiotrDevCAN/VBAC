/*
 *
 *
 *
 */

let personTable = await cacheBustImport('./modules/tables/person.js');
let actions = await cacheBustImport('./modules/actions/person/personPortalActions.js');

let restartPesBox = await cacheBustImport('./modules/boxes/restartPesBox.js');
let amendPESLevelBox = await cacheBustImport('./modules/boxes/amendPESLevelBox.js');
let amendPESStatusBox = await cacheBustImport('./modules/boxes/amendPESStatusBox.js');

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

class personPortal {

    table;
    tableObj;

    constructor() {

        $('[data-toggle="tooltip"]').tooltip();
        $('[data-toggle="popover"]').popover();

        // pass table to actions
        // const Actions = new actions(this);

        if (document.open) {
            $(document).on('init.dt', function () {
                $('#footerNOTESID').val(document.open).trigger('change');
                console.log($('.btnEditPerson'));
                $('.btnEditPerson').trigger('click');
            });
        }
    }
}

const PersonPortal = new personPortal();

const PersonTable = new personTable(document.tableType);
PersonPortal.table = PersonTable.table;
PersonPortal.tableObj = PersonTable;

// pass table to actions
const Actions = new actions(PersonPortal);

const RestartPesBox = new restartPesBox(PersonPortal);
const AmendPesLevelBox = new amendPESLevelBox(PersonPortal);
const AmendPesStatusBox = new amendPESStatusBox(PersonPortal);

const EditRegularEntryBox = new editRegularPersonBox(PersonPortal);
const EditVendorEntryBox = new editVendorPersonBox(PersonPortal);
const EditEmailAddressBox = await new editEmailAddressBox(PersonPortal);

const PesInitiateFromPortalBox = new pesInitiateFromPortalBox(PersonPortal);
const SetFmFlagBox = new setFmFlagBox(PersonPortal);

const StopOffboardingBox = new stopOffboardingBox(PersonPortal);
const DeoffBoardingBox = new deoffBoardingBox(PersonPortal);
const OffboardedBox = new offboardedBox(PersonPortal);
const OffboardingBox = new offboardingBox(PersonPortal);
const ClearCtidBox = new clearCtidBox(PersonPortal);

const SetPmoStatusBox = new setPmoStatusBox(PersonPortal);
const SendPesEmailBox = new sendPesEmailBox(PersonPortal);
const TogglePesTrackerStatusDetailsBox = new togglePesTrackerStatusDetailsBox(PersonPortal);
const PesStopBox = new pesStopBox(PersonPortal);
const PesProgressingBox = new pesProgressingBox(PersonPortal);

const EditAgileNumberBox = new editAgileNumberBox(PersonPortal);
const ClearSquadNumberBox = new clearSquadNumberBox(PersonPortal);

export { PersonPortal as default };