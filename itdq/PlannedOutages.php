<?php
namespace itdq;

class PlannedOutages
{

    protected $outages;

    function defineOutage(\DateTime $date, $description)
    {
        $this->outages[$date->format('Ymd')] = array('date'=>$date,'description'=>$description);       
      
    }

    function displayOutages()
    {
        $now = new \DateTime();        
        asort($this->outages);        
        foreach ($this->outages as $key => $details) {
            if ($details['date'] >= $now) {
                echo "<div class='alert alert-warning' role='alert'>";
                echo "<h4>" . $details['date']->format('D jS M Y') . "<h4>";
                echo "<p>" . $details['description'] . "</p>";
                echo "</div>";
            }
        }
    }

    function getBadge()
    {
        $now = new \DateTime();
        $outagesOutstanding = 0;
        foreach ($this->outages as $key => $details) {
            if ($details['date'] >= $now) {
                $outagesOutstanding ++;
            } 
        }
        $badge = $outagesOutstanding>0 ?  "<span class='badge'>$outagesOutstanding</span>" : null;
        
        return $badge;
    }
}