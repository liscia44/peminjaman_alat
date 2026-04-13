<?php

namespace App\Http\Controllers;

use App\Models\Alat;
use App\Models\AlatUnit;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use PDF;
use Illuminate\Http\Request;

class QrCodeController extends Controller
{
    public function indexManagement()
    {
        return view('pages.admin.qr-management', [
            'alatUnits' => AlatUnit::with('alat')->get()
        ]);
    }

   public function generateQr(AlatUnit $alatUnit)
{
    try {
        $alat = $alatUnit->alat;
        
        // ✅ FIXED: Hanya kirim data unit, jangan nomor_unit dari alat
        $qrData = json_encode([
            'alat_unit_id' => $alatUnit->id,
            'alat_id' => $alat->alat_id,
            'nama_alat' => $alat->nama_alat,
            'unit_number' => $alatUnit->unit_number,
            // ✅ REMOVED: 'nomor_unit' => $alat->nomor_unit,
        ]);

        $qrCode = new QrCode($qrData);
        $qrCode->setSize(300);
        $qrCode->setMargin(10);

        $writer = new PngWriter();
        $result = $writer->write($qrCode);

        $base64 = 'data:image/png;base64,' . base64_encode($result->getString());
        $alatUnit->update(['qr_code' => $base64]);

        return response()->json([
            'success' => true,
            'message' => "QR Code Unit {$alatUnit->unit_number} berhasil digenerate",
            'qr_code' => $base64
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ], 500);
    }
}

    // ✅ UPDATED: Generate QR untuk semua UNIT dari satu ALAT
   public function generateAllQrByAlat(Alat $alat)
{
    try {
        $units = $alat->units;
        $successCount = 0;

        foreach ($units as $unit) {
            try {
                $qrData = json_encode([
                    'alat_unit_id' => $unit->id,
                    'alat_id' => $alat->alat_id,
                    'nama_alat' => $alat->nama_alat,
                    'unit_number' => $unit->unit_number,
                    // ✅ REMOVED: 'nomor_unit' => $alat->nomor_unit,
                ]);

                $qrCode = new QrCode($qrData);
                $qrCode->setSize(300);
                $qrCode->setMargin(10);

                $writer = new PngWriter();
                $result = $writer->write($qrCode);

                $base64 = 'data:image/png;base64,' . base64_encode($result->getString());
                $unit->update(['qr_code' => $base64]);
                
                $successCount++;
            } catch (\Exception $e) {
                \Log::error("QR generate error: " . $e->getMessage());
            }
        }

        return redirect()->route('qr-management')
            ->with('success', "✅ Berhasil generate {$successCount} QR Code untuk {$alat->nama_alat}!");
    } catch (\Exception $e) {
        return redirect()->route('qr-management')
            ->with('error', 'Error: ' . $e->getMessage());
    }
}

public function generateAllQr()
{
    try {
        $allUnits = AlatUnit::all();
        $successCount = 0;

        foreach ($allUnits as $unit) {
            try {
                $alat = $unit->alat;
                
                $qrData = json_encode([
                    'alat_unit_id' => $unit->id,
                    'alat_id' => $alat->alat_id,
                    'nama_alat' => $alat->nama_alat,
                    'unit_number' => $unit->unit_number,
                    // ✅ REMOVED: 'nomor_unit' => $alat->nomor_unit,
                ]);

                $qrCode = new QrCode($qrData);
                $qrCode->setSize(300);
                $qrCode->setMargin(10);

                $writer = new PngWriter();
                $result = $writer->write($qrCode);

                $base64 = 'data:image/png;base64,' . base64_encode($result->getString());
                $unit->update(['qr_code' => $base64]);
                
                $successCount++;
            } catch (\Exception $e) {
                \Log::error("QR generate error: " . $e->getMessage());
            }
        }

        return redirect()->route('qr-management')
            ->with('success', "✅ Berhasil generate {$successCount} QR Code untuk semua unit!");
    } catch (\Exception $e) {
        return redirect()->route('qr-management')
            ->with('error', 'Error: ' . $e->getMessage());
    }
}

    // ✅ UPDATED: Download single UNIT QR as PDF
    public function downloadQrPdf(AlatUnit $alatUnit)
    {
        if (!$alatUnit->qr_code) {
            return redirect()->back()->with('error', 'QR Code belum digenerate');
        }

        $alat = $alatUnit->alat;
        $html = $this->generateQrHtml(
            $alatUnit->qr_code,
            $alat->nama_alat,
            $alat->nomor_unit,
            $alatUnit->unit_number
        );
        
        $pdf = PDF::loadHTML($html)
            ->setPaper('A6', 'portrait')
            ->setOptions([
                'margin_top' => 5,
                'margin_right' => 5,
                'margin_bottom' => 5,
                'margin_left' => 5,
            ]);

        return $pdf->download("QR-{$alat->nama_alat}-Unit{$alatUnit->unit_number}.pdf");
    }

