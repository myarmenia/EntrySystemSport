<?php
namespace App\Repositories\Interfaces;

use App\DTO\UserDto;
use App\Models\User;

interface UserRepositoryInterface
{
    public function store(array $data): User;
    public function findByEmail(string $email): ?User;
    public function update($id, $data);
    public function delete($id);
}
