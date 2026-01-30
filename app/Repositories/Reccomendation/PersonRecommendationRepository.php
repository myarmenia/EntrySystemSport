<?php

namespace App\Repositories\Reccomendation;

use App\Models\Recommendation;
use App\Domain\Exceptions\RepositoryException;
use App\Mail\RecommendationMail;
use App\Models\Person;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Mail;

class PersonRecommendationRepository
{

    public function syncPersons(int $recommendationId, array $userIds): void
    {

        try {
            $recommendation = Recommendation::findOrFail($recommendationId);


            $before = $recommendation->persons()->pluck('people.id')->toArray();

            $recommendation->persons()->syncWithoutDetaching($userIds);

            $after = $recommendation->persons()->pluck('people.id')->toArray();

            $added = array_diff($after, $before);


            if (empty($added)) {
                throw RepositoryException::alreadyExists();
            }

            $people = Person::whereIn('id', $added)->get();

            foreach ($people as $person) {
                Mail::to($person->email)
                    ->queue(
                        (new RecommendationMail($recommendation, $person))
                            ->onQueue('emails')
                    );
            }
        } catch (RepositoryException $e) {
            throw $e;
        } catch (ModelNotFoundException $e) {
            throw RepositoryException::recommendationNotFound($e);
        } catch (\Throwable $e) {
            dd($e);
            throw RepositoryException::syncFailed($e);
        }
    }
}
