@extends('layouts.app')

@section('title', 'List Data CashBack')

@push('style')
    <!-- CSS Libraries -->
    <link rel="stylesheet"
        href="{{ asset('library/selectric/public/selectric.css') }}">
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>List Data CashBack</h1>
                {{-- <div class="section-header-button">
                    <a href="{{ route('users.create') }}"
                        class="btn btn-primary">Add New</a>
                </div> --}}
                <div class="section-header-button">
                    {{-- <form action="{{ route('hasilupload.export') }}" method="GET">
                        <input type="hidden" name="search" value="{{ request('search') }}">
                        <button type="submit" class="btn btn-success">Export to Excel</button>
                    </form> --}}
                    {{-- <a href="{{ route('export.uploadbonus', ['idupload' => request('idupload'), 'start_date' => request('start_date'), 'end_date' => request('end_date'), 'search' => request('search')]) }}" class="btn btn-success">
                        Export to Excel
                    </a> --}}
                    <a href="{{ route('hasilcashback.export', [
                            'idupload' => request('idupload'),
                            'start_date' => request('start_date'),
                            'end_date' => request('end_date'),
                            'search' => request('search')
                        ]) }}" class="btn btn-success">
                        Export to Excel
                    </a>
                </div>

                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
                    <div class="breadcrumb-item"><a href="#">Data CashBack</a></div>
                    <div class="breadcrumb-item">Data Upload</div>
                </div>
            </div>
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        @include('layouts.alert')
                    </div>
                </div>
                <h2 class="section-title">Data CashBack</h2>

                <p class="section-lead">
                    The data we display is only 2 months from today <br>
                    You can Filter Data By Date and ID Upload.
                </p>
                <!-- Filter Form -->
                <form method="GET" action="{{ route('hasilcashback.index') }}">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="idupload">ID Upload</label>
                                <input type="text" name="idupload" class="form-control" value="{{ request('idupload') }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="start_date">Start Date</label>
                                <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="end_date">End Date</label>
                                <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary mt-3">Filter</button>
                </form>

                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>All Upload</h4>
                            </div>
                            <div class="card-body">
                                <div class="float-right">
                                    <form method="GET" action="{{ route('hasilcashback.index') }}" >
                                        <div class="input-group">
                                            <input type="text" class="form-control" placeholder="Search" name="search" value="{{ request('search') }}">
                                            <div class="input-group-append">
                                                <button class="btn btn-primary"><i class="fas fa-search"></i></button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <button class="btn btn-success mt-2" style="margin-left: 10px;">Proses All Pending</button>


                                <div class="clearfix mb-3"></div>

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
                                        <th>Created At</th>
                                        <th>Action</th>
                                    </tr>
                                    @foreach($uploadcashbacks as $cashback)
                                    <tr>
                                        <td>{{ $cashback->id }}</td>
                                        <td>{{ $cashback->idupload }}</td>
                                        <td>{{ $cashback->user ? $cashback->user->name : 'User tidak ditemukan' }}</td>
                                        <td>{{ $cashback->member }}</td>
                                        <td>{{ number_format($cashback->total, 0, ',', '.') }}</td>
                                        {{-- <td>{{ $bonus->status }}</td> --}}
                                        <td>
                                            @if ($cashback->status == 1)
                                                <div class="badge badge-success">Success</div>
                                            @elseif ($cashback->status == 2)
                                                <div class="badge badge-danger">Error</div>
                                            @else
                                                <div class="badge badge-warning">Pending</div>
                                            @endif
                                        </td>
                                        <td>{{ $cashback->created_at }}</td>
                                        <td>
                                            @if (!$cashback->status == 1)
                                                <div class="d-flex justify-content-center">
                                                    <a href='{{ route('hasilcashback.edit', $cashback->id) }}'
                                                        class="btn btn-sm btn-info btn-icon">
                                                        <i class="fas fa-edit"></i>
                                                        Edit
                                                    </a>

                                                    <form action="{{ route('hasilcashback.destroy', $cashback->id) }}"
                                                        method="POST" class="ml-2">
                                                        <input type="hidden" name="_method" value="DELETE" />
                                                        <input type="hidden" name="_token"
                                                            value="{{ csrf_token() }}" />
                                                        <button class="btn btn-sm btn-danger btn-icon confirm-delete">
                                                            <i class="fas fa-times"></i> Delete
                                                        </button>
                                                    </form>
                                                </div>
                                            @endif

                                        </td>
                                    </tr>
                                    @endforeach
                                    </table>
                                </div>
                                <div class="float-right">
                                    {{ $uploadcashbacks->withQueryString()->links() }}
                                </div>
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
    <script src="{{ asset('library/selectric/public/jquery.selectric.min.js') }}"></script>

    <!-- Page Specific JS File -->
    <script src="{{ asset('js/page/features-posts.js') }}"></script>

@endpush
