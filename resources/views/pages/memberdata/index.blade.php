@extends('layouts.app')

@section('title', 'Member')

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
                <h1>Member</h1>
            </div>

            <div class="row">
                <div class="col-12">
                    @include('layouts.alert')
                </div>
                <div class="col-lg-12 col-md-12 col-12 col-sm-12">
                    <form action="" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label>Member</label>
                            <input type="text"
                                class="form-control" name="member">
                        </div>
                        <div class="section-header-button">
                            <button type="submit"
                                class="btn btn-primary" >Cari</button>
                        </div>
                     </form>
                </div>

            </div>

            <div class="clearfix mb-3"></div>
            <div class="section-body">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="invoice-title">
                           <!-- Menampilkan Hasil Pencarian -->

                            @if (!empty($member))
                                @if ($member['success'])
                                    <h2>User: {{ $member['user'] }}</h2>
                                    <div class="invoice-number">Balance:{{  number_format($member['balance'], 2, ',', '.') }}</div>
                                @else
                                    <h2> {{ $member['message'] }}</h2>
                                    <div class="invoice-number">Balance: 0</div>
                                @endif
                            @endif

                        </div>
                    </div>
                </div>
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>Riwayat Deposit Terakhir</h4>
                            </div>
                            <div class="card-body">
                                {{-- @php
                                    dd($latesdepos);
                                @endphp --}}
                                @if (!empty($latesdepos))
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Tanggal</th>
                                                <th>Jumlah</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($latesdepos as $latesdepo)
                                                <tr>
                                                    <td>{{ $latesdepo['date'] }}</td>
                                                    <td>{{ number_format($latesdepo['amount'], 0, ',', '.') }}</td>
                                                    <td>{{ $latesdepo['status'] }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                @else
                                    <p>Riwayat deposit terakhir tidak ditemukan.</p>
                                @endif
                            </div>
                        </div>

                    </div>
                </div>
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>Riwayat Deposit</h4>
                            </div>
                            <div class="card-body">

                                @if (!empty($history))

                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Tanggal</th>
                                                    <th>Jumlah</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($history as $deposit)
                                                    <tr>
                                                        <td>{{ $deposit['date'] }}</td>
                                                        <td>{{ number_format($deposit['amount'], 0, ',', '.') }}</td>
                                                        <td>{{ $deposit['status'] }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>

                                    </table>
                                @else
                                    <p>Riwayat deposit tidak ditemukan.</p>
                                @endif
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

@endpush

