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
use App\Models\Package;
use App\Models\Person;
use App\Models\PersonPermission;
use App\Models\PersonSessionBooking;
use App\Models\ScheduleDepartmentPerson;
use App\Models\ScheduleName;
use App\Models\SessionDuration;
use App\Models\Staff;
use App\Repositories\Interfaces\PersonRepositoryInterface;
use App\Services\FileUploadService;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;



class PersonRepository implements PersonRepositoryInterface
{

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
        return DB::transaction(function () use ($personDTO) {

            $entry_code_id = $personDTO['entry_code_id'];

            $department_id = $personDTO['department_id'] ?? null;
            $schedule_name_id = $personDTO['schedule_name_id'] ?? null;

            $session_duration_id = $personDTO['session_duration_id'] ?? null;

            // ✅ OLD single booking fields (may be empty now)


            // ✅ NEW multi booking JSON
            $weekly_slots_json = $personDTO['weekly_slots_json'] ?? '[]';

            $image = $personDTO['image'] ?? null;
            $package_id = $personDTO['package_id'] ?? null;
            $trainer_id = $personDTO['trainer_id'] ?? null;

            // ❗ fields that MUST NOT go to Person::create()
            unset($personDTO['entry_code_id']);
            unset($personDTO['schedule_name_id'], $personDTO['department_id'], $personDTO['session_duration_id']);
            unset($personDTO['weekly_slots_json']); // ✅ NEW
            unset($personDTO['image']);

            $entry_code = EntryCode::where('id', $entry_code_id)->first();

            // create person
            $person = Person::create($personDTO);

            if ($person && isset($package_id)) {
                $person->package_id = $package_id;
                $person->save();
            }

            if ($person && isset($trainer_id)) {
                $person->trainer_id = $trainer_id;
                $person->save();
            }

            // ✅ schedule_department_people save
            if ($person) {
                $schedule_department_people = new ScheduleDepartmentPerson();
                $schedule_department_people->client_id = $entry_code->client_id;
                $schedule_department_people->department_id = $department_id;
                $schedule_department_people->schedule_name_id = $schedule_name_id;
                $schedule_department_people->session_duration_id = $session_duration_id;
                $schedule_department_people->person_id = $person->id;
                $schedule_department_people->save();
            }

            // upload image
            if ($image != null) {
                $path = FileUploadService::upload($image, 'people/' . $person->id);
                $person->image = $path;
                $person->save();
            }


            $nextDateForWeekday = function (string $weekday) {

                $today = Carbon::today();

                if (strcasecmp($today->format('l'), $weekday) === 0) {
                    return $today->copy();
                }

                return Carbon::parse('next ' . $weekday);
            };

            /**
             * Prices snapshot helper
             */
            $duration = $session_duration_id ? SessionDuration::find($session_duration_id) : null;
            $package = $package_id ? Package::find($package_id) : null;

            $months = (int)($package?->months ?? 1);

            $packagePrice = $this->calcDiscountedPackagePrice($package_id, (int)$entry_code->client_id);


            $durationPrice = (int)($duration?->price_amd ?? 0);
            $durationTotal = $durationPrice * $months;
            $totalPrice = (int)(($packagePrice ?? 0) + $durationTotal);

            // ----------------------------------------------------
            // ✅ NEW: MULTI-WEEKLY BOOKINGS (from weekly_slots_json)
            // ----------------------------------------------------
            $weeklySlots = [];
            try {
                $weeklySlots = json_decode($weekly_slots_json, true, 512, JSON_THROW_ON_ERROR);
            } catch (\Throwable $e) {
                $weeklySlots = [];
            }

            if ($person && $trainer_id && $schedule_name_id && $session_duration_id && is_array($weeklySlots) && count($weeklySlots)) {
                foreach ($weeklySlots as $slot) {
                    $weekDay = $slot['week_day'] ?? null;       // Monday...
                    $slotStart = $slot['start_time'] ?? null;   // HH:mm
                    $slotEnd   = $slot['end_time'] ?? null;     // HH:mm
                    //dd($weekDay, $slotStart, $slotEnd);


                    // ✅ choose next date for that weekday (so we can keep session_date logic consistent)
                    $dateObj = $nextDateForWeekday($weekDay);
                    $slotDate = $dateObj->format('Y-m-d');

                    $sessionStartDate = Carbon::parse($slotDate)->startOfDay(); // 00:00:00

                    // եթե ուզում ես session_end_time-ը լինի ամիսներով ավարտը՝ պահենք date-only
                    $periodEndDate = $sessionStartDate->copy()->addMonthsNoOverflow($months)->toDateString();

                    // եթե ուզում ես session_end_time-ը լինի հենց slot-ի end-ի օրը՝ date-only (overnight-ի դեպքում +1 day)
                    $slotEndDate = $sessionStartDate->copy();
                    if ($slotEnd <= $slotStart) {
                        $slotEndDate->addDay();
                    }
                    $slotEndDate = $slotEndDate->toDateString();


                    // Conflict check (trainer/date/start_time)


                    PersonSessionBooking::create([
                        'client_id' => $entry_code->client_id,
                        'person_id' => $person->id,
                        'trainer_id' => $trainer_id,
                        'schedule_name_id' => $schedule_name_id,
                        'department_id' => $department_id,
                        'session_duration_id' => $session_duration_id,
                        'day' => $weekDay,
                        'start_time' => $slotStart,
                        'end_time' => $slotEnd,
                        'session_start_time' => $sessionStartDate->toDateString(),
                        'session_end_time' => $periodEndDate, // եթե ուզում ես հենց slot end՝ դիր $sessionEndAt
                        'package_months' => $months,
                        'package_price_amd' => $packagePrice,
                        'duration_price_amd' => $durationPrice,
                        'duration_total_amd' => $durationTotal,
                        'total_price_amd' => $totalPrice,
                    ]);
                }
            }

            $person_permission_entry_code = PersonPermission::where([
                'entry_code_id' => $entry_code_id,
                'status' => 1
            ])->first();

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
        });
    }

    private function calcDiscountedPackagePrice(?int $packageId, int $clientId): ?int
    {
        if (!$packageId) return null;

        $now = Carbon::now();

        // վերցնում ենք ակտիվ discount-ը տվյալ package-ի համար
        $discount = DB::table('discount_package as dp')
            ->join('discounts as d', 'd.id', '=', 'dp.discount_id')
            ->where('dp.package_id', $packageId)
            ->where('d.client_id', $clientId)
            ->where('d.status', 1)
            ->whereNull('d.deleted_at')
            ->where('d.starts_at', '<=', $now)
            ->where('d.ends_at', '>=', $now)
            ->orderByDesc('d.starts_at')
            ->select('d.type', 'd.value')
            ->first();

        $package = Package::find($packageId);
        if (!$package) return null;

        $base = (int) $package->price_amd;

        // եթե ակտիվ discount չկա՝ վերադարձնում ենք base
        if (!$discount) return $base;

        $type = (string) $discount->type;   // 'percent' կամ 'fixed'
        $val  = (float) $discount->value;

        if ($type === 'percent') {
            $base = (int) round($base * (1 - ($val / 100)));
        } elseif ($type === 'fixed') {
            $base = (int) max(0, $base - (int) round($val));
        }

        return (int) max(0, $base);
    }



    public function editPerson($personId)
    {
        $person_connected_schedule_department = [];
        $client_id = MyHelper::find_auth_user_client();

        $person = Person::where('id', $personId)
            ->with([
                'schedule_department_people',
                // եթե սրանք relations-ով ունես ScheduleDepartmentPerson model-ում՝
                'schedule_department_people.schedule_name',
                'schedule_department_people.sessionDuration',
            ])
            ->first();

        $person_connected_schedule_department['person'] = $person;

        $client_schedule = ClientSchedule::where('client_id', $client_id)->pluck("schedule_name_id");

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
        //dd($person_connected_schedule_department);
        return $person_connected_schedule_department;
    }

    public function updatePerson(PersonDTO $personDTO, array $data)
    {
        return DB::transaction(function () use ($personDTO, $data) {

            $person = Person::with('schedule_department_people')->findOrFail($personDTO->id);

            // -------------------------
            // 1) Person fields update
            // -------------------------
            $person->name       = $data['name'] ?? $personDTO->name;
            $person->surname    = $data['surname'] ?? $personDTO->surname;
            $person->email      = $data['email'] ?? $personDTO->email;
            $person->phone      = $data['phone'] ?? $personDTO->phone;
            $person->type       = $data['type'] ?? $personDTO->type;
            $person->package_id = $data['package_id'] ?? $personDTO->package_id;
            $person->trainer_id = $data['trainer_id'] ?? $personDTO->trainer_id;

            if (isset($data['image'])) {
                $path = FileUploadService::upload($data['image'], 'people/' . $person->id);
                $person->image = $path;
            }

            $person->save();

            // -------------------------
            // 2) schedule_department_people update
            // -------------------------
            $department_id       = $data['department_id'] ?? null;
            $schedule_name_id    = $data['schedule_name_id'] ?? null;
            $session_duration_id = $data['session_duration_id'] ?? null;

            if ($person) {
                foreach ($person->schedule_department_people as $item) {
                    $item->update([
                        "schedule_name_id"    => $schedule_name_id,
                        "department_id"       => $department_id,
                        "session_duration_id" => $session_duration_id,
                    ]);
                }
            }

            // -------------------------
            // 3) permissions logic (your existing)
            // -------------------------
            if ($personDTO->entry_code_id != null) {

                $person_permission_old = PersonPermission::where([
                    'person_id' => $person->id,
                    'status' => 1
                ])->first();

                if ($person_permission_old) {
                    $person_permission_old->status = 0;
                    $person_permission_old->save();

                    $old_entry_code = EntryCode::findOrFail($person_permission_old->entry_code_id);
                    $old_entry_code->activation = 0;
                    $old_entry_code->save();
                }

                $person_permission = new PersonPermission();
                $person_permission->person_id = $personDTO->id;
                $person_permission->entry_code_id = $personDTO->entry_code_id;
                $person_permission->status = 1;
                $person_permission->save();

                $entry_code = EntryCode::findOrFail($personDTO->entry_code_id);
                $entry_code->activation = 1;
                $entry_code->save();
            }

            // -------------------------
            // 4) Sync bookings from weekly_slots_json
            // -------------------------
            $weekly_slots_json = $data['weekly_slots_json'] ?? '[]';

            $weeklySlots = [];
            try {
                $weeklySlots = json_decode($weekly_slots_json, true, 512, JSON_THROW_ON_ERROR);
            } catch (\Throwable $e) {
                $weeklySlots = [];
            }

            // ⚠️ client_id needed for bookings
            // use active permission -> entry_code -> client_id
            $activePerm = PersonPermission::query()
                ->where('person_id', $person->id)
                ->where('status', 1)
                ->latest('id')
                ->first();

            $clientId = null;
            if ($activePerm) {
                $ec = EntryCode::find($activePerm->entry_code_id);
                $clientId = $ec?->client_id;
            }

            $trainer_id = $data['trainer_id'] ?? $person->trainer_id ?? null;
            $package_id = $data['package_id'] ?? $person->package_id ?? null;

            // եթե booking-ի համար պարտադիր դաշտեր չկան՝ ուղղակի թողնենք booking-ները չփոխենք
            // բայց քո պահանջով՝ “ջնջի ու նորից ստեղծի” — ես անում եմ միայն եթե կարող ենք ստեղծել
            $canSyncBookings = (
                $person &&
                $clientId &&
                $trainer_id &&
                $schedule_name_id &&
                $session_duration_id &&
                is_array($weeklySlots)
            );

            if ($canSyncBookings) {

                // --- pricing snapshot ---
                $duration = $session_duration_id ? SessionDuration::find($session_duration_id) : null;
                $package  = $package_id ? Package::find($package_id) : null;

                $months = (int)($package?->months ?? 1);

                // ✅ discounted package price via pivot discounts
                $packagePrice = $this->calcDiscountedPackagePrice($package_id, (int)$clientId);

                $durationPrice = (int)($duration?->price_amd ?? 0);
                $durationTotal = $durationPrice * $months;
                $totalPrice = (int)(($packagePrice ?? 0) + $durationTotal);

                $nextDateForWeekday = function (string $weekday) {
                    $today = Carbon::today();
                    if (strcasecmp($today->format('l'), $weekday) === 0) return $today->copy();
                    return Carbon::parse('next ' . $weekday);
                };

                // ✅ 4.1 delete old bookings for this person
                PersonSessionBooking::where('person_id', $person->id)->delete();

                // ✅ 4.2 create new bookings
                foreach ($weeklySlots as $slot) {
                    $weekDay = $slot['week_day'] ?? null;
                    $slotStart = $slot['start_time'] ?? null;
                    $slotEnd   = $slot['end_time'] ?? null;

                    if (!$weekDay || !$slotStart || !$slotEnd) continue;

                    $slotDate = $nextDateForWeekday($weekDay)->format('Y-m-d');
                    $sessionStartDate = Carbon::parse($slotDate)->startOfDay();
                    $periodEndDate = $sessionStartDate->copy()->addMonthsNoOverflow($months)->toDateString();



                    PersonSessionBooking::create([
                        'client_id' => $clientId,
                        'person_id' => $person->id,
                        'trainer_id' => $trainer_id,
                        'schedule_name_id' => $schedule_name_id,
                        'department_id' => $department_id,
                        'session_duration_id' => $session_duration_id,

                        'day' => $weekDay,
                        'start_time' => $slotStart,
                        'end_time' => $slotEnd,

                        // ✅ date-only
                        'session_start_time' => $sessionStartDate->toDateString(),
                        'session_end_time'   => $periodEndDate,

                        'package_months' => $months,
                        'package_price_amd' => $packagePrice,
                        'duration_price_amd' => $durationPrice,
                        'duration_total_amd' => $durationTotal,
                        'total_price_amd' => $totalPrice,
                    ]);
                }
            }

            return $person;
        });
    }
}
