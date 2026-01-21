<?php

namespace App\Repositories;

use App\Models\ScheduleDetails;
use App\Repositories\Interfaces\ScheduleDetailsInterface;

class ScheduleDetailsRepository implements ScheduleDetailsInterface
{
    //public function update($dto, $id)
    //{
    //    //dd($dto,$id);
    //
    //    $existingData = ScheduleDetails::where('schedule_name_id', $id)->get();
    //
    //    foreach ($dto['week_days'] as $item) {
    //
    //        $existingRecord = $existingData->firstWhere('week_day', $item['week_day']);
    //
    //        if ($existingRecord) {
    //
    //            $existingRecord->update($item);
    //        } else {
    //
    //            ScheduleDetails::create(array_merge($item, ['schedule_name_id' => $id]));
    //        }
    //    }
    //}
    //public function update($dto, $id)
    //{
    //    $existingData = ScheduleDetails::where('schedule_name_id', $id)->get();
    //
    //    foreach (($dto['week_days'] ?? []) as $item) {
    //
    //        $weekDay = $item['week_day'] ?? null;
    //        if (!$weekDay) continue;
    //
    //        $existingRecord = $existingData->firstWhere('week_day', $weekDay);
    //
    //        // ✅ եթե enabled=0 (checkbox-ը հանած է) → ջնջում ենք տվյալ օրվա schedule_details-ը
    //        if (isset($item['enabled']) && (string)$item['enabled'] === '0') {
    //            if ($existingRecord) {
    //                $existingRecord->delete();
    //            }
    //            continue;
    //        }
    //
    //        // ✅ եթե enabled=1 → update/create
    //        if (empty($item['enabled'])) {
    //            // եթե enabled-ը չի եկել (պետք չի լինի hidden-ից հետո), ուղղակի skip
    //            continue;
    //        }
    //
    //        $payload = [
    //            'week_day'         => $weekDay,
    //            'day_start_time'   => $item['day_start_time'] ?? null,
    //            'day_end_time'     => $item['day_end_time'] ?? null,
    //            'break_start_time' => $item['break_start_time'] ?? null,
    //            'break_end_time'   => $item['break_end_time'] ?? null,
    //        ];
    //
    //        if ($existingRecord) {
    //            $existingRecord->update($payload);
    //        } else {
    //            ScheduleDetails::create(array_merge($payload, ['schedule_name_id' => $id]));
    //        }
    //    }
    //}

    public function update($dto, $id)
    {
        $existingData = ScheduleDetails::where('schedule_name_id', $id)->get();

        foreach (($dto['week_days'] ?? []) as $item) {

            $weekDay = $item['week_day'] ?? null;
            if (!$weekDay) continue;

            $existingRecord = $existingData->firstWhere('week_day', $weekDay);

            $enabled = (int)($item['enabled'] ?? 0);

            // payload՝ միայն իրական սյունակները
            $payload = [
                'week_day'         => $weekDay,
                'enabled'          => $enabled,
                'day_start_time'   => $item['day_start_time'] ?? null,
                'day_end_time'     => $item['day_end_time'] ?? null,
                'break_start_time' => $item['break_start_time'] ?? null,
                'break_end_time'   => $item['break_end_time'] ?? null,
            ];

            // Եթե disable է արել, ուզում ես ժամերը ջնջե՞լ (մաքրել) — խորհուրդ եմ տալիս
            if ($enabled === 0) {
                $payload['day_start_time'] = null;
                $payload['day_end_time'] = null;
                $payload['break_start_time'] = null;
                $payload['break_end_time'] = null;
            }

            if ($existingRecord) {
                $existingRecord->update($payload);
            } else {
                ScheduleDetails::create(array_merge($payload, ['schedule_name_id' => $id]));
            }
        }
    }
}
