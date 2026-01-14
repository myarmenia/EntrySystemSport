

    <div class="card">
        <div class="card-body">
            <h5 class="card-title">

            </h5>


            <!-- General Form Elements -->


                <div class="row mb-3">

                    <label for="inputText" class="col-sm-3 col-form-label">Գործատուի անուն</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" name="client[name]"
                            placeholder="Գործատուի անուն"
                            value="{{ old('client.name') }}">
                            @error('client.name')
                                <div class="mb-3 row justify-content-start">
                                    <div class="col-sm-9 text-danger fts-14">{{ $message }}
                                    </div>
                                </div>
                            @enderror
                    </div>

                </div>
                <div class="row mb-3">
                    <label for="inputEmail" class="col-sm-3 col-form-label">Գործատուի Էլ.հասցե</label>
                    <div class="col-sm-9">
                        <input type="email" class="form-control" name="client[email]"
                            placeholder="example@gmail.com"
                            value="{{ old('client.email')  }}">
                            @error('client.email')
                                <div class="mb-3 row justify-content-start">
                                    <div class="col-sm-9 text-danger fts-14">{{ $message }}
                                    </div>
                                </div>
                            @enderror
                    </div>
                </div>
                <div class="row mb-3">
                    <label for="inputEmail" class="col-sm-3 col-form-label">Գործատուի հասցեն</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" name="client[address]"
                            placeholder="Գործատուի հասցե"
                            value="{{ old('client.address')  }}">
                            @error('client.address')
                                <div class="mb-3 row justify-content-start">
                                    <div class="col-sm-9 text-danger fts-14">{{ $message }}
                                    </div>
                                </div>
                            @enderror
                    </div>
                </div>
            </form>

        </div>
    </div>
    <div class="row mb-3 d-flex justify-content-end" >

        <div class="col-sm-10 d-flex justify-content-end">
            <button type="submit" class="btn btn-primary">Ստեղծել</button>
        </div>
    </div>


