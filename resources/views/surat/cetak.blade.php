<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $surat->nomor_surat }} - Surat Keluar SMK Negeri 1 Sadaniang</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        @page {
            size: A4 portrait;
            margin: 15mm 20mm;
        }
        * {
            box-sizing: border-box;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            color: #000;
            background: #e9ecef;
            margin: 0;
            padding: 0;
            font-size: 13.5px;
            line-height: 1.5;
        }
        .no-print {
            text-align: center;
            padding: 12px 20px;
            background: #ffffff;
            border-bottom: 2px solid #dee2e6;
            position: sticky;
            top: 0;
            z-index: 999;
            box-shadow: 0 2px 6px rgba(0,0,0,0.08);
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }
        .btn-action {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border: none;
            padding: 8px 16px;
            border-radius: 5px;
            font-size: 13.5px;
            cursor: pointer;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s;
        }
        .btn-print {
            background: #0d6efd;
            color: #fff;
        }
        .btn-print:hover { background: #0b5ed7; }
        .btn-pdf {
            background: #dc3545;
            color: #fff;
        }
        .btn-pdf:hover { background: #bb2d3b; }
        .btn-img {
            background: #198754;
            color: #fff;
        }
        .btn-img:hover { background: #157347; }
        .btn-close-page {
            background: #6c757d;
            color: #fff;
        }
        .btn-close-page:hover { background: #5c636a; }

        .paper-container {
            padding: 25px 0;
            display: flex;
            justify-content: center;
        }
        .page-surat {
            width: 210mm;
            min-height: 297mm;
            background: #ffffff;
            padding: 16mm 22mm 20mm 22mm;
            box-shadow: 0 4px 15px rgba(0,0,0,0.15);
            position: relative;
        }
        .kop-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 4px;
        }
        .kop-table td {
            vertical-align: middle;
        }
        .kop-logo {
            width: 78px;
            height: auto;
            display: block;
        }
        .kop-text {
            text-align: center;
            padding: 0 10px;
        }
        .kop-text h4 {
            margin: 0;
            font-size: 14.5px;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        .kop-text h2 {
            margin: 3px 0 4px 0;
            font-size: 19.5px;
            font-weight: 800;
            letter-spacing: 0.8px;
            text-transform: uppercase;
        }
        .kop-text p {
            margin: 0;
            font-size: 12px;
            line-height: 1.4;
        }
        .kop-divider {
            border-top: 3.5px solid #000;
            border-bottom: 1.2px solid #000;
            height: 2px;
            margin: 6px 0 18px 0;
        }
        .tanggal-surat {
            text-align: right;
            margin-bottom: 16px;
            font-size: 13.5px;
        }
        .table-meta {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 13.5px;
        }
        .table-meta td {
            vertical-align: top;
            padding: 2px 0;
        }
        .tujuan-surat {
            margin-bottom: 20px;
            line-height: 1.5;
            font-size: 13.5px;
        }
        .salam-pembuka {
            margin-bottom: 14px;
            font-size: 13.5px;
        }
        .isi-surat {
            text-align: justify;
            text-justify: inter-word;
            line-height: 1.6;
            margin-bottom: 16px;
            font-size: 13.5px;
        }
        .table-rincian {
            margin: 12px 0 16px 20px;
            border-collapse: collapse;
            font-size: 13.5px;
        }
        .table-rincian td {
            vertical-align: top;
            padding: 3px 6px 3px 0;
        }
        .paragraf-penutup {
            text-align: justify;
            line-height: 1.6;
            margin-bottom: 24px;
            font-size: 13.5px;
        }
        .ttd-container {
            width: 100%;
            margin-top: 30px;
        }
        .ttd-box {
            float: right;
            width: 260px;
            text-align: left;
            position: relative;
            font-size: 13.5px;
            line-height: 1.4;
        }
        .ttd-stempel-img {
            position: absolute;
            left: -40px;
            top: 25px;
            width: 155px;
            height: auto;
            pointer-events: none;
            z-index: 2;
        }
        .nama-pejabat {
            font-weight: bold;
            text-decoration: underline;
            margin-top: 75px;
            display: block;
            font-size: 13.5px;
        }
        .nip-pejabat {
            display: block;
            margin-top: 2px;
            font-size: 13px;
        }

        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background: #fff;
            }
            .paper-container {
                padding: 0;
            }
            .page-surat {
                width: 100%;
                margin: 0;
                padding: 0;
                box-shadow: none;
                min-height: auto;
            }
        }
    </style>
</head>
<body>
    <div class="no-print">
        <button type="button" class="btn-action btn-print" onclick="window.print()">
            <i class="bi bi-printer-fill"></i> Cetak Dokumen
        </button>
        <button type="button" class="btn-action btn-pdf" id="btnUnduhPDF">
            <i class="bi bi-file-earmark-pdf-fill"></i> Unduh PDF
        </button>
        <button type="button" class="btn-action btn-img" id="btnUnduhGambar">
            <i class="bi bi-file-earmark-image-fill"></i> Unduh Gambar (PNG)
        </button>
        <button type="button" class="btn-action btn-close-page" onclick="window.close()">
            <i class="bi bi-x-circle"></i> Tutup
        </button>
    </div>

    <div class="paper-container">
        <div class="page-surat" id="suratContent">
            <!-- KOP SURAT -->
            <table class="kop-table">
                <tr>
                    <td style="width: 80px; text-align: left;">
                        <img src="{{ asset('assets/surat/logo_kalbar.png') }}" class="kop-logo" alt="Logo Pemprov Kalbar">
                    </td>
                    <td class="kop-text">
                        <h4>PEMERINTAH PROVINSI KALIMANTAN BARAT</h4>
                        <h2>SMK NEGERI 1 SADANIANG</h2>
                        <p>Jalan Raya Pudak, Provinsi Kalimantan Barat, Kode Pos 78361</p>
                        <p>Hp. 081348969697, Website: <span style="text-decoration: underline; color: #0056b3;">http://www.smkn1sadaniang.sch.id</span></p>
                    </td>
                    <td style="width: 80px; text-align: right;">
                        <img src="{{ asset('assets/surat/logo_smk.png') }}" class="kop-logo" alt="Logo SMK">
                    </td>
                </tr>
            </table>
            <div class="kop-divider"></div>

            <!-- TANGGAL SURAT -->
            @php
                $bulanIndo = [
                    1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
                ];
                $tglObj = \Carbon\Carbon::parse($surat->tanggal_surat);
                $formattedTanggal = 'Sadaniang, ' . $tglObj->day . ' ' . ($bulanIndo[$tglObj->month] ?? '') . ' ' . $tglObj->year;
            @endphp
            <div class="tanggal-surat">
                {{ $formattedTanggal }}
            </div>

            <!-- NOMOR & PERIHAL -->
            <table class="table-meta">
                <tr>
                    <td style="width: 85px;">Nomor</td>
                    <td style="width: 15px;">:</td>
                    <td><strong>{{ $surat->nomor_surat }}</strong></td>
                </tr>
                <tr>
                    <td>Perihal</td>
                    <td>:</td>
                    <td>{{ $surat->perihal }}</td>
                </tr>
            </table>

            <!-- TUJUAN SURAT -->
            <div class="tujuan-surat">
                Kepada Yth.<br>
                <strong>{{ $surat->tujuan_surat ?: 'Bapak/Ibu Dewan Guru' }}</strong><br>
                @if(stripos($surat->tujuan_surat, 'SMK') === false)
                    SMK Negeri 1 Sadaniang<br>
                @endif
                di Tempat
            </div>

            <!-- SALAM PEMBUKA -->
            <div class="salam-pembuka">
                Dengan hormat,
            </div>

            <!-- ISI SURAT -->
            <div class="isi-surat">
                @if(!empty($surat->keterangan))
                    {!! nl2br(e($surat->keterangan)) !!}
                @else
                    Sehubungan dengan akan dilaksanakannya kegiatan <strong>{{ $surat->perihal }}</strong> pada SMK Negeri 1 Sadaniang, yang pelaksanaannya mengacu pada agenda resmi sekolah. Maka kami mengundang / menugaskan Bapak/Ibu untuk dapat menghadiri pelaksanaan kegiatan tersebut.
                @endif
            </div>

            <!-- PARAGRAF PENEGASAN & PENUTUP -->
            <div class="paragraf-penutup">
                Mengingat pentingnya agenda tersebut, kami mengharapkan kehadiran Bapak/Ibu tepat pada waktunya agar seluruh persiapan dan kegiatan dapat dikoordinasikan dengan baik.
                <br><br>
                Demikian surat ini kami sampaikan. Atas perhatian dan kerja samanya, kami mengucapkan terima kasih.
            </div>

            <!-- TANDA TANGAN & STEMPEL -->
            <div class="ttd-container">
                <div class="ttd-box">
                    Mengetahui,<br>
                    Kepala Sekolah
                    <img src="{{ asset('assets/surat/ttd_stempel.png') }}" class="ttd-stempel-img" alt="Stempel & Tanda Tangan">
                    <span class="nama-pejabat">Fransiska Ika, S.S, M.Pd.</span>
                    <span class="nip-pejabat">NIP. 19850122 200904 2 002</span>
                </div>
                <div style="clear: both;"></div>
            </div>
        </div>
    </div>

    <!-- Scripts CDN untuk generate PDF & Image -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script>
        const nomorSurat = "{{ preg_replace('/[^a-zA-Z0-9_-]/', '_', $surat->nomor_surat) }}";

        document.getElementById('btnUnduhGambar').addEventListener('click', function() {
            const btn = this;
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="bi bi-arrow-repeat"></i> Memproses...';
            btn.disabled = true;

            html2canvas(document.getElementById('suratContent'), {
                scale: 2,
                useCORS: true,
                backgroundColor: '#ffffff'
            }).then(canvas => {
                const link = document.createElement('a');
                link.download = 'Surat_Keluar_' + nomorSurat + '.png';
                link.href = canvas.toDataURL('image/png');
                link.click();
                btn.innerHTML = originalText;
                btn.disabled = false;
            }).catch(err => {
                console.error(err);
                alert('Gagal menghasilkan gambar surat.');
                btn.innerHTML = originalText;
                btn.disabled = false;
            });
        });

        document.getElementById('btnUnduhPDF').addEventListener('click', function() {
            const btn = this;
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="bi bi-arrow-repeat"></i> Memproses PDF...';
            btn.disabled = true;

            html2canvas(document.getElementById('suratContent'), {
                scale: 2,
                useCORS: true,
                backgroundColor: '#ffffff'
            }).then(canvas => {
                const { jsPDF } = window.jspdf;
                const pdf = new jsPDF('p', 'mm', 'a4');
                const imgData = canvas.toDataURL('image/png');
                const pdfWidth = pdf.internal.pageSize.getWidth();
                const pdfHeight = (canvas.height * pdfWidth) / canvas.width;

                pdf.addImage(imgData, 'PNG', 0, 0, pdfWidth, pdfHeight);
                pdf.save('Surat_Keluar_' + nomorSurat + '.pdf');

                btn.innerHTML = originalText;
                btn.disabled = false;
            }).catch(err => {
                console.error(err);
                alert('Gagal menghasilkan file PDF.');
                btn.innerHTML = originalText;
                btn.disabled = false;
            });
        });
    </script>
</body>
</html>
