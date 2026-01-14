<?php

namespace App\Services;


use App\DTO\PersonDTO;
use App\Models\Client;
use App\Models\EntryCode;
use App\Repositories\Interfaces\PersonRepositoryInterface;
use App\Repositories\Interfaces\PackageRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class PackageService
{
    protected $PackageRepositoryInterface;

    public function __construct(PackageRepositoryInterface $PackageRepositoryInterface)
    {
        $this->PackageRepositoryInterface = $PackageRepositoryInterface;
    }

    public function list()
    {

        $data = $this->PackageRepositoryInterface->index();
        return $data;
    }

    public function store(array $data)
    {
        return $this->PackageRepositoryInterface->store($data);
    }
}
