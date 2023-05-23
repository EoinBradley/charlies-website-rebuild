<?php

namespace App\Repositories;

use App\Models\OpeningTimes\OpeningTime;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Carbon\CarbonPeriod;
use Illuminate\Support\Collection;
use PDO;

class OpeningHoursRepository
{
    public function __construct(private readonly PDO $db)
    {
    }

    public function getOpeningHoursForWeek(?Carbon $startingAt = null): Collection
    {
        if ($startingAt === null) {
            $startingAt = Carbon::now()->startOfDay()->addHours(5);
        }

        return collect(iterator_to_array(
            new CarbonPeriod(
                $startingAt,
                new CarbonInterval('P1D'),
                $startingAt->copy()->addDays(6)
            )
        ))->map(function (Carbon $date) {
            return $this->getOpeningHoursForDate($date);
        })->filter()->sort(function (OpeningTime $a, OpeningTime $b) {
            return ($a->date->dayOfWeek ?: 7) < ($b->date->dayOfWeek ?: 7) ? -1 : 1;
        })->values();
    }

    public function getOpeningHoursForDate(Carbon $date): OpeningTime
    {
        $stmt = $this->db->prepare("
            SELECT opening_time.day_of_week,
                   IF(exception.id IS NOT NULL, exception.opened_at, opening_time.opened_at) AS opened_at,
                   IF(exception.id IS NOT NULL, exception.closed_at, opening_time.closed_at) AS closed_at,
                   IF(exception.id IS NOT NULL, TRUE, FALSE) AS is_exception,
                   exception.description
            FROM opening_hours AS opening_time
            LEFT JOIN opening_hour_exceptions AS exception
                ON exception.exception_date IN(DATE_SUB(DATE(:date), INTERVAL 1 DAY), DATE(:date))
                    AND opening_time.day_of_week = DAYOFWEEK(exception.exception_date)
                    AND exception.deleted_at IS NULL
            WHERE opening_time.day_of_week IN(DAYOFWEEK(DATE_SUB(DATE(:date), INTERVAL 1 DAY)), DAYOFWEEK(DATE(:date)))
                AND opening_time.deleted_at IS NULL
            ORDER BY FIELD(opening_time.day_of_week, DAYOFWEEK(DATE_SUB(DATE(:date), INTERVAL 1 DAY)), DAYOFWEEK(DATE(:date)));
        ");

        $stmt->execute([
            'date' => $date
        ]);

        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) AS $dayData) {
            if ($dayData['day_of_week'] == $date->dayOfWeek + 1) {
                $dayOfWeek = $date->copy()->startOfDay();
            } else {
                $dayOfWeek = $date->copy()->subDay()->startOfDay();
                if ($dayData['opened_at'] === null && $dayData['closed_at'] === null) {
                    continue;
                }
            }

            if ($dayData['opened_at'] === null && $dayData['closed_at'] === null) {
                return new OpeningTime(
                    null,
                    null,
                    $date,
                    $dayData['is_exception'] == 1,
                    $dayData['description']
                );
            }

            $openingTime = strtotime($dayData['opened_at']);
            $closingTime = strtotime($dayData['closed_at']);

            $day = new OpeningTime(
                Carbon::parse($dayOfWeek->format('Y-m-d') . ' ' . $dayData['opened_at']),
                $closingTime < $openingTime
                    ? Carbon::parse($dayOfWeek->copy()->addDay()->format('Y-m-d') . ' ' . $dayData['closed_at'])
                    : Carbon::parse($dayOfWeek->format('Y-m-d') . ' ' . $dayData['closed_at']),
                $date,
                $dayData['is_exception'] == 1,
                $dayData['description']
            );

            if ($dayData['day_of_week'] == $date->dayOfWeek + 1) {
                if ($date->between($day->openedAt, $day->closedAt) || $date->isBefore($day->openedAt)) {
                    return $day;
                }
            } else {
                if ($date->between($day->openedAt, $day->closedAt)) {
                    return $day;
                }
            }
        }

        return new OpeningTime(
            null,
            null,
            $date,
        );
    }
}