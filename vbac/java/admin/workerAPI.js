/*
 *
 *
 *
 */

class workerAPI {

  static formId = 'workerAPIlookupForm';

  static activeAlert = 'activeAlert';
  static notActiveAlert = 'notActiveAlert';

  static managerAlert = 'managerAlert';
  static notManagerAlert = 'notManagerAlert';

  static emptyCNUMAlert = 'emptyCNUMAlert';
  static emptyWorkerIdAlert = 'emptyWorkerIdAlert';
  static emptyEmailAlert = 'emptyEmailAlert';

  constructor() {

    this.listenForName();
  }

  deleteItems() {
    $('.alert').addClass('hide');
    $('.employee-data').remove();
  }

  addItem(attr, value) {
    let parent = document.getElementById(workerAPI.formId);

    let group = document.createElement('div');
    group.setAttribute('class', 'form-group employee-data');

    let label = document.createElement('label');
    label.setAttribute('class', 'col-md-2 control-label ceta-label-left');
    label.appendChild(document.createTextNode(attr));

    let p = document.createElement('p');
    p.setAttribute('class', 'form-control');
    p.appendChild(document.createTextNode(value));

    let rightDiv = document.createElement('div');
    rightDiv.setAttribute('class', 'col-md-6');

    rightDiv.appendChild(p);

    group.appendChild(label);
    group.appendChild(rightDiv);

    parent.appendChild(group);
  }

  listenForName() {
    var $this = this;
    $(".typeahead").bind("typeahead:select", async function (ev, suggestion) {
      $(".tt-menu").hide();

      $this.deleteItems();

      let suggestionKeys = Object.keys(suggestion);

      // check if employee has CNUM
      if ($.inArray('cnum', suggestionKeys) == -1) {
        $('#' + workerAPI.emptyCNUMAlert).removeClass('hide');
      } else {
        $('#' + workerAPI.emptyCNUMAlert).addClass('hide');
      }

      // check if employee has workerID
      if ($.inArray('workerID', suggestionKeys) == -1) {
        $('#' + workerAPI.emptyWorkerIdAlert).removeClass('hide');
      } else {
        $('#' + workerAPI.emptyWorkerIdAlert).addClass('hide');
      }

      // check if employee has Email Address
      if ($.inArray('email', suggestionKeys) == -1) {
        $('#' + workerAPI.emptyEmailAlert).removeClass('hide');
      } else {
        $('#' + workerAPI.emptyEmailAlert).addClass('hide');
      }

      let suggestionArr = Object.entries(suggestion);
      for (const [key, value] of suggestionArr) {

        // check if employee is active
        if (key == 'isActive') {
          if (value == true) {
            $('#' + workerAPI.activeAlert).removeClass('hide');
            $('#' + workerAPI.notActiveAlert).addClass('hide');
          } else {
            $('#' + workerAPI.activeAlert).addClass('hide');
            $('#' + workerAPI.notActiveAlert).removeClass('hide');
          }
        }

        // check if employee is a manager
        if (key == 'isManager') {
          if (value == true) {
            $('#' + workerAPI.managerAlert).removeClass('hide');
            $('#' + workerAPI.notManagerAlert).addClass('hide');
          } else {
            $('#' + workerAPI.managerAlert).addClass('hide');
            $('#' + workerAPI.notManagerAlert).removeClass('hide');
          }
        }

        // check if employee has CNUM
        if (key == 'cnum') {
          var trimmedCNUM = value.trim();
          if (trimmedCNUM == "") {
            $('#' + workerAPI.emptyCNUMAlert).removeClass('hide');
          } else {
            $('#' + workerAPI.emptyCNUMAlert).addClass('hide');
          }
        }

        // check if employee has Worker Id
        if (key == 'workerID') {
          if (value == "") {
            $('#' + workerAPI.emptyWorkerIdAlert).removeClass('hide');
          } else {
            $('#' + workerAPI.emptyWorkerIdAlert).addClass('hide');
          }
        }

        // check if employee has Email Address
        if (key == 'email') {
          var trimmedEmail = value.trim();
          if (trimmedEmail == "") {
            $('#' + workerAPI.emptyEmailAlert).removeClass('hide');
          } else {
            $('#' + workerAPI.emptyEmailAlert).addClass('hide');
          }
        }

        $this.addItem(key, value);
      }
    });
  }
}

const WorkerAPI = new workerAPI();

export { WorkerAPI as default };