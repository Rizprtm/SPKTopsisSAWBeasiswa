@extends('template.index')

@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Periode Beasiswa</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">

                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </div>
        <!-- /.content-header -->

        <!-- Main content -->
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                @if ($message = Session::get('success'))
                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                        {{ $message }}
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                @endif
                                @if (Auth::check() && (Auth::user()->role == 'admin' || Auth::user()->role == 'co_admin'))
                                    <a href="{{ url('periode_beasiswa/tambah') }}" class="btn btn-primary"> <span
                                            class="fa fa-plus"></span> Tambah
                                        Periode</a>
                                @endif
                                <br>
                                <table id="mytable" class="display nowrap table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Tanggal Buka</th>
                                            <th>Pendaftar</th>
                                            <th>Status</th>
                                            <th>File Pengumuman</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($periodeData as $dataperiode)
                                            <tr>
                                                <td>{{ $dataperiode->periode_id }}</td>
                                                <td>Pendaftaran dimulai : {{ $dataperiode->tanggal_buka }} <br>
                                                    Pendaftaran ditutup : {{ $dataperiode->tanggal_tutup }} </td>
                                                <td> {{ $dataperiode->jumlah_mahasiswa }} </td>
                                                <td class="text-center">
                                                    @if ($dataperiode->periode_status == 1)
                                                        <button class="btn btn-success">Aktif</button>
                                                    @else
                                                        <button class="btn btn-danger">Tidak Aktif</button>
                                                    @endif
                                                </td>
                                                <td> <a href="{{ url('periode_beasiswa/view', ['pdfPath' => $dataperiode->periode_file]) }}"
                                                        class="btn btn-primary btn-view-pdf" target="_blank">Lihat
                                                        Pengumuman</a>

                                                </td>
                                                <td>
                                                    @if (Auth::check())
                                                        @if (Auth::user()->role == 'mahasiswa')
                                                            @if ($dataperiode->periode_status == 0)
                                                                <button class="btn btn-danger disabled">Tutup</button>
                                                            @else
                                                                <a href="{{ route('alternatives.create', ['periode_id' => $dataperiode->periode_id]) }}"
                                                                    class="btn btn-primary">Daftar</a>
                                                            @endif
                                                        @elseif(Auth::check() && (Auth::user()->role == 'admin' || Auth::user()->role == 'co_admin'))
                                                            <a href="periode_beasiswa/{{ $dataperiode->periode_id }}/edit"
                                                                class="btn btn-primary btn-sm">Edit</a>
                                                            <form
                                                                action="{{ url('periode_beasiswa/delete', $dataperiode->periode_id) }}"
                                                                method="POST" style="display: inline;">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-danger btn-sm"
                                                                    onclick="return confirm('Apakah Anda yakin ingin menghapus periode ini?')">Hapus</button>
                                                            </form>
                                                        @endif
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- /.card -->

                    </div>
                    <!-- /.col-md-6 -->

                </div>
                <!-- /.row -->
            </div>
            <!-- /.container-fluid -->
        </div>
        <!-- /.content -->
    </div>
    @include('modal-pdf')

    <!-- /.content-wrapper -->
@endsection

@section('script')
    <script>
        $(function() {
            $('[data-toggle="tooltip"]').tooltip()

            $('#mytable').DataTable({
                "paging": true,
                "lengthChange": false,
                "searching": false,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": true,
            });
        });

        $(document).ready(function() {
            $('.btn-view-pdf').click(function() {
                var pdfUrl = $(this).data('pdf');
                $('#pdfViewer').attr('src', pdfUrl);
                $('#pdfModal').modal('show');
            });
        });
    </script>
@endsection
