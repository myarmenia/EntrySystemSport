<?php

namespace App\Http\Controllers\WorkTimeManagment;

use App\DTO\WorkTimeManagmentDto;
use App\Helpers\MyHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\WorkTimeManagmentRequest;
use App\Services\WorkTimeManagmentService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WorkTimeManagmentController extends Controller
{
    public function __construct( protected WorkTimeManagmentService $service){}
    public function index(): View{

        $data = $this->service->list();
        $i = 0;

        return view('work-time-managment.index', compact('data', 'i'));
    }
    public function create(): View{

        $weekdays = MyHelper::week_days();

        return view('work-time-managment.create',compact('weekdays'));
    }
    public function store(WorkTimeManagmentRequest $request){
        // dd($request->all());

        $dto = WorkTimeManagmentDto::fromRequest($request);

        $client_id = MyHelper::find_auth_user_client();
        $data = $this->service->store($dto,  $client_id);
        return redirect()->route('schedule.work-time-list');
    }
    public function edit($id)
    {
        $weekdays = MyHelper::week_days();

        $data = $this->service->editScheduleName($id);

        return view('work-time-managment.edit', compact('data', 'weekdays'));
    }
    public function update(WorkTimeManagmentRequest $request,int $scheduleId) {
        $dto = WorkTimeManagmentDto::fromRequest($request);

        $clientId = MyHelper::find_auth_user_client();

        $this->service->update($scheduleId, $dto, $clientId);

        return redirect()->route('schedule.work-time-edit',$scheduleId);
    }

}
