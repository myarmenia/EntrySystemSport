<?php

namespace App\Services;

use App\DTO\UserDto;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
class UserService
{

    protected $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }
    public function createUser(array $data)
    {
        $data['password'] = Hash::make($data['password']);

        return $this->userRepository->store($data);
    }

    public function getUserByEmail(string $email)
    {
        // Используем репозиторий для поиска пользователя по email
        return $this->userRepository->findByEmail($email);
    }
    public function updateUser($id,  $data){


        if(!empty($data['password'])){
            $data['password'] = Hash::make($data['password']);
        }else{

            $data = Arr::except($data,['password','confirm-password']);
        }


        return $this->userRepository->update( $id, $data);

    }

    public function deleteUser($id){
        
        return  $this->userRepository->delete($id);
    }
}
