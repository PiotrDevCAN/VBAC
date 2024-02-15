/*
 *
 *
 *
 */

let personTable = await cacheBustImport('./modules/tables/person.js');
let actions = await cacheBustImport('./modules/actions/person/personPortalActions.js');

let restartPesBox = await cacheBustImport('./modules/boxes/PES/restartPesBox.js');
let amendPESLevelBox = await cacheBustImport('./modules/boxes/PES/amendPESLevelBox.js');
let amendPESStatusBox = await cacheBustImport('./modules/boxes/PES/amendPESStatusBox.js');

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

class personPortalArchive {

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

const PersonPortalArchive = new personPortalArchive();

const PersonTable = new personTable(document.tableType);
PersonPortalArchive.table = PersonTable.table;
PersonPortalArchive.tableObj = PersonTable;

// pass table to actions
const Actions = new actions(PersonPortalArchive);

const RestartPesBox = new restartPesBox(PersonPortalArchive);
const AmendPesLevelBox = new amendPESLevelBox(PersonPortalArchive);
const AmendPesStatusBox = new amendPESStatusBox(PersonPortalArchive);

const EditRegularEntryBox = new editRegularPersonBox(PersonPortalArchive);
const EditVendorEntryBox = new editVendorPersonBox(PersonPortalArchive);
const EditEmailAddressBox = await new editEmailAddressBox(PersonPortalArchive);

const PesInitiateFromPortalBox = new pesInitiateFromPortalBox(PersonPortalArchive);
const SetFmFlagBox = new setFmFlagBox(PersonPortalArchive);

const StopOffboardingBox = new stopOffboardingBox(PersonPortalArchive);
const DeoffBoardingBox = new deoffBoardingBox(PersonPortalArchive);
const OffboardedBox = new offboardedBox(PersonPortalArchive);
const OffboardingBox = new offboardingBox(PersonPortalArchive);
const EditCtidBox = new editCtidBox(PersonPortalArchive);
const ClearCtidBox = new clearCtidBox(PersonPortalArchive);

const SetPmoStatusBox = new setPmoStatusBox(PersonPortalArchive);
const SendPesEmailBox = new sendPesEmailBox(PersonPortalArchive);
const TogglePesTrackerStatusDetailsBox = new togglePesTrackerStatusDetailsBox(PersonPortalArchive);
const PesStopBox = new pesStopBox(PersonPortalArchive);
const PesProgressingBox = new pesProgressingBox(PersonPortalArchive);

const EditAgileNumberBox = new editAgileNumberBox(PersonPortalArchive);
const ClearSquadNumberBox = new clearSquadNumberBox(PersonPortalArchive);

export { PersonPortalArchive as default };