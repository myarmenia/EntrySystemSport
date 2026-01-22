<?php

namespace App\Providers;

use App\Helpers\MyHelper;
use App\Interfaces\AbsenceInterface;
use App\Interfaces\AttendanceSheetInterface;
use App\Interfaces\AttendanceSheetTimeInterface;
use App\Interfaces\CheckEntryCodeInterface;
use App\Interfaces\ClientIdFromTurnstileInterface;
use App\Interfaces\CreateEntryCodeInterface;
use App\Interfaces\DepartmentInterface;
use App\Repositories\AbsenceRepository;
use App\Repositories\AttendanceSheetRepository;
use App\Repositories\AttendanceSheetTimeRepository;
use App\Repositories\DepartmentRepository;
use App\Repositories\DiscountRepositoryInterface;
use App\Repositories\Interfaces\DiscountRepository;
use App\Repositories\EntryCodeRepository;
use App\Repositories\Interfaces\PackageRepositoryInterface;
use App\Repositories\Interfaces\PersonRepositoryInterface;
use App\Repositories\Interfaces\ScheduleDetailsInterface;
use App\Repositories\Interfaces\ScheduleNameInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\PackageRepository;
use App\Repositories\PersonRepository;
use App\Repositories\ScheduleDetailsRepository;
use App\Repositories\ScheduleNameRepository;
use App\Repositories\TurnstileRepository;
use App\Repositories\UserRepository;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Carbon\Carbon;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(CreateEntryCodeInterface::class, EntryCodeRepository::class);
        $this->app->bind(ClientIdFromTurnstileInterface::class, TurnstileRepository::class);
        $this->app->bind(CheckEntryCodeInterface::class, TurnstileRepository::class);
        $this->app->bind(AttendanceSheetInterface::class, AttendanceSheetRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(PackageRepositoryInterface::class, PackageRepository::class);

        $this->app->bind(PersonRepositoryInterface::class, PersonRepository::class);
        $this->app->bind(ScheduleNameInterface::class, ScheduleNameRepository::class);
        $this->app->bind(DepartmentInterface::class, DepartmentRepository::class);
        $this->app->bind(ScheduleDetailsInterface::class, ScheduleDetailsRepository::class);
        $this->app->bind(AbsenceInterface::class, AbsenceRepository::class);
        $this->app->bind(AttendanceSheetTimeInterface::class, AttendanceSheetTimeRepository::class);
        $this->app->bind(DiscountRepositoryInterface::class, DiscountRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();
        Carbon::setLocale('hy');
    }
}
