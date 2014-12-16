<?php
namespace Pyz\Zed\Catalog\Component\Internal;

use ProjectA\Shared\Library\Filter\FilterChain;
use ProjectA\Shared\Library\Filter\SeparatorToCamelCaseFilter;
use ProjectA\Zed\Library\Dependency\DependencyFactoryInterface;
use ProjectA\Zed\Library\Dependency\DependencyFactoryTrait;
use ProjectA\Zed\Library\File\Filesystem\FilesystemFile;
use Pyz\Shared\Catalog\Code\ProductAttributeSetConstantInterface;

class ImportOptionsForAttributes implements
    DependencyFactoryInterface,
    ProductAttributeSetConstantInterface
{

    use DependencyFactoryTrait;

    const INDEX_KEY = 'SKU';

    const FILE_NAME_PREFIX = 'attribute_option_value_';
    const FILE_TYPE = '.csv';

    const CSV_DELIMITER = ";";
    const CSV_ENCLOSURE = '"';
    const CSV_ESCAPE = '\\';

    /**
     * @param $attributeSetName
     * @return array
     */
    public function getOptionsForAttributeMap($attributeSetName)
    {
        $stream = $this->getFileStream($attributeSetName);

        $tmpMap = array();
        if ($stream) {
            $stream = new \ProjectA_Zed_Library_Stream_Filter_RemoveEmpty($stream);
            $stream = $this->parseCsvToArray($stream);
            $tmpMap = iterator_to_array($stream);
        }

        $options = array();
        foreach ($tmpMap as $entry) {
            $attributeName = $entry[0][0];
            if (!isset($options[$attributeName])) {
                $options[$attributeName] = [];
            }
            $options[$attributeName][] = $entry[0][1];
        }

        return $options;
    }

    /**
     * @param $attributeSetName
     * @return FilesystemFile
     */
    protected function getFileStream($attributeSetName)
    {
        $filename = $this->getFileName($attributeSetName);
        $file = null;
        $directory = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'File' . DIRECTORY_SEPARATOR;
        if (is_file($directory . $filename)) {
            $file = new FilesystemFile($directory, $filename);
        }

        return $file;
    }

    /**
     * @param $attributeSetName
     * @return string
     */
    protected function getFileName($attributeSetName)
    {
        $filter = new FilterChain();
        $filter->addFilter(function ($string) {
            return str_replace(' ', '_', $string);
        });
        $filter->addFilter(new SeparatorToCamelCaseFilter('_', true));

        return self::FILE_NAME_PREFIX . $filter->filter($attributeSetName) . self::FILE_TYPE;
    }

    /**
     * @param \ProjectA_Zed_Library_Stream_String $stream
     * @return \ProjectA_Zed_Library_Stream_String_Parser
     */
    protected function parseCsvToArray(\ProjectA_Zed_Library_Stream_String $stream)
    {
        $csvParser = new \ProjectA_Zed_Library_Parser_Csv(self::CSV_DELIMITER, self::CSV_ENCLOSURE, self::CSV_ESCAPE);

        return new \ProjectA_Zed_Library_Stream_String_Parser($stream, $csvParser);
    }
}