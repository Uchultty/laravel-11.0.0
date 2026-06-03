@php
	// Konversi tanggal kirim untuk tampilan surat jalan
	$tanggalKirims = $tanggal_kirim instanceof \Illuminate\Support\Carbon
		? $tanggal_kirim->format('d/m/Y')
		: \Illuminate\Support\Carbon::parse($tanggal_kirim)->format('d/m/Y');
@endphp

@if($pdf_mode)
<!DOCTYPE html>
<html lang="id">
<head>
	<meta charset="utf-8">
	<title>Surat Jalan {{ $nomor_surat_jalan }}</title>
</head>
<body style="font-family: Arial, sans-serif; color: #0f172a; font-size: 12px;">
	<table width="100%" cellspacing="0" cellpadding="0" style="border-collapse: collapse;">
		<tr>
			<td style="text-align: right; padding-bottom: 10px;">Bekasi, {{ $tanggal_kirim instanceof \Illuminate\Support\Carbon ? $tanggal_kirim->format('d/m/Y') : \Illuminate\Support\Carbon::parse($tanggal_kirim)->format('d/m/Y') }}</td>
		</tr>
		<tr>
			<td style="border-bottom: 2px solid #0f4c81; padding-bottom: 10px;">
				<table width="100%" cellspacing="0" cellpadding="0" style="border-collapse: collapse;">
					<tr>
						<td style="width: 180px; vertical-align: top; padding-right: 12px;">
							<img src="{{ $logo_src }}" alt="Logo PT. Metal Amanah Baru" style="width: 160px; height: auto; display: block;">
						</td>
						<td style="vertical-align: top;">
							<div style="font-size: 18px; font-weight: bold;">PT. METAL AMANAH BARU</div>
							<div style="margin-top: 4px; line-height: 1.4; color: #475569;">
								Alamat : Villa Mutiara Indah Gading 3<br>
								Taman Kebalen Blok E1 No. 48, Babelan - Bekasi<br>
								No-Telp : 0813-9870-0989<br>
								Email : metalamanahbaru@yahoo.com
							</div>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td style="padding-top: 12px;">
				<table width="100%" cellspacing="0" cellpadding="0" style="border-collapse: collapse;">
					<tr>
						<td style="width: 150px; font-weight: bold; padding: 4px 0;">Nomor Surat Jalan</td>
						<td style="padding: 4px 0;">{{ $nomor_surat_jalan }}</td>
					</tr>
					<tr>
						<td style="width: 150px; font-weight: bold; padding: 4px 0;">Customer</td>
						<td style="padding: 4px 0;">{{ $customer_name }}</td>
					</tr>
					<tr>
						<td style="width: 150px; font-weight: bold; padding: 4px 0;">No PO</td>
						<td style="padding: 4px 0;">{{ $no_po ?? '-' }}</td>
					</tr>
					<tr>
						<td style="width: 150px; font-weight: bold; padding: 4px 0;">No Gambar</td>
						<td style="padding: 4px 0;">{{ $no_gambar ?? '-' }}</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td style="padding-top: 16px;">
				<div style="font-weight: bold; margin-bottom: 8px;">Rincian Barang</div>
				<table width="100%" cellspacing="0" cellpadding="0" style="border-collapse: collapse; border: 1px solid #dbe3ee;">
					<tr style="background: #0f4c81; color: #ffffff;">
						<th style="padding: 8px; border-right: 1px solid #ffffff; width: 80px;">Jumlah</th>
						<th style="padding: 8px; border-right: 1px solid #ffffff;">Nama Barang</th>
						<th style="padding: 8px; width: 180px;">Keterangan</th>
					</tr>
					@foreach ($items as $item)
						<tr>
							<td style="padding: 8px; border-top: 1px solid #dbe3ee; text-align: center; vertical-align: top;">{{ $item['quantity'] }}</td>
							<td style="padding: 8px; border-top: 1px solid #dbe3ee; vertical-align: top;">
								<div style="font-weight: bold; line-height: 1.35;">{{ $item['nama_barang'] }}</div>
								<div style="margin-top: 4px; font-weight: bold; line-height: 1.35;">{{ $item['material'] }}</div>
							</td>
							<td style="padding: 8px; border-top: 1px solid #dbe3ee; vertical-align: top; text-align: center;">{{ $item['keterangan'] }}</td>
						</tr>
					@endforeach
				</table>
			</td>
		</tr>
		<tr>
			<td style="padding-top: 28px;">
				<table width="100%" cellspacing="0" cellpadding="0" style="border-collapse: collapse;">
					<tr>
						<td style="width: 50%; vertical-align: top; padding-right: 20px;">
							<div style="font-weight: bold;">Tanda Terima</div>
							<div style="height: 60px;"></div>
							<div style="border-top: 1px solid #dbe3ee; width: 85%;"></div>
						</td>
						<td style="width: 50%; vertical-align: top; padding-left: 20px; text-align: left;">
							<div style="font-weight: bold;">Hormat Kami</div>
							<div style="color: #475569; font-weight: bold;">PT. Metal Amanah Baru</div>
							<div style="height: 60px;"></div>
							<div style="border-top: 1px solid #dbe3ee; width: 85%;"></div>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</body>
