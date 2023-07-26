/*
 *
 *
 *
 */

let pesTrackerTable = await cacheBustImport('./modules/tables/pesTracker.js');
let pesevent = await cacheBustImport('./modules/pesEvent.js');
let restartPesBox = await cacheBustImport('./modules/boxes/restartPesBox.js');
let amendPESLevelBox = await cacheBustImport('./modules/boxes/amendPESLevelBox.js');
let amendPESStatusBox = await cacheBustImport('./modules/boxes/amendPESStatusBox.js');

let pesInitiateFromPesTrackerBox = await cacheBustImport('./modules/boxes/person/pesInitiateFromPesTrackerBox.js');
let sendPesEmailBox = await cacheBustImport('./modules/boxes/person/sendPesEmailBox.js');
let togglePesTrackerStatusDetailsBox = await cacheBustImport('./modules/boxes/person/togglePesTrackerStatusDetailsBox.js');

class PESTracker {

    table;
    tableObj;

    constructor() {
        // transform to boxes
        pesevent.listenForBtnRecordSelection();
        pesevent.listenForPesStageValueChange();
        pesevent.listenForSavePesComment();
        pesevent.listenForPesProcessStatusChange();
        pesevent.listenForPesPriorityChange();
        pesevent.listenForFilterPriority();
        pesevent.listenForFilterProcess();
        pesevent.listenForBtnChaser();
        pesevent.listenForBtnSetPesLevel();
    }
}

const PesTracker = new PESTracker();

const PesTrackerTable = new pesTrackerTable(document.tableType);
// PesTracker.table = PesTrackerTable.table;
// PesTracker.tableObj = PesTrackerTable;
PesTracker.table = false;
PesTracker.tableObj = false;

const RestartPesBox = new restartPesBox(PesTracker);
const AmendPesLevelBox = new amendPESLevelBox(PesTracker);
const AmendPesStatusBox = new amendPESStatusBox(PesTracker);

const PesInitiateFromPesTrackerBox = new pesInitiateFromPesTrackerBox(PesTracker);
const SendPesEmailBox = new sendPesEmailBox(PesTracker);
const TogglePesTrackerStatusDetailsBox = new togglePesTrackerStatusDetailsBox(PesTracker);

export { PesTracker as default };