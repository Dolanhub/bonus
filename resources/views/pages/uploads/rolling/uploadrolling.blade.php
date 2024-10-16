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
                    <form action="" method="POST" enctype="multipart/form-data" id="uploadForm">
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
                                class="btn btn-primary" id="uploadButton">Upload Rolling</button>
                        </div>
                     </form>
                </div>


            </div>
            <div class="clearfix mb-3"></div>
            <div class="clearfix mb-3"></div>
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>All Upload</h4>
                        </div>
                        <div class="card-body">
                            <div class="float-right">
                                <form method="GET" action="{{ route('uploadscashback.index') }}" >
                                    <div class="input-group">
                                        <input type="text" class="form-control" placeholder="Search" name="search" value="{{ request('search') }}">
                                        <div class="input-group-append">
                                            <button class="btn btn-primary"><i class="fas fa-search"></i></button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="table-responsive">
                                {{-- @include('pages.users.table',$users) --}}
                                <table class="table-striped table">
                                <tr>
                                    <th>ID</th>
                                    <th>ID Upload</th>
                                    <th>User ID</th>
                                    <th>Member</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Response</th>
                                    <th>Message</th>
                                    <th>Created At</th>
                                    <th>Action</th>
                                </tr>
                                @foreach($uploadrolling as $rolling)
                                @php
                                    // Parsing JSON dari responseapi
                                    $response = json_decode($rolling->responseapi, true);
                                    $success = isset($response['success'])
                                                ? ($response['success'] ? 'Berhasil' : 'Gagal')
                                                : 'Data Error';

                                    $messageFromApi = $response['message'] ?? 'N/A';
                                        // Logika Message berdasarkan Status
                                        if ($rolling->status == 3) {
                                            $message = 'unproses';
                                        } elseif (in_array($rolling->status, [1, 2])) {
                                            $message = $messageFromApi;
                                        } else {
                                            $message = 'Status tidak diketahui';
                                        }
                                @endphp
                                <tr>
                                    <td>{{ $rolling->id }}</td>
                                    <td>{{ $rolling->idupload }}</td>
                                    <td>{{ $rolling->user ? $rolling->user->name : 'User tidak ditemukan' }}</td>
                                    <td>{{ $rolling->member }}</td>
                                    <td>{{ number_format($rolling->total, 0, ',', '.') }}</td>
                                    {{-- <td>{{ $bonus->status }}</td> --}}
                                    <td>
                                        @if ($rolling->status == 1)
                                            <div class="badge badge-success">Success</div>
                                        @elseif ($rolling->status == 2)
                                            <div class="badge badge-danger">Error</div>
                                        @elseif ($rolling->status == 0)
                                            <div class="badge badge-warning">Pending</div>
                                        @elseif ($rolling->status == 3)
                                            <div class="badge badge-info">UnUpload</div>
                                        @endif
                                    </td>
                                    <td>{{ $success }}</td> <!-- Tampilkan success -->
                                    <td>{{ $message }}</td> <!-- Tampilkan message sesuai kondisi -->
                                    <td>{{ $rolling->created_at }}</td>
                                    <td>
                                        <div class="d-flex justify-content-center">
                                            <a href='{{ route('uploadsrolling.edit', $rolling->id) }}'
                                                class="btn btn-sm btn-info btn-icon">
                                                <i class="fas fa-edit"></i>
                                                Edit
                                            </a>

                                            <form action="{{ route('uploadsrolling.send', $rolling->id) }}" method="POST" class="ml-2">
                                                @csrf
                                                <button class="btn btn-sm btn-warning btn-icon">
                                                    <i class="fas fa-times"></i> Send
                                                </button>
                                            </form>
                                        </div>

                                    </td>
                                </tr>
                                @endforeach
                                </table>
                            </div>
                            <div class="float-right">
                                {{ $uploadrolling->withQueryString()->links() }}
                            </div>
                        </div>
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
    <script>
        const form = document.getElementById('uploadForm');
        const uploadButton = document.getElementById('uploadButton');

        form.addEventListener('submit', function (e) {
            // Nonaktifkan tombol untuk mencegah klik berulang
            uploadButton.disabled = true;
            uploadButton.innerText = 'Uploading...';
        });
    </script>
@endpush