</html>
@else
<!DOCTYPE html>
<html lang="id">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Surat Jalan {{ $nomor_surat_jalan }}</title>
	<style>
		:root {
			--page-bg: #f5f7fb;
			--paper: #ffffff;
			--ink: #0f172a;
			--muted: #475569;
			--line: #dbe3ee;
			--accent: #0f4c81;
		}

		.preview-shell {
			padding: 20px 24px 24px;
			overflow-x: auto;
		}

		/* Toolbar preview: judul, deskripsi, dan tombol aksi */
		.toolbar {
			display: flex;
			justify-content: space-between;
			align-items: center;
			gap: 16px;
			padding: 16px 24px 0;
			flex-wrap: wrap;
		}

		/* Panel teks di toolbar preview */
		.toolbar-panel {
			display: flex;
			flex-direction: column;
			gap: 4px;
		}

		.toolbar-title {
			margin: 0;
			font-size: 20px;
			font-weight: 900;
			letter-spacing: 0.02em;
			color: #0f172a;
		}

		.toolbar-subtitle {
			margin: 0;
			font-size: 12px;
			color: #475569;
		}

		/* Area tombol export, print, dan kembali */
		.toolbar-actions {
			display: flex;
			gap: 10px;
			flex-wrap: wrap;
			align-items: center;
		}

		.toolbar-actions .btn {
			border-radius: 999px;
			padding: 10px 16px;
			font-size: 13px;
			font-weight: 800;
			transition: transform 0.15s ease, box-shadow 0.15s ease, background-color 0.15s ease;
		}

		.toolbar-actions .btn:hover {
			transform: translateY(-1px);
			box-shadow: 0 8px 18px rgba(15, 23, 42, 0.14);
		}

		.btn-primary {
			background: linear-gradient(180deg, #0f4c81, #0c3f6b);
			color: #fff;
		}

		.btn-secondary {
			background: #fff;
			color: #0f172a;
			border: 1px solid #dbe3ee;
		}

		/* Kertas utama yang dipakai untuk preview dan PDF */
		.paper {
			width: 210mm;
			min-height: 297mm;
			background: #ffffff;
			margin: 0 auto;
			padding: 16mm 15mm 14mm;
			box-shadow: 0 18px 50px rgba(15, 23, 42, 0.12);
			border: 1px solid rgba(219, 227, 238, 0.9);
		}

		/* Header surat jalan: logo, nama perusahaan, dan tanggal */
		.header {
			display: flex;
			flex-direction: column;
			align-items: flex-start;
			gap: 2px;
			position: relative;
			padding-bottom: 16px;
			border-bottom: 2px solid #0f4c81;
		}

		/* Logo PT. Metal Amanah Baru */
		.logo {
			width: 320px;
			flex: 0 0 160px;
		}

		.logo img {
			display: block;
			width: 100%;
			height: auto;
			object-fit: contain;
		}

		/* Blok nama dan alamat perusahaan */
		.company {
			flex: 1;
			text-align: left;
			margin-top: 4px;
		}

		.company-name {
			margin: 0;
			font-size: 22px;
			font-weight: 800;
			letter-spacing: 0.06em;
		}

		.company-text {
			margin: 0;
			color: #475569;
			font-size: 12.8px;
			line-height: 1.35;
		}

		/* Tanggal kanan atas: Bekasi, tanggal */
		.header-topline {
			position: absolute;
			top: 54px;
			right: 15px;
			width: auto;
			display: flex;
			justify-content: flex-end;
			align-items: center;
			font-size: 15px;
			font-weight: 700;
			color: #475569;
			letter-spacing: 0.02em;
			margin: 0;
			padding-right: 10px;
			z-index: 1;
		}

		/* Gaya teks Bekasi agar lebih tebal */
		.header-topline span:first-child {
			font-weight: 900;
		}

		/* Pemisah teks tanggal */
		.header-topline span + span::before {
			content: ', ';
			font-weight: 700;
		}

		/* Informasi nomor surat jalan dan customer */
		.meta-grid {
			display: grid;
			grid-template-columns: 1fr;
			gap: 10px;
			margin-top: 18px;
		}

		.meta-box {
			border: 1px solid #dbe3ee;
			border-radius: 14px;
			padding: 14px 16px;
			background: linear-gradient(180deg, #fff, #fbfdff);
		}

		.meta-row {
			display: grid;
			grid-template-columns: 140px 1fr;
			gap: 10px;
			margin: 6px 0;
			font-size: 14px;
		}

		.meta-label {
			color: #475569;
			font-weight: 700;
		}

		.meta-value {
			font-weight: 700;
		}

		/* Judul bagian rincian barang */
		.document-title {
			text-align: center;
			margin: 18px 0 8px;
		}

		.document-title h2 {
			margin: 0;
			font-size: 26px;
			letter-spacing: 0.18em;
			font-weight: 900;
		}

		/* Tabel detail barang surat jalan */
		.table-wrap {
			margin-top: 18px;
			border: 1px solid #dbe3ee;
			border-radius: 14px;
			overflow: hidden;
			background: #fff;
		}

		table {
			width: 100%;
			border-collapse: collapse;
			font-size: 13.5px;
		}

		thead th {
			background: linear-gradient(180deg, #0f4c81, #0c3f6b);
			color: #fff;
			text-align: center;
			padding: 13px 12px;
			font-size: 13px;
			letter-spacing: 0.04em;
			text-transform: uppercase;
			border-right: 2px solid rgba(255, 255, 255, 0.55);
		}

		thead th:last-child {
			border-right: 0;
		}

		tbody td {
			padding: 12px;
			border-bottom: 2px solid #c2cfdd;
			border-right: 2px solid #c2cfdd;
			vertical-align: top;
			background: #fff;
		}

		tbody td:last-child {
			border-right: 0;
		}

		tbody tr:nth-child(even) td {
			background: #fbfdff;
		}

		tbody tr:last-child td {
			border-bottom: 0;
		}

		tbody tr:hover td {
			background: #f6f9fc;
		}

		.col-no {
			width: 64px;
			text-align: center;
		}

		.col-qty {
			width: 110px;
			text-align: center;
		}

		.col-ket {
			width: 200px;
			text-align: center;
		}

		/* Area tanda tangan penerima dan perusahaan */
		.signature {
			display: grid;
			grid-template-columns: 1fr 1fr;
			gap: 30px;
			margin-top: 36px;
		}

		.signature-box {
			min-height: 150px;
			border-top: 1px solid #dbe3ee;
			padding-top: 12px;
			display: flex;
			flex-direction: column;
			justify-content: flex-start;
			font-size: 14px;
		}

		.signature-box-left .signature-spacer {
			height: 18px;
			margin-top: 4px;
		}

		.signature-box-right .signature-company {
			min-height: 22px;
			margin-top: 2px;
		}

		.signature-title {
			font-weight: 800;
		}

		.signature-company {
			font-weight: 700;
			color: #475569;
		}

		.signature-line {
			margin-top: 92px;
			width: 85%;
			align-self: center;
			border-bottom: 1px solid #cbd5e1;
			height: 1px;
		}

		.signature-name {
			margin-top: 10px;
			font-weight: 700;
			color: #475569;
		}

		/* Catatan kecil di bawah tabel */
		.table-caption {
			margin: 0 0 8px;
			font-size: 12px;
			font-weight: 700;
			color: #475569;
			text-transform: uppercase;
			letter-spacing: 0.08em;
		}

		.footnote {
			margin-top: 14px;
			font-size: 12px;
			color: #475569;
		}

		/* Mode cetak: toolbar disembunyikan dan ukuran kertas disesuaikan */
		@media print {
			body { background: #fff; }
			.toolbar { display: none; }
			.preview-shell { padding: 0; }
			.paper {
				width: auto;
				min-height: auto;
				margin: 0;
				padding: 0;
				border: 0;
				box-shadow: none;
			}
			@page { size: A4 portrait; margin: 12mm; }
		}

		@media (max-width: 720px) {
			.toolbar {
				padding: 12px 16px 0;
			}

			.toolbar-actions {
				width: 100%;
			}

			.toolbar-actions .btn {
				flex: 1 1 0;
				justify-content: center;
			}

			.paper { width: 100%; padding: 20px 16px; }
			.header { flex-direction: column; }
			.meta-grid { grid-template-columns: 1fr; }
			.meta-row { grid-template-columns: 1fr; gap: 4px; }
			.signature { grid-template-columns: 1fr; }
		}

		@if($pdf_mode)
			/* Mode PDF: dibuat sederhana agar mPDF tidak membuat halaman berlebihan */
			body { background: #fff; }
			.toolbar { display: none !important; }
			.preview-shell { padding: 0; }
			.paper {
				width: auto;
				min-height: auto;
				margin: 0;
				padding: 0;
				border: 0;
				box-shadow: none;
			}
			.header,
			.meta-box,
			.table-wrap,
			.signature-box {
				border-radius: 0;
			}
			.signature {
				gap: 18px;
				margin-top: 28px;
			}
			@page { size: A4 portrait; margin: 12mm; }
		@endif
	</style>
</head>
<body>
	@if (! $pdf_mode)
		<div class="toolbar">
			<div class="toolbar-panel">
				<h1 class="toolbar-title">Preview Surat Jalan</h1>
				<p class="toolbar-subtitle">Gunakan tombol di kanan untuk export PDF, print, atau kembali ke detail pengiriman.</p>
			</div>
			<div class="toolbar-actions">
				<a href="{{ route('pengiriman-produk.surat-jalan.pdf', $pengiriman_produk) }}" class="btn btn-primary" target="_blank" rel="noopener">Export PDF</a>
				<button type="button" class="btn btn-secondary" onclick="window.print()">Print</button>
				<a href="{{ route('pengiriman-produk.show', $pengiriman_produk) }}" class="btn btn-secondary">Kembali</a>
			</div>
		</div>
	@endif

	<div class="preview-shell">
		<section class="paper">
			@if($pdf_mode)
				<table width="100%" cellspacing="0" cellpadding="0" style="border-collapse: collapse; font-family: Arial, sans-serif; color: #0f172a;">
					<tr>
						<td style="text-align: right; font-size: 12px; padding-bottom: 10px;">Bekasi, {{ $tanggal_kirim instanceof \Illuminate\Support\Carbon ? $tanggal_kirim->format('d/m/Y') : \Illuminate\Support\Carbon::parse($tanggal_kirim)->format('d/m/Y') }}</td>
					</tr>
					<tr>
						<td style="padding-bottom: 12px; border-bottom: 2px solid #0f4c81;">
							<div style="font-size: 20px; font-weight: bold; letter-spacing: 0.04em;">PT. METAL AMANAH BARU</div>
							<div style="font-size: 11px; line-height: 1.4; margin-top: 4px; color: #475569;">
								Alamat : Villa Mutiara Indah Gading 3<br>
								Taman Kebalen Blok E1 No. 48, Babelan - Bekasi<br>
								No-Telp : 0813-9870-0989<br>
								Email : metalamanahbaru@yahoo.com
							</div>
						</td>
					</tr>
					<tr>
						<td style="padding-top: 12px;">
							<table width="100%" cellspacing="0" cellpadding="0" style="border-collapse: collapse; font-size: 12px;">
								<tr>
									<td style="width: 150px; font-weight: bold; padding: 4px 0;">Nomor Surat Jalan</td>
									<td style="padding: 4px 0;">{{ $nomor_surat_jalan }}</td>
								</tr>
								<tr>
									<td style="width: 150px; font-weight: bold; padding: 4px 0;">Customer</td>
									<td style="padding: 4px 0;">{{ $customer_name }}</td>
								</tr>
								<tr>
									<td style="width: 150px; font-weight: bold; padding: 4px 0;">No PO</td>
									<td style="padding: 4px 0;">{{ $no_po ?? '-' }}</td>
								</tr>
								<tr>
									<td style="width: 150px; font-weight: bold; padding: 4px 0;">No Gambar</td>
									<td style="padding: 4px 0;">{{ $no_gambar ?? '-' }}</td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td style="padding-top: 16px;">
							<div style="font-size: 12px; font-weight: bold; margin-bottom: 8px;">Rincian Barang</div>
							<table width="100%" cellspacing="0" cellpadding="0" style="border-collapse: collapse; border: 1px solid #dbe3ee; font-size: 12px;">
								<thead>
									<tr style="background: #0f4c81; color: #fff;">
										<th style="padding: 8px; border-right: 1px solid #ffffff; width: 80px;">Jumlah</th>
										<th style="padding: 8px; border-right: 1px solid #ffffff;">Nama Barang</th>
										<th style="padding: 8px; width: 180px;">Keterangan</th>
									</tr>
								</thead>
								<tbody>
									@foreach ($items as $item)
										<tr>
											<td style="padding: 8px; border-top: 1px solid #dbe3ee; text-align: center; vertical-align: top;">{{ $item['quantity'] }}</td>
											<td style="padding: 8px; border-top: 1px solid #dbe3ee; vertical-align: top;">
												<div style="font-weight: bold; line-height: 1.35;">{{ $item['nama_barang'] }}</div>
												<div style="margin-top: 4px; font-weight: bold; line-height: 1.35;">{{ $item['material'] }}</div>
											</td>
											<td style="padding: 8px; border-top: 1px solid #dbe3ee; vertical-align: top; text-align: center;">{{ $item['keterangan'] }}</td>
										</tr>
									@endforeach
								</tbody>
							</table>
						</td>
					</tr>
					<tr>
						<td style="padding-top: 28px;">
							<table width="100%" cellspacing="0" cellpadding="0" style="border-collapse: collapse; font-size: 12px;">
								<tr>
									<td style="width: 50%; vertical-align: top; padding-right: 20px;">
										<div style="font-weight: bold;">Tanda Terima</div>
										<div style="height: 70px;"></div>
										<div style="border-top: 1px solid #dbe3ee; width: 85%;"></div>
									</td>
									<td style="width: 50%; vertical-align: top; padding-left: 20px; text-align: left;">
										<div style="font-weight: bold;">Hormat Kami</div>
										<div style="color: #475569; font-weight: bold;">PT. Metal Amanah Baru</div>
										<div style="height: 70px;"></div>
										<div style="border-top: 1px solid #dbe3ee; width: 85%;"></div>
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
			@else
				<!-- Tanggal surat jalan: Bekasi, tanggal -->
				<header class="header">
					<div class="header-topline">
						<span>Bekasi</span>
						<span>{{ \Illuminate\Support\Carbon::now('Asia/Jakarta')->format('d/m/Y') }}</span>
					</div>
					<div class="logo">
						<img src="{{ $logo_src }}" alt="Logo PT. Metal Amanah Baru">
					</div>
					<div class="company">
						<h1 class="company-name">PT. METAL AMANAH BARU</h1>
						<p class="company-text">
							Alamat : Villa Mutiara Indah Gading 3<br>
							Taman Kebalen Blok E1 No. 48, Babelan - Bekasi<br>
							<strong>No-Telp :</strong> 0813-9870-0989<br>
							<strong>Email :</strong> metalamanahbaru@yahoo.com
						</p>
					</div>
				</header>

				<!-- Informasi nomor surat jalan dan customer -->
				<div class="meta-grid">
					<div class="meta-box">
						<div class="meta-row">
							<div class="meta-label">Nomor Surat Jalan</div>
							<div class="meta-value">{{ $nomor_surat_jalan }}</div>
						</div>
						<div class="meta-row">
							<div class="meta-label">Customer</div>
							<div class="meta-value">{{ $customer_name }}</div>
						</div>
					</div>
				</div>

				<!-- Tabel rincian barang -->
				<div class="table-wrap">
					<p class="table-caption">Rincian Barang</p>
					<table>
						<thead>
							<tr>
								<th class="col-qty">Jumlah</th>
								<th style="width: 58%;">Nama Barang</th>
								<th class="col-ket">Keterangan</th>
							</tr>
						</thead>
						<tbody>
							@foreach ($items as $item)
								<tr>
									<td class="col-qty">{{ $item['quantity'] }}</td>
									<td>
										<div style="font-weight: 700; color: #0f172a; line-height: 1.35;">{{ $item['nama_barang'] }}</div>
										<div style="margin-top: 4px; font-weight: 700; color: #0f172a; line-height: 1.35;">{{ $item['material'] }}</div>
									</td>
									<td class="col-ket">{{ $item['keterangan'] }}</td>
								</tr>
							@endforeach
						</tbody>
					</table>
				</div>

				<!-- Tanda tangan penerima dan perusahaan -->
				<div class="signature">
					<div class="signature-box signature-box-left">
						<div>
							<div class="signature-title">Tanda Terima</div>
							<div class="signature-spacer" aria-hidden="true"></div>
							<div class="signature-line"></div>
						</div>
					</div>
					<div class="signature-box signature-box-right" style="text-align: left;">
						<div>
							<div class="signature-title">Hormat Kami</div>
							<div class="signature-company">PT. Metal Amanah Baru</div>
							<div class="signature-line"></div>
						</div>
					</div>
				</div>
			@endif
		</section>
	</div>
</body>
</html>
@endif
