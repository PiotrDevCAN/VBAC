<?php
namespace itdq;

/**
 * Provides Methods that create Input fields in an HTML Form and loads them into the descending classes variables.
 *
 * Most classes that use forms will be descended from this class.
 *
 * @author GB001399
 * @package itdqLib
 *
 */
class FormClass
{

    protected $state;

    protected $chkState;

    protected $notEditable;

    public $mode;

    private $fcFormName;

    /**
     * Only just learned the benefit of Static variables, so have defined some here for MODE - but they are not used throughout the code
     * You'll find examples where I've hardcoded the string 'Display' etc.
     *
     * @static
     *
     */
    public static $modeDISPLAY = 'Display';

    public static $modeDEFINE = 'Define';

    public static $modeEDIT = 'edit';

    public static $bpFieldIntranet = 'Intranet';

    public static $bpFieldNotesid = 'Notesid';

    public static $bpFieldBoth = 'Both';

    public static $bpFieldBothUid = 'Uid';

    public static $javaValidateStart = "<script>
				  	function checkSubmit(){
			      	var form = document.Form.fcFormName.value;
				  	switch(form){";

    public static $javaValidateEnd = "	default:
				   	break;
				  	}
				  }
				  </script>";

    public static $yesNo = array(
        'Yes'=>'Yes',
        'No'=>'No'
    );

    public static $trueFalse = array(
        'True' => 'True',
        'False' => 'False'
    );

    public static $ragSelect = array(
        'Red',
        'Amber',
        'Green'
    );

    public static $formNewRow = true;

    public static $formNotNewRow = false;

    public static $formDisabled = ' DISABLED ';

    public static $formReadonly = ' READONLY ';

    public static $formNull = null;

    public static $formNoHelp = null;

    public static $formNoOnChange = null;

    public static $formNoHighlight = FALSE;

    public static $formFirstSelect = 'Select.....';

    public static $formClass = 'blue-med-light';

    public static $db2TimeFormat = 'Y-m-d H:i:s';

    public static $ragRed = 'red';

    public static $ragAmber = 'yellow';

    public static $ragGreen = '#00ff00';

    public static $formDummySelectArray = array(
        'Select....'
    );

    public static $labelColCssReadOnly = "col-sm-3";
    public static $fieldColCssReadOnly = "col-sm-9";


    public $toolTipPosition = 'left';
    public $toolTip = '';
    public $onChange='';




    const SELECT_DISPLAY_VALUE_RETURN_VALUE = 'displayValueReturnValue';
    const SELECT_DISPLAY_VALUE_RETURN_KEY   = 'displayValueReturnKey';
    const SELECT_DISPLAY_KEY_RETURN_VALUE   = 'displayKeyReturnValue';
    const SELECT_DISPLAY_KEY_RETURN_KEY     = 'displayKeyReturnKey';


    function __construct(){
        $this->enableDisabledSelects();
    }




    /**
     * Will step through all the Variables in the Class and if there is an element in the $_REQUEST module
     * of the same name, then it will populate the Variable with the value from $_REQUEST.
     * So basically, any classes that descend from this class, can pick up value from the screen directly into
     * the class' variables.
     */
    function getForm()
    {
        foreach ($this as $key => $value) {
            if (isset($_REQUEST[$key])) {
               !is_array($_REQUEST[$key]) ? trim($_REQUEST[$key]) : null;
               $this->{$key} = $this->validateFormField($key, $_REQUEST[$key]);
            }
        }
        $_SESSION['fcFormName'] = $this->fcFormName;
    }

    /**
     *
     * If $_REQUEST[$fieldName] is populated(even with null), you'll get that value back
     * Else If $_SESSION[$fieldName] is populated(even with null), you'll get that value back
     * Else you'll get null back.
     *
     * @param unknown_type $fieldName
     * @return string|unknown|NULL
     */
    static function getFormFieldValue($fieldName)
    {
        if (isset($_REQUEST[$fieldName])) {
            return trim($_REQUEST[$fieldName]);
        } elseif (isset($_SESSION[$fieldName])) {
            return $_SESSION[$fieldName];
        } else {
            return NULL;
        }
    }

    /**
     * Clear a field in the form that is displayed
     *
     * @param string $propertyName
     */
    function clearProperty($propertyName){
        $this->$propertyName = null;
    }

    /*
     * Runs a js script to enable disabled selects
     */
    function enableDisabledSelects(){
        ?>
            <script type="text/javascript">
            $(document).ready(function() {
                 $('.disabledSelect').attr('disabled', 'disabled');
                 });

            $('form').submit(function() {
                $('select').removeAttr('disabled');
                });
       		</script>
            <?php
        }

    /*
     * Allows you to perform validation on the field from the onscreen Form.
     * Expects you to return a validated value that will then be stored in $this->$field.
     */
    function validateFormField($field, $value)
    {
        return $value;
    }

    /**
     * Not sure this does anything useful - I think it can be ignored.
     *
     * @param string $name
     */
    function setfcformName($name)
    {
        $this->fcFormName = $name;
    }

    /**
     * Used for Debugging.
     * It will display the values of any Variables in the class.
     * It will iteritvely call it's self, should any of those variables contain other Objects.
     * Each field is displayed with it's name in Bold then it's current value.
     *
     * By default, the variables follow on after the other, specify $length as anything other than 'short' and
     * each variable will be displayed on it's own line.
     *
     * @param string $length
     */
    function iterateVisible($length = 'short', $dbFieldsOnly = true, $color = '#888888')
    {
        echo "<span style='color:$color'>";
        echo "<b>" . get_class($this) . ":</b><br/>\n";
        if ($length != 'short') {
            echo "<br>";
        }
        foreach ($this as $key => $value) {
            if (! $dbFieldsOnly and is_array($value)) {
                echo "<b>$key =></b>";
                echo "<span style='color:red'>";
                print_r($value);
                echo "</span>";
            } elseif (! $dbFieldsOnly and is_object($value)) {
                echo "<br/><b> OBJECT (" . get_class($value) . "): $key</b>";
                $value->iterateVisible($length);
                echo "<b>:OBJECT END </b><br/>";
            } elseif (is_a($value,'DateTime')){
                echo $value->format('y-m-d h:i:s');
            } else {
                if ($dbFieldsOnly and $key == strtoupper($key))
                    print "<b>$key =></b><span style='color:red'>$value</span>\n";
            }

            if ($length != 'short') {
                echo "<br>";
            }
        }
        echo "</span>";
    }

    /**
     * Used for Debugging.
     * It will display the values of any Variables in the class.
     * It will iteritvely call it's self, should any of those variables contain other Objects.
     * Each field is displayed with it's name in Bold then it's current value.
     *
     * By default, the variables follow on after the other, specify $length as anything other than 'short' and
     * each variable will be displayed on it's own line.
     *
     * @param string $length
     */
    function iterateSpecificFields($fieldsToDisplay = null, $length = 'short')
    {
        echo "<B>" . get_class($this) . "::iterateFieldsToDisplay()</B><BR/>\n";
        if ($length != 'short') {
            echo "<BR>";
        }
        foreach ($fieldsToDisplay as $field) {
            foreach ($this as $key => $value) {
                if ($key == $field) {
                    if (is_array($value)) {
                        echo "<B>$key =></B>";
                        print_r($value);
                    } elseif (is_object($value)) {
                        echo "<BR/><B> OBJECT (" . get_class($value) . "): $key</B>";
                        $value->iterateVisible($length);
                        echo "<B>:OBJECT END </B><BR/>";
                    } else {
                        print "<B>$key =></B> $value\n";
                    }
                    if ($length != 'short') {
                        echo "<BR>";
                    }
                }
            }
        }
    }

    /**
     * Used for Debugging.
     * Rather than displaying the contents of the Class on the screen, it returns an XML Form.
     *
     * @param string $class
     *            names the 'object' being defined in the XML.
     * @return string the XML form.
     */
    function toXML($class = 'object')
    {
        $xmlReply = new \XMLWriter();
        $xmlReply->startElement($class);

        foreach ($this as $key => $value) {
            if (is_array($value)) {
                $xmlReply->writeElement($key, print_r($value));
                // $xml .= "<$key>";
                // $xml .= print_r ( $value );
                // $xml .= "</$key>\n";
            } else {
                $xmlReply->writeElement($key, trim($value));
                // $xml .= "<$key>" . trim($value) . "</$key>\n";
            }
        }
        // $xml .= "</$class>";
        $xmlReply->endElement();
        return $xmlReply->outputMemory();
    }

