<?php

namespace App\Repositories;

use App\DTO\UserDto;
use App\Models\Client;
use App\Models\SessionDuration;
use App\Models\Staff;
use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Auth;
use DB;

class UserRepository implements UserRepositoryInterface
{
    //public function store(array $data): User
    //{
    //    //dd($data);
    //    $user = User::create($data);
    //
    //    $user->assignRole($data['roles']);
    //
    //        if(isset($data['client']['name']) && $data['client']['name']!=null){
    //            $client=new Client();
    //
    //            $client->user_id = $user->id;
    //            $client->name = $data['client']['name'];
    //            $client->email = $data['client']['email'];
    //            $client->address = $data['client']['address'];
    //            //$client->shedule_name_id = $data['client']['schedule_name_id'] ?? null;
    //            $client->save();
    //
    //        }
    //        if (Auth::user()->hasAnyRole(['client_admin','client_admin_rfID','client_sport'])){
    //
    //            $client_admin_id = Client::where('user_id',Auth::id())->first()->id;
    //
    //        $client_admin_id = Client::where('user_id', Auth::id())->first()->id;
    //
    //        $staff_user = [
    //            'user_id' =>  $user->id,
    //            'client_admin_id' => $client_admin_id
    //        ];
    //
    //        Staff::create($staff_user);
    //    }
    //
    //    return $user;
    //}
    //public function store(array $data): User
    //{
    //    // ❗ Եթե request-ից գալիս են schedule_name_ids, User::create-ին չտանք
    //    $scheduleIds = $data['schedule_name_ids'] ?? [];
    //    unset($data['schedule_name_ids']);
    //    unset($data['schedule_name_id']); // եթե հին դաշտը դեռ գալիս ա
    //
    //    $user = User::create($data);
    //
    //    $user->assignRole($data['roles']);
    //
    //    // ✅ schedule կապում ենք pivot table-ով (մի քանի schedule)
    //    if (!empty($scheduleIds)) {
    //        $user->scheduleNames()->sync($scheduleIds);
    //    }
    //
    //    // client create (քո լոգիկան թողնում ենք)
    //    if (isset($data['client']['name']) && $data['client']['name'] != null) {
    //        $client = new Client();
    //        $client->user_id = $user->id;
    //        $client->name = $data['client']['name'];
    //        $client->email = $data['client']['email'];
    //        $client->address = $data['client']['address'];
    //        $client->save();
    //    }
    //
    //    // staff create (քո լոգիկան թողնում ենք, բայց կրկնվող տողը հանում եմ)
    //    if (Auth::user()->hasAnyRole(['client_admin', 'client_admin_rfID', 'client_sport'])) {
    //        $client_admin_id = Client::where('user_id', Auth::id())->value('id');
    //
    //        if ($client_admin_id) {
    //            Staff::create([
    //                'user_id' => $user->id,
    //                'client_admin_id' => $client_admin_id,
    //            ]);
    //        }
    //    }
    //
    //    return $user;
    //}
    public function store(array $data): User
    {
        return DB::transaction(function () use ($data) {

            $role = $data['roles'] ?? null;

            // schedules
            $scheduleIds = $data['schedule_name_ids'] ?? [];
            unset($data['schedule_name_ids'], $data['schedule_name_id']);

            // session durations
            $sessionDurations = $data['session_durations'] ?? [];
            unset($data['session_durations']);

            unset($data['roles']);

            $user = User::create($data);

            if ($role) {
                $user->assignRole($role);
            }

            if (!empty($scheduleIds)) {
                $user->scheduleNames()->sync($scheduleIds);
            }

            /* ===========================
           TRAINER SESSION DURATIONS
        ============================ */
            if ($role === 'trainer' && !empty($sessionDurations)) {

                $pivotIds = [];

                foreach ($sessionDurations as $row) {

                    // skip empty rows
                    if (
                        empty($row['minutes']) &&
                        empty($row['title']) &&
                        empty($row['price_amd'])
                    ) {
                        continue;
                    }

                    // 1️⃣ create or find session_duration
                    $sessionDuration = SessionDuration::firstOrCreate(
                        [
                            'minutes' => (int)$row['minutes'],
                            'title' => $row['title'] ?? null,
                            'price_amd' => (int)$row['price_amd'],
                        ]
                    );

                    // 2️⃣ prepare pivot attach
                    $pivotIds[$sessionDuration->id] = [
                        'is_active' => true,
                    ];
                }

                // 3️⃣ attach all at once
                if (!empty($pivotIds)) {
                    $user->sessionDurations()->syncWithoutDetaching($pivotIds);
                }
            }

            /* ===========================
           CLIENT / STAFF LOGIC (քոնն 그대로)
        ============================ */

            if (isset($data['client']['name']) && $data['client']['name'] != null) {
                Client::create([
                    'user_id' => $user->id,
                    'name' => $data['client']['name'],
                    'email' => $data['client']['email'],
                    'address' => $data['client']['address'],
                ]);
            }

            if (Auth::user()->hasAnyRole(['client_admin', 'client_admin_rfID', 'client_sport'])) {
                $client_admin_id = Client::where('user_id', Auth::id())->value('id');

                if ($client_admin_id) {
                    Staff::create([
                        'user_id' => $user->id,
                        'client_admin_id' => $client_admin_id,
                    ]);
                }
            }

            return $user;
        });
    }

    public function findByEmail(string $email): ?User
    {
        // Поиск пользователя по email
        return User::where('email', $email)->first();
    }

    //public function update($id, $data)
    //{
    //
    //    $user = User::findOrFail($id);
    //
    //    $user->update($data);
    //
    //
    //    if (isset($data['client']['name']) && $data['client']['name'] != null) {
    //
    //        $user->client->update($data['client']);
    //        $user->client->save();
    //    }
    //
    //
    //    return $user;
    //}



    public function update($id, $data)
    {
        return DB::transaction(function () use ($id, $data) {

            $user = User::with(['client', 'roles', 'sessionDurations'])->findOrFail($id);

            // schedules ids separate
            $scheduleIds = $data['schedule_name_ids'] ?? [];
            unset($data['schedule_name_ids'], $data['schedule_name_id']);

            // session durations separate
            $sessionDurations = $data['session_durations'] ?? null;
            unset($data['session_durations']);

            // roles field-ը edit-ում disabled է, բայց եթե հանկարծ գա request-ից՝ չթողնենք user->update-ին
            unset($data['roles'], $data['roles.*']);

            // update user main fields
            $user->update($data);

            // ✅ sync schedules (եթե եկել է array)
            if (is_array($scheduleIds)) {
                $user->scheduleNames()->sync($scheduleIds);
            }

            // ✅ update client
            if (isset($data['client']['name']) && $data['client']['name'] != null) {
                // եթե client relation-ը միշտ կա՝ ok, եթե չէ՝ safeguard
                if ($user->client) {
                    $user->client->update($data['client']);
                }
            }

            /* ===========================
           TRAINER SESSION DURATIONS SYNC
        ============================ */

            $isTrainer = $user->hasRole('trainer');

            if ($isTrainer && is_array($sessionDurations)) {

                // ✅ sync-ից առաջ պահում ենք ինչ ids էին արդեն կցված
                $oldIds = $user->sessionDurations()->pluck('session_durations.id')->toArray();

                $pivotIds = [];

                foreach ($sessionDurations as $row) {

                    $minutes = $row['minutes'] ?? null;
                    $title = $row['title'] ?? null;
                    $priceAmd = $row['price_amd'] ?? null;

                    $isEmpty =
                        ($minutes === null || $minutes === '' || (int)$minutes === 0) &&
                        ($priceAmd === null || $priceAmd === '') &&
                        (empty($title));

                    if ($isEmpty) continue;

                    $sd = SessionDuration::firstOrCreate([
                        'minutes' => (int)$minutes,
                        'title' => $title ?: null,
                        'price_amd' => (int)$priceAmd,
                    ]);

                    $pivotIds[$sd->id] = ['is_active' => true];
                }

                // ✅ pivot sync (detach what is removed)
                $user->sessionDurations()->sync($pivotIds);

                // ✅ orphan cleanup (delete from session_durations if unused anywhere)
                $newIds = array_keys($pivotIds);
                $detachedIds = array_diff($oldIds, $newIds);

                if (!empty($detachedIds)) {
                    SessionDuration::whereIn('id', $detachedIds)
                        ->whereDoesntHave('users')
                        ->delete();
                }
            }


            // եթե user-ը trainer ՉԷ, բայց request-ից ինչ-որ session_durations գա, ignore ենք անում
            // (կամ եթե ուզում ես trainer role-ը չունեցող user-ի durations-ը մաքրվի՝ կարող ենք անել detach)

            return $user;
        });
    }


    public function delete($id)
    {


        $user = User::find($id);
        if ($user) {
            $client = Client::where('user_id', $id)->first();
            if ($client) {
                $client->delete();
            }

            $user->delete();

            return true;
        } else {
            return false;
        }
    }
}
