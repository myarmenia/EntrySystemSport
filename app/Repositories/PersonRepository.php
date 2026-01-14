<?php

namespace App\Repositories;

use App\DTO\NewPersonDto;
use App\DTO\PersonDTO;
use App\Helpers\MyHelper;
use App\Http\Controllers\People\PeopleController;
use App\Models\Client;
use App\Models\ClientSchedule;
use App\Models\Department;
use App\Models\EntryCode;
use App\Models\Person;
use App\Models\PersonPermission;
use App\Models\ScheduleDepartmentPerson;
use App\Models\ScheduleName;
use App\Models\Staff;
use App\Repositories\Interfaces\PersonRepositoryInterface;
use App\Services\FileUploadService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class PersonRepository implements PersonRepositoryInterface
{
    //public function getAllPeople(?string $type = null)
    //{
    //    if (auth()->user()->hasRole(['client_admin', 'client_admin_rfID'])) {
    //
    //        $client = Client::where('user_id', Auth::id())->first();
    //
    //        if ($client != null) {
    //            $query = Person::with(['trainer', 'package'])
    //                ->where('client_id', $client->id);
    //
    //            if ($type) {
    //                $query->where('type', $type); // visitor / worker
    //            }
    //
    //            return $query->latest()
    //                ->paginate(10)
    //                ->withQueryString();
    //        }
    //
    //        return Person::whereRaw('1=0')
    //            ->paginate(10)
    //            ->withQueryString();
    //    } else {
    //
    //        $query = Person::with(['trainer', 'package']);
    //
    //        if ($type) {
    //            $query->where('type', $type);
    //        }
    //
    //        return $query->latest()
    //            ->paginate(10)
    //            ->withQueryString();
    //    }
    //}

    public function getAllPeople(?string $type = null): LengthAwarePaginator
    {
        $user = auth()->user();
        $with = ['department', 'package']; // ✅ այստեղ գրիր քո հարաբերությունները
        $query = Person::with($with)->latest();

        if ($user->hasRole('trainer')) {

            $query->where('trainer_id', $user->id);
        } elseif ($user->hasAnyRole([
            'client_admin',
            'client_admin_rfID',
            'client_sport'
        ])) {

            $client = Client::where('user_id', $user->id)->first();

            if (!$client) {
                return collect([])->paginate(10);
            }

            $query->where('client_id', $client->id);
        }

        if ($type) {
            $query->where('type', $type);
        }

        return $query
            ->paginate(10)
            ->withQueryString();
    }
    public function createPerson()
    {
        $client = Client::where('id', MyHelper::find_auth_user_client())->first();
        // dd( $client);
        if ($client != null) {

            $query = EntryCode::where(['client_id' => $client->id, 'activation' => 0])->get();
            $client_schedule = ClientSchedule::where('client_id', $client->id)->pluck("schedule_name_id");
            $query['client_id'] = $client->id;

            $department = Department::where('client_id', $client->id)->get();
            if (count($client_schedule) == 0 || count($department) == 0) {
                return false;
            }
            if (count($client_schedule) > 0) {

                $schedule_name = ScheduleName::whereIn('id', $client_schedule)
                    ->where('status', 1)
                    ->get();

                if (count($schedule_name) > 0) {

                    $query['client_schedule'] = $schedule_name;
                }
            }
            if (count($department) > 0) {

                $query['department'] = Department::where('client_id', $client->id)->get();
            }
        }

        return $query;
    }

    public function storePerson($personDTO)
    {
        // dd($personDTO);
        $entry_code_id = $personDTO['entry_code_id'];
        $department_id = $personDTO['department_id'] ?? null;
        $schedule_name_id = $personDTO['schedule_name_id'] ?? null;
        $image = $personDTO['image'] ?? null;
        $package_id = $personDTO['package_id'] ?? null;
        $trainer_id = $personDTO['trainer_id'] ?? null;
        // dd($entry_code_id );
        unset($personDTO['entry_code_id']);
        if (isset($personDTO['schedule_name_id'])) {
            unset($personDTO['schedule_name_id']);
        }
        if (isset($personDTO['department_id'])) {
            unset($personDTO['department_id']);
        }

        // dd($personDTO);
        if (isset($personDTO['image'])) {
            unset($personDTO['image']);
        }
        // dd($entry_code_id,$department_id, $schedule_name_id);
        $entry_code = EntryCode::where('id', $entry_code_id)->first();
        //dd($personDTO);
        $person = Person::create($personDTO);
        if ($person && isset($package_id)) {
            $person->package_id = $package_id;
            $person->save();
        }
        if ($person && isset($trainer_id)) {
            $person->trainer_id = $trainer_id;
            $person->save();
        }
        if ($person) {
            // dd($entry_code->client_id,)
            $schedule_department_people = new ScheduleDepartmentPerson();
            $schedule_department_people->client_id = $entry_code->client_id;
            $schedule_department_people->department_id = $department_id;
            $schedule_department_people->schedule_name_id = $schedule_name_id;
            $schedule_department_people->person_id = $person->id;
            $schedule_department_people->save();
        }

        // dd($person);
        if ($image != null) {
            // dd($image);
            $path = FileUploadService::upload($image,  'people/' . $person->id);
            $person->image = $path;
            $person->save();
            //    dd($path);
        }
        // dd($person);
        // եթե նախկինում կցված է եղել
        $person_permission_entry_code = PersonPermission::where(['entry_code_id' => $entry_code_id, 'status' => 1])->first();

        if ($person_permission_entry_code) {

            $person_permission_entry_code->status = 0;
            $person_permission_entry_code->save();
        }

        $person_permission = new PersonPermission();

        $person_permission->person_id = $person->id;
        $person_permission->entry_code_id = $entry_code_id;
        $person_permission->save();

        if ($person_permission) {

            $entry_code->activation = 1;
            $entry_code->save();
        }
        return $person;
    }
    public function editPerson($personId)
    {
        $person_connected_schedule_department = [];
        $client_id = MyHelper::find_auth_user_client();

        $person = Person::where('id', $personId)
            ->with('schedule_department_people')
            ->first();

        $person_connected_schedule_department['person'] = $person;

        $client_schedule = ClientSchedule::where('client_id', $client_id)->pluck("schedule_name_id");
        // dd($client_schedule);
        if (count($client_schedule) > 0) {

            $client_schedules_name = ScheduleName::whereIn('id', $client_schedule)
                ->where('status', 1)
                ->get();
            if (count($client_schedules_name) > 0) {
                $person_connected_schedule_department['client_schedules'] = $client_schedules_name;
            }
        }
        $department = Department::where('client_id', $client_id)->get();
        if (count($department) > 0) {
            $person_connected_schedule_department['department'] = $department;
        }
        // dd($person_connected_schedule_department);
        return $person_connected_schedule_department;
    }
    public function updatePerson(PersonDTO $personDTO, array $data)
    {
        // dd($personDTO, $data);
        $person = Person::with('schedule_department_people')->findOrFail($personDTO->id);
        // dd($person->schedule_department_people);
        // Update fields based on data from DTO or request
        $person->name = $data['name'] ?? $personDTO->name;
        $person->surname = $data['surname'] ?? $personDTO->surname;
        $person->email = $data['email'] ?? $personDTO->email;
        $person->phone = $data['phone'] ?? $personDTO->phone;
        $person->type = $data['type'] ?? $personDTO->type;
        $person->package_id = $data['package_id'] ?? $personDTO->package_id;
        $person->trainer_id = $data['trainer_id'] ?? $personDTO->trainer_id;

        // Update image if a new one is provided
        if (isset($data['image'])) {
            $path = FileUploadService::upload($data['image'], 'people/' . $person->id);
            $person->image = $path;
        }

        $person->save();

        if ($person) {
            // dd( $personDTO);
            // $scheduleDepartmentPeople = $person->schedule_department_people()->get();
            foreach ($person->schedule_department_people as $item) {

                $item->update([
                    "schedule_name_id" => $data['schedule_name_id'] ?? null,
                    "department_id" => $data['department_id'] ?? null
                ]);
                $item->save();
            }

            if ($personDTO->entry_code_id != null) {

                $person_permission_old = PersonPermission::where(['person_id' => $person->id, 'status' => 1])->first();
                if ($person_permission_old) {
                    $person_permission_old->status = 0;
                    $person_permission_old->save();
                    // Update old entry code activation
                    $old_entry_code = EntryCode::findOrFail($person_permission_old->entry_code_id);
                    $old_entry_code->activation = 0;
                    $old_entry_code->save();
                }

                // creating new personPermission
                $person_permission = new PersonPermission();
                $person_permission->person_id = $personDTO->id;
                $person_permission->entry_code_id = $personDTO->entry_code_id;
                $person_permission->status = 1;
                $person_permission->save();
                // Update new entry code activation
                $entry_code = EntryCode::findOrFail($personDTO->entry_code_id);
                $entry_code->activation = 1;
                $entry_code->save();
            }
        }
        return $person;
    }
}
