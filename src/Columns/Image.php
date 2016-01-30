<?php
namespace Extasy\Columns {
    use \Faid\UParser, \Faid\DB, \Faid\debug;
    use \CMS_log;
    use \DAO;
    use \DAO_FileSystem;
    use \DAO_Image;
    use \SystemRegister;
    use \ForbiddenException;
    use \NotFoundException;
    use \imageHelper;
    use \DAO_Exception;
    use \Exception;
    use \Faid\Configure\Configure;

//************************************************************//
//                                                            //
//                 Элемент Image                              //
//       Copyright (c) 2006  ООО SmartDesign                  //
//               отдел/сектор                                 //
//       Email: support@smartdesign.by                        //
//                                                            //
//  Разработчик: Gisma 2006.02.20                             //
//  Модифицирован:  08.09.2006  by Gisma                      //
//                                                            //
//************************************************************//

    class Image extends BaseColumn
    {
        const thumbWidth = 160;
        const thumbHeight = 90;
        var $image;

        var $bDelete;
        /**
         * Хранит значение максимального размера нового обрабатываемого файла
         * @var float
         */
        protected $maxFileSize = 0;

        /**
         *
         * @param unknown $szFieldName
         * @param unknown $fieldInfo
         * @param unknown $Value
         */
        public function __construct($szFieldName, $fieldInfo, $Value)
        {
            self::validateFieldInfo($fieldInfo, $szFieldName);

            // after initialization config will be always array
            $fieldInfo['images'] = self::returnImagesArray($fieldInfo['images']);

            parent::__construct($szFieldName, $fieldInfo, $Value);

            $this->image = DAO_Image::getInstance();

            if (!empty($_REQUEST[$this->szFieldName . '_delete'])) {
                $this->bDelete = true;
            }

            $register = new SystemRegister('System/columns/image/');
            // Сразу конвертируем в байты из мегабайт
            $this->maxFileSize = floatval($register->max_size->value) * 1024 * 1024;
        }

        /**
         * (non-PHPdoc)
         * @see \Extasy\Columns\BaseColumn::onInsert()
         */
        public function onInsert(\Extasy\ORM\QueryBuilder $query)
        {
            $this->loadFromFiles();
            $query->setSet($this->szFieldName, $this->aValue);
        }

        /**
         * (non-PHPdoc)
         * @see \Extasy\Columns\BaseColumn::onAfterInsert()
         */
        public function onAfterSelect($dbData)
        {
            if (isset($dbData[$this->szFieldName])) {
                $this->aValue = $dbData[$this->szFieldName];
            }
        }

        public function copyFrom($newValue)
        {
            if (!file_exists($newValue)) {
                throw new NotFoundException('File not found');
            }
            if (!is_file($newValue) && !is_readable($newValue)) {
                throw new ForbiddenException('Not a file or non-readable file:' . $newValue);
            }
            $isEmpty = empty($this->document->id->getValue());
            if ($isEmpty) {
                throw new \RuntimeException('Impossible to copy image file. Document must have an id. May be you forgot to insert model into database first ?');
            }
            $relativePath = $this->fieldInfo['base_dir'] . $this->document->id->getValue();
            $result = copy($newValue, FILE_PATH . $relativePath);
            if (!$result) {
                throw new ForbiddenException('Failed to copy file');
            }
            $this->processNewFile($relativePath);
        }

        /**
         * (non-PHPdoc)
         * @see \Extasy\Columns\BaseColumn::onUpdate()
         */
        public function onUpdate(\Extasy\ORM\QueryBuilder $query)
        {

            if ($this->bDelete) {
                $this->onDelete($query);
            }
            $this->loadFromFiles();
            $query->setSet($this->szFieldName, $this->aValue);
        }

        /**
         * (non-PHPdoc)
         * @see \Extasy\Columns\BaseColumn::onDelete()
         */
        public function onDelete(\Extasy\ORM\QueryBuilder $queryBuilder)
        {
            $file = $this->aValue;
            if (empty($file)) {
                return;
            }
            $filePath = FILE_PATH . $file;
            $canBeDeleted = file_exists($filePath) && is_file($filePath);
            if ($canBeDeleted) {
                unlink($filePath);
            }
        }

        /**
         * (non-PHPdoc)
         * @see \Extasy\Columns\BaseColumn::getFormValue()
         */
        public function getFormValue()
        {
            // Удалил старую форму
        }

        /**
         * (non-PHPdoc)
         * @see \Extasy\Columns\BaseColumn::getAdminFormValue()
         */
        public function getAdminFormValue($onlyResizes = false)
        {
            static $librariesLoaded = false;
            $nId = $this->document->id->getValue();
            if (!empty($nId)) {
                $baseUrl = \Extasy\CMS::getFilesHttpRoot() . $this->getSrc();
                $filePath = \Extasy\CMS::getFilesPath() . $this->getSrc();
                $aPlaceholder = array();
                $aParse = array();
                if ($this->imageExists()) {
                    $aSize = getimagesize($filePath);
                    $aParse[] = array(
                        'url' => $baseUrl,
                        'size_x' => $aSize[0],
                        'size_y' => $aSize[1],
                        'basename' => 'default',
                    );

                    // Если передан массив images
                    $bIsArray = isset($this->fieldInfo['images']) && is_array($this->fieldInfo['images']);
                    if ($bIsArray) {
                        foreach ($this->fieldInfo['images'] as $key => $value) {
                            $value = explode('x', $value);
                            if (sizeof($value) == 1) {
                                $w = $h = $value[0];
                            } else {
                                list($w, $h) = $value;
                            }
                            $imageUrl = imageHelper::getTimthumbUrl($baseUrl, $w, $h) . '&rand=' . rand();
                            $aParse[] = array(
                                'size_x' => $w,
                                'size_y' => $h,
                                'url' => $imageUrl,
                                'basename' => $key
                            );
                        }
                    }
                    // setup thumbnail
                    $aPlaceholder['thumbnailSrc'] = imageHelper::getTimthumbUrl($baseUrl,
                            self::thumbWidth,
                            self::thumbHeight) . '&rand=' . rand();
                } else {
                    $aSize = null;
                }
                $aPlaceholder['aList'] = $aParse;
            }
            $aPlaceholder['name'] = $this->szFieldName;
            $aPlaceholder['value'] = $this->aValue;
            $aPlaceholder['librariesLoaded'] = $librariesLoaded;
            $aPlaceholder['required'] = !empty($this->fieldInfo['required']);
            $aPlaceholder['title'] = !empty($this->fieldInfo['title']) ? $this->fieldInfo['title'] : '';
            if ($onlyResizes) {
                $template = __DIR__ . DIRECTORY_SEPARATOR . 'image/resizes.tpl';
            } else {
                $template = __DIR__ . DIRECTORY_SEPARATOR . 'image/formAdmin.tpl';
            }
            $szResult = UParser::parsePHPFile($template, $aPlaceholder);
            $librariesLoaded = true;

            return $szResult;
        }

        /**
         * @desc Проверяем существует ли указанное изображение
         */
        public function imageExists()
        {
            if (!empty($this->aValue)) {
                return file_exists(FILE_PATH . $this->aValue);
            }

            return false;
        }

        public function getSrc()
        {
            return $this->getImageSrc();
        }

        /**
         * Returns image source if it exists
         *
         * @param string $name
         */
        function getImageSrc()
        {
            // если файл не существует, можем заменить на что-нибудь дефолтвое
            if (!$this->imageExists()) {
                // trying to load default image
                if (!empty($this->fieldInfo['default'])) {
                    $defaultPath = $this->fieldInfo['default'];
                    if (file_exists(FILE_PATH . $defaultPath)) {
                        return $defaultPath;
                    }
                }
            } else {
                return $this->aValue;
            }

            return null;
        }

        public function getBaseUrl()
        {
            return \Extasy\CMS::getFilesHttpRoot() . $this->GetSrc();
        }

