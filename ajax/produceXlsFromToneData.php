<?php

ob_start();

// $fileName = 'toneAnalysis.csv';
// $handle = fopen($fileName,'w');

$tones = array('anger','fear','joy','sadness','analytical','confident','tentative');

$response = print_r($_POST,true);

if(isset($_POST['tonetext'])){
    $data = array();    
    $toneText = json_decode($_POST['tonetext']);  

    foreach ($toneText->sentences_tone as $key => $sentence){       
        $data[$sentence->sentence_id]['text'] = $sentence->text;
        foreach ($sentence->tones as $toneDetails) {
            foreach ($tones as $tone) {
                $data[$sentence->sentence_id][$tone] = $toneDetails->tone_id==$tone ? $toneDetails->score : null ;
            }     
        }
    }
    
    $messages = ob_get_clean();
    ob_start();
    $headerLine = 'Text';
    foreach ($tones  as $tone) {
        $headerLine.= "," .  $tone;
    }
    echo $headerLine;

   
    foreach ($data as $toneRow){
        $line = $toneRow['text'];
        foreach ($tones as $tone) {
            $line.= isset($toneRow[$tone]) ? ",". $toneRow[$tone] : "," ;
        }
        echo $line;
        $line = '';        
    }    
    $csv = ob_get_clean();
} else {
    echo "No data passed to process";
}



echo json_encode(array('success'=>empty($messages),'messages'=>$messages,'csv'=>$csv,'data'=>$data));





