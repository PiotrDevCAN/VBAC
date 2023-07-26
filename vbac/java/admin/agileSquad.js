/*
 *
 *
 *
 */

let buttonCommon = await cacheBustImport('./modules/functions/buttonCommon.js');
let formatTribeName = await cacheBustImport('./modules/functions/formatTribeName.js');

let agileSquadTable = await cacheBustImport('./modules/tables/agileSquad.js');
let editSquadBox = await cacheBustImport('./modules/boxes/editSquadBox.js');

class agileSquad {

  table;
  tableObj;

  constructor() {
    this.version = $('#version').prop('checked') ? 'Original' : 'New';
    $('#version').bootstrapToggle();
    $('#version').change({ tribe: this.table }, function (event) {
      console.log(event);
      console.log(event.data);
      event.data.tribe.ajax.reload();
    });

    this.initialiseTribeNumber();
    // this.setListenerForOrganisation();

    $('#version').bootstrapToggle();
    $('#version').attr('disabled', true);
  }

  initialiseTribeNumber(selectedTribeNumber) {
    if ($('#TRIBE_NUMBER').hasClass("select2-hidden-accessible")) {
      // 	Select2 has been initialized
      $('#TRIBE_NUMBER').empty().trigger('change');
      $('#TRIBE_NUMBER').select2('destroy');
    }

    $('#TRIBE_NUMBER').select2({
      data: tribes,
      templateResult: formatTribeName
    });

    if (selectedTribeNumber) {
      $('#TRIBE_NUMBER').val(selectedTribeNumber).trigger('change');
    }
    $('#SHIFT').select2();
  }

  initialiseTribeNumberPerOrganisation(selectedTribeNumber) {
    var version = $('#version').prop('checked') ? 'Original' : 'New';
    var organisation = $('#radioTribeOrganisationManaged').prop('checked') ? 'Managed' : 'Project';
    var tribeSelectName = 'tribes' + version + organisation;
    var tribesSelect = eval(tribeSelectName);
    if ($('#TRIBE_NUMBER').hasClass("select2-hidden-accessible")) {
      // 	Select2 has been initialized
      $('#TRIBE_NUMBER').empty().trigger('change');
      $('#TRIBE_NUMBER').select2('destroy');
    }

    $('#TRIBE_NUMBER').select2({
      data: tribesSelect,
      templateResult: formatTribeName
    });

    if (selectedTribeNumber) {
      $('#TRIBE_NUMBER').val(selectedTribeNumber).trigger('change');
    }
    $('#SHIFT').select2();
  }

  // Set the listener for change to Organisation
  /*
  setListenerForOrganisation() {
    var $this = this;
    console.log('set listener for radio button');
    $('input[type=radio]').click(function () {
      $this.initialiseTribeNumber();
    });
  }
  */
}

const AgileSquad = new agileSquad();

const AgileSquadTable = new agileSquadTable(AgileSquad.version);
AgileSquad.table = AgileSquadTable.table;
AgileSquad.tableObj = AgileSquadTable;

const EditSquadBox = new editSquadBox(AgileSquad);

export { AgileSquad as default };