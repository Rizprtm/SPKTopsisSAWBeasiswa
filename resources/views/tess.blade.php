@extends('template.index')

@section('content')
    @include('mahasiswa.successmodal')
    @include('mahasiswa.errormodal')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        {{-- <h1 class="m-0">Add new alternative</h1> --}}
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
                <div class="row justify-content-center">
                    <div class="col-lg-6">
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">Formulir</h3>
                            </div>
                            <div class="card-body">

                                @if ($errors->any())
                                    <div class="alert alert-danger">
                                        <strong>Whoops!</strong> There were some problems with your input.<br><br>
                                        <ul>
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                <form id="add" action="{{ route('alternatives.store') }}" method="POST"
                                    enctype="multipart/form-data">
                                    @csrf
                                    @if (session('duplicate_entry'))
                                        <div class="alert alert-warning">
                                            {{ session('duplicate_entry') }}
                                        </div>
                                    @endif
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">

                                                <label for="name">Pendaftaran Dimulai</label>
                                                <div class="input-group">
                                                    <input id="" type="text" class="form-control" value=""
                                                        name="">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="NIM">Pendaftaran Ditutup</label>
                                                <div class="input-group">
                                                    <input id="" type="text" class="form-control" value=""
                                                        name="">
                                                </div>
                                            </div>
                                            <div class="mb-6">
                                                <label for="formFile" class="form-label">Dokumen</label>
                                                <input class="form-control" name ="dokumen" type="file" id="dokumen">
                                            </div>

                                            <div class="form-group">
                                                <label for="prodi">Status</label>
                                                <div class="input-group">
                                                    <input id="" type="text" class="form-control" value=""
                                                        name="">
                                                </div>
                                            </div>

                                        </div>


                                    </div>



                            </div>

                            <button type="submit" class="btn btn-primary">Submit</button>


                            </form>
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

    <!-- /.content-wrapper -->

    <div class="alert alert-danger" id="duplicateAlert" style="display: none;"></div>

    <script>
        function showSuccessModal(message) {
            $('#successModalBody').text(message);
            $('#successModal').modal('show');
        }

        // Function to show a Bootstrap error modal
        function showErrorModal(message) {
            $('#errorModalBody').text(message);
            $('#errorModal').modal('show');
        }

        // Function to show a Bootstrap duplicate alert
        function showDuplicateAlert(message) {
            $('#duplicateAlert').text(message);
            $('#duplicateAlert').show();
            setTimeout(function() {
                $('#duplicateAlert').hide();
            }, 5000); // Hide the alert after 5 seconds
        }

        // Check for session flash messages and show modals
        $(document).ready(function() {
            @if (session('success'))
                showSuccessModal('{{ session('success') }}');
            @elseif (session('error'))
                @if (strpos(session('error'), 'Duplicate Data') !== false)
                    showDuplicateAlert('{{ session('error') }}');
                @else
                    showErrorModal('{{ session('error') }}');
                @endif
            @endif
        });

        // function updateData() {
        //     var formData = $('#add').serialize();

        //     $.ajax({
        //         url: ,
        //         type: "POST",
        //         data: formData,
        //         success: function(response) {
        //             if (response.status === 'success') {
        //                 // Tampilkan pesan sukses
        //                 alert(response.message);
        //                 // Tambahkan kode lain yang diperlukan setelah operasi update berhasil
        //             } else {
        //                 // Tampilkan pesan error
        //                 alert(response.message);
        //             }
        //         },
        //         error: function(xhr, status, error) {
        //             // Tangani kesalahan yang mungkin terjadi
        //             alert('Terjadi kesalahan saat melakukan permintaan AJAX.');
        //         }
        //     });

        // }
    </script>
@endsection
