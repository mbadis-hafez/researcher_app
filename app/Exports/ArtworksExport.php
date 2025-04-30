<?php


namespace App\Exports;

use App\Models\Artwork;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class ArtworksExport implements FromCollection, WithHeadings, WithMapping, WithEvents, WithDrawings
{
    private $artworks;

    public function collection()
    {
        $this->artworks = Artwork::select(
            'artworks.id',
            'artworks.image',
            'artworks.title',
            'artworks.year',
            'artworks.medium',
            'artworks.copyright_line',
            'artworks.description',
            'artworks.additional_information',
            'artworks.provenance',
            'artworks.exhibitions',
            'artworks.location',
            'users.name as author_name'
        )
            ->leftJoin('users', 'users.id', '=', 'artworks.author_id')
            ->get();

        return $this->artworks;
    }

    public function headings(): array
    {
        return [
            'Image',
            'Title',
            'Year',
            'Medium',
            'Copyright Line',
            'Description',
            'Additional Information',
            'Provenance',
            'Exhibitions',
            'Location',
            'Author Name',
        ];
    }

    public function map($artwork): array
    {
        return [
            '', // Empty for image
            $artwork->title,
            $artwork->year,
            $artwork->medium,
            $artwork->copyright_line,
            $artwork->description,
            $artwork->additional_information,
            $artwork->provenance,
            $artwork->exhibitions,
            $artwork->location,
            $artwork->author_name,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Header styling
                $sheet->getStyle('A1:K1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => 'FFFFFF'],
                    ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '4F81BD'],
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    ],
                ]);

                foreach (range('A', 'K') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }

                // Row height for images
                $sheet->getDefaultRowDimension()->setRowHeight(60);
            },
        ];
    }

    public function drawings()
    {
        $drawings = [];

        foreach ($this->artworks as $index => $artwork) {
            if (!empty($artwork->image)) {
                $relativePath = str_replace('\\', '/', $artwork->image);

                $publicPath = public_path($relativePath);
                $storagePath = storage_path('app/public/' . $relativePath);

                if (file_exists($publicPath)) {
                    $path = $publicPath;
                } elseif (file_exists($storagePath)) {
                    $path = $storagePath;
                } else {
                    logger()->error("Image not found: {$relativePath}");
                    continue; // Skip this one
                }

                $drawing = new Drawing();
                $drawing->setPath($path);
                $drawing->setHeight(50);
                $drawing->setCoordinates('A' . ($index + 2));
                $drawing->setOffsetX(5);
                $drawing->setOffsetY(5);

                $drawings[] = $drawing;
            }
        }

        return $drawings;
    }
}
