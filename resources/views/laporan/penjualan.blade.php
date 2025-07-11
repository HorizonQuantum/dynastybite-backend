<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Penjualan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 30px;
        }

        .header {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }

        .header img {
            width: 80px;
            margin-right: 20px;
        }

        .header-text {
            font-size: 14px;
        }

        .header-text h1 {
            font-size: 20px;
            margin: 0;
        }

        .header-text p {
            margin: 2px 0;
        }

        .section-title {
            margin-top: 40px;
            font-size: 14px;
            font-weight: bold;
        }

        @media print {
            .no-print {
                display: none;
            }
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            table-layout: fixed;
        }

        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
            word-wrap: break-word;
            font-size: 12px;
        }

        th {
            background-color: #f0f0f0;
        }

        .print-button {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

    <div class="header">
        <img src="{{ asset('images/logo.png') }}" alt="Logo">
        <div class="header-text">
            <h1>Laporan Penjualan - Dynasty Bite</h1>
            <p>Email: dynasty.bite.official@gmail.com</p>
            <p>No. Telepon: 0855-2497-6693</p>
            <p>Alamat: Jl. Desa Gombang, Kec. Plumbon, Kab. Cirebon, Jawa Barat 45155</p>
        </div>
    </div>

    <!-- <button onclick="window.print()" class="no-print print-button">🖨️ Cetak</button> -->

    @foreach([
        ['data' => $periode1, 'label' => 'Order Periode 1'],
        ['data' => $periode2, 'label' => 'Order Periode 2'],
        ['data' => $periode3, 'label' => 'Order Periode 3'],
        ['data' => $periodeco, 'label' => 'Custom Order'],
    ] as $section)
        @if($section['data']->isNotEmpty())
            <div class="section-title">{{ $section['label'] }}</div>
            <table>
                <thead>
                    <tr>
                        <th style="width: 5%;">No</th>
                        <th style="width: 15%;">Nama</th>
                        <th style="width: 15%;">Tipe Order</th>
                        <th style="width: 15%;">Status</th>
                        <th style="width: 20%;">Nominal Belanja</th>
                        <th style="width: 20%;">Tanggal Pesan</th>
                        <th style="width: 20%;">Tanggal Pengiriman</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($section['data'] as $index => $order)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $order->user->username }}</td>
                            <td>{{ $order->typeOrder->name }}</td>
                            <td>{{ $order->status->name }}</td>
                            <td>{{ 'Rp ' . number_format($order->total_price, 0, ',', '.') }},00</td>
                            <td>{{ \Carbon\Carbon::parse($order->created_at)->translatedFormat('l, d F Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($order->delivery_date)->translatedFormat('l, d F Y') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    @endforeach

</body>
<script>
    window.onload = function () {
        window.print();
    };
</script>

</html>
