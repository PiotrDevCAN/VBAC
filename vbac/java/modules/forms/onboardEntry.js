/**
 *
 */

class onboardEntry {

  child;

  constructor(child) {
    console.log('+++ Function +++ onboardEntry.constructor');

    this.child = child;

    if (this.constructor == onboardEntry) {
      throw new Error('Cannot create a instance of Abstract class');
    }

    this.listenForSaveBoarding();

    console.log('--- Function --- onboardEntry.constructor');
  }

  listenForSaveBoarding() {
    var $this = this;
    $(document).on("click", "#" + $this.child.saveButtonId, function () {
      $(this).attr("disabled", true);
      var form = $("#" + $this.child.formId);
      var saveButton = $("#" + $this.child.saveButtonId);
      var initiatePesButton = $("#" + $this.child.initiatePesButtonId);
      $this.child.saveBoarding("Save", form, saveButton, initiatePesButton);
    });
  }
}

export { onboardEntry as default };