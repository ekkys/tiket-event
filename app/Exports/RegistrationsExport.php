<?php

namespace App\Exports;

use App\Models\Registration;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;

class RegistrationsExport extends DefaultValueBinder implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithColumnFormatting, WithCustomValueBinder
{
    use Exportable;

    private $filters;
    private $rowNumber = 0;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $userId = auth()->id();
        $query = Registration::whereHas('event', fn($q) => $q->where('user_id', $userId))
            ->with(['ticket', 'event'])
            ->latest();

        if (isset($this->filters['search']) && $this->filters['search']) {
            $s = $this->filters['search'];
            $query->where(function ($q) use ($s) {
                $q->where('full_name', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%")
                  ->orWhere('registration_code', 'like', "%{$s}%")
                  ->orWhere('phone', 'like', "%{$s}%");
            });
        }

        if (isset($this->filters['status']) && $this->filters['status']) {
            $query->where('payment_status', $this->filters['status']);
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            'No',
            'Kode Registrasi',
            'Event',
            'Nama Lengkap',
            'Email',
            'HP',
            'NIK',
            'Gender',
            'Institusi',
            'Alamat',
            'Status Pembayaran',
            'Status Check-in',
            'Tanggal Daftar'
        ];
    }

    public function map($registration): array
    {
        $this->rowNumber++;
        return [
            $this->rowNumber,
            $registration->registration_code,
            $registration->event?->name ?? '-',
            $registration->full_name,
            $registration->email,
            $registration->phone,
            $registration->id_number,
            $registration->gender ?? '-',
            $registration->institution,
            $registration->address,
            strtoupper($registration->payment_status),
            $registration->ticket?->is_used ? 'Ya (Sudah Scan)' : 'Belum Scan',
            $registration->created_at->format('d/m/Y H:i')
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function columnFormats(): array
    {
        return [
            'F' => NumberFormat::FORMAT_TEXT, // HP
            'G' => NumberFormat::FORMAT_TEXT, // NIK
        ];
    }

    public function bindValue(Cell $cell, $value)
    {
        if ($cell->getColumn() == 'F' || $cell->getColumn() == 'G') {
            $cell->setValueExplicit($value, DataType::TYPE_STRING);
            return true;
        }

        return parent::bindValue($cell, $value);
    }
}
