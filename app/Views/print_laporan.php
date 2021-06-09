<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Laporan</title>
    <style>
        body {
            font-family: Times;
        }

        .table-border {
            border-collapse: collapse;

        }

        .table-border th {
            border: 1px solid black;
            padding: 2px 5px;
            text-align: center;
            font-family: Times;
            font-size: 10pt;
        }

        .table-border td {
            border: 1px solid black;
            text-align: center;
            padding: 2px 5px;
            line-height: 1.5;
            /*padding: 2px;*/
            font-family: Times;
            font-size: 10pt;
        }
    </style>
</head>

<body>
    <h2><?= $title ?></h2>
    <table class="table-border" style="width:100%">
        <tr>
            <th style="width:2%">No</th>
            <th style="width:20%">Nama</th>
            <th>Alamat</th>
            <th style="width:12%">No Telepon / HP</th>
            <th style="width:12%">Jumlah Anggota</th>
            <th style="width:12%">Tanggal Masuk</th>
            <th style="width:12%">Tanggal Keluar</th>
        </tr>
        <?php
        $no = 1;
        foreach ($_bookings as $booking) : ?>
            <tr>
                <td><?= $no ?></td>
                <td style="text-align: left;"><?= $booking['booking_nama'] ?></td>
                <td style="text-align: left;"><?= $booking['booking_alamat'] ?></td>
                <td><?= $booking['booking_no_telp'] ?></td>
                <td><?= $booking['booking_jml_anggota'] ?></td>
                <td><?= tgl_indo(date("Y-m-d", strtotime($booking['booking_tgl_masuk']))) ?></td>
                <td><?= tgl_indo(date("Y-m-d", strtotime($booking['booking_tgl_keluar']))) ?></td>
            </tr>
        <?php
            $no++;
        endforeach ?>
    </table>
</body>

</html>