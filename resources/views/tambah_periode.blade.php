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
                    <div class="col-lg-3">
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
                                @if (session('success'))
                                    <div class="alert alert-success">
                                        {{ session('success') }}
                                    </div>
                                @endif
                                <form id="add" action="{{ url('periode_beasiswa/tambah/store') }}" method="POST"
                                    enctype="multipart/form-data">
                                    @csrf

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">

                                                <label for="name">Pendaftaran Dimulai</label>
                                                <div class="input-group">
                                                    <input id="tanggal_buka" type="date" class="form-control"
                                                        value="" name="tanggal_buka">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="NIM">Pendaftaran Ditutup</label>
                                                <div class="input-group">
                                                    <input id="" type="date" class="form-control" value=""
                                                        name="tanggal_tutup">
                                                </div>
                                            </div>
                                            <div class="mb-6">
                                                <label for="formFile" class="form-label">Pengumuman</label>
                                                <input class="form-control" name ="periode_file" type="file"
                                                    id="periode_file">
                                            </div>

                                            <div class="form-group">
                                                <label for="inputState">Status</label>
                                                <select id="inputState"name="periode_status" class="form-control">
                                                    <option value="1" selected>Aktif</option>
                                                    <option value="0">Tidak Aktif</option>
                                                </select>
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


@endsection
