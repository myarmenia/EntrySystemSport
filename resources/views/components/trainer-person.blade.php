<div class="modal fade" id="trainerPerson" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document"> <!-- модалка по центру и маленькая -->
        <div class="modal-content rounded-3 shadow-sm"> <!-- скругления и легкая тень -->

            <!-- Ошибки -->
            <div id="trainerPersonsFormErrors" class="alert alert-danger d-none mx-3 mt-3"></div>

            <form id="trainerPersonsForm" class="p-3"> <!-- внутренние отступы -->
                @csrf
                <input type="hidden" name="recommendation_id" id="recommendation_id">

                <!-- Заголовок -->
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">Մարզչի խորհուրդներ</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <!-- Выбор всех -->
                <div class="mb-2">
                    <label class="form-check">
                        <input type="checkbox" class="form-check-input" id="select_all">
                        <span class="form-check-label fw-semibold">
                            Ընտրել բոլորը
                        </span>
                    </label>
                </div>

                <hr class="my-2">

                <!-- Список пользователей -->
                <div id="users_list" class="mb-3" style="max-height: 200px; overflow-y: auto;"> <!-- прокрутка если много -->
                    @foreach ($booking as $user)
                        <label class="form-check d-block small mb-1">
                            <input type="checkbox" class="form-check-input user-checkbox" name="user_ids[]" value="{{ $user->id }}">
                            <span class="form-check-label">
                                {{ $user->name }} {{ $user->surname }}
                            </span>
                        </label>
                    @endforeach
                </div>

                <!-- Кнопка -->
                <button type="submit" class="btn btn-primary w-100 rounded-pill">
                    Հաստատել
                </button>
            </form>
        </div>
    </div>
</div>
