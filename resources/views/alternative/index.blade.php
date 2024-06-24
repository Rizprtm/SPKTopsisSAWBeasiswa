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
                                                <input type="hidden" name="periode_id" value="{{ $periode_id }}">
                                                <td>{{ $dokumen ? $dokumen->dokumen : 'N/A' }}</td>
                                                <td>
                                                    <button type="button" class="btn btn-primary btn-sm"
                                                        data-toggle="modal" data-target="#editStatusModal"
                                                        data-alternative-id="{{ $alternative->alternative_id }}"
                                                        data-status="{{ $score->status }}">
                                                        Edit
                                                    </button>
                                                    <form
                                                        action="{{ route('alternatives.destroy', ['alternative' => $alternative->alternative_id]) }}"
                                                        method="POST" style="display: inline;">
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
    <div class="modal fade" id="editStatusModal" tabindex="-1" role="dialog" aria-labelledby="editStatusModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editStatusModalLabel">Edit Status</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="editForm" method="POST"
                        action="{{ route('alternatives.updateMultiple', ['alternative' => $alternative->alternative_id, 'periode_id' => $periode_id]) }}">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="alternative_id" id="alternative_id">
                        <input type="hidden" name="periode_id" value="{{ $periode_id }}">

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
        $(document).ready(function() {
            $('#editStatusModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var alternativeId = button.data('alternative-id');
                var status = button.data('status');

                var modal = $(this);
                modal.find('#alternative_id').val(alternativeId);
                modal.find('#status').val(status);
            });

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
