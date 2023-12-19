<?php
namespace itdq;


class DateClass {
    const FRIDAY = 5;
    const SATURDAY = 6;
    const SUNDAY = 7;

    static function weekEnding($date){
        $dateObj = new \DateTime($date);

        $monday = new \DateTime($dateObj->format('Y-m-d'));
        $monday->modify('next monday');
        $weekEndingFriday = new \DateTime($monday->format('Y-m-d'));
        $weekEndingFriday->modify('previous Friday');

        $oneWeek = new \DateInterval('P1W');
        if($weekEndingFriday < $dateObj){
            $weekEndingFriday->add($oneWeek);
        }
        return $weekEndingFriday;
    }

    static function claimMonth($date){
        $dateObj = new \DateTime($date);
        $month = $dateObj->format('m');
        $year = $dateObj->format('Y');

        $nextMonth = $month + 1;
        if($nextMonth>12){
            $nextMonth = 1;
            $year++;
        }

        $nextMonthString = $year . "-" . $nextMonth . "-01";
        $nextMonthObj = new \DateTime($nextMonthString);
        $lastMondayOfMonthObj = new \DateTime($nextMonthObj->format('Y-m-d'));
        $lastMondayOfMonthObj->modify('previous Monday');
        $claimCutofFriday = new \DateTime($lastMondayOfMonthObj->format('Y-m-d'));
        $claimCutofFriday->modify('previous Friday');

        if($dateObj > $claimCutofFriday){
            $claimCutofFriday = self::claimMonth($nextMonthString); // We've past the cutoff - get the next cutoff
        }

        return $claimCutofFriday;
    }



}