@extends('layouts.app')

@section('page-script')
<script src="https://cdn.ckeditor.com/ckeditor5/40.2.0/classic/ckeditor.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const editorElement = document.querySelector('#description');

    if (editorElement) {
        ClassicEditor
            .create(editorElement, {
                toolbar: [
                    'heading', '|',
                    'bold', 'italic', 'underline', 'link',
                    'bulletedList', 'numberedList', '|',
                    'blockQuote', 'insertTable', '|',
                    'undo', 'redo'
                ],
                language: 'hy'
            })
            .catch(error => {
                console.error(error);
            });
    }
});
</script>


@endsection
@section('content')
<main id="main" class="main">
    <section class="section">
        <form action="{{ route('recommendation.update',$data->id) }}" method="post" >
              @method('put')
            <div class="row">
                <div class="col-lg-6">

                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">
                                <nav>
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="{{ route('recommendation.list') }}">Մարզչի խորհուրդներ</a></li>

                                            <li class="breadcrumb-item active">Ստեղծել </li>
                                        </ol>
                                    </nav>
                                </h5>

                            <!-- General Form Elements -->


                            <div class="row mb-3">

                                <label for="inputText" class="col-sm-3 col-form-label">Անուն</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="name" placeholder="անուն"
                                        value="{{ old('name', $data->name ?? '') }}">
                                    @error('name')
                                    <div class="mb-3 row justify-content-start">
                                        <div class="col-sm-9 text-danger fts-14">{{ $message }}
                                        </div>
                                    </div>
                                    @enderror
                                </div>

                            </div>
                            <div class="row mb-3">

                                <label for="inputText" class="col-sm-3 col-form-label">Նկարագրություն</label>
                                <div class="col-sm-9">
                                    <textarea
                                        name="description"
                                        id="description"
                                        class="form-control"
                                        rows="4"
                                        placeholder="Գրեք մարզչի խորհուրդները..."
                                        style="resize: none;"
                                    >{{ old('description', $data->description ?? '') }}</textarea>

                                    @error('description')
                                    <div class="mb-3 row justify-content-start">
                                        <div class="col-sm-9 text-danger fts-14">{{ $message }}
                                        </div>
                                    </div>
                                    @enderror
                                </div>

                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <div class="row mb-3" id="loginBtn">
                <label class="col-sm-2 col-form-label"></label>
                <div class="col-sm-10">
                    <button type="submit" class="btn btn-primary">Ստեղծել</button>
                </div>
            </div>

            </div>
        </form>
    </section>
</main>
@endsection
