<?php

namespace App\Http\Controllers;

use App\Models\Alat;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Http\Request;

class QrCodeController extends Controller
{
    public function index()
{
    return view('pages.admin.qr-management', [
        'alats' => \App\Models\Alat::all()
    ]);
}
    // Generate QR untuk satu barang
    public function generateQr(Alat $alat)
    {
        // Data yang disimpan di QR: ID alat + nomor unit
        $qrData = json_encode([
            'alat_id' => $alat->alat_id,
            'nama_alat' => $alat->nama_alat,
            'nomor_unit' => $alat->nomor_unit,
        ]);

        // Generate QR Code
        $qrCode = QrCode::create($qrData)
            ->setSize(300)
            ->setMargin(10);

        $writer = new PngWriter();
        $result = $writer->write($qrCode);

        // Save ke database sebagai base64
        $base64 = 'data:image/png;base64,' . base64_encode($result->getString());
        $alat->update(['qr_code' => $base64]);

        return response()->json([
            'success' => true,
            'message' => 'QR Code generated',
            'qr_code' => $base64
        ]);
    }

    // Generate QR untuk semua barang
    public function generateAllQr()
    {
        $alats = Alat::all();

        foreach ($alats as $alat) {
            $qrData = json_encode([
                'alat_id' => $alat->alat_id,
                'nama_alat' => $alat->nama_alat,
                'nomor_unit' => $alat->nomor_unit,
            ]);

            $qrCode = QrCode::create($qrData)
                ->setSize(300)
                ->setMargin(10);

            $writer = new PngWriter();
            $result = $writer->write($qrCode);

            $base64 = 'data:image/png;base64,' . base64_encode($result->getString());
            $alat->update(['qr_code' => $base64]);
        }

        return redirect()->back()->with('success', 'Semua QR Code berhasil digenerate!');
    }

    // API: Scan QR dan return alat data
    public function scanQr(Request $request)
    {
        $validated = $request->validate([
            'qr_data' => 'required|json'
        ]);

        $data = json_decode($validated['qr_data'], true);

        $alat = Alat::find($data['alat_id']);

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
                'harga_alat' => $alat->harga_alat,
            ]
        ]);
    }
}