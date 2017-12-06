<?php
namespace Domain\Model;

use Carbon\Carbon;

/**
 *
 * @author Daniel Vaqueiro <danielvc4 at gmail.com>
 */
class BookingChecker
{
    public function checkInDate()
    {
        $out = true;

        $hoy = Carbon::now();
        if($hoy->isWeekend()) {
            $out = false;
        }
        if($hoy->isFriday() && $hoy->hour < 12) {
            $out = false;
        }

        return $out;
    }
}