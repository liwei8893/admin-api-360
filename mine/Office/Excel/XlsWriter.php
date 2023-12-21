<?php

declare(strict_types=1);

namespace Mine\Office\Excel;

use Closure;
use Exception;
use Mine\Exception\MineException;
use Mine\MineModel;
use Mine\MineRequest;
use Mine\MineResponse;
use Mine\Office\ExcelPropertyInterface;
use Mine\Office\MineExcel;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;
use Vtiful\Kernel\Excel;
use Vtiful\Kernel\Format;

class XlsWriter extends MineExcel implements ExcelPropertyInterface
{
    public static function getSheetData(mixed $request)
    {
        $file = $request->file('file');
        $tempFileName = 'import_' . time() . '.' . $file->getExtension();
        $tempFilePath = BASE_PATH . '/runtime/' . $tempFileName;
        file_put_contents($tempFilePath, $file->getStream()->getContents());
        $xlsxObject = new \Vtiful\Kernel\Excel(['path' => BASE_PATH . '/runtime/']);
        return $xlsxObject->openFile($tempFileName)->openSheet()->getSheetData();
    }

    /**
     * 导入数据.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    public function import(MineModel $model, ?Closure $closure = null): bool
    {
        $request = container()->get(MineRequest::class);
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $tempFileName = 'import_' . time() . '.' . $file->getExtension();
            $tempFilePath = BASE_PATH . '/runtime/' . $tempFileName;
            file_put_contents($tempFilePath, $file->getStream()->getContents());
            $xlsxObject = new Excel(['path' => BASE_PATH . '/runtime/']);
            $data = $xlsxObject->openFile($tempFileName)->openSheet()->getSheetData();
            unset($data[0]);

            $importData = [];
            foreach ($data as $item) {
                $tmp = [];
                foreach ($item as $key => $value) {
                    $tmp[$this->property[$key]['name']] = (string) $value;
                }
                $importData[] = $tmp;
            }

            if ($closure instanceof Closure) {
                return $closure($model, $importData);
            }

            try {
                foreach ($importData as $item) {
                    $model::create($item);
                }
                @unlink($tempFilePath);
            } catch (Exception $e) {
                @unlink($tempFilePath);
                throw new RuntimeException($e->getMessage());
            }
            return true;
        }

        return false;
    }

    /**
     * 导出excel.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function export(string $filename, array|Closure $closure, Closure $callbackData = null): ResponseInterface
    {
        $filename .= '.xlsx';
        is_array($closure) ? $data = &$closure : $data = $closure();

        $aligns = [
            'left' => Format::FORMAT_ALIGN_LEFT,
            'center' => Format::FORMAT_ALIGN_CENTER,
            'right' => Format::FORMAT_ALIGN_RIGHT,
        ];

        $columnName = [];
        $columnField = [];

        foreach ($this->property as $item) {
            $columnName[] = $item['value'];
            $columnField[] = $item['name'];
        }

        $tempFileName = 'export_' . time() . '.xlsx';
        $xlsxObject = new Excel(['path' => BASE_PATH . '/runtime/']);
        $fileObject = $xlsxObject->constMemory($tempFileName, 'Sheet1', false)->header($columnName);
        $columnFormat = new Format($fileObject->getHandle());
        $rowFormat = new Format($fileObject->getHandle());

        $index = 0;
        for ($i = 65; $i < (65 + count($columnField)); ++$i) {
            $columnNumber = chr($i) . '1';
            $fileObject->setColumn(
                sprintf('%s:%s', $columnNumber, $columnNumber),
                $this->property[$index]['width'] ?? mb_strlen($columnName[$index]) * 5,
                $columnFormat->align($this->property[$index]['align'] ? $aligns[$this->property[$index]['align']] : $aligns['center'])
//                    ->background($this->property[$index]['bgColor'] ?? Format::COLOR_WHITE)
//                    ->border(Format::BORDER_THIN)
                    ->fontColor($this->property[$index]['color'] ?? Format::COLOR_BLACK)
                    ->toResource()
            );
            ++$index;
        }

        // 表头加样式
        $fileObject->setRow(
            sprintf('A1:%s1', chr(65 + count($columnField))),
            20,
            $rowFormat->bold()->align(Format::FORMAT_ALIGN_CENTER, Format::FORMAT_ALIGN_VERTICAL_CENTER)
//                ->background(0x4ac1ff)->fontColor(Format::COLOR_BLACK)
//                ->border(Format::BORDER_THIN)
                ->toResource()
        );
        $exportData = [];
        foreach ($data as $item) {
            $yield = [];
            if ($callbackData) {
                $item = $callbackData($item);
            }
            foreach ($this->property as $property) {
                if (! empty($property['customField'])) {
                    $value = \Hyperf\Collection\Arr::get($item, $property['customField']);
                } elseif (! empty($property['path'])) {
                    $value = \Hyperf\Collection\data_get($item, $property['path']);
                } else {
                    $value = \Hyperf\Collection\Arr::get($item, $property['name']);
                }
                if (! empty($property['dictName'])) {
                    $value = $property['dictName'][$value];
                } elseif (! empty($property['dictData'])) {
                    $value = $property['dictData'][$value];
                }
                $yield[] = $value;
            }
            $exportData[] = $yield;
        }

        $response = container()->get(MineResponse::class);
        $filePath = $fileObject->data($exportData)->output();

        $response->download($filePath, $filename);

        ob_start();
        if (copy($filePath, 'php://output') === false) {
            throw new MineException('导出数据失败', 500);
        }
        $res = $this->downloadExcel($filename, ob_get_clean());
        @unlink($filePath);

        return $res;
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function bigExport(string $filename, Closure $closure): ResponseInterface
    {
        $filename .= '.xlsx';
        $columnName = [];
        foreach ($this->property as $item) {
            $columnName[] = $item['value'];
        }
        $tempFileName = 'export_' . time() . '.xlsx';
        $xlsxObject = new Excel(['path' => BASE_PATH . '/runtime/']);
        $fileObject = $xlsxObject->constMemory($tempFileName, 'Sheet1', false)->header($columnName);
        $closure($fileObject, $this->property);

        $response = container()->get(MineResponse::class);
        $filePath = $fileObject->output();

        $response->download($filePath, $filename);

        ob_start();
        if (copy($filePath, 'php://output') === false) {
            throw new MineException('导出数据失败', 500);
        }
        $res = $this->downloadExcel($filename, ob_get_clean());
        @unlink($filePath);

        return $res;
    }
}
