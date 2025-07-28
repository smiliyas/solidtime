<?php

declare(strict_types=1);

namespace App\Service;

use App\Enums\TimeEntryRoundingType;
use Illuminate\Support\Carbon;
use LogicException;

class TimeEntryService
{
    public function getStartSelectRawForRounding(?TimeEntryRoundingType $roundingType, ?int $roundingMinutes): string
    {
        if ($roundingType === null || $roundingMinutes === null) {
            return 'start';
        }
        if ($roundingMinutes < 1) {
            throw new LogicException('Rounding minutes must be greater than 0');
        }

        // MySQL compatible version - round to the nearest minute
        return 'FROM_UNIXTIME(FLOOR(UNIX_TIMESTAMP(start) / 60) * 60)';
    }

    public function getEndSelectRawForRounding(?TimeEntryRoundingType $roundingType, ?int $roundingMinutes): string
    {
        if ($roundingType === null || $roundingMinutes === null) {
            return 'coalesce("end", \''.Carbon::now()->toDateTimeString().'\')';
        }
        if ($roundingMinutes < 1) {
            throw new LogicException('Rounding minutes must be greater than 0');
        }
        $end = 'coalesce("end", \''.Carbon::now()->toDateTimeString().'\')';
        $minutesInSeconds = $roundingMinutes * 60;

        // MySQL compatible versions for different rounding types
        if ($roundingType === TimeEntryRoundingType::Down) {
            // Round down to the nearest interval
            return 'FROM_UNIXTIME(FLOOR(UNIX_TIMESTAMP('.$end.') / '.$minutesInSeconds.') * '.$minutesInSeconds.')';
        } elseif ($roundingType === TimeEntryRoundingType::Up) {
            // Round up to the nearest interval
            return 'FROM_UNIXTIME(CEILING(UNIX_TIMESTAMP('.$end.') / '.$minutesInSeconds.') * '.$minutesInSeconds.')';
        } elseif ($roundingType === TimeEntryRoundingType::Nearest) {
            // Round to the nearest interval
            return 'FROM_UNIXTIME(ROUND(UNIX_TIMESTAMP('.$end.') / '.$minutesInSeconds.') * '.$minutesInSeconds.')';
        }

        // Default fallback
        return $end;
    }
}
