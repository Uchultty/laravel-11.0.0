<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Surat Jalan</title>
    <style>
        html, body { margin:0; padding:0; }
        .page { position: relative; width:210mm; height:297mm; }
        .bg { position:absolute; left:0; top:0; width:210mm; height:297mm; z-index:0 }
        .overlay { position:absolute; z-index:2; font-family: sans-serif; color:#000 }
        /* Header */
        .logo { top: 12mm; left: 12mm; width:32mm; }
        .company-name { top: 12mm; left: 46mm; font-size:14pt; font-weight:700; }
        .company-address { top: 20mm; left: 46mm; font-size:9pt; }
        .company-contact { top: 28mm; left: 46mm; font-size:9pt; }

        /* Header right - date and surat jalan number */
        .header-right { top: 14mm; left: 130mm; font-size:11pt; text-align:right; }
        .surat-no { top: 30mm; left: 130mm; font-size:12pt; font-weight:700; text-align:right; }

        /* Body mappings */
        .kepada { top: 44mm; left: 18mm; font-size:11pt; }
        .customer { top: 50mm; left: 18mm; font-size:12pt; font-weight:600; }

        .col-nama { top: 78mm; left: 18mm; font-size:11pt; }
        .col-index { top: 92mm; left: 18mm; font-size:10pt; }
        .col-material { top: 106mm; left: 18mm; font-size:10pt; }
        .col-qty { top: 92mm; left: 150mm; font-size:11pt; }
        .col-po { top: 120mm; left: 18mm; font-size:10pt; }

        /* Footer signatures */
        .footer-left { top: 230mm; left: 18mm; width:80mm; font-size:11pt; }
        .footer-right { top: 230mm; left: 120mm; width:80mm; font-size:11pt; text-align:center; }
    </style>
</head>
<body>
    <div class="page">
        <img class="bg" src="{{ $background }}" alt="template">

        <div class="overlay logo"><img src="{{ $logo }}" style="width:32mm;"></div>
        <div class="overlay company-name">PT. METAL AMANAH BARU</div>
        <div class="overlay company-address">Villa Mutiara Indah Gading 3<br>Taman Kebalen Blok E1 No. 48, Babelan - Bekasi</div>
        <div class="overlay company-contact">Telp: 0813-9870-0989 &nbsp;|&nbsp; Email: metalamanahbaru@yahoo.com</div>

        <div class="overlay header-right">Bekasi, {{ $tanggal_kirim }}</div>
        <div class="overlay surat-no">SURAT JALAN NO<br>{{ $nomor_surat_jalan }}</div>

        <div class="overlay kepada">Kepada Yth</div>
        <div class="overlay customer">{{ $customer }}</div>

        <div class="overlay col-nama">Nama Barang: {{ $product_name }}</div>
        <div class="overlay col-index">INDEX : {{ $product_index }}</div>
        <div class="overlay col-material">MATERIAL : {{ $material_name }}</div>
        <div class="overlay col-qty">Qty: {{ $quantity }}</div>
        <div class="overlay col-po">NO. PO: {{ $no_po }}</div>

        <div class="overlay footer-left">
            Tanda Terima
            <div style="margin-top:18mm;">__________________________</div>
        </div>

        <div class="overlay footer-right">
            Hormat Kami
            <div style="margin-top:18mm;">__________________________</div>
        </div>
    </div>
</body>
</html>