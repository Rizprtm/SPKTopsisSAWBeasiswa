@extends('template.index')

@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Decision Matriks</h1>
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
                                <div class="row">
                                    @foreach ($periode as $periode)
                                        <div class="col-md-4">
                                            <div class="card">
                                                <div class="icon-wrap px-4 pt-4">
                                                    <div
                                                        class="icon d-flex justify-content-center align-items-center bg-info rounded-circle">
                                                        <span class="ion-logo-ionic text-light"></span>
                                                    </div>
                                                </div>
                                                <div class="card-body pb-5 px-4">
                                                    <h5 class="card-title bold">Periode ke - </h5>
                                                    <p class="card-text">Tanggal Buka:
                                                        {{ $periode->tanggal_buka }}<br>Tanggal Tutup:
                                                        {{ $periode->tanggal_tutup }}</p>
                                                    <a href="{{ $periode->periode_id }}/decision"
                                                        class="btn btn-info">Analisis</a>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

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
