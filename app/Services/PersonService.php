<?php

namespace App\Services;


use App\DTO\PersonDTO;
use App\Models\Client;
use App\Models\EntryCode;
use App\Models\User;
use App\Repositories\Interfaces\PersonRepositoryInterface;
use App\Repositories\Interfaces\PackageRepositoryInterface;

use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class PersonService
{
    protected $personRepository;
    protected $packageRepository;

    public function __construct(
        PersonRepositoryInterface $personRepository,
        PackageRepositoryInterface $packageRepository
    ) {
        $this->personRepository = $personRepository;
        $this->packageRepository = $packageRepository;
    }

    // public function getPeopleList()
    // {
    //     $people = $this->personRepository->getAllPeople();
    //
    //     return $people;
    // }

    public function getPeopleList(?string $type = null)
    {
        return $this->personRepository->getAllPeople($type);
    }

    public function create()
    {

        $data = $this->personRepository->createPerson();

        return $data;
    }

    public function store($personDTO)
    {
        return $this->personRepository->storePerson($personDTO->toArray());
    }
    public function edit($personId)
    {

        $data = $this->personRepository->editPerson($personId);


        return [
            "non_active_entry_code" => $this->getAllNonActivatedEntryCode(),
            "person_connected_schedule_department" => $data
        ];
    }
    public function update(PersonDTO $personDTO, array $data)
    {
        return $this->personRepository->updatePerson($personDTO, $data);
    }

    //public function getAllNonActivatedEntryCode()
    //{
    //    $client = Client::where('user_id',Auth::id())->first();
    //
    //    $entry_code = EntryCode::where(['client_id'=>$client->id,'activation'=>0,'status'=>1])->get();
    //
    //    if(count($entry_code)>0)
    //    {
    //        return $entry_code;
    //    }else{
    //        return false;
    //
    //    }
    //
    //}
    public function getAllNonActivatedEntryCode()
    {
        $clientId = Client::where('user_id', Auth::id())->value('id');

        if (!$clientId) return collect();

        return EntryCode::where([
            'client_id'  => $clientId,
            'activation' => 0,
            'status'     => 1,
        ])->get();
    }

    public function getPackagesList()
    {
        return $this->packageRepository->getActivePackages(); // կամ Package::active()->get()
    }

    public function getPackagesByDiscount()
    {
        return $this->packageRepository->getPackagesByDiscount();
    }

    //public function getTrainersList()
    //{
    //    // current client_admin-ի client id
    //    $clientId = Client::where('user_id', Auth::id())->value('id');
    //
    //    $trainerRole = Role::where('name', 'trainer')->firstOrFail();
    //
    //    $trainerUsers = $trainerRole->users()
    //        ->where('model_has_roles.model_type', User::class)
    //        ->whereNull('users.deleted_at') // soft delete filter
    //        ->when($clientId, function ($q) use ($clientId) {
    //            $q->whereIn('users.id', function ($sub) use ($clientId) {
    //                $sub->select('user_id')
    //                    ->from('staff')
    //                    ->where('client_admin_id', $clientId);
    //            });
    //        })
    //        ->get();
    //
    //    return $trainerUsers;
    //}
    public function getTrainersList()
    {
        // current client_admin-ի client id
        $clientId = Client::where('user_id', Auth::id())->value('id');

        $trainerRole = Role::where('name', 'trainer')->firstOrFail();

        $trainerUsers = $trainerRole->users()
            ->where('model_has_roles.model_type', User::class)
            ->whereNull('users.deleted_at') // soft delete filter
            ->when($clientId, function ($q) use ($clientId) {
                $q->whereIn('users.id', function ($sub) use ($clientId) {
                    $sub->select('user_id')
                        ->from('staff')
                        ->where('client_admin_id', $clientId);
                });
            })
            // ✅ bring trainer schedules (հերթափոխերը)
            ->with(['scheduleNames' => function ($q) {
                $q->select('schedule_names.id', 'schedule_names.name'); // միայն պետքական դաշտերը
            }])
            ->select('users.id', 'users.name') // ցանկության դեպքում ավելացրու այլ դաշտեր
            ->orderBy('users.name')
            ->get();

        return $trainerUsers;
    }
}