    /**
     * Creates a TextArea input field on the screen
     *
     * @param string $title
     *            displayed as a seperate Cell, typically would be the label for this input field.
     * @param string $fieldName
     *            becomes the ID and the NAME of the TEXTAREA, so this needs to be the variable in the Class that you want GetForm to return the value of this field to.
     * @param string $state
     *            the field be DISABLED, READONLY etc.
     * @param string $class
     *            Class to format the field.
     * @param string $help
     *            text in here is displayed when the mouse id hovered over the field.
     * @param string $maxLength
     *            length expected on the Input field. But you need to code a javascript form checker to impose it. NOTE: The last digit can be used to determin how many rows on the screen the textarea will occupy. 503 would be a 500 byte field over 3 lines.
     */
function formTextArea($label, $fieldName, $state = null, $textAreaclass = null, $help = null, $maxlength = 15000, $dataPlacement = 'top', $textAreaDivClass = null, $rows = 2, $placeholder = null)
    {
        if (strtoupper($state) == "READONLY") {
            ?>
            <div class='form-group' id='<?=$fieldName;?>" . "FormGroup'>
            <label for='<?=$fieldName;?>' class='col-md-2 control-label'><?=$label;?></label>
            <div class='col-md-10'>
            <div class='<?=$textAreaDivClass;?>'>
            <textarea rows='<?=$rows;?>' class='form-control <?=$textAreaclass;?>' id='<?=$fieldName;?>' name='<?=$fieldName;?>' <?=$state;?> maxlength='<?=$maxlength;?>' placeholder='<?=$placeholder;?>' readonly><?=trim($this->$fieldName)?></textarea>
            </div>
            </div>
            </div>
            <?php
        } else {
            ?>
            <div class='form-group' id='<?=$fieldName;?>" . "FormGroup' >
            <label for='<?=$fieldName;?>' class='col-md-2 control-label' data-toggle='tooltip' data-placement='<?=$dataPlacement;?>' title='<?=$help;?>' ><?=$label;?></label>
            <div class='col-md-10'>
            <div id='spaceForGlyphicon' style='display: none;'></div>
            <div class='<?=$textAreaDivClass;?>'>
            <textarea rows='<?=$rows;?>' class='form-control <?=$textAreaclass;?>' id='<?=$fieldName;?>' name='<?=$fieldName;?>' <?=$state;?> maxLength='<?=$maxlength;?>' placeholder='<?=$placeholder;?>'><?=trim($this->$fieldName);?></textarea>
            <div id='textarea_feedback<?=$fieldName;?>' class='textAreaFeedback'></div>
            </div>
            </div>
            </div>
            <script>
            $(document).ready(function() {
                var text_max = <?=$maxlength?>;
                $('#textarea_feedback<?=$fieldName?>').html(text_max + ' characters remaining');

                $('#<?=$fieldName?>').keyup(function() {
                    var text_length = $('#<?=$fieldName?>').val().length;
                    var text_remaining = text_max - text_length;

                    $('#textarea_feedback<?=$fieldName?>').html(text_remaining + ' characters remaining');
                    if(text_remaining<=0){
                        $("#<?=$fieldName?>FormGroup").addClass("has-error");
                        $("#textarea_feedback<?=$fieldName?>").addClass("textarea-full");
                    } else {
                   	 	$("#<?=$fieldName?>FormGroup").removeClass("has-error");
                      	 $("#textarea_feedback<?=$fieldName?>").removeClass("textarea-full");
                    }
                });
            });
            </script>
            <?php
        }
    }

    /**
     * Creates a INPUT HTML Tag, in two cells, $title in a TH cell and INPUT in a TD cell.
     *
     * @param string $title
     *            for the input field, displayed in it's own <TH> cell
     * @param string $fieldName
     *            is the ID and the NAME of the input field, so this needs to be the variable in the Class that you want GetForm to return the value of this field to.
     * @param string $state
     *            the field be DISABLED, READONLY etc.
     * @param string $size
     *            the Size of the input field.
     * @param string $onchange
     *            you can pass the javascript function to be invoked when the field changeds.
     * @param string $typeAttribute
     *            type attribute - default is text.
     */
    function formInput($title, $fieldName, $state = null, $class='col-md-10', $help=null,  $onchange = null, $dataPlacement = 'top', $typeAttribute = 'text', $placeHolder=null)
    {
        $placeHolder = empty($placeHolder) ? "Enter $title" : $placeHolder;
        if ($state == "READONLY") {
            ?>
            <div class="form-group" id='<?=$fieldName?>FormGroup' >
               <label for='<?=$fieldName?>' class='col-md-2 control-label ceta-label-left'><?=$title?></label>
               <div class='<?=$class?>'>
                    <input class='form-control' type='<?=$typeAttribute?>' id='<?=$fieldName?>' name='<?=$fieldName?>' value='<?=$this->$fieldName;?>' <?=$state?> placeholder='<?=$placeHolder?>' <?=$onchange?> />
               </div>
               </div>

                <?php

        } else {
                ?>
                <div class="form-group" id='<?=$fieldName?>FormGroup' >
                    <label for='<?=$fieldName?>' class='col-md-2 control-label ceta-label-left' data-toggle='tooltip' data-placement='<?=$dataPlacement?>' title='<?=$help?>'><?=$title?></label>
                    <div class='<?=$class?>'>
                        <input class='form-control'  type='<?=$typeAttribute?>' id='<?=$fieldName?>' name='<?=$fieldName?>' value='<?php echo trim($this->$fieldName);?>' <?=$state?> placeholder='<?=$placeHolder?>' <?=$onchange?> required='required' />
                    	<input type='hidden' id='original<?=$fieldName?>' name='original<?=$fieldName?>' value='<?=trim($this->$fieldName)?>' />
                    </div>
                </div>
                <?php

        }
    }


    function formFileInput($title, $fieldName, $state = null, $multiple = 'true', $uploadPath)
    {
        $dir = $uploadPath;
        if ($state == "READONLY") {
            // get the dir listing and show the files currently uploaded

            // Open a directory, and read its contents
            ?>
            <p class="ibm-form-elem-grp" id='<?php echo $fieldName . "FormGroup";?>'>
            	<label for='<?=$fieldName?>'><?=$title?></label>
            	<span>
            		<?php
                    if (is_dir($dir)) {
                        $handle = @opendir($dir);
                        if ($handle) {
                            // if there is a directory, list contents
                            ?>
                            <table class="table table-condensed">
                               <?php
                                while (false !== ($entry = readdir($handle))) {
                                    if ($entry != "." && $entry != "..") {
                                        ?>
                                                <tr>
                        				<td><a href="<? echo trim($dir) . '/' . $entry;?> " target="_blank"><button
                        							type="button" class="ibm-btn-sec ibm-btn-small ibm-btn-blue-50">View</button></a>&nbsp;<?= $entry;?></td>
                        				</tr>
                                        <?php
                                    }
                                }
                                closedir($handle);
                                ?>
                            </table>

                            <?php
                        } else { // no directory yet
                            ?>
                                <a class="list-group-item">No attachments available currently (no files in directory)</a>
                             <?php
                        }
                    }
                    ?>
            	</span>
            	<span class="ibm-item-note">Note that file attachments are NOT stored securely</span>
            </p>
            <?php
        } else {
            ?>

            <p class="ibm-form-elem-grp" id='<?php echo $fieldName . "FormGroup";?>'>
            	<label for='<?=$fieldName?>'><?=$title?></label>
            	<span>
            		<input id="<?=$fieldName?>" type="file" data-widget="fileinput" data-multiple="<?=$multiple?>" />
            		<input type='hidden' id='<?php echo $fieldName."FilePath";?>' value='<?=$uploadPath?>' />

				</span>
				<span class="ibm-item-note">Note that file attachments are NOT stored securely</span>
			</p>
			<p class="ibm-form-elem-grp" id='<?php echo $fieldName . "FormGroup";?>'>
			<label for='####'>Attachments</label>
            	<span>
                    <?php
                    if (is_dir($dir)) {
                        $handle = @opendir($dir);
                        if ($handle) {
                            // if there is a directory, list contents
                            ?>

                               <?php
                                while (false !== ($entry = readdir($handle))) {
                                    if ($entry != "." && $entry != "..") {
                                        ?>
                                        <span>
                            				<a href="<? echo trim($dir) . '/' . $entry;?> " target="_blank"><button	type="button" class="ibm-btn-sec ibm-btn-small ibm-btn-blue-50"><?= $entry;?></button></a>
                        				</span>
                                        <?php
                                    }
                                }
                                closedir($handle);
                                ?>


                            <?php
                        } else { // no directory yet
                            ?>
                                <a class="list-group-item">No attachments available currently (no files in directory)</a>
                             <?php
                        }
                    }
                    ?>

            	</span>
            	</p>

            <?php

        }



    }

    function formHiddenInput($fieldId = 'mode', $fieldValue = 'insert',$fieldName=null)
    {
       $fieldName = empty($fieldName) ? $fieldId : $fieldName;
       ?>
       <input type='hidden' id='<?=$fieldId?>' name='<?=$fieldName?>' value='<?=$fieldValue?>' />
       <?php
    }

    /**
     * Offers a set of Checkbox Buttons in the bootstrap style.
     * requires bootstrap buttons.js or a bootstrap.js containing the buttons javascript
     *
     * @param array $arrayOfOptions.
     *            An array of the buttons values
     * @param char $label
     *            Label for the set of buttons.
     * @param char $field
     *            Field that the response will be stored in.
     * @param char $onChange
     *            js script to call on change.
     * @param char $defaultSelection
     *            Button that will be initially selected
     * @param char $buttonClass
     *            css class to apply to the button (if using bootstrap: btn-default, btn-success, btn-danger etc etc)
     *
     *            Note: The value is stored in a field that is created dynamically and appended to the form with id=$field
     */
    function formCheckbox($arrayOfOptions, $label, $field, $vertical = false, $state = null)
    {
        $this->$field = is_array($this->$field) ? $this->$field : array();
        $disabled = (strtolower(trim($state))=='readonly') ? 'disabled' : null;
        ?>
            <div class='form-group' id='<?=$field?>FormGroup'>
            <label  class='col-sm-2 control-label ceta-label-left' data-toggle='tooltip' data-placement='<?=$this->toolTipPosition?>' title='<?=$this->toolTip?>'><?=$label?></label>
            <div class='col-sm-10'>
            <span class="ibm-input-group ibm-radio-group">
            <?php
            $fieldCounter = 0;
            foreach ($arrayOfOptions as $value) {
                $checked = in_array($value, $this->$field) ? 'checked' : null;
                ?><input class='ceta_checkbox' id='<?=$field.++$fieldCounter?>' name='<?=$field?>[]' type="checkbox" value="<?=$value?>" <?=$disabled?> <?=$checked?> onchange='<?=$this->onChange?>' />&nbsp;<label for="<?=$field.$fieldCounter?>"><?=$value?></label>
                <?=$vertical ? "<br/>" : null;?>
                <?php
            }
            ?>
            </span>
            </div>
            </div>
            <?php
    	$this->setToolTip();
    	$this->setOnchange();
        }

    /**
     * Offers a set of Radio Buttons in the bootstrap style.
     * requires bootstrap buttons.js or a bootstrap.js containing the buttons javascript
     *
     * @param array $arrayOfOptions.
     *            An array of the buttons values
     * @param char $label
     *            Label for the set of buttons.
     * @param char $field
     *            Field that the response will be stored in.
     * @param char $onChange
     *            js script to call on change.
     * @param char $defaultSelection
     *            Button that will be initially selected
     * @param char $toolTip
     *            text to display in any tooltip
     * @param char $toolTipPosition
     *            positon of tooltip (top, botton, left, right)
     * @param char $buttonClass
     *            css class to apply to the button (if using bootstrap: btn-default, btn-success, btn-danger etc etc)
     *
     *            Note: The value is stored in a field that is created dynamically and appended to the form with id=$field
     */
    function formRadioButtons($arrayOfOptions, $label, $field, $vertical=false, $state = null)
    {
        $disabled = (strtolower(trim($state))=='readonly') ? 'disabled' : null;
        ?>
        <div class='form-group' id='<?=$field?>FormGroup'>
        <label class='col-sm-2 control-label ceta-label-left' data-toggle='tooltip' data-placement='<?=$this->toolTipPosition?>' title='<?=$this->toolTip?>'><?=$label?></label>
        <div class='col-sm-10'>
        <span class="ibm-input-group ibm-radio-group">
        <?php
        $fieldCounter =0;
        foreach ($arrayOfOptions as $value) {
            $checked = trim($this->$field)==trim($value) ? 'checked' : null;
            ?><input class='ceta_radio' id='<?=$field.++$fieldCounter?>' name='<?=$field?>' type="radio" value="<?=$value?>" <?=$disabled?> <?=$checked?> onchange='<?=$this->onChange?>' />&nbsp;<label for="<?=$field.$fieldCounter?>"><?=$value?></label>
            <?=$vertical ? "<br/>" : null;?>
            <?php
        }
        ?>
        </span>
        </div>
        </div>
        <?php
	$this->setToolTip();
	$this->setOnchange();
    }

    function setRadioButtonColor($field,$color,$fieldValue=null){
        $colorField = 'color' . $field;
        $colorField .= isset($fieldValue) ? trim($fieldValue) : null;
        $this->$colorField = $color;
    }

    function formTime($title, $fieldName, $help = null, $state = null)
    {
        if (is_null($state)) {
            echo "<div class='form-group' id='$fieldName" . "FormGroup'>";
            echo "<label for='$fieldName' class='col-sm-3 control-label' data-toggle='tooltip' data-placement='top' title='$help'>$title</label>";
            echo "<div class='col-sm-7'>";
            echo "<div id='calendarFormGroup$fieldName' class='input-group date form_time col-sm-3'  data-date-format='hh:ii' data-link-field='$fieldName' data-link-format='hh:ii'>";
            echo "<input class='form-control' size='16' type='text' $state />";
            echo "<input type='hidden' id='$fieldName' name='$fieldName' />";
            echo "<span class='input-group-addon'><span id='calendarIcon$fieldName' class='glyphicon glyphicon-time'></span></span>";
            echo "</div>"; // input-group
            echo "</div>"; // col-sm-8
            echo "</div>"; // form-group
        } else {
            echo "<div class='form-group' id='$fieldName" . "FormGroup'>";
            echo "<label for='$fieldName' class='col-sm-3 control-label' data-toggle='tooltip' data-placement='top' title='$help'>$title</label>";
            echo "<div class='col-sm-7'>";
            $date = date_create($this->$fieldName);
            $dateToDisplay = date_format($date, 'H:i');

            echo "<p>$dateToDisplay</p>";
            //echo "<div class='input-group col-sm-3'>";
            //echo "<input class='form-control' size='16' type='text' $state id='$fieldName' name='$fieldName' value='" . $this->$fieldName . "' />";
            //echo "</div>"; // input-group
            echo "</div>"; // col-sm-8
            echo "</div>"; // form-group
        }
    }



