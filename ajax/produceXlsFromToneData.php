<?php

ob_start();

// $fileName = 'toneAnalysis.csv';
// $handle = fopen($fileName,'w');

$tones = array('anger','fear','joy','sadness','analytical','confident','tentative');

$response = print_r($_POST,true);

if(isset($_POST['tonetext'])){
    $data = array();    
    $table=array();
    $toneText = json_decode($_POST['tonetext']);  

    foreach ($toneText->sentences_tone as $key => $sentence){ 
        $toneScores = array();
        foreach ($tones as $tone) {
            $toneScores[$tone] = null; // Initialise ToneScores
        }
        foreach ($sentence->tones as $toneDetails) {
            $toneScores[$toneDetails->tone_id] = $toneDetails->score; // Set the actual Tone Score
        }
        $tableRow = "<tr><td>" .  $sentence->text . "</td>";    
        foreach ($tones as $tone) {
            $strength= $toneScores[$tone] > 0.5 ? 'normal' : 'none';
            $strength= $toneScores[$tone] > 0.75 ? 'strong' : $strength;
            
            $tableRow.= "<td class='$strength$tone'>" . $toneScores[$tone]  . "</td>";
        }
        $tableRow.= "</tr>";
        $table[] = $tableRow;
    }
    
    $messages = ob_get_clean();

} else {
    echo "No data passed to process";
}



echo json_encode(array('success'=>empty($messages),'messages'=>$messages,'tablerows'=>$table));