        /**
         * Returns hash-array with full information about image and all his resizes including html code, url, image sizes
         * @throws Exception
         * @return array
         */
        public function makeViewElement()
        {
            $nId = $this->document->id->getValue();
            $baseUrl = $this->getBaseUrl();
            $path = FILE_PATH . $this->getSrc();

            $result = array();

            // proceed only if file exists
            if ($this->imageExists()) {
                // значит добавляем инфо об иконке
                $result['base_src'] = $result['_base_src'] = $baseUrl;
                // load base image sizes
                $aInfo = getimagesize($path);
                $result['_base_width'] = $aInfo[0];
                $result['_base_height'] = $aInfo[1];

                // add thumbnail
                $result['thumbnail_src'] = imageHelper::getTimthumbUrl($baseUrl,
                    self::thumbWidth,
                    self::thumbHeight);
                $result['thumbnail_width'] = self::thumbWidth;
                $result['thumbnail_height'] = self::thumbHeight;
                $i = 1;
                // Если передан массив images
                $bIsArray = isset($this->fieldInfo['images']) && is_array($this->fieldInfo['images']);
                if ($bIsArray) {
                    foreach ($this->fieldInfo['images'] as $key => $value) {
                        if (empty($value)) {
                            continue;
                        }
                        $value = explode('x', $value);
                        if (sizeof($value) == 2) {
                            list($w, $h) = $value;
                        } else {
                            $w = $h = $value[0];
                        }
                        $thumbHttpPath = imageHelper::getTimthumbUrl($baseUrl, $w, $h);
                        $result[$key . '_src'] = $thumbHttpPath;
                        $result[$key . '_width'] = $w;
                        $result[$key . '_height'] = $h;
                    }
                }
            } else {
                $result = array();
            }

            return $result;
        }

        /**
         * (non-PHPdoc)
         * @see \Extasy\Columns\BaseColumn::getViewValue()
         */
        public function getViewValue()
        {
            return $this->makeViewElement();
        }

        /**
         * (non-PHPdoc)
         * @see \Extasy\Columns\BaseColumn::getAdminViewValue()
         */
        public function getAdminViewValue()
        {
            $viewValue = $this->makeViewElement();

            if (!empty($viewValue)) {
                $src = imageHelper::getTimthumbUrl($viewValue['_base_src'], self::thumbWidth, self::thumbHeight);
                $result = sprintf('<img src="%s" title="thumbnail"/>', $src);
            } else {
                $result = '';
            }

            return $result;
        }

        public function ajaxCall($action, $params)
        {
            $result = array();
            if ($action == 'upload') {
                // upload image
                $this->loadFromFiles();
                // return image path
                $this->document->update();
                $result['imagePath'] = imageHelper::getTimthumbUrl(\Extasy\CMS::getFilesHttpRoot() . $this->aValue,
                    self::thumbWidth,
                    self::thumbHeight);
                $result['resizes'] = $this->getAdminFormValue(true);
                $result['value'] = $this->aValue;
            } elseif ($action == 'clear') {
                $query = new \Extasy\ORM\QueryBuilder('delete');
                $this->onDelete( $query );
            } else {
                throw new Exception('Unknown action `' . $action . '`');
            }

            return $result;
        }

        public function setValue($newValue)
        {
            if (!is_string($newValue)) {
                throw new ForbiddenException('Incorrect value type, must be string');
            }
            $this->aValue = $newValue;
        }

        /**
         *
         * @param unknown $fieldInfo
         * @param string $fieldName
         *
         * @throws DAO_Exception
         */
        private static function validateFieldInfo($fieldInfo, $fieldName = false)
        {
            if (empty($fieldInfo['base_dir'])) {
                throw new DAO_Exception('parameter `base_dir` not defined', $fieldName);
            }
            // Проверяем существование ключа images
            if (!isset($fieldInfo['images'])) {
                throw new DAO_Exception('DAO_TImage parameter `images` not defined', $fieldName);
            }
        }


        /**
         *
         * @param unknown $images
         *
         * @return array
         */
        private static function returnImagesArray($images)
        {
            if (!is_array($images)) {
                if (!empty($images)) {
                    return explode(';', $images);
                } else {
                    return array();
                }
            }

            return $images;


        }

        /**
         *
         * @throws Exception
         * @throws DAO_Exception
         */
        protected function loadFromFiles()
        {
            // key for check file upload record in $_FILES array
            $fileKey = $this->szFieldName . '_file';
            $isUpload = !empty($_FILES[$fileKey]);
            // if movie uploaded
            if ($isUpload) {
                // build relative path and absolute
                $relativePath = $this->fieldInfo['base_dir'] . $this->document->id->getValue();
                $newFilePath = FILE_PATH . $relativePath;
                // Check mime-type

                $fs = DAO_FileSystem::getInstance();
                if ($fs->upload($fileKey, $newFilePath, $this->maxFileSize)) {
                    $this->processNewFile($relativePath);
                } else {
                }
            }
        }

        protected function processNewFile($relativePath)
        {
            $filePath = FILE_PATH . $relativePath;
            if (!file_exists($filePath)) {
                throw new \ForbiddenException('File not found');
            }
            $fs = DAO_FileSystem::getInstance();
            $imageInfo = getimagesize($filePath);
            $isValidImage = !empty($imageInfo) && preg_match('#jpeg|gif|png#', $imageInfo['mime']);
            if (!$isValidImage) {
                $error = sprintf('Only next image types supported: image/jpeg, image/gif, image/png.');

                $this->aValue = '';
                $fs->delete($filePath);
                throw new \InvalidArgumentException($error);
            }

            try {
                $extension = $this->getExtension($filePath);
            } catch (Exception $e) {
                $fs->delete($filePath);
                throw $e;
            }
            // Создаем копию с расширением
            $fs->copy($filePath, $filePath . $extension);
            $fs->delete($filePath);
            $this->aValue = $relativePath . $extension;
            if (!empty($this->fieldInfo['sign'])) {
                $signPath = FILE_PATH . $this->fieldInfo['sign'];
                imageHelper::addSignToImage($this->aValue, $signPath);
            }
        }

        /**
         * Detects image extension
         *
         * @param $imagePath
         */
        protected function getExtension($imagePath)
        {
            $aInfo = getimagesize($imagePath);
            $extensionType = $aInfo[2];
            switch ($extensionType) {
                case 1:
                    $result = 'gif';
                    break;
                case 2:
                    $result = 'jpg';
                    break;
                case 3:
                    $result = 'png';
                    break;
                case 4:
                    $result = 'swf';
                    break;
                case 5:
                    $result = 'psd';
                    break;
                case 6:
                    $result = 'bmp';
                    break;
                case 7:
                    $result = 'tiff';
                    break;
                case 8:
                    $result = 'tiff';
                    break;
                case 9:
                    $result = 'jpc';
                    break;
                case 10:
                    $result = 'jp2';
                    break;
                case 11:
                    $result = 'jpx';
                    break;
                case 12:
                    $result = 'jb2';
                    break;
                case 13:
                    $result = 'swc';
                    break;
                case 14:
                    $result = 'iff';
                    break;
                case 15:
                    $result = 'wbmp';
                    break;
                case 16:
                    $result = 'xbm';
                    break;
                default:
                    $error = sprintf('imageColumn:: Uknown extension - "%s". File passed - "%s"',
                        $aInfo[2],
                        $this->szFieldName);
                    throw new DAO_Exception($error, $this->szFieldName);
                    break;
            }

            if (!empty($result)) {
                return '.' . $result;
            } else {
                return '';
            }
        }

        public function onCreateTable(\Extasy\ORM\QueryBuilder $queryBuilder)
        {
            $queryBuilder->addFields(sprintf('`%s` varchar(255) not null default ""', $this->szFieldName));
        }
    }

}
?>