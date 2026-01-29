<?php

namespace App\Services\Recommendation;

use App\Domain\Exceptions\RepositoryException;
use App\Domain\Exceptions\ServiceException;
use App\Repositories\Reccomendation\PersonRecommendationRepository;
use Illuminate\Support\Facades\DB;

class PersonRecommendationService
{

    public function __construct(protected  PersonRecommendationRepository  $repository) {}

    public function attachPersons(array $data): void
    {

        try {
            DB::transaction(
                fn() =>
                $this->repository->syncPersons(
                    (int) $data['recommendation_id'],
                    $data['user_ids']
                )
            );
        } catch (RepositoryException $e) {
            // важное — НЕ глотаем
            throw $e;
        } catch (\Throwable $e) {
            throw ServiceException::attachPersonsFailed($e);
        }
    }
}
