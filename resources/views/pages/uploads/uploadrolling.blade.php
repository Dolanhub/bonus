@extends('layouts.app')

@section('title', 'Upload Rolling')

@push('style')
    <!-- CSS Libraries -->
    <link rel="stylesheet"
        href="{{ asset('library/jqvmap/dist/jqvmap.min.css') }}">
    <link rel="stylesheet"
        href="{{ asset('library/summernote/dist/summernote-bs4.min.css') }}">
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Upload Rolling</h1>
            </div>

            <div class="row">
                <div class="col-12">
                    @include('layouts.alert')
                </div>
                <div class="col-lg-12 col-md-12 col-12 col-sm-12">
                    <form action="" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label>File</label>
                            <input type="file"
                                class="form-control" name="excel_file" accept=".xls" id="file">

                                <!-- Menampilkan pesan error untuk input file excel -->
                                @error('excel_file')
                                    <div class="alert alert-danger mt-2">
                                        {{ $message }}
                                    </div>
                                @enderror
                        </div>
                        <div class="section-header-button">
                            <button type="submit"
                                class="btn btn-primary">Upload Rolling</button>
                        </div>
                     </form>
                </div>


            </div>
            <div class="clearfix mb-3"></div>
            <div class="clearfix mb-3"></div>
            <div class="row">
                <div class="col-12">
                    <div class="table-responsive">
                        {{-- @include('pages.users.table',$users) --}}
                    @if(Session::has('uploadedDataRolling') && Session::get('uploadedDataRolling')->isNotEmpty())
                        <h4>Hasil Upload:</h4>
                        <table class="table-striped table">
                            <tr>
                                <th>ID</th>
                                <th>ID Upload</th>
                                <th>Created At</th>
                                <th>User ID</th>
                                <th>Member</th>
                                <th>Total</th>

                            </tr>
                            @foreach (Session::get('uploadedDataRolling') as $data)
                            <tr>
                                <td>{{ $data->id }}</td>
                                <td>{{ $data->idupload }}</td>
                                <td>{{ $data->created_at }}</td>
                                <td>{{ $data->user ? $data->user->name : 'User tidak ditemukan' }}</td>
                                <td>{{ $data->member }}</td>
                                <td>{{  number_format($data->total, 0, ',', '.') }}</td>

                            </tr>
                        @endforeach
                        </table>
                    @endif
                    </div>
                </div>
            </div>


        </section>
    </div>
@endsection

@push('scripts')
    <!-- JS Libraies -->
    <script src="{{ asset('library/simpleweather/jquery.simpleWeather.min.js') }}"></script>
    <script src="{{ asset('library/chart.js/dist/Chart.min.js') }}"></script>
    <script src="{{ asset('library/jqvmap/dist/jquery.vmap.min.js') }}"></script>
    <script src="{{ asset('library/jqvmap/dist/maps/jquery.vmap.world.js') }}"></script>
    <script src="{{ asset('library/summernote/dist/summernote-bs4.min.js') }}"></script>
    <script src="{{ asset('library/chocolat/dist/js/jquery.chocolat.min.js') }}"></script>

    <!-- Page Specific JS File -->
    <script src="{{ asset('js/page/index-0.js') }}"></script>
@endpush
