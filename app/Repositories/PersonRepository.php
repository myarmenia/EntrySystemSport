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
use App\Models\PersonPayment;




class PersonRepository implements PersonRepositoryInterface
{

    //public function getAllPeople(?string $type = null): LengthAwarePaginator
    //{
    //    $user = auth()->user();
    //
    //    $with = [
    //        'department',
    //        'package',
    //        'activeAbsences', // ✅ սա ավելացրու
    //    ];
    //
    //    $query = Person::with($with)->latest();
    //
    //    if ($user->hasRole('trainer')) {
    //        $query->where('trainer_id', $user->id);
    //    } elseif ($user->hasAnyRole(['client_admin', 'client_admin_rfID', 'client_sport'])) {
    //        $client = Client::where('user_id', $user->id)->first();
    //        if (!$client) return collect([])->paginate(10);
    //        $query->where('client_id', $client->id);
    //    }
    //
    //    if ($type) $query->where('type', $type);
    //    //dd($query->paginate(10)->withQueryString());
    //    return $query->paginate(10)->withQueryString();
    //}

    public function getAllPeople(?string $type = null): LengthAwarePaginator
    {
        $user = auth()->user();

        $with = [
            'department',
            'package',
            'activeAbsences',
            'latestPayment',
            'activeBookings',
            'latestBooking',
            'activeBookingsForFilter'

        ];

        $query = Person::with($with)->latest();

        // ✅ role filters
        if ($user->hasRole('trainer')) {
            $query->where('trainer_id', $user->id);
        } elseif ($user->hasAnyRole(['client_admin', 'client_admin_rfID', 'client_sport'])) {
            $client = Client::where('user_id', $user->id)->first();
            if (!$client) {
                return Person::whereRaw('1=0')->paginate(10)->withQueryString();
            }
            $query->where('client_id', $client->id);
        }

        if ($type) $query->where('type', $type);

        // =========================
        // ✅ Filters from query string
        // =========================

        // 1) search by name/surname
        if (request()->filled('q')) {
            $q = trim(request('q'));
            $query->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('surname', 'like', "%{$q}%");
            });
        }

        // 2) absence filter: all | has | none
        if (request()->filled('absence')) {
            $absence = request('absence'); // has | none | all
            if ($absence === 'has') {
                $query->whereHas('activeAbsences');
            } elseif ($absence === 'none') {
                $query->whereDoesntHave('activeAbsences');
            }
        }

        // 3) payment filter (based on active package + payment status)
        // payment = paid | unpaid | noactive | all
        if (request()->filled('payment')) {
            $payment = request('payment');
            //dd($payment);
            if ($payment === 'paid') {
                $query->whereHas('activeBookingsForFilter')
                    ->whereHas('latestPayment', fn($p) => $p->where('status', 'paid'));
            } elseif ($payment === 'pending') {
                $query->whereHas('activeBookingsForFilter')
                    ->whereHas('latestPayment', fn($p) => $p->where('status', 'pending'));
            }
        }

        return $query->paginate(10)->withQueryString();
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

    //public function storePerson($personDTO)
    //{
    //    return DB::transaction(function () use ($personDTO) {
    //
    //        $entry_code_id = $personDTO['entry_code_id'];
    //
    //        $department_id = $personDTO['department_id'] ?? null;
    //        $schedule_name_id = $personDTO['schedule_name_id'] ?? null;
    //
    //        $session_duration_id = $personDTO['session_duration_id'] ?? null;
    //
    //        // ✅ OLD single booking fields (may be empty now)
    //
    //
    //        // ✅ NEW multi booking JSON
    //        $weekly_slots_json = $personDTO['weekly_slots_json'] ?? '[]';
    //
    //        $image = $personDTO['image'] ?? null;
    //        $package_id = $personDTO['package_id'] ?? null;
    //        $trainer_id = $personDTO['trainer_id'] ?? null;
    //
    //        // ❗ fields that MUST NOT go to Person::create()
    //        unset($personDTO['entry_code_id']);
    //        unset($personDTO['schedule_name_id'], $personDTO['department_id'], $personDTO['session_duration_id']);
    //        unset($personDTO['weekly_slots_json']); // ✅ NEW
    //        unset($personDTO['image']);
    //
    //        $entry_code = EntryCode::where('id', $entry_code_id)->first();
    //
    //        // create person
    //        $person = Person::create($personDTO);
    //
    //        if ($person && isset($package_id)) {
    //            $person->package_id = $package_id;
    //            $person->save();
    //        }
    //
    //        if ($person && isset($trainer_id)) {
    //            $person->trainer_id = $trainer_id;
    //            $person->save();
    //        }
    //
    //        // ✅ schedule_department_people save
    //        if ($person) {
    //            $schedule_department_people = new ScheduleDepartmentPerson();
    //            $schedule_department_people->client_id = $entry_code->client_id;
    //            $schedule_department_people->department_id = $department_id;
    //            $schedule_department_people->schedule_name_id = $schedule_name_id;
    //            $schedule_department_people->session_duration_id = $session_duration_id;
    //            $schedule_department_people->person_id = $person->id;
    //            $schedule_department_people->save();
    //        }
    //
    //        // upload image
    //        if ($image != null) {
    //            $path = FileUploadService::upload($image, 'people/' . $person->id);
    //            $person->image = $path;
    //            $person->save();
    //        }
    //
    //
    //        $nextDateForWeekday = function (string $weekday) {
    //
    //            $today = Carbon::today();
    //
    //            if (strcasecmp($today->format('l'), $weekday) === 0) {
    //                return $today->copy();
    //            }
    //
    //            return Carbon::parse('next ' . $weekday);
    //        };
    //
    //        /**
    //         * Prices snapshot helper
    //         */
    //        $duration = $session_duration_id ? SessionDuration::find($session_duration_id) : null;
    //        $package = $package_id ? Package::find($package_id) : null;
    //
    //        $months = (int)($package?->months ?? 1);
    //
    //        $packagePrice = $this->calcDiscountedPackagePrice($package_id, (int)$entry_code->client_id);
    //
    //
    //        $durationPrice = (int)($duration?->price_amd ?? 0);
    //        $durationTotal = $durationPrice * $months;
    //        $totalPrice = (int)(($packagePrice ?? 0) + $durationTotal);
    //
    //        // ----------------------------------------------------
    //        // ✅ NEW: MULTI-WEEKLY BOOKINGS (from weekly_slots_json)
    //        // ----------------------------------------------------
    //        $weeklySlots = [];
    //        try {
    //            $weeklySlots = json_decode($weekly_slots_json, true, 512, JSON_THROW_ON_ERROR);
    //        } catch (\Throwable $e) {
    //            $weeklySlots = [];
    //        }
    //
    //        if ($person && $trainer_id && $schedule_name_id && $session_duration_id && is_array($weeklySlots) && count($weeklySlots)) {
    //
    //            // ✅ Բոլոր row-երի համար նույն package start/end
    //            $packageStartDate = Carbon::today()->toDateString();
    //            $packageEndDate   = Carbon::today()->addMonthsNoOverflow($months)->toDateString();
    //
    //            foreach ($weeklySlots as $slot) {
    //                $weekDay   = $slot['week_day'] ?? null;     // Monday...
    //                $slotStart = $slot['start_time'] ?? null;   // HH:mm
    //                $slotEnd   = $slot['end_time'] ?? null;     // HH:mm
    //
    //                if (!$weekDay || !$slotStart || !$slotEnd) continue;
    //
    //                PersonSessionBooking::create([
    //                    'client_id'           => $entry_code->client_id,
    //                    'person_id'           => $person->id,
    //                    'trainer_id'          => $trainer_id,
    //                    'schedule_name_id'    => $schedule_name_id,
    //                    'department_id'       => $department_id,
    //                    'session_duration_id' => $session_duration_id,
    //
    //                    'day'                 => $weekDay,
    //                    'start_time'          => $slotStart,
    //                    'end_time'            => $slotEnd,
    //
    //                    // ✅ բոլորի համար նույնը՝ այսօրվանից
    //                    'session_start_time'  => $packageStartDate,
    //                    'session_end_time'    => $packageEndDate,
    //
    //                    'package_months'      => $months,
    //                    'package_price_amd'   => $packagePrice,
    //                    'duration_price_amd'  => $durationPrice,
    //                    'duration_total_amd'  => $durationTotal,
    //                    'total_price_amd'     => $totalPrice,
    //                ]);
    //            }
    //        }
    //
    //
    //        $person_permission_entry_code = PersonPermission::where([
    //            'entry_code_id' => $entry_code_id,
    //            'status' => 1
    //        ])->first();
    //
    //        if ($person_permission_entry_code) {
    //            $person_permission_entry_code->status = 0;
    //            $person_permission_entry_code->save();
    //        }
    //
    //        $person_permission = new PersonPermission();
    //        $person_permission->person_id = $person->id;
    //        $person_permission->entry_code_id = $entry_code_id;
    //        $person_permission->save();
    //
    //        if ($person_permission) {
    //            $entry_code->activation = 1;
    //            $entry_code->save();
    //        }
    //
    //        return $person;
    //    });
    //}

    public function storePerson($personDTO)
    {
        return DB::transaction(function () use ($personDTO) {

            $entry_code_id       = $personDTO['entry_code_id'];
            $department_id       = $personDTO['department_id'] ?? null;
            $schedule_name_id    = $personDTO['schedule_name_id'] ?? null;
            $session_duration_id = $personDTO['session_duration_id'] ?? null;

            // ✅ NEW multi booking JSON
            $weekly_slots_json   = $personDTO['weekly_slots_json'] ?? '[]';

            $image      = $personDTO['image'] ?? null;
            $package_id = $personDTO['package_id'] ?? null;
            $trainer_id = $personDTO['trainer_id'] ?? null;

            // ✅ payment fields from form/DTO
            $payment_method = $personDTO['payment_method'] ?? null; // cash|cashless|credit
            $payment_bank   = $personDTO['payment_bank'] ?? null;

            // ❗ fields that MUST NOT go to Person::create()
            unset($personDTO['entry_code_id']);
            unset($personDTO['schedule_name_id'], $personDTO['department_id'], $personDTO['session_duration_id']);
            unset($personDTO['weekly_slots_json']);
            unset($personDTO['image']);

            // եթե payment-ը պահում ես առանձին table-ում՝ կարող ես սրանք էլ հանել create-ից
            unset($personDTO['payment_method'], $personDTO['payment_bank']);

            $entry_code = EntryCode::where('id', $entry_code_id)->firstOrFail();

            // create person
            $person = Person::create($personDTO);

            if ($person && isset($package_id)) {
                $person->package_id = $package_id;
            }

            if ($person && isset($trainer_id)) {
                $person->trainer_id = $trainer_id;
            }

            if ($person) {
                $person->save();
            }

            // ✅ schedule_department_people save
            if ($person) {
                $schedule_department_people = new ScheduleDepartmentPerson();
                $schedule_department_people->client_id           = $entry_code->client_id;
                $schedule_department_people->department_id       = $department_id;
                $schedule_department_people->schedule_name_id    = $schedule_name_id;
                $schedule_department_people->session_duration_id = $session_duration_id;
                $schedule_department_people->person_id           = $person->id;
                $schedule_department_people->save();
            }

            // upload image
            if ($image != null) {
                $path = FileUploadService::upload($image, 'people/' . $person->id);
                $person->image = $path;
                $person->save();
            }

            /**
             * Prices snapshot helper
             */
            $duration = $session_duration_id ? SessionDuration::find($session_duration_id) : null;
            $package  = $package_id ? Package::find($package_id) : null;

            $months = (int)($package?->months ?? 1);

            // ✅ վերջնական package գին (զեղչված կամ ոչ)
            $packagePrice = (int)($this->calcDiscountedPackagePrice($package_id, (int)$entry_code->client_id) ?? 0);

            $durationPrice = (int)($duration?->price_amd ?? 0);
            $durationTotal = $durationPrice * $months;
            $totalPrice    = (int)($packagePrice + $durationTotal);

            // ----------------------------------------------------
            // ✅ Payment insert (person_payments table)
            // ----------------------------------------------------
            if ($person && $payment_method) {

                // normalize bank
                if ($payment_method === 'cash') {
                    $payment_bank = null;
                }

                $isPaidNow = in_array($payment_method, ['cash', 'cashless'], true);

                PersonPayment::create([
                    'person_id'      => $person->id,
                    'client_id'      => $entry_code->client_id,

                    'payment_method' => $payment_method,
                    'payment_bank'   => $payment_bank,

                    'amount_amd'     => $totalPrice,
                    'currency'       => 'AMD',

                    'status'         => $isPaidNow ? 'paid' : 'pending',
                    'paid_at'        => $isPaidNow ? now() : null,
                ]);
            }

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

                $packageStartDate = Carbon::today()->toDateString();
                $packageEndDate   = Carbon::today()->addMonthsNoOverflow($months)->toDateString();

                foreach ($weeklySlots as $slot) {
                    $weekDay   = $slot['week_day'] ?? null;
                    $slotStart = $slot['start_time'] ?? null;
                    $slotEnd   = $slot['end_time'] ?? null;

                    if (!$weekDay || !$slotStart || !$slotEnd) continue;

                    PersonSessionBooking::create([
                        'client_id'           => $entry_code->client_id,
                        'person_id'           => $person->id,
                        'trainer_id'          => $trainer_id,
                        'schedule_name_id'    => $schedule_name_id,
                        'department_id'       => $department_id,
                        'session_duration_id' => $session_duration_id,

                        'day'                 => $weekDay,
                        'start_time'          => $slotStart,
                        'end_time'            => $slotEnd,

                        'session_start_time'  => $packageStartDate,
                        'session_end_time'    => $packageEndDate,

                        'package_months'      => $months,
                        'package_price_amd'   => $packagePrice,
                        'duration_price_amd'  => $durationPrice,
                        'duration_total_amd'  => $durationTotal,
                        'total_price_amd'     => $totalPrice,
                    ]);
                }
            }

            // permissions + entry_code activation
            $person_permission_entry_code = PersonPermission::where([
                'entry_code_id' => $entry_code_id,
                'status'        => 1
            ])->first();

            if ($person_permission_entry_code) {
                $person_permission_entry_code->status = 0;
                $person_permission_entry_code->save();
            }

            $person_permission = new PersonPermission();
            $person_permission->person_id     = $person->id;
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

    //public function updatePerson(PersonDTO $personDTO, array $data)
    //{
    //    return DB::transaction(function () use ($personDTO, $data) {
    //
    //        $person = Person::with('schedule_department_people')->findOrFail($personDTO->id);
    //
    //        // -------------------------
    //        // 1) Person fields update
    //        // -------------------------
    //        $person->name       = $data['name'] ?? $personDTO->name;
    //        $person->surname    = $data['surname'] ?? $personDTO->surname;
    //        $person->email      = $data['email'] ?? $personDTO->email;
    //        $person->phone      = $data['phone'] ?? $personDTO->phone;
    //        $person->type       = $data['type'] ?? $personDTO->type;
    //        $person->package_id = $data['package_id'] ?? $personDTO->package_id;
    //        $person->trainer_id = $data['trainer_id'] ?? $personDTO->trainer_id;
    //
    //        if (isset($data['image'])) {
    //            $path = FileUploadService::upload($data['image'], 'people/' . $person->id);
    //            $person->image = $path;
    //        }
    //
    //        $person->save();
    //
    //        // -------------------------
    //        // 2) schedule_department_people update
    //        // -------------------------
    //        $department_id       = $data['department_id'] ?? null;
    //        $schedule_name_id    = $data['schedule_name_id'] ?? null;
    //        $session_duration_id = $data['session_duration_id'] ?? null;
    //
    //        if ($person) {
    //            foreach ($person->schedule_department_people as $item) {
    //                $item->update([
    //                    "schedule_name_id"    => $schedule_name_id,
    //                    "department_id"       => $department_id,
    //                    "session_duration_id" => $session_duration_id,
    //                ]);
    //            }
    //        }
    //
    //        // -------------------------
    //        // 3) permissions logic (your existing)
    //        // -------------------------
    //        if ($personDTO->entry_code_id != null) {
    //
    //            $person_permission_old = PersonPermission::where([
    //                'person_id' => $person->id,
    //                'status' => 1
    //            ])->first();
    //
    //            if ($person_permission_old) {
    //                $person_permission_old->status = 0;
    //                $person_permission_old->save();
    //
    //                $old_entry_code = EntryCode::findOrFail($person_permission_old->entry_code_id);
    //                $old_entry_code->activation = 0;
    //                $old_entry_code->save();
    //            }
    //
    //            $person_permission = new PersonPermission();
    //            $person_permission->person_id = $personDTO->id;
    //            $person_permission->entry_code_id = $personDTO->entry_code_id;
    //            $person_permission->status = 1;
    //            $person_permission->save();
    //
    //            $entry_code = EntryCode::findOrFail($personDTO->entry_code_id);
    //            $entry_code->activation = 1;
    //            $entry_code->save();
    //        }
    //
    //        // -------------------------
    //        // 4) Sync bookings from weekly_slots_json
    //        // -------------------------
    //        $weekly_slots_json = $data['weekly_slots_json'] ?? '[]';
    //
    //        $weeklySlots = [];
    //        try {
    //            $weeklySlots = json_decode($weekly_slots_json, true, 512, JSON_THROW_ON_ERROR);
    //        } catch (\Throwable $e) {
    //            $weeklySlots = [];
    //        }
    //
    //        // ⚠️ client_id needed for bookings
    //        // use active permission -> entry_code -> client_id
    //        $activePerm = PersonPermission::query()
    //            ->where('person_id', $person->id)
    //            ->where('status', 1)
    //            ->latest('id')
    //            ->first();
    //
    //        $clientId = null;
    //        if ($activePerm) {
    //            $ec = EntryCode::find($activePerm->entry_code_id);
    //            $clientId = $ec?->client_id;
    //        }
    //
    //        $trainer_id = $data['trainer_id'] ?? $person->trainer_id ?? null;
    //        $package_id = $data['package_id'] ?? $person->package_id ?? null;
    //
    //        // եթե booking-ի համար պարտադիր դաշտեր չկան՝ ուղղակի թողնենք booking-ները չփոխենք
    //        // բայց քո պահանջով՝ “ջնջի ու նորից ստեղծի” — ես անում եմ միայն եթե կարող ենք ստեղծել
    //        $canSyncBookings = (
    //            $person &&
    //            $clientId &&
    //            $trainer_id &&
    //            $schedule_name_id &&
    //            $session_duration_id &&
    //            is_array($weeklySlots)
    //        );
    //
    //        if ($canSyncBookings) {
    //
    //            // --- pricing snapshot ---
    //            $duration = $session_duration_id ? SessionDuration::find($session_duration_id) : null;
    //            $package  = $package_id ? Package::find($package_id) : null;
    //
    //            $months = (int)($package?->months ?? 1);
    //
    //            // ✅ discounted package price via pivot discounts
    //            $packagePrice = $this->calcDiscountedPackagePrice($package_id, (int)$clientId);
    //
    //            $durationPrice = (int)($duration?->price_amd ?? 0);
    //            $durationTotal = $durationPrice * $months;
    //            $totalPrice = (int)(($packagePrice ?? 0) + $durationTotal);
    //
    //            $nextDateForWeekday = function (string $weekday) {
    //                $today = Carbon::today();
    //                if (strcasecmp($today->format('l'), $weekday) === 0) return $today->copy();
    //                return Carbon::parse('next ' . $weekday);
    //            };
    //
    //            // ✅ 4.1 delete old bookings for this person
    //            PersonSessionBooking::where('person_id', $person->id)->delete();
    //
    //            // ✅ 4.2 create new bookings
    //            foreach ($weeklySlots as $slot) {
    //                $weekDay = $slot['week_day'] ?? null;
    //                $slotStart = $slot['start_time'] ?? null;
    //                $slotEnd   = $slot['end_time'] ?? null;
    //
    //                if (!$weekDay || !$slotStart || !$slotEnd) continue;
    //
    //                $slotDate = $nextDateForWeekday($weekDay)->format('Y-m-d');
    //                $sessionStartDate = Carbon::parse($slotDate)->startOfDay();
    //                $periodEndDate = $sessionStartDate->copy()->addMonthsNoOverflow($months)->toDateString();
    //
    //
    //
    //                PersonSessionBooking::create([
    //                    'client_id' => $clientId,
    //                    'person_id' => $person->id,
    //                    'trainer_id' => $trainer_id,
    //                    'schedule_name_id' => $schedule_name_id,
    //                    'department_id' => $department_id,
    //                    'session_duration_id' => $session_duration_id,
    //
    //                    'day' => $weekDay,
    //                    'start_time' => $slotStart,
    //                    'end_time' => $slotEnd,
    //
    //                    // ✅ date-only
    //                    'session_start_time' => $sessionStartDate->toDateString(),
    //                    'session_end_time'   => $periodEndDate,
    //
    //                    'package_months' => $months,
    //                    'package_price_amd' => $packagePrice,
    //                    'duration_price_amd' => $durationPrice,
    //                    'duration_total_amd' => $durationTotal,
    //                    'total_price_amd' => $totalPrice,
    //                ]);
    //            }
    //        }
    //
    //        return $person;
    //    });
    //}
    public function updatePerson(PersonDTO $personDTO, array $data)
    {
        return DB::transaction(function () use ($personDTO, $data) {

            $person = Person::with('schedule_department_people')->findOrFail($personDTO->id);

            // ✅ package change flag (checkbox)
            // Blade-ում checkbox-ը ուղարկիր name="change_package" value="1"
            $changePackage = !empty($data['change_package']) && (string)$data['change_package'] === '1';
            // -------------------------
            // ✅ Payment fields from edit form
            // -------------------------
            $payment_method = $data['payment_method'] ?? null; // cash|cashless|credit
            $payment_bank   = $data['payment_bank'] ?? null;

            // checkbox -> եթե չկա request-ում => false
            $is_paid = !empty($data['is_paid']) && (string)$data['is_paid'] === '1';

            if ($payment_method === 'cash') {
                $payment_bank = null;
            }

            $payment_status = $is_paid ? 'paid' : 'pending';
            $paid_at = $is_paid ? now() : null;

            // -------------------------
            // 1) Person fields update
            // -------------------------
            $person->name       = $data['name'] ?? $personDTO->name;
            $person->surname    = $data['surname'] ?? $personDTO->surname;
            $person->email      = $data['email'] ?? $personDTO->email;
            $person->phone      = $data['phone'] ?? $personDTO->phone;
            $person->type       = $data['type'] ?? $personDTO->type;
            $incomingTrainer = $data['trainer_id'] ?? null;
            $person->trainer_id = !empty($incomingTrainer) ? $incomingTrainer : null;

            // ✅ package_id update միայն checkbox-ով
            if ($changePackage && !empty($data['package_id'])) {
                $person->package_id = $data['package_id'];
            }

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

            // client_id needed for bookings: active permission -> entry_code -> client_id
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

            // ✅ booking calculations համար package_id-ը վերցնենք person-ից (already saved),
            // որպեսզի checkbox չնշված դեպքում հինը մնա, նշված դեպքում՝ նորն օգտագործվի
            $package_id = $person->package_id;

            $canSyncBookings = (
                $person &&
                $clientId &&
                $trainer_id &&
                $schedule_name_id &&
                $session_duration_id &&
                is_array($weeklySlots)
            );
            if ($canSyncBookings) {

                $duration = $session_duration_id ? SessionDuration::find($session_duration_id) : null;
                $package  = $package_id ? Package::find($package_id) : null;

                $months = (int)($package?->months ?? 1);

                $packagePrice  = $this->calcDiscountedPackagePrice($package_id, (int)$clientId);
                $durationPrice = (int)($duration?->price_amd ?? 0);
                $durationTotal = $durationPrice * $months;
                $totalPrice    = (int)(($packagePrice ?? 0) + $durationTotal);
                // -------------------------
                // ✅ Update / Create PersonPayment
                // -------------------------
                if ($payment_method) {
                    $latestPayment = PersonPayment::where('person_id', $person->id)->latest('id')->first();

                    if ($latestPayment) {
                        // update latest payment
                        $latestPayment->update([
                            'payment_method' => $payment_method,
                            'payment_bank'   => $payment_bank,
                            'status'         => $payment_status,
                            'paid_at'        => $paid_at,

                            // եթե ուզում ես միշտ պահվի վերջին հաշվարկված գումարը
                            'amount_amd'     => (int)$totalPrice,
                        ]);
                    } else {
                        // create new payment record
                        if (!$clientId) {
                            // fallback եթե clientId չգտանք
                            $clientId = $person->client_id ?? null;
                        }

                        if ($clientId) {
                            PersonPayment::create([
                                'person_id'      => $person->id,
                                'client_id'      => $clientId,
                                'payment_method' => $payment_method,
                                'payment_bank'   => $payment_bank,
                                'amount_amd'     => (int)$totalPrice,
                                'currency'       => 'AMD',
                                'status'         => $payment_status,
                                'paid_at'        => $paid_at,
                            ]);
                        }
                    }
                }


                $nextDateForWeekday = function (string $weekday) {
                    $today = Carbon::today();
                    if (strcasecmp($today->format('l'), $weekday) === 0) return $today->copy();
                    return Carbon::parse('next ' . $weekday);
                };

                if ($changePackage) {
                    // =====================================
                    // ✅ CASE 1: PACKAGE CHANGED
                    // → FULL REBUILD (dates will change)
                    // =====================================

                    PersonSessionBooking::where('person_id', $person->id)->delete();

                    foreach ($weeklySlots as $slot) {
                        $weekDay   = $slot['week_day'] ?? null;
                        $slotStart = $slot['start_time'] ?? null;
                        $slotEnd   = $slot['end_time'] ?? null;

                        if (!$weekDay || !$slotStart || !$slotEnd) continue;

                        $slotDate = $nextDateForWeekday($weekDay)->format('Y-m-d');
                        $sessionStartDate = Carbon::parse($slotDate)->startOfDay();
                        $periodEndDate = $sessionStartDate
                            ->copy()
                            ->addMonthsNoOverflow($months)
                            ->toDateString();

                        PersonSessionBooking::create([
                            'client_id'           => $clientId,
                            'person_id'           => $person->id,
                            'trainer_id'          => $trainer_id,
                            'schedule_name_id'    => $schedule_name_id,
                            'department_id'       => $department_id,
                            'session_duration_id' => $session_duration_id,

                            'day'                 => $weekDay,
                            'start_time'          => $slotStart,
                            'end_time'            => $slotEnd,

                            // 🔥 dates are recalculated
                            'session_start_time'  => $sessionStartDate->toDateString(),
                            'session_end_time'    => $periodEndDate,

                            'package_months'      => $months,
                            'package_price_amd'   => $packagePrice,
                            'duration_price_amd'  => $durationPrice,
                            'duration_total_amd'  => $durationTotal,
                            'total_price_amd'     => $totalPrice,
                        ]);
                    }
                } else {
                    // =====================================
                    // ✅ CASE 2: PACKAGE NOT CHANGED
                    // → SYNC days/times: update existing + create missing (+ delete removed)
                    // → keep session_start_time & session_end_time unchanged (same period)
                    // =====================================

                    $existingBookings = PersonSessionBooking::where('person_id', $person->id)->get();

                    // ✅ պահում ենք նույն package period-ը՝ գոյություն ունեցող booking-ներից
                    // (եթե կան)՝ վերցնենք առաջինից
                    $periodStart = $existingBookings->first()?->session_start_time;
                    $periodEnd   = $existingBookings->first()?->session_end_time;

                    // եթե հանկարծ booking-ներ չկան (edge case)՝ fallback to nextDate logic
                    if (!$periodStart || !$periodEnd) {
                        // այստեղ ստիպված ենք նորից հաշվարկել period-ը, որովհետև պահելու բան չկա
                        // (կարող ես նաև return անել ու չստեղծել booking)
                        $periodStart = Carbon::today()->toDateString();
                        $periodEnd   = Carbon::parse($periodStart)->addMonthsNoOverflow($months)->toDateString();
                    }

                    // ✅ slots map by day
                    $slotsByDay = [];
                    foreach ($weeklySlots as $slot) {
                        $d = $slot['week_day'] ?? null;
                        $s = $slot['start_time'] ?? null;
                        $e = $slot['end_time'] ?? null;
                        if ($d && $s && $e) {
                            $slotsByDay[$d] = ['start_time' => $s, 'end_time' => $e];
                        }
                    }

                    // ✅ existing bookings map by day
                    $bookingsByDay = $existingBookings->keyBy('day');

                    // 1) UPDATE + CREATE
                    foreach ($slotsByDay as $day => $slot) {

                        if ($bookingsByDay->has($day)) {
                            // ✅ update existing (keep dates!)
                            $b = $bookingsByDay->get($day);

                            $b->update([
                                'trainer_id'          => $trainer_id,
                                'schedule_name_id'    => $schedule_name_id,
                                'department_id'       => $department_id,
                                'session_duration_id' => $session_duration_id,

                                'start_time'          => $slot['start_time'],
                                'end_time'            => $slot['end_time'],

                                // ❌ session_start_time unchanged
                                // ❌ session_end_time unchanged

                                // ✅ կարող ես pricing snapshot update անել (սա ժամկետ չի փոխում)
                                'package_months'      => $months,
                                'package_price_amd'   => $packagePrice,
                                'duration_price_amd'  => $durationPrice,
                                'duration_total_amd'  => $durationTotal,
                                'total_price_amd'     => $totalPrice,
                            ]);
                        } else {
                            // ✅ NEW DAY → create new booking (but same period dates!)
                            PersonSessionBooking::create([
                                'client_id'           => $clientId,
                                'person_id'           => $person->id,
                                'trainer_id'          => $trainer_id,
                                'schedule_name_id'    => $schedule_name_id,
                                'department_id'       => $department_id,
                                'session_duration_id' => $session_duration_id,

                                'day'                 => $day,
                                'start_time'          => $slot['start_time'],
                                'end_time'            => $slot['end_time'],

                                // ✅ keep same package period
                                'session_start_time'  => Carbon::parse($periodStart)->toDateString(),
                                'session_end_time'    => Carbon::parse($periodEnd)->toDateString(),

                                'package_months'      => $months,
                                'package_price_amd'   => $packagePrice,
                                'duration_price_amd'  => $durationPrice,
                                'duration_total_amd'  => $durationTotal,
                                'total_price_amd'     => $totalPrice,
                            ]);
                        }
                    }

                    // 2) DELETE REMOVED DAYS (եթե ուզում ես, որ DB-ում մնան միայն ընտրած օրերը)
                    PersonSessionBooking::where('person_id', $person->id)
                        ->whereNotIn('day', array_keys($slotsByDay))
                        ->delete();
                }
            }


            return $person;
        });
    }
}