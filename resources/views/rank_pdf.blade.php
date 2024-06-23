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
    <p>Admin Jurusan: {{ $co_admin->nama }}</p>
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
</body>

</html>