    function formDateTime($title, $fieldName, $help = null, $state = null, $class="input-group date form_datetime", $addOnClass="input-group-addon",$placeholder=null)
    {
        $placeholder = empty($placeholder) ? "Select Date/Time" : $placeholder;
        if (is_null($state)) {
            // all other modes
            if (isset($this->$fieldName)){
                $rawDateString = $this->$fieldName;
                $date = date_create_from_format('Y-m-d-H.i.s.u', $rawDateString);
                $shortDateToDisplay = date_format($date, 'd-m-y');
                $dateToDisplay = date_format($date, 'l jS F Y');
                $timeToDisplay = date_format($date, 'H:i');
                $dateTimeToDisplay = date_format($date, 'd F Y - h:i a');
            } else {
                $rawDateString = "";
                $dateTimeToDisplay = "";
            }


            //28 October 2015 - 02:50 pm
            ?>
            <div class='form-group' id='<?=$fieldName?>FormGroup'>
            <label for='<?=$fieldName?>' class='col-md-2 control-label ceta-label-left' data-toggle='tooltip' data-placement='top' title='<?=$help?>'><?=$title?></label>
            <div class='col-md-10'>
            <div id='calendarFormGroup<?=$fieldName?>' class='<?=$class?>' data-date-format='dd MM yyyy - HH:ii p' data-link-field='<?=$fieldName?>' data-link-format='yyyy-mm-dd-hh.ii.00'>
            <input id='Input<?=$fieldName?>' class='form-control'  type='date' readonly value='<?=$dateTimeToDisplay?>' placeholder='<?=$placeholder?>' />
            <input type='hidden' id='<?=$fieldName?>' name='<?=$fieldName?>' value='<?=$rawDateString?>' />
            <span class='<?=$addOnClass?>'><span id='calendarIcon<?=$fieldName?>' class='glyphicon glyphicon-calendar'></span></span>
            </div>
            </div>
            </div>
            <?php
        } else {
            //display mode
            ?>
            <div class='form-group' id='<?=$fieldName?>" . "FormGroup'>
            <label for='<?=$fieldName?>' class='col-md-2 control-label ceta-label-left' data-toggle='tooltip' data-placement='top' title='<?=$help?>'><?=$title?></label>
            <div class='col-md-10'>
            <?php
            if ($this->$fieldName){
                $date = date_create_from_format('Y-m-d-H.i.s.u', $this->$fieldName);
                $shortDateToDisplay = date_format($date, 'd-m-y');
                $dateToDisplay = date_format($date, 'l jS F Y');
                $timeToDisplay = date_format($date, 'H:i');
                ?>
                <input type='text' class='form-control' value='<?=$dateToDisplay?>(<?=$shortDateToDisplay?>) at <?=$timeToDisplay?>' readonly  />
                <?php
            } else {
                ?>
                <input type='text' class='form-control' value='' readonly />
                <?php
            }
            ?>
            </div>
            </div>
            <?php
        }

        ?><script>
        $(document).ready(function() {
        var picker = new Pikaday({
            field: document.getElementById('Input<?=$fieldName?>'),
            format: 'D MMM YYYY HH:mm:ss',
            showTime: true,
            onSelect: function() {
                console.log(this.getMoment().format('Do MMMM YYYY'));
                var db2Value = this.getMoment().format('YYYY-MM-DD HH:mm:ss')
                console.log(db2Value);
                jQuery('#<?=$fieldName?>').val(db2Value);
            }
        });
            jQuery('#calendarIcon<?=$fieldName?>').click(function(){
                jQuery('#Input<?=$fieldName?>').click();
            });
        });

        </script>
        <?php
    }


    function formDate($title, $fieldName, $help = null, $state = null, $class="input-group date form_datetime", $addOnClass="input-group-addon")
    {
        $placeholder = empty($placeholder) ? "Select Date" : $placeholder;
        if (is_null($state)) {
            // all other modes
            if (isset($this->$fieldName)){
                $rawDateString = $this->$fieldName;
                $date = date_create_from_format('Y-m-d-H.i.s.u', $rawDateString);
                $shortDateToDisplay = date_format($date, 'd-m-y');
                $dateToDisplay = date_format($date, 'l jS F Y');
                $timeToDisplay = date_format($date, 'H:i');
                $dateTimeToDisplay = date_format($date, 'd F Y - h:i a');
            } else {
                $rawDateString = "";
                $dateTimeToDisplay = "";
            }


            //28 October 2015 - 02:50 pm
            ?>
            <div class='form-group' id='<?=$fieldName?>" . "FormGroup'>
            <label for='<?=$fieldName?>' class='col-md-2 control-label ceta-label-left' data-toggle='tooltip' data-placement='top' title='<?=$help?>'><?=$title?></label>
            <div class='col-md-10'>
            <div id='calendarFormGroup<?=$fieldName?>' class='<?=$class?>' data-date-format='dd MM yyyy - HH:ii p' data-link-field='<?=$fieldName?>' data-link-format='yyyy-mm-dd-hh.ii.00'>
            <input id='Input<?=$fieldName?>' class='form-control' type='text' readonly value='<?=$dateTimeToDisplay?>' placeholder='<?=$placeholder?>'  />
            <input type='hidden' id='<?=$fieldName?>' name='<?=$fieldName?>' value='<?=$rawDateString?>' />
            <span class='<?=$addOnClass?>'><span id='calendarIcon<?=$fieldName?>' class='glyphicon glyphicon-calendar'></span></span>
            </div>
            </div>
            </div>
            <?php
        } else {
            //display mode
            ?>
            <div class='form-group' id='<?=$fieldName?>" . "FormGroup'>
            <label for='<?=$fieldName?>' class='col-md-2 control-label ceta-label-left' data-toggle='tooltip' data-placement='top' title='<?=$help?>'><?=$title?></label>
            <div class='col-md-10'>";
            <?php
            if ($this->$fieldName){
                $date = date_create_from_format('Y-m-d-H.i.s.u', $this->$fieldName);
                $shortDateToDisplay = date_format($date, 'd-m-y');
                $dateToDisplay = date_format($date, 'l jS F Y');
                $timeToDisplay = date_format($date, 'H:i');
                ?>
                "<input type='text' class='form-control' value='<?=$dateToDisplay?> (<?=$shortDateToDisplay?>) at <?=$timeToDisplay?>' readonly />
                <?php
            } else {
                ?>
                <input type='text' class='form-control' value='' readonly />
                <?php
            }
            ?>
            </div>
            </div>
            <?php
        }

        ?><script>
        $(document).ready(function() {
        var picker = new Pikaday({
                field: document.getElementById('Input<?=$fieldName?>'),
                format: 'D MMM YYYY',
                showTime: false,
                onSelect: function() {
                    console.log(this.getMoment().format('Do MMMM YYYY'));
                    var db2Value = this.getMoment().format('YYYY-MM-DD')
                    console.log(db2Value);
                    jQuery('#<?=$fieldName?>').val(db2Value);
                }
            });
                jQuery('#calendarIcon<?=$fieldName?>').click(function(){
                    jQuery('#Input<?=$fieldName?>').click();
                });

        });
            </script>
            <?php
        }



    /**
     * Creates a <SELECT> in the HTML Form.
     *
     * @param array $wayToHandleArray
     *            How does the function use the $arrayOfSelectableValues in the SELECT, is $key The Value Returned and or Displayed, etc.
     * @param array $arrayOfSelectableValues
     *            array will form the entries in the Drop Down for the Select
     * @param string $label
     *            for the drop down, appears in the <TH> immediately before the <TD> this <SELECT> is in.
     * @param string $fieldName
     *            the ID and NAME for the <SELECT>, so will be the Class Variable into which GetForm places the entry from the Form.
     * @param string $allowMultipleSelections
     *            Boolean field, is this a Multiple Select ?
     * @param string $arrayOfSelectedValues
     *            For Multiple fields these values are pre-selected
     * @param string $readonly
     *            set to TRUE if the Form's MODE is EDIT but this particular field is not editable
     * @param string $class
     *            to provide the CSS Class for formating the field. A simple text string of class names is all that's required.
     * @param string $onChange
     *            to point to a Javascript Function that will be invoked onChange. A String of JS calls is all that's required.
     * @param string placeHolder
     *            is the first entry in the drop down - typically would be 'Select...' as opposed to an actual value from the array     *
     * @param string $arrayOfDisabledValues
     *            Set of Values in the SELECT that are actually disabled, ie not selectable.
     */




        function formSelect($wayToHandleArray=null, $arrayOfSelectableValues, $label, $fieldName, $readonly=false, $class = null, $onChange = null,   $placeHolder = 'Select...', $arrayOfDisabledValues=array())
        {
            $allowMultipleSelections = is_array($this->$fieldName);
            $wayToHandleArray = empty($wayToHandleArray) ? self::SELECT_DISPLAY_VALUE_RETURN_KEY : $wayToHandleArray;
            $selectedValues = $allowMultipleSelections ? $this->$fieldName : array(trim($this->$fieldName) => trim($this->$fieldName));
            $disabled = ($readonly or ($this->mode ==FormClass::$modeDISPLAY)) ? ' disabledSelect ' : null;
            ?>
                <div class='form-group' id='<?=$fieldName ."FormGroup"?>'>
        			<label for='<?=$fieldName?>' class='col-md-2 control-label ceta-label-left'><?=$label?></label>
        			<div class='col-md-10'>
                  	<select <?=$allowMultipleSelections ? 'multiple': null?> <?=($class or $disabled) ? "class='form-control select $class $disabled '" : "class='form-control select $class' "?>  id='<?=$fieldName?>'
                  	          name='<?=$allowMultipleSelections ? $fieldName."[]" : $fieldName?>'
                  	          required='required'
                  	          <?=$onChange ? "onchange=\"$onChange\"" : null?>
                  	          data-tags="true" data-placeholder="<?=$placeHolder?>" data-allow-clear="true"
                  	           >
            		<option value=''><?=$placeHolder?><option>
                    <?php
                    foreach ($arrayOfSelectableValues as $key => $value) {

                        switch ($wayToHandleArray) {
                            case self::SELECT_DISPLAY_VALUE_RETURN_KEY:
                                   $displayValue = trim($value);
                                   $returnValue  = trim($key);
                                   break;
                            case self::SELECT_DISPLAY_KEY_RETURN_VALUE:
                                   $displayValue = trim($key);
                                   $returnValue  = trim($value);
                                   break;
                           case self::SELECT_DISPLAY_KEY_RETURN_KEY:
                                   $displayValue = trim($key);
                                   $returnValue  = trim($key);
                                   break;
                            case self::SELECT_DISPLAY_VALUE_RETURN_VALUE:
                            default:
                                    $displayValue = trim($value);
                                    $returnValue  = trim($value);
                            break;
                        }
                   ?>
                   <option value='<?=$returnValue?>'<?=in_array($returnValue,$selectedValues) ? 'selected' : null?><?=in_array($returnValue,$arrayOfDisabledValues) ? 'disabled' : null?>><?=$displayValue?></option>
                   <?php
                    }
                    ?>

                    </select>
                    </div>
                    </div>
            		<?php
            		if($allowMultipleSelections){
            		    foreach ($selectedValues as $key => $value){
                            ?><input type='hidden' name='original<?=$fieldName."[]"?>' id='original<?=$fieldName." []"?>' value='<?=$value?>' /><?php
            		    }
            		} else {
            		    ?><input type='hidden' name='original<?=$fieldName?>' id='original<?=$fieldName?>' value='<?=$this->$fieldName?>' /><?php
            		}
                }


    function formSelectAsButtons($array, $label, $fieldName, $state = null, $first = 'Select...', $class = null, $onChange = null, $multiple = false, $help = null)
    {


        if ($state == 'readonly') {
            $state = 'disabled';
        }
        // if(!isset($array)) { return;}
        // Drop Down Selection on a form
        $val = $this->$fieldName;
        if ($multiple == true) {
            $multiple = 'multiple';
        } else {
            $multiple = Null;
        }
        echo "<div class='form-group' id='$fieldName" . "FormGroup'>";
        echo "<label for='$fieldName' class='col-sm-2 control-label' >$label</label>";
        echo "<div class='col-sm-10'>";
        $selectedValue = false;
        $buttonColour = "#FFFFFF";
        $buttonHTML = "";
        if (isset($array)) {

            $collapseName = "";
            $clickFunction = "";
            $glyphicon = "";
            $labelHTML = "<span data-toggle='tooltip' data-placement='right' title='Expand or collapse the buttons view'><a style='font-size: 15px;' data-toggle='collapse' href='#$collapseName' aria-expanded='false' aria-controls='CollapsableNameButtons'><span class='glyphicon glyphicon-collapse-up'></span></a></span>";


            foreach ($array as $key => $value) {

                $decodedValue = htmlspecialchars_decode($value);
                $buttonHTML =  $buttonHTML . "<button type='button' class='btn btn-default btn-sm' style='background-color: $buttonColour;' data-toggle='tooltip' data-placement='right' title='Select Account' $clickFunction ><span class='$glyphicon' aria-hidden='true'></span>$decodedValue</button>&nbsp";


            }

            echo $buttonHTML;

        } else {
            echo "<INPUT class='form-control' ";
            echo " name='$fieldName' disabled value='$val' maxlength='20'>";
        }
        if ($selectedValue) {
            echo "<INPUT TYPE='hidden' id='original$fieldName' name='original$fieldName' value='" . $selectedValue . "' />";
        }
        echo "</div>"; // col-sm-8
        echo "</div>"; // form-group

    }


    function formUserid($fieldName, $title, $state = null, $help = null, $tooltipPosition = "top")
    {
        if ($state == "READONLY") {
            $notesid = $fieldName . "_NOTES_ID";
            $name = $fieldName . "_NAME";
            $intranet = $fieldName . "_INTRANET_ID";
            $phone = $fieldName . "_PHONE";
            $uid = $fieldName . "_UID";
            $valueToDisplay = trim($this->$notesid);
            ?>
            <p class="ibm-form-elem-grp" id='<?php echo $fieldName. "FormGroup"?>'>
            <label for='<?=$fieldName?>'><?=$title?></label>
           	<span>
            <input type='text' value='<?=$valueToDisplay?>' readonly />
            </span>
            </p>
            <?php
        } else {
            $notesid = $fieldName . "_NOTES_ID";
            $name = $fieldName . "_NAME";
            $intranet = $fieldName . "_INTRANET_ID";
            $phone = $fieldName . "_PHONE";
            $uid = $fieldName . "_UID";

            $displayValue = trim(addslashes($this->$notesid));
            $intranetValue = trim($this->$intranet);

            ?>
            <p class="ibm-form-elem-grp" id='<?php echo $fieldName. "FormGroup"?>'>
            <label for='<?=$notesid?>' data-toggle='tooltip' data-placement='<?=$tooltipPosition?>' title='<?=$help?>'><?=$title?></label>
            <span>
            <span class='glyphicon glyphicon-book' data-toggle='tooltip' data-placement='right' title='Start typing a name and then select the person you want to select from the list that appears'></span>
            <input type='text' name='<?=$notesid?>' class='typeahead' id='<?=$fieldName?>' value='<?=$displayValue?>' <?=$state?> autocomplete='off' size='64' maxlength='64' placeholder='Start typing a name to perform a lookup' required   />
<?php
            // if the values eg $intranet, $name etc are set in the class proprties then create them, else don't.
            echo property_exists($this, $intranet) ? "<input name='$intranet'  id='$intranet' value='$intranetValue' type='hidden' />" : null;
            echo property_exists($this, $name) ? "<input name='$name'      id='$name'  type='hidden' />" : null;
            echo property_exists($this, $uid) ? "<input name='$uid'       id='$uid'  type='hidden' />" : null;
            echo property_exists($this, $phone) ? "<input name='$phone'     id='$phone'  type='hidden' />" : null;
            ?>
            </span>
            </p>
            <?php
?>
           <script>
	           var config = {
	               //API Key [REQUIRED]
	               key: 'vbac;rob.daniel@uk.ibm.com',
	               faces: {
	                   //The handler for clicking a person in the drop-down.
	                   onclick: function(person) {
	                	   console.log(person);
	                	   var intranet = document.forms.<?=$this->fcFormName;?>.elements['<?=$intranet?>'];
                            if(typeof(intranet) !== 'undefined'){ intranet.value = person['email'];};

                            var name =  document.forms.<?=$this->fcFormName;?>.elements['<?=$name?>'];
                            if(typeof(name) !== 'undefined'){ name.value = person['name'];};

                            var uid = document.forms.<?=$this->fcFormName;?>.elements['<?=$uid?>'];
                            if(typeof(uid) !== 'undefined'){ uid.value = person['uid'];};

                            var phone = document.forms.<?=$this->fcFormName;?>.elements['<?=$phone?>'];
                            if(typeof(phone) !== 'undefined'){ phone.value = person['phone'];};

                            return person['notes-id'];
        }
        }
        };
        FacesTypeAhead.init(
        	document.forms.<?php echo $this->fcFormName;?>.elements['<?=$notesid?>'],
        	config
        );
        </script>
        <?php
        }
    }

    function formUseridMultiSelect($title, $fieldName, $placeHolder=null, $tooltip = null, $tooltipPosition = 'top', $state = null, $lookupTable, $lookupKey, $lookupValue, $intranetReturnColumn, $nameReturnColumn, $emailField, $buttonField){
        if ($state=="READONLY"){
            $loader = new Loader();

            $listOfIntranetValues = $loader->load($intranetReturnColumn,$lookupTable, " $lookupKey='$lookupValue'");
            ?>
            <div class='form-group'>
            <label for='accounts' class='col-md-3 control-label' ><?=$title?></label>
            <div class='col-md-9'>
            <?php
            echo "<ul>";
            foreach ($listOfIntranetValues as $value) {
                $this->formInsertCharacters("<li>$value</li>");

            }
            echo "</ul>";
            ?>
            </div>
            </div>
            <?php
	    } else {
            // when displaying in edit mode we need to populate these variables with comma separated strings and then build the buttons

            // SME_NAME
            // SME_INTRANET_ID
	        $buttonHTML = "";
            $namesValuesString = "";
            $intranetValuesString = "";

            if ($this->mode == "edit") {
                $loader = new Loader();
                $listOfIntranetValues = $loader->load($intranetReturnColumn, $lookupTable, " $lookupKey='$lookupValue'");
                $loader = new Loader();
                $listOfNameValues = $loader->load($nameReturnColumn, $lookupTable, " $lookupKey='$lookupValue'");
                ?>
                            <!--<pre>-->
                            <?php
                            $listOfIntranetValues = array_keys($listOfIntranetValues);
                            //print_r($listOfIntranetValues)?>
                            <!--</pre>-->
                            <!--<pre>-->
                            <?php
                            //print_r($listOfNameValues)?>
                            <!--  </pre> -->
                            <?php
                $index=0;
                if ($listOfIntranetValues && $listOfNameValues) {




                    $intranetValuesString = implode(',', $listOfIntranetValues);
                    //echo $intranetValuesString . ":";
                    $namesValuesString = implode(',', $listOfNameValues);
                    //echo $namesValuesString;


                    foreach($listOfNameValues as $x_key => $x_value){

                        $intranetAddress = $listOfIntranetValues[$index];


                        //removeName function(nameToRemove, fieldToRemoveNameFrom, emailToRemove, fieldToRemoveEmailFrom, fieldToRemoveButtonFrom, onclickJsFunction)
                        $fieldToRemoveNameFrom = $fieldName."_NAME";
                        $buttonHTML .= "<button type='button' class='btn btn-default btn-sm' data-toggle='tooltip' data-placement='right' title='Remove Name' onclick='FormClass.removeName(\"$x_value\",\"$fieldToRemoveNameFrom\",\"$intranetAddress\",\"$emailField\",\"$buttonField\", \"FormClass.removeName\");'><span class='glyphicon glyphicon-remove-circle' aria-hidden='true'></span> $x_value</button>";
                                      //<button type="button" class="btn btn-default btn-sm" data-toggle="tooltip" data-placement="right" title="Remove Name" onclick="FormClass.removeName('Tim J Minter/UK/IBM','SME_NAME','tim.j.minter@uk.ibm.com','SME_INTRANET_ID','SME_display','FormClass.removeName');"><span class="glyphicon glyphicon-remove-circle" aria-hidden="true"></span> Tim J Minter/UK/IBM</button>

                       $index++;


                    }
                }

            }

            $notesid = $fieldName . "_NOTES_ID";
            $name = $fieldName . "_NAME";
            $intranet = $fieldName . "_INTRANET_ID";
            $phone = $fieldName . "_PHONE";
            $uid = $fieldName . "_UID";
            $inputField = $fieldName . "_input";
            $buttonsDisplay = $fieldName . "_display";
            $nameTemp = $fieldName . "_name_temp";
            $intranetTemp = $fieldName . "_intranet_id_temp";
            ?>
<script>
    	    if (!document.getElementById('<?=$fieldName?>')) {
        	    //create the FieldName field if it doesn't exist
    	       var x = document.createElement('INPUT');
    	       x.setAttribute('type', 'hidden');
    	       x.setAttribute('id', '<?=$fieldName?>');
    	       document.body.appendChild(x);
    	    }
    	    </script>


<div class='panel panel-default'><div class='panel-body'>
    	    <div class='form-group' id='<?=$fieldName."FormGroup"; ?>'>
    	    <label for='<?=$notesid?>' class='col-md-3 control-label' data-toggle='tooltip' data-placement='<?=$tooltipPosition?>' title='<?=$tooltip?>'><?=$title?></label>
    	    <span style='padding-left:16px; padding-right:16px' class='glyphicon glyphicon-book' data-toggle='tooltip' data-placement='right' title='Start typing a name, select the person you want to select from the list that appears and then click the plus (+) button. Multiple names can be added.'></span>
    	    <div class='input-group col-md-9' style='padding-left:16px; padding-right:16px'>

    	    <input type='text' class='form-control typeahead' name='<?=$inputField?>' id='<?=$inputField?>' autocomplete='off' placeholder='<?=$placeHolder?>' size='64' maxlength='64'>
    	    <span class='input-group-btn'>
    	    <button class='btn btn-default' type='button' onclick="FormClass.addName('<?=$nameTemp?>', '<?=$name?>', '<?=$intranetTemp?>', '<?=$intranet?>', '<?=$buttonsDisplay?>', 'FormClass.removeName', '<?=$inputField?>');"><span class='glyphicon glyphicon-plus' aria-hidden='true'></span></button>
    	   	</span>

    	    </div>

    	    </div>

    	    <div class='form-group'>
    	   <label class='col-sm-3 control-label'></label>
    	    <div class='input-group col-sm-9' style='padding-left:16px; padding-right:16px'>
    	    <?php
            $collapseName = $name."collapseNameButtons";
            $labelID = $name."labelID";
            $labelHTML = "<span data-toggle='tooltip' data-placement='right' title='Expand or collapse the buttons view'><a style='font-size: 15px;' data-toggle='collapse' href='#' $collapseName aria-expanded='false' aria-controls='CollapsableNameButtons'><span class='glyphicon glyphicon-collapse-up'></span></a></span>";
            ?>

    	    <div id='<?=$labelID?>'></div>
    	    <div class='collapse in spaced' id='<?=$collapseName?>'>
    	    <div  id='<?=$buttonsDisplay?>'><?=$buttonHTML?></div>
    	    </div>
    	    </div>
    	    </div>
    	    </div></div>


    	    <input type='hidden' id='<?=$fieldName?>'/>
    	    <input type='hidden' id='<?=$name?>' value='<?=$namesValuesString?>'/>
    	    <input type='hidden' id='<?=$intranet?>' value='<?=$intranetValuesString?>'/>
    	    <input type='hidden' id='<?=$nameTemp?>'/>
    	    <input type='hidden' id='<?=$intranetTemp?>'/>

    	    <script>
    	    $( document ).ready(function() {
        	    console.log('ready again');
    	           $('body').on('focus',"#<?=$inputField?>", function(){
    	           console.log('Focus');

        	           var config = {
            	               //API Key [REQUIRED]
            	               key: 'mcab;tim.j.minter@uk.ibm.com',
            	               faces: {
            	                   //The handler for clicking a person in the drop-down.
            	                   onclick: function(person) {

            	                   //var intranet = document.forms." . $this->fcFormName . ".elements['$intranet'];
            	    	           //        if(typeof(intranet) !== 'undefined'){ intranet.value = person['email'];};

            	    	           var intranetTemp = document.forms.<?=$this->fcFormName;?>.elements['<?=$intranetTemp?>'];
            	    	                   if(typeof(intranetTemp) !== 'undefined'){intranetTemp.value = person['email'];};

            	    	           var nameTemp = document.forms.<?=$this->fcFormName;?>.elements['<?=$nameTemp?>'];
            	    	                   if(typeof(nameTemp) !== 'undefined'){ nameTemp.value = person['notes-id'];};

            	    	           //var name =  document.forms." . $this->fcFormName . ".elements['$name'];
            	    	           //      if(typeof(name) !== 'undefined'){ name.value = person['name'];};


            	    	           var uid = document.forms.<?=$this->fcFormName;?>.elements['<?=$uid?>'];
            	    	                   if(typeof(uid) !== 'undefined'){ uid.value = person['uid'];};


            	    	           var phone = document.forms.<?=$this->fcFormName;?>.elements['<?=$phone?>'];
            	    	                   if(typeof(phone) !== 'undefined'){ phone.value = person['phone'];};


            	    	                   return person['notes-id'];
            	    }
            	    }
            	    };
    	    FacesTypeAhead.init(
    	    document.forms.<?=$this->fcFormName;?>.elements['<?=$inputField?>'],
    	    config
    	    );

    	    });
    	    })
    	    </script>
    	    <?php

	    }

	}
    function formUseridMk1($mode, $title, $fieldName, $state = null, $help = null, $fieldsRequired = 'Intranet')
    {
        echo "<div class='form-group'>";
        echo "<label for='$fieldName' class='col-sm-3 control-label' >$title</label>";
        echo "<div class='col-sm-7'>";

        $name = $fieldName . "_NAME";
        $notesid = $fieldName . "_NOTESID";
        $intranet = $fieldName . "_INTRANET";
        $phone = $fieldName . "_PHONE";
        $uid = $fieldName . "_UID";

        if (isset($this->$name)) {
            $nameValue = $this->$name;
        } elseif (isset($this->$notesid)) {
            $nameValue = $this->$notesid;
        } elseif (isset($this->$intranet)) {
            $nameValue = $this->$intranet;
        } else {
            $nameValue = null;
        }

        echo "<label for='$name' class='col-sm-2 control-label' >Name :</label>";
        if ($mode != self::$modeDISPLAY) {
            echo "<div class='" . $fieldName . "Details'>";
            echo "<input id='faces-input$fieldName' name='$name' class='typeahead' size='30'  $state value='" . htmlspecialchars(trim($nameValue), ENT_QUOTES) . "' />";
            echo "      <script>";
            echo "		var config = { key: 'esoft;rob.daniel@uk.ibm.com',";
            echo "			           faces: {";
            echo "			              onclick: function(person) {";
            echo "			     		  document.getElementById('callback-target$fieldName').innerHTML = person['bio'];";
            switch ($fieldsRequired) {
                case self::$bpFieldIntranet:
                    echo "document.getElementById('$intranet').value = person['email'];";
                    break;
                case self::$bpFieldNotesid:
                    echo "document.getElementById('$notesid').value = person['notes-id'];";
                    break;
                case self::$bpFieldBothUid:
                    echo "var uid = document.getElementById('$uid');";
                    echo "if(typeof(uid)==='object'){ uid.value = person['uid'];};";
                case self::$bpFieldBoth:
                    echo "document.getElementById('$intranet').value = person['email'];";
                    echo "document.getElementById('$notesid').value = person['notes-id'];";

                    break;
                default:
                    echo "alert('default');";
                    ;
                    break;
            }
            echo "			 	                	return person['name'];";
            echo "		 	                   }";
            echo "	 	               },";
            echo "					   topsearch: {";
            echo "						   enabled: false";
            echo "	 	               }";
            echo "				 };";
            echo "	        FacesTypeAhead.init(";
            echo "				document.getElementById('faces-input$fieldName'),";
            echo " 				config";
            echo "	         );";
            echo "    </script> ";
            echo "</div>";
            echo "<div id='callback-target$fieldName'><B>$help</B>Allow <a href='http://ciolab.ibm.com/misc/typeahead/' target='_blank'>Faces Type-ahead</a> time to work.</div> ";


        } else {
            echo "<INPUT size='40' name='$notesid' id='$notesid' READONLY value=' " . trim($this->$notesid) . "' /></TD></TR>";
            echo "<TR>";
            echo "<th style='background-color:#bd6' width=120><B>Email</B></th>";
            echo "<td><INPUT size='40' name='$intranet' id='$intranet' READONLY value='" . trim($this->$intranet) . "' /></td>";
            echo "</TR>";
            echo "<INPUT TYPE='hidden' id='original$intranet' value='" . trim($this->$intranet) . "' />";
        }
    }

    /**
     * Used to place an "Input type='hidden'" in the form.
     * This can then be used to pass from one form to the next page
     * the "mode" the next page should perform. *
     *
     * @param string $mode
     *            placed in value element of input statement
     * @param string $state
     *            placed in input statement
     *
     */
    static function nextMode($mode, $state = null)
    {
        echo "<input type='hidden' name='mode' $state value='$mode' >";
    }

    /**
     * Used to place an "Input type='submit'" in the form.
     * This can then be used to pass from one form to the next page
     * the "mode" the next page should perform. *
     *
     * @param string $mode
     *            placed in value element of input statement
     * @param string $state
     *            placed in input statement
     *
     */
    static function submitMode($mode, $state = null)
    {
        echo "<input type='submit' name='mode' $state value='$mode' >";
    }

    static function setModeVariables($mode)
    {
        $modeVariables['mode'] = $mode;
        if ($mode == 'Display') {
            $modeVariables['state'] = self::$formReadonly;
            $modeVariables['chkState'] = self::$formDisabled;
            $modeVariables['notEditable'] = self::$formReadonly;
        } else {
            $modeVariables['state'] = self::$formNull;
            $modeVariables['chkState'] = self::$formNull;
            $modeVariables['notEditable'] = self::$formNull;
        }
        if ($mode == 'edit') {
            $modeVariables['notEditable'] = self::$formReadonly;
        }
        return $modeVariables;
    }

    function standardInputFields()
    {
        echo "<INPUT type='hidden' name='LAST_UPDATER' value='" . $GLOBALS['ltcuser']['mail'] . "' />";
        $now = new \DateTime();
        echo "<INPUT type='hidden' name='LAST_UPDATED' value='" . $now->format('Y-m-d H:i:s') . "' />";

        if (! isset($this->CREATED)) {
            echo "<INPUT type='hidden' name='CREATED' value='" . $now->format('Y-m-d H:i:s') . "' />";
            echo "<INPUT type='hidden' name='CREATOR' value='" . $GLOBALS['ltcuser']['mail'] . "' />";
        } else {
            echo "<INPUT type='hidden' name='CREATED' value='" . $this->CREATED . "' />";
            echo "<INPUT type='hidden' name='CREATOR' value='" . $this->CREATOR . "' />";
        }
    }

    static function formHeader($formName = 'myForm', $action = '$_SERVER[PHP_SELF]')
    {
        var_dump($action);
        echo "<FORM name='$formName' method='POST' action='" . $action . "' >";
    }

    static function formEnd()
    {
        echo "</FORM>";
    }

    static function formToggelableDivisionStart($divisionName = 'dropSelection', $divisionComment = 'Unnamed Division', $initialDisplayStyle = 'block', $border = true, $backgroundColor = '#e5e5e5')
    {
        echo "<TABLE><TBODY><TR>";
        echo "<TH><a id='x" . $divisionName . "' href=\"javascript:Toggle('" . $divisionName . "');\" >";
        $iconName = $initialDisplayStyle == 'block' ? 'close' : 'open';
        echo "<img src='../ItdqLib_V1/ui/images/icon-list-" . $iconName . ".gif' width='12' height='12' alt='" . $iconName . " link icon' /></a>";
        echo "&nbsp;" . $divisionComment . "</TH></TR>";
        echo "</TBODY></TABLE>";
        $borderParms = $border ? "padding:7px;border:1px solid;border-radius:5px;background-color:$backgroundColor;overflow-x:scroll" : null;
        echo "<div id='" . $divisionName . "' style='display:" . $initialDisplayStyle . "; margin-left:4em;$borderParms' >";
    }

    static function formToggelableDivisionEnd()
    {
        echo "</DIV>";
    }

    static function formBlueButtons($buttonDetails = null)
    {
        echo "<span class='button-blue' style='font-size:1.4em'>";
        foreach ($buttonDetails as $button) {
            echo "<input class='btn " . $button['class'] . "' type='" . $button['type'] . "' name='" . $button['name'] . "' id='" . $button['id'] . "' " . $button['state'] . " value='" . $button['value'] . "'  >&nbsp;";
        }
        // echo "<input type='submit' name='btnNewProduct' id='btnNewProduct' enabled value='Define New Product' >";
        echo "</span>";
    }

    static function formButton($type = null, $name = null, $id = null, $state = null, $value = null,$class='btn-primary')
    {
        $buttonArray = array();
        $buttonArray['type'] = ! empty($type) ? trim($type) : null;
        $buttonArray['name'] = ! empty($name) ? trim($name) : null;
        $buttonArray['id'] = ! empty($id) ? trim($id) : null;
        $buttonArray['state'] = ! empty($state) ? trim($state) : null;
        $buttonArray['value'] = ! empty($value) ? trim($value) : null;
        $buttonArray['class'] = ! empty($class) ? trim($class) : null;
        return $buttonArray;
    }

    /**
     * Will produce a number of <INPUT fields in the currect form, using $arrayTo[] as the NAME of the field.
     *
     * Use this when you need to pass an ARRAY of fields FROM one form onto the next form so they get passed to the next page.
     *
     * This method will also return FALSE if $arrayFrom was empty or an "implode" CSV of the values it wrote to the form.
     *
     *
     * @param array $arrayFrom
     * @param array $arrayTo
     * @return Ambigous <boolean, string>
     */
    static function saveFormArray($arrayFrom, $arrayTo = null)
    {
        $listOfSavedItems = false;
        $arrayTo = empty($arrayTo) ? $arrayFrom : $arrayTo;
        if (isset($_REQUEST[$arrayFrom])) {
            foreach ($_REQUEST[$arrayFrom] as $value) {
                echo "<input type='hidden' name='" . $arrayTo . "[]' value='" . urlencode($value) . "' >";
                // $savedSomething = true; // We saved something
            }
            $listOfSavedItems = implode(",", $_REQUEST[$arrayFrom]);
        }
        return $listOfSavedItems;
    }

    static function getRagColour($rag)
    {
        switch (strtoupper(substr(trim($rag), 0, 1))) {
            case 'R':
                return FormClass::$ragRed;
                break;
            case 'A':
                return FormClass::$ragAmber;
                break;
            case 'G':
                return FormClass::$ragGreen;
                break;
            default:
                return '#fff';
                ;
                break;
        }
    }

    function modeSetup($mode)
    {
        if ($mode == FormClass::$modeDISPLAY) {
            $this->state = 'READONLY';
            $this->chkState = 'DISABLED';
            $this->notEditable = 'READONLY';
        } else {
            $this->state = null;
            $this->chkState = null;
            $this->notEditable = 'READONLY';
        }
    }

    function openFormDivs($title = null)
    {
        echo "<div class='panel panel-primary'>";
        echo "<div class='panel-heading'>$title</div>";
        echo "<div class='container'>";
        echo "<div class='form-horizontal'>";
    }

    function closeFormDivs()
    {
        echo "</div>"; // form-horiztonal
        echo "</div>"; // container
        echo "</div>"; // panel
    }

    function displayFormButtons(array $buttons)
    {
        ?>
        <p class="ibm-btn-row">
        <?php
        foreach($buttons as $button){
            $button->getButton();
        }
        ?>
        </p>
        <?php
    }


    /**
     * Tim Minter
     *
     **/
    function formFileUpload($title, $fieldName, $uploadPath, $state, $width="100%", $dataPlacement='top', $help=""){
        if ($state == "READONLY") {
            //get the dir listing and show the files currently uploaded
            $dir = $uploadPath;
            // Open a directory, and read its contents

            if (is_dir($dir)) {
                $handle = @opendir($dir);
                if ($handle) {
                    // if there is a directory, list contents
                    ?>

                    <label class='col-md-3 control-label'>Attached Files</label>
                    <div class='col-md-9'>
                    <table table class="table table-condensed">
                    <?php
                    while (false !== ($entry = readdir($handle))) {
                        if ($entry != "." && $entry != "..") {
                            ?>
                            <tr><td><a href="<? echo trim($dir) . '/' . $entry;?> " target="_blank"><button type="button" class="btn btn-xs btn-info">View</button></a>&nbsp;<?= $entry;?></td></tr>
                            <?php
                        }
                    }
                    ?>
                    </table>
                    </div>
                    <?php
                    closedir($handle);
                } else { // no directory yet
                    ?>
                    <a class="list-group-item">No attachments available currently (no files in directory)</a>
                    <?php
                }
            }

        } else {
            ?>

            		<div class='form-group' id='<?= $fieldName . "FormGroup"; ?>'>
            			<label for='<?= $fieldName?>' class='col-md-3 control-label' data-toggle='tooltip' data-placement='<?=$dataPlacement?>' title='<?=$help?>'><?=$title?></label>
            			<div class='col-md-9'>


            				<input type='file' name="filesToUpload[]" id="filesToUpload" multiple="multiple" onChange="FormClass.makeFileList();" style='height: 0; width: 0;'/>


            				<label for='filesToUpload' class='btn btn-warning'>Add File(s)</label>



            				<h5>Note: Files are NOT currently stored securely. Assume read acccess is available to everyone.</h5>
            				<!-- <p><input type="file" name="filesToUpload[]" id="filesToUpload" multiple="multiple" onChange="FormClass.makeFileList();" /></p> -->

                        	<div id="attachmentAreaInfo"></div>
                        	<ul id="fileList">
                        		<!-- <li>No files selected to attach</li> -->

                        	</ul>

                        	<?php





                        	$dir = $uploadPath;
                        	// Open a directory, and read its contents

                        	if (is_dir($dir)) {
                        	    $handle = @opendir($dir);
                        	    if ($handle) {
                        	        // if there is a directory, list contents
                        	        ?>
                        	                        <div class='panel panel-default'><div class='panel-body'>
                        	                        <label class='col-md-3 control-label'>Currently Attached Files</label>
                        	                        <div class='col-md-9'>
                        	                        <table class="table table-condensed">
                        	                        <?php
                        	                        while (false !== ($entry = readdir($handle))) {
                        	                            if ($entry != "." && $entry != "..") {
                        	                                ?>
                        	                                <tr><td><a href="<? echo trim($dir) . '/' . $entry;?> " target="_blank"><button type="button" class="btn btn-xs btn-info">View</button></a>&nbsp;<button type="button" id="deleteButton<? echo trim($dir) . '/' . $entry;?>" class="btn btn-xs btn-danger" onclick="deleteAttachment('<? echo trim($dir) . '/' . $entry;?>');">Delete</button>&nbsp;<?= $entry;?></td></tr>
                        	                                <?php
                        	                            }
                        	                        }
                        	                        ?>
                        	                        </table>
                        	                        </div></div></div>
                        	                        <?php
                        	                        closedir($handle);
                        	                    }
                        	                }






                        	?>





            		</div>
            		</div>

              <?php
        }
    }

    function formProgressBar($progressBarID='progress', $title='Save Progress', $help=null, $dataPlacement='top'){
        ?>
        <div class='form-group' id='<?=$progressBarID."FormGroup"?>'>
        <label for='<?=$progressBarID?>' class='col-md-3 control-label' data-toggle='tooltip' data-placement='<?=$dataPlacement?>' title='<?=$help?>'><?=$title?></label>
		<div class='col-md-9'>
			<div class='progress' >
				<div class='progress-bar' id='<?php echo $progressBarID; ?>' role='progressbar' aria-valuenow='0' aria-valuemin='0' aria-valuemax='100' style="min-width: 2em;">
					 0%
				</div>
			</div>
		</div>
		</div>
		<?php
    }

    /**
     * Tim Minter
     * Simply inserts the characters(text) you place in the parameter at the location in the form that you call it.
     * The characters can be optionally encoded.
     * Designed to be used to insert HTML eg to open and close a DIV or insert a heading etc but any characters you supply will be inserted
     * Optionally the characters can be encoded using various PHP encoding methods
     *
     * $encodeMethod options
     * 0: no encoding (Default. Use if just inserting html markup)
     * 1: htmlspecialchars (convert just special characters which have HTML character entity equivalents to those equivalents - use this for all output that will be displayed on screen)
     * 2: htmlentities (convert all characters which have HTML character entity equivalents to those equivalents - use with care as this can break UTF-8 characters)
     *
     *
     * @param string $charactersToInsert
     * @param string $encodeMethod
     * @param string $flags
     *            (see PHP help for htmlspecialchars for an explanation of this parameter)
     * @param string $charSet
     *            (see PHP help for htmlspecialchars for an explanation of this parameter)
     * @param string $doubleEncode
     *            (see PHP help for htmlspecialchars for an explanation of this parameter)
     */
    function formInsertCharacters($charactersToInsert, $encodeMethod = 0, $flags = "ENT_COMPAT | ENT_HTML5", $charSet = "ISO-8859-1", $doubleEncode = false)
    {
        switch ($encodeMethod) {
            case 0:
                echo $charactersToInsert;
                break;
            case 1:
                echo htmlspecialchars($charactersToInsert, $flags, $charSet, $doubleEncode);
                break;
            case 2:
                echo htmlentities($charactersToInsert, $flags, $charSet, $doubleEncode);
                break;
            default:
                echo $charactersToInsert;
                break;
        }
    }

    /*
     * @param char $message
     *            text to display in any tooltip
     * @param char $position
     *            positon of tooltip (top, botton, left, right)
     */

    function setToolTip($message=null, $position='left'){
        $this->toolTip = trim($message);
        $this->toolTipPosition = $position;
    }

    function setOnchange($onChange=null){
        $this->onChange = $onChange;
    }
}


?>