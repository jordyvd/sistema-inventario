<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeSheet;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class DocumentosElectronicos implements FromCollection, WithColumnWidths, WithCustomStartCell, WithEvents, WithHeadings
{
    public function __construct($data)
    {
        $this->data = $data;
        $this->return = [];
    }

    public function collection()
    {
        $return = [];
        foreach ($this->data['data'] as $value) {
            $documento = "";
            if($value['tipo'] == 1){
                $documento = "Factura";
            }else if($value['tipo'] == 3){
                $documento = "Boleta";
            }else if($value['tipo'] == 7){
                $documento = "N. Credito";
            }    
            $return[] = [
                "serie" => substr($value['serie'], -13),
                "documento" => $documento,
                "afectado" => substr($value['afectado'], -13),
                "dni/ruc " => $value['ruc_dni_v'],
                "cliente" => $value['nombre_cliente'],
                "total" => number_format($value['total'], 2),
            ];
        }
        $this->return = $return;
        return collect($return);
    }
    public function startCell(): string
    {
        return 'A4';
    }

    public function headings(): array
    {
        return ['SERIE', ' DOCUMENTO ', ' AFECTADO ' ,' DNI/RUC ', ' CLIENTE ', ' TOTAL '];
    }
    public function columnWidths(): array
    {
        return ["A" => 30, "B" => 15, "C" => 15];
    }
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $style_text_center = [
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER
                    ]
                ];
                $bg_category = [
                    'fillType' => 'solid',
                    'rotation' => 0,
                    'color' => ['rgb' => 'F4F4F4']
                ];
                $color_black_b = [
                    'font' => [
                        'color' => ['rgb' => '706D7D'],
                        'bold' => true,
                    ],
                ];
                $color_black = [
                    'font' => [
                        'color' => ['rgb' => '706D7D']
                    ],
                ];
                $border = [
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ];
                $bold = [
                    'font' => [
                        'bold' => true,
                    ]
                ];
                $bg_primary = [
                    'fillType' => 'solid',
                    'rotation' => 0,
                    'color' => ['rgb' => 'ad1103']
                ];
                $footer = [
                    'fillType' => 'solid',
                    'rotation' => 0,
                    'color' => ['rgb' => '636869']
                ];
                $color_white = [
                    'font' => [
                        'color' => ['rgb' => 'FFFFFF'],
                    ],
                ];
                $color_tran = [
                    'font' => [
                        'color' => ['rgb' => '706d7d'],
                    ],
                ];
                $letra_fin = "F";
                foreach($this->return as $key => $value){
                    $number = (int)$key + 5;
                    $event->sheet->getStyle('A'. $number .':'.$letra_fin.$number)->applyFromArray(array_merge($style_text_center));
                }
                $fecha = date_create($this->data['fecha']);
                $fecha_end = date_create($this->data['fecha_end']);
                $event->sheet->mergeCells('A2:'.$letra_fin .'2');
                $event->sheet->setCellValue('A2', '' . date_format($fecha, 'd-m-Y') . ' - ' .date_format($fecha_end, 'd-m-Y')  . '');
                $event->sheet->getStyle('A2:B2')->applyFromArray(array_merge($style_text_center, $bold));
                $event->sheet->getStyle('A4:'.$letra_fin .'4')->getFill()->applyFromArray($bg_primary);
                $event->sheet->getStyle('A4:'.$letra_fin .'4')->applyFromArray(array_merge($style_text_center, $bold, $color_white));
                $event->sheet->getStyle('A4:'.$letra_fin .'4')->applyFromArray(array_merge($border));
                $event->sheet->getColumnDimension('D')->setWidth(20);
                $event->sheet->getColumnDimension('E')->setWidth(80);
            }
        ];
    }
}
