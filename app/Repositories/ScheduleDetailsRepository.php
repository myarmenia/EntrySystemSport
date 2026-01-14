<?php

namespace App\Repositories;

use App\Models\ScheduleDetails;
use App\Repositories\Interfaces\ScheduleDetailsInterface;

class ScheduleDetailsRepository implements ScheduleDetailsInterface
{
    public function update($dto,$id){
        // dd($dto,$id);

            $existingData = ScheduleDetails::where('schedule_name_id', $id)->get();

            foreach ($dto['week_days'] as $item) {

                $existingRecord = $existingData->firstWhere('week_day', $item['week_day']);

                if ($existingRecord) {

                    $existingRecord->update($item);
                } else {

                    ScheduleDetails::create(array_merge($item, ['schedule_name_id' => $id]));
                }
            }

    }

}
