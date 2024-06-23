@extends('template.index')

@section('content')
    <div class="content-wrapper" style="min-height: 912px;">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        {{-- <h1>Profile</h1> --}}
                    </div>

                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">User Profile</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row justify-content-center">
                    <div class="col-md-6">

                        <div class="card card-primary card-outline">
                            <div class="card-body box-profile">
                                <div class="text-center">
                                    <img class="profile-user-img img-fluid img-circle"
                                        src="../../dist/img/user4-128x128.jpg" alt="User profile picture">
                                </div>
                                <?php $mahasiswa; ?><h3 class="profile-username text-center">{{ $mahasiswa->nama }}</h3>
                                <p class="text-muted text-center">{{ $mahasiswa->userId }}</p>

                                {{-- <a href="#" class="btn btn-primary btn-block"><b>Follow</b></a> --}}
                            </div>

                        </div>


                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">About Me</h3>
                            </div>

                            <div class="card-body">
                                <strong><i class="fas fa-book mr-1"></i> Jurusan</strong>
                                <p class="text-muted">
                                    {{ $mahasiswa->jurusan }}
                                </p>
                                <hr>
                                <strong><i class="fas fa-map-marker-alt mr-1"></i> Program Studi</strong>
                                <p class="text-muted">{{ $mahasiswa->prodi }}</p>
                                <hr>
                                <strong><i class="fas fa-pencil-alt mr-1"></i> Email</strong>
                                <p class="text-muted">
                                    <span class="tag tag-danger">{{ $mahasiswa->email }}</span>

                                </p>
                                <hr>
                                <strong><i class="far fa-file-alt mr-1"></i> Handphone</strong>
                                <p class="text-muted">{{ $mahasiswa->hp }}</p>
                            </div>

                        </div>

                    </div>



                </div>

            </div>
        </section>
    </div>
@endsection