    // ✅ NEW: Download all UNIT QR from one ALAT as PDF
    public function downloadAllQrByAlatPdf(Alat $alat)
    {
        $units = $alat->units()->whereNotNull('qr_code')->get();

        if ($units->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada QR Code untuk alat ini');
        }

        $html = '<style>
            * { margin: 0; padding: 0; }
            body { font-family: Arial, sans-serif; }
            .page-break { page-break-after: always; }
            .qr-container {
                display: flex;
                justify-content: center;
                align-items: center;
                flex-direction: column;
                height: 100%;
                border: 2px dashed #000;
                padding: 10px;
                text-align: center;
            }
            .qr-container img { width: 150px; margin-bottom: 10px; }
            .qr-container p { font-size: 12px; font-weight: bold; margin: 5px 0; }
        </style>';

        foreach ($units as $index => $unit) {
            $html .= '<div class="qr-container">';
            $html .= '<img src="' . $unit->qr_code . '" alt="QR Code" />';
            $html .= '<p>' . $alat->nama_alat . '</p>';
            $html .= '<p>Unit ' . $unit->unit_number . '</p>';
            $html .= '</div>';
            
            if ($index < $units->count() - 1) {
                $html .= '<div class="page-break"></div>';
            }
        }

        $pdf = PDF::loadHTML($html)
            ->setPaper('A6', 'portrait')
            ->setOptions([
                'margin_top' => 0,
                'margin_right' => 0,
                'margin_bottom' => 0,
                'margin_left' => 0,
            ]);

        return $pdf->download("QR-{$alat->nama_alat}-All-" . date('Y-m-d') . ".pdf");
    }

    // ✅ UPDATED: Download all QR codes as PDF
    public function downloadAllQrPdf()
    {
        $units = AlatUnit::whereNotNull('qr_code')->with('alat')->get();

        if ($units->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada QR Code yang sudah digenerate');
        }

        $html = '<style>
            * { margin: 0; padding: 0; }
            body { font-family: Arial, sans-serif; }
            .page-break { page-break-after: always; }
            .qr-container {
                display: flex;
                justify-content: center;
                align-items: center;
                flex-direction: column;
                height: 100%;
                border: 2px dashed #000;
                padding: 10px;
                text-align: center;
            }
            .qr-container img { width: 150px; margin-bottom: 10px; }
            .qr-container p { font-size: 12px; font-weight: bold; margin: 5px 0; }
        </style>';

        foreach ($units as $index => $unit) {
            $alat = $unit->alat;
            $html .= '<div class="qr-container">';
            $html .= '<img src="' . $unit->qr_code . '" alt="QR Code" />';
            $html .= '<p>' . $alat->nama_alat . '</p>';
            $html .= '<p>Unit ' . $unit->unit_number . '</p>';
            $html .= '</div>';
            
            if ($index < $units->count() - 1) {
                $html .= '<div class="page-break"></div>';
            }
        }

        $pdf = PDF::loadHTML($html)
            ->setPaper('A6', 'portrait')
            ->setOptions([
                'margin_top' => 0,
                'margin_right' => 0,
                'margin_bottom' => 0,
                'margin_left' => 0,
            ]);

        return $pdf->download('QR-All-' . date('Y-m-d-His') . '.pdf');
    }

    // ✅ Helper method untuk generate HTML
    private function generateQrHtml($qrBase64, $namaAlat, $nomorUnit, $unitNumber = null)
    {
        $unitText = $unitNumber ? "Unit {$unitNumber}" : $nomorUnit;
        
        return '
            <html>
            <head>
                <style>
                    * { margin: 0; padding: 0; }
                    body { 
                        font-family: Arial, sans-serif;
                        display: flex;
                        justify-content: center;
                        align-items: center;
                        height: 100vh;
                    }
                    .sticker {
                        width: 200px;
                        text-align: center;
                        border: 2px dashed #000;
                        padding: 15px;
                    }
                    .sticker img { width: 150px; margin-bottom: 10px; display: block; }
                    .sticker p { 
                        margin: 5px 0; 
                        font-size: 12px; 
                        font-weight: bold;
                        word-wrap: break-word;
                    }
                </style>
            </head>
            <body>
                <div class="sticker">
                    <img src="' . $qrBase64 . '" alt="QR Code" />
                    <p>' . $namaAlat . '</p>
                    <p>' . $unitText . '</p>
                </div>
            </body>
            </html>
        ';
    }

       // API: Scan QR dan return alat data
    public function scanQr(Request $request)
    {
        $validated = $request->validate([
            'qr_data' => 'required|json'
        ]);

        $data = json_decode($validated['qr_data'], true);

        // ✅ UPDATED: Cari dari alat_units dulu
        $alatUnit = AlatUnit::find($data['alat_unit_id'] ?? null);
        
        if ($alatUnit) {
            // Jika dapat unit spesifik
            $alat = $alatUnit->alat;
            
            return response()->json([
                'success' => true,
                'alat' => [
                    'alat_unit_id' => $alatUnit->id,
                    'alat_id' => $alat->alat_id,
                    'nama_alat' => $alat->nama_alat,
                    'nomor_unit' => $alat->nomor_unit,
                    'unit_number' => $alatUnit->unit_number,
                    'stok_tersedia' => $alat->stok_tersedia,
                    'harga_alat' => (float) $alat->harga_alat,
                    'status' => $alatUnit->status,
                ]
            ]);
        }

        // Fallback: Cari dari alat jika format lama
        $alat = Alat::find($data['alat_id'] ?? null);
        
        if (!$alat) {
            return response()->json([
                'success' => false,
                'message' => 'Alat tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'alat' => [
                'alat_id' => $alat->alat_id,
                'nama_alat' => $alat->nama_alat,
                'nomor_unit' => $alat->nomor_unit,
                'stok_tersedia' => $alat->stok_tersedia,
                'harga_alat' => (float) $alat->harga_alat,
            ]
        ]);
    }
}