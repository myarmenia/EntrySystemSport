<?php

namespace App\Repositories;
use App\Interfaces\AttendanceSheetInterface;
use App\Models\AttendanceSheet;
use App\Models\EntryCode;
use Illuminate\Support\Facades\Log;

class AttendanceSheetRepository implements AttendanceSheetInterface
{

    public function create(array $data): AttendanceSheet
    {
        Log::info('Creating attendance record', [
            'server_time' => now()->toDateTimeString(),
            'data' => $data,
        ]);

        return AttendanceSheet::create($data);
    }
}
