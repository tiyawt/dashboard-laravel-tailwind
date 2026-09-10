<?php

namespace App\Services;

use RuntimeException;
use ZipArchive;

class SimpleXlsxExporter
{
    public static function download(array $columns, array $rows, string $fileName, string $sheetName)
    {
        $temporaryFile = tempnam(sys_get_temp_dir(), 'export_');

        if ($temporaryFile === false) {
            throw new RuntimeException('Gagal membuat file sementara untuk ekspor Excel.');
        }

        $zip = new ZipArchive();
        if ($zip->open($temporaryFile, ZipArchive::OVERWRITE) !== true) {
            throw new RuntimeException('Gagal membuat file Excel.');
        }

        $sheetRows = [
            '<row r="1">' . self::row($columns, 1, 1) . '</row>',
        ];

        foreach ($rows as $rowIndex => $row) {
            $excelRow = $rowIndex + 2;
            $sheetRows[] = '<row r="' . $excelRow . '">' . self::row($row, $excelRow) . '</row>';
        }

        $columnWidths = '';
        foreach ($columns as $index => $column) {
            $columnNumber = $index + 1;
            $width = max(14, min(34, mb_strlen((string) $column) + 6));
            $columnWidths .= '<col min="' . $columnNumber . '" max="' . $columnNumber . '" width="' . $width . '" customWidth="1"/>';
        }

        $escapedSheetName = htmlspecialchars($sheetName, ENT_XML1 | ENT_QUOTES, 'UTF-8');

        $zip->addFromString('[Content_Types].xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types"><Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/><Default Extension="xml" ContentType="application/xml"/><Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/><Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/><Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/></Types>');
        $zip->addFromString('_rels/.rels', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/></Relationships>');
        $zip->addFromString('xl/workbook.xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships"><sheets><sheet name="' . $escapedSheetName . '" sheetId="1" r:id="rId1"/></sheets></workbook>');
        $zip->addFromString('xl/_rels/workbook.xml.rels', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/><Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/></Relationships>');
        $zip->addFromString('xl/styles.xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"><fonts count="2"><font><sz val="11"/><name val="Calibri"/></font><font><b/><sz val="11"/><name val="Calibri"/></font></fonts><fills count="2"><fill><patternFill patternType="none"/></fill><fill><patternFill patternType="gray125"/></fill></fills><borders count="1"><border><left/><right/><top/><bottom/><diagonal/></border></borders><cellStyleXfs count="1"><xf/></cellStyleXfs><cellXfs count="2"><xf xfId="0"/><xf xfId="0" fontId="1" applyFont="1"/></cellXfs></styleSheet>');
        $zip->addFromString('xl/worksheets/sheet1.xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"><cols>' . $columnWidths . '</cols><sheetData>' . implode('', $sheetRows) . '</sheetData></worksheet>');
        $zip->close();

        return response()->download($temporaryFile, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    private static function row(array $values, int $rowNumber, ?int $style = null): string
    {
        return collect($values)->map(function ($value, $columnIndex) use ($rowNumber, $style) {
            $cell = self::column($columnIndex + 1) . $rowNumber;
            $styleAttribute = $style === null ? '' : ' s="' . $style . '"';
            $escapedValue = htmlspecialchars((string) $value, ENT_XML1 | ENT_QUOTES, 'UTF-8');

            return '<c r="' . $cell . '" t="inlineStr"' . $styleAttribute . '><is><t xml:space="preserve">' . $escapedValue . '</t></is></c>';
        })->implode('');
    }

    private static function column(int $columnNumber): string
    {
        $column = '';

        while ($columnNumber > 0) {
            $remainder = ($columnNumber - 1) % 26;
            $column = chr(65 + $remainder) . $column;
            $columnNumber = intdiv($columnNumber - 1, 26);
        }

        return $column;
    }
}
