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

        return view('work-time-managment.index');
    }
    public function create(): View{

        $weekdays = MyHelper::week_days();

        return view('work-time-managment.create',compact('weekdays'));
    }
    public function store(WorkTimeManagmentRequest $request){

        $dto = WorkTimeManagmentDto::fromRequest($request);

        $client_id = MyHelper::find_auth_user_client();
        $data = $this->service->store($dto,  $client_id);
        return redirect()->route('schedule.work-time-list');
    }
}
