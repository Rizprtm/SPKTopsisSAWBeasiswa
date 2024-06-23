@extends('template.index')

@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Alternative & Score</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <!-- Additional header content can be added here -->
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

                                {{-- <a href="{{ route('alternatives.create') }}" class='btn btn-primary'> <span class='fa fa-plus'></span> Add Alternative</a> --}}
                                <br>
                                <table id="mytable" class="display nowrap table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>User ID</th>
                                            <th>Nama</th>
                                            <th>Jurusan</th>
                                            <th>Prodi</th>
                                            @foreach ($criteriaweights as $c)
                                                <th>{{ $c->name }}</th>
                                            @endforeach
                                            <th>Status</th>
                                            <th>Dokumen</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($alternatives as $alternative)
                                            <tr>
                                                <td>{{ $i++ }}</td>
                                                <td>{{ $alternative->userId }}</td>
                                                <td>{{ $alternative->mahasiswa_nama }}</td>
                                                <td>{{ $alternative->mahasiswa_jurusan }}</td>
                                                <td>{{ $alternative->mahasiswa_prodi }}</td>
                                                @foreach ($criteriaweights as $criteria)
                                                    @php
                                                        $score = $scores
                                                            ->where('alternative_id', $alternative->alternative_id)
                                                            ->where('criteria_id', $criteria->id)
                                                            ->first();
                                                    @endphp
                                                    <td>{{ $score ? $score->rating : 'N/A' }}</td>
                                                @endforeach
                                                @php
                                                    $dokumen = $scores
                                                        ->where('alternative_id', $alternative->alternative_id)
                                                        ->first();
                                                @endphp
                                                <td>
                                                    @if ($score->status == 'pending')
                                                        <button class="btn btn-danger ">Pending</button>
                                                    @elseif ($score->status == 'terverifikasi')
                                                        <button class="btn btn-success ">Verifikasi</button>
                                                    @elseif ($score->status == 'ditolak')
                                                        <button class="btn btn-danger ">Ditolak</button>
                                                    @endif
                                                    </span>
                                                </td>
                                                <td>{{ $dokumen ? $dokumen->dokumen : 'N/A' }}</td>
                                                <td>
                                                    <button class="btn btn-primary btn-sm edit-btn" data-toggle="modal"
                                                        data-target="#editModal" data-id="{{ $alternative->id }}"
                                                        data-status="{{ $alternative->status }}">Edit</button>
                                                    <form action="" method="POST" style="display: inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm"
                                                            onclick="return confirm('Apakah Anda yakin ingin menghapus periode ini?')">Hapus</button>
                                                    </form>
                                                </td>
                                            </tr>
                                            <!-- Modal -->
                                            <div class="modal fade" id="editModal{{ $score->id }}" tabindex="-1"
                                                role="dialog" aria-labelledby="editModalLabel{{ $score->id }}"
                                                aria-hidden="true">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="editModalLabel{{ $score->id }}">
                                                                Edit Status</h5>
                                                            <button type="button" class="close" data-dismiss="modal"
                                                                aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <form method="POST"
                                                            action="{{ route('alternativescores.updateStatus', $score->id) }}">
                                                            @csrf
                                                            <div class="modal-body">
                                                                <div class="form-group">
                                                                    <label for="status">Status</label>
                                                                    <select class="form-control" id="status"
                                                                        name="status">
                                                                        <option value="pending"
                                                                            {{ $score->status == 'pending' ? 'selected' : '' }}>
                                                                            Pending</option>
                                                                        <option value="terverifikasi"
                                                                            {{ $score->status == 'terverifikasi' ? 'selected' : '' }}>
                                                                            Terverifikasi</option>
                                                                        <option value="ditolak"
                                                                            {{ $score->status == 'ditolak' ? 'selected' : '' }}>
                                                                            Ditolak</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-dismiss="modal">Close</button>
                                                                <button type="submit" class="btn btn-primary">Save
                                                                    changes</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
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
    <!-- Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Status</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="editForm" method="POST" action="{{ url('') }}">
                        @csrf
                        <!-- <input type="hidden" name="_method" value="PATCH"> --> <!-- Hapus atau komen baris ini -->
                        <input type="hidden" name="score_id" id="score_id">

                        <!-- Dropdown untuk status -->
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select class="form-control" id="status" name="status">
                                <option value="pending">Pending</option>
                                <option value="terverifikasi">Terverifikasi</option>
                                <option value="ditolak">Ditolak</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(function() {
            $('[data-toggle="tooltip"]').tooltip();

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
