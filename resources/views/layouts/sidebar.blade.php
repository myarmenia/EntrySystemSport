<aside id="sidebar" class="sidebar">
    @php
    $user = Auth::user();
    @endphp

    <ul class="sidebar-nav" id="sidebar-nav">
        {{-- <li class="nav-heading">Pages</li> --}}
        @if ($user->hasRole(['super_admin', 'client_admin', 'client_admin_rfID', 'client_sport']))
        <li class="nav-item">
            <a class="nav-link {{ Route::is(['entry-codes-list', 'entry-codes-create', 'entry-codes-edit']) ? '' : 'collapsed' }}"
                href="{{ route('entry-codes-list') }} ">
                <i class="bi bi-person"></i>
                <span>Նույնականացման կոդեր</span>
            </a>
        </li>
        @endif
        @if (!$user->hasRole(['manager', 'trainer']))
        <li class="nav-item">
            <a class="nav-link {{ Route::is(['users.index', 'users.create', 'users.edit']) ? '' : 'collapsed' }}"
                href=" {{ route('users.index') }}">
                <i class="bi bi-person"></i>
                <span>Օգտատերեր</span>
            </a>
        </li>
        @endif
        @if ($user->hasRole('super_admin'))
        <li class="nav-item">
            <a class="nav-link {{ Route::is(['roles.index', 'roles.create', 'roles.edit']) ? '' : 'collapsed' }}"
                href="{{ route('roles.index') }}">
                <i class="bi bi-person"></i>
                <span>Դերեր</span>
            </a>
        </li>
        @endif
        @if ($user->hasRole(['client_admin', 'client_admin_rfID', 'manager', 'client_sport','trainer']))
        <li class="nav-item">
            <a class="nav-link {{ Route::is(['schedule.list', 'schedule.create', 'schedule.edit']) ? '' : 'collapsed' }}"
                href="{{ route('schedule.list') }}">
                <i class="bi bi-calendar-event"></i>
                <span>
                    @if ($user->hasAnyRole(['client_admin', 'client_admin_rfID', 'manager']))
                    Հերթափոխեր
                    @elseif ($user->hasAnyRole(['client_sport', 'trainer']))
                    Ժամային գրաֆիկ
                    @endif
                </span>
            </a>
        </li>
        @endif
        @if ($user->hasRole(['client_sport']))
        <li class="nav-item">
            <a class="nav-link {{ Route::is(['package.list', 'package.create', 'package.edit']) ? '' : 'collapsed' }}"
                href="{{ route('package.list') }}">
                <i class="bi bi-person"></i>
                <span>Փաթեթներ</span>
            </a>
        </li>
        @endif
        @if ($user->hasRole(['client_sport']))
        <li class="nav-item">
            <a class="nav-link {{ Route::is('discounts.*') ? '' : 'collapsed' }}" href="{{ route('discounts.index') }}">
                <i class="bi bi-percent"></i>
                <span>Զեղչեր</span>
            </a>
        </li>
        @endif

        @if ($user->hasRole(['client_admin', 'client_admin_rfID', 'client_sport', 'manager']))
        <li class="nav-item">
            <a class="nav-link {{ Route::is(['department.list', 'department.create', 'department.edit', 'department.*']) ? '' : 'collapsed' }}"
                href="{{ route('department.list') }}">
                <i class="bi bi-person"></i>
                <span>Ստորաբաժանումներ</span>
            </a>
        </li>
        @endif

        @if ($user->hasRole(['client_admin', 'client_admin_rfID', 'client_sport', 'manager', 'trainer']))
        <li class="nav-item">
            <a class="nav-link {{ Route::is('visitors.*') ? '' : 'collapsed' }}" href="{{ route('visitors.list') }}">
                <i class="bi bi-person"></i>
                <span>Այցելուներ</span>
            </a>
        </li>

        @endif
        @if ($user->hasRole(['trainer']))
         <li class="nav-item">
            <a class="nav-link {{ Route::is('trainer-schedule-calendar') ? '' : 'collapsed' }}" href="{{ route('trainer-schedule-calendar',auth()->id()) }}">
                <i class="bi bi-clock"></i>
                <span>Զբաղվածություն</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ Route::is('recommendation.*') ? '' : 'collapsed' }}" href="{{ route('recommendation.list') }}">
                <i class="bi bi-journal-text"></i>
                <span>Մարզչի խորհուրդներ</span>
            </a>
        </li>
        @endif
        @if ($user->hasRole(['client_admin', 'client_admin_rfID', 'client_sport', 'manager']))
        <li class="nav-item">
            <a class="nav-link {{ Route::is(['reportFilter.list']) ? '' : 'collapsed' }}"
                href="{{ route('reportFilter.list') }}">
                <i class="bi bi-person"></i>
                <span>Հաշվետվություն ֆիլտրացիա</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ Route::is(['report-enter-exit.list']) ? '' : 'collapsed' }}"
                href="{{ route('report-enter-exit.list') }}">
                <i class="bi bi-person"></i>
                <span>Հաշվետվություն ըստ մուտքի և ելքի</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ Route::is(['reportAllMonth.list']) ? '' : 'collapsed' }}"
                href="{{ route('reportAllMonth.list') }}">
                <i class="bi bi-person"></i>
                <span>Հաշվետվություն ըստ տարիների և ամիսների</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ Route::is(['supervisedStaff']) ? '' : 'collapsed' }}"
                href="{{ route('supervisedStaff') }}">
                <i class="bi bi-person"></i>
                <span>Վերահսկվող անձնակազմի հաշվետվություն</span>
            </a>
        </li>
        @endif

    </ul>

</aside><!-- End Sidebar-->
<!-- ======= Sidebar ======= -->
