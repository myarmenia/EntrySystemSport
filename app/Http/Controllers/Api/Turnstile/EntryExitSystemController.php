<?php

namespace App\Http\Controllers\Api\Turnstile;

use App\DTO\EntryExitSystemDto;
use App\Http\Controllers\Api\BaseController;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\EntryExitSystemRequest;
use App\Http\Resources\Api\EntryExitSystemResource;
use App\Services\EntryExitSystemService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EntryExitSystemController extends BaseController
{
    public function __construct(protected EntryExitSystemService $service)
    {

    }
    public function __invoke(EntryExitSystemRequest $request): JsonResponse
    {

       \Log::info("comming request",$request->all());

        $ees = $this->service->ees(
            EntryExitSystemDto::fromEntryExitSystemDto($request)
        );

        // $data = $ees->result != null ? EntryExitSystemResource::make($ees->result) : null;
        $additionals = ['online' => $request->online];

          \Log::info("ees message", ["ees message"=>$ees->message]);
        return $ees->result != null ? $this->sendResponse(null, $ees->message, $additionals) : $this->sendError($ees->message, $additionals);

    }
}
