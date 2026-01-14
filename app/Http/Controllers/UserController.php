<?php

namespace App\Http\Controllers;

use App\DTO\UserDto;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Models\Client;
use App\Models\ScheduleName;
use App\Models\Staff;
use App\Models\User;
use App\Services\UserService;
use Spatie\Permission\Models\Role;
use DB;
use Hash;
use Illuminate\Support\Arr;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    protected $userService;
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request): View
    {
        $client = Client::where('user_id', Auth::id())->first();
        // dd($client);
        $query = User::latest();
        // dd($query );
        if ($client) {

            $staff = Staff::where('client_admin_id', $client->id)->pluck('user_id');

            $data = User::whereIn('id', $staff)->latest()->paginate(5);
        } else {


            if (Auth::user()->hasRole('super_admin')) {

                $user_id = Client::all()->pluck('user_id');

                $query = $query->whereIn('id', $user_id);
            } else {


                $client_id = Staff::where('user_id', Auth::id())->value('client_admin_id');
                $staff = Staff::where('client_admin_id', $client_id)->pluck('user_id');
                $query = $query->whereIn('id', $staff);
            }
            $data = $query->paginate(5);
        }



        return view('users.index', compact('data'))
            ->with('i', ($request->input('page', 1) - 1) * 5);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(): View
    {
        // dd(Auth::user()->roles);

        if (Auth::user()->roles[0]->interface == 'client') {
            $roles = Role::where('position_name', 'client')->pluck('name', 'name')->all();
        } else {
            $roles = Role::where('position_name', 'admin')->pluck('name', 'name')->all();
        }
   
        $clientId = Client::where('user_id', Auth::id())->value('id');

        //$scheduleNames = \App\Models\ScheduleName::query()
        //    ->whereNull('deleted_at') // եթե soft delete ունես
        //    ->where('status', 1)
        //    ->whereIn('id', function ($q) use ($clientId) {
        //        $q->select('schedule_name_id')
        //            ->from('client_schedules')
        //            ->where('client_id', $clientId);
        //    })
        //    ->orderByDesc('id')
        //    ->pluck('name', 'id')
        //    ->all();

        $scheduleNames = ScheduleName::query()
            ->whereNull('deleted_at')
            ->where('status', 1)
            // ✅ պատկանում է տվյալ client-ին (client_schedules)
            ->whereIn('id', function ($q) use ($clientId) {
                $q->select('schedule_name_id')
                    ->from('client_schedules')
                    ->where('client_id', $clientId);
            })
            ->orderByDesc('id')
            ->pluck('name', 'id')
            ->all();

        return view('users.create', compact('roles', 'scheduleNames'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserRequest $request): RedirectResponse
    {

        $data = $request->all();
        $user = $this->userService->createUser($data);

        return redirect()->route('users.index')
            ->with('success', 'Օգտատերը ստեղծվել է հաջողությամբ');
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id): View
    {

        $user = User::find($id);

        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id): View
    {
        $user = User::with(['scheduleNames', 'client', 'sessionDurations'])->findOrFail($id);

        if (Auth::user()->roles[0]->interface == 'client') {
            $roles = Role::where('position_name', 'client')->pluck('name', 'name')->all();
        } else {
            $roles = Role::where('position_name', 'admin')->pluck('name', 'name')->all();
        }

        $userRole = $user->roles->pluck('name', 'name')->all();
        $isEditMode = true;

        $clientId = Client::where('user_id', Auth::id())->value('id');

        $scheduleNames = \App\Models\ScheduleName::query()
            ->whereNull('deleted_at')
            ->where('status', 1)
            ->whereIn('id', function ($q) use ($clientId) {
                $q->select('schedule_name_id')
                    ->from('client_schedules')
                    ->where('client_id', $clientId);
            })
            ->where(function ($q) use ($user) {
                $q->whereNotIn('id', function ($qq) use ($user) {
                    $qq->select('schedule_name_id')
                        ->from('schedule_name_user')
                        ->where('user_id', '!=', $user->id);
                })
                    ->orWhereIn('id', $user->scheduleNames->pluck('id'));
            })
            ->orderByDesc('id')
            ->pluck('name', 'id')
            ->all();

        $trainerSessionDurations = $user->sessionDurations
            ->where('pivot.is_active', true)
            ->values();

        return view('users.edit', compact(
            'user',
            'roles',
            'userRole',
            'isEditMode',
            'scheduleNames',
            'trainerSessionDurations'
        ));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // public function update(Request $request, $id): RedirectResponse
    // {
    //     $this->validate($request, [
    //         'name' => 'required',
    //         'email' => 'required|email|unique:users,email,'.$id,
    //         'password' => 'same:confirm-password',
    //         'roles' => 'required'
    //     ]);

    //     $input = $request->all();
    //     if(!empty($input['password'])){
    //         $input['password'] = Hash::make($input['password']);
    //     }else{
    //         $input = Arr::except($input,array('password'));
    //     }

    //     $user = User::find($id);
    //     $user->update($input);
    //     DB::table('model_has_roles')->where('model_id',$id)->delete();

    //     $user->assignRole($request->input('roles'));

    //     return redirect()->route('users.index')
    //                     ->with('success','User updated successfully');
    // }
    public function update(UserRequest $request, string $id)
    {



        $data = $this->userService->updateUser($id, $request->all());

        if ($data) {
            return redirect()->route('users.index')->with('success', "Օգտատերը խմբագրվել է հաջողությամբ");
        }

        return redirect()->back()->withErrors('Failed to update the user.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id): RedirectResponse
    {

        $data = $this->userService->deleteUser($id);

        if ($data) {
            return redirect()->route('users.index')
                ->with('success', 'Օգտատերը ջնջվել է հաջողությամբ');
        } else {
            return redirect()->route('users.index')
                ->with('error', 'Նման օգտատեր չի գտնվել');
        }
    }
}
