<div class="modal fade" id="trainerPerson" tabindex="-1" aria-hidden="true">

    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <form id="trainerPersonsForm">
                @csrf
                <input type="hidden" name="recommendation_id" id="recommendation_id">
                <div class="modal-header">
                    <h5 class="modal-title">Մարզչի խորհուրդներ</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                {{-- <div class="modal-body">
                    <div class="mb-3 text-center">
                        <label for="user_ids" class="form-label">Ընտրեք մարզիկներին</label>
                        <select name="user_ids[]" id="user_ids" class="form-select" multiple>
                            <option value="all">Ընտրել բոլորը</option>
                            @foreach($booking as $user)

                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="message_cont text-center"></div>
                </div> --}}
                <div class="mb-2">
        <label class="form-check">
            <input
                type="checkbox"
                class="form-check-input"
                id="select_all"
            >
            <span class="form-check-label fw-bold">
                Ընտրել բոլորը
            </span>
        </label>
    </div>

    <hr>

    <div id="users_list">
        @foreach($booking as $user)
            <label class="form-check d-block">
                <input
                    type="checkbox"
                    class="form-check-input user-checkbox"
                    name="user_ids[]"
                    value="{{ $user->id }}"
                >
                <span class="form-check-label">
                    {{ $user->name }} {{ $user->surname }}
                </span>
            </label>
        @endforeach
    </div>

    <button type="submit" class="btn btn-primary w-100 mt-3">
        Հաստատել
    </button>
</form>

                {{-- <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Չեղարկել</button>
                    <button type="submit" class="btn btn-primary">Հաստատել</button>
                </div>
            </form> --}}
        </div>
    </div>
</div>
