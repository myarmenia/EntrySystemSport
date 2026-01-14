<?php

namespace App\Http\Controllers\Api\Turnstile;

use App\DTO\EntryCodeDto;
use App\Http\Controllers\Api\BaseController;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\EntryCodeRequest;
use App\Http\Resources\Api\EntryCodeResource;
use App\Services\EntryCodeService;
use Illuminate\Http\Request;
use lluminate\Http\JsonResponse;

class EntryCodeController extends BaseController
{
    public function __construct(protected EntryCodeService $service) {

    }

    public function store(EntryCodeRequest $request): mixed
    {

        $entryCode = $this->service->store(
            EntryCodeDto::fromApiRequest($request)
        );

        $data = $entryCode->result != null ? EntryCodeResource::make($entryCode->result) : null;

        return $entryCode->result != null ? $this->sendResponse($data, $entryCode->message) : $this->sendError($entryCode->message);
    }


}
