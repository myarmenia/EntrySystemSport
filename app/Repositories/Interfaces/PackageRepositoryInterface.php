<?php

namespace App\Repositories\Interfaces;

interface PackageRepositoryInterface
{
    public function index();
    public function store(array $data);
    public function getPackagesList();
    public function getActivePackages();
    public function getPackagesByDiscount();
}
