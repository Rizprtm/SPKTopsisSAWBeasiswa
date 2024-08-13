<!DOCTYPE html>
<html>

<head>
    <title>Ranking Report</title>
    <style>
        body {
            font-family: sans-serif;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
</head>

<body>
    <h1>Ranking Report for Periode {{ $periode_id }}</h1>
    {{-- <p>Admin Jurusan: {{ $co_admin->nama }}</p> --}}
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>NIM</th>
                <th>Nama</th>
                <th>Jurusan</th>
                <th>Prodi</th>
                <th>Nilai</th>
            </tr>
        </thead>
        <tbody>
            @php $rank = 1; @endphp
            @foreach ($selectedAlternatives as $alternativeId => $preferenceValue)
                @php
                    $alternative = $alternatives->firstWhere('id', $alternativeId);
                @endphp
                <tr>
                    <td>{{ $rank++ }}</td>
                    <td>{{ $alternative->userId }}</td>
                    <td>{{ $alternative->mahasiswa->nama }}</td>
                    <td>{{ $alternative->mahasiswa->jurusan }}</td>
                    <td>{{ $alternative->mahasiswa->prodi }}</td>
                    <td>{{ $preferenceValue }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
