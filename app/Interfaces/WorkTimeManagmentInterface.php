<?php
namespace App\Interfaces;

use App\Models\ScheduleName;

interface WorkTimeManagmentInterface
{
    public function index();
    public function createScheduleName(string $name, int $status): ScheduleName;

    public function attachClient(
        int $clientId,
        int $scheduleNameId
    ): void;

    public function createScheduleDetail(
        int $scheduleNameId,
        array $dayData
    ): void;

    public function createSmokeBreak(
        int $clientId,
        int $scheduleNameId,
        string $day,
        array $smoke
    ): void;

    public function edit($id): ScheduleName;
}
