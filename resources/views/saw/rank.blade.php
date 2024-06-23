@extends('template.index')

@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Rank</h1>
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
                                <a href="{{ url('rank/pdf', ['periode_id' => $periode_id]) }}"
                                    class="btn btn-secondary buttons-excel buttons-html5" tabindex="0"
                                    aria-controls="example1" type="button"><span>Excel</span></a>
                                <table id="mytable" class="display nowrap table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>User ID</th>
                                            <th>Nama</th>
                                            <th>Jurusan</th>
                                            <th>Prodi</th>

                                            <th>Nilai</th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $rank = 1; @endphp
                                        @foreach ($selectedAlternatives as $prodi => $group)
                                            @foreach ($group as $alternative_id => $preference)
                                                @php
                                                    $alternative = $alternatives->firstWhere('id', $alternative_id);
                                                @endphp
                                                @if ($alternative)
                                                    <tr>
                                                        <td>{{ $rank++ }}</td>
                                                        <td>{{ $alternative->userId }}</td>
                                                        <td>{{ $alternative->mahasiswa->nama ?? 'N/A' }}</td>
                                                        <td>{{ $alternative->mahasiswa->jurusan ?? 'N/A' }}</td>
                                                        <td>{{ $alternative->mahasiswa->prodi ?? 'N/A' }}</td>


                                                        <td>{{ number_format($preference, 4) }}</td>
                                                    </tr>
                                                @endif
                                            @endforeach
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
    </script>
@endsection
