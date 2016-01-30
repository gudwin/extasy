<?php
namespace Extasy\Api {
    use \Extasy\acl\ACLUser;
    use \Extasy\Audit\Record;
    use \UsersLogin;

    class ApiOperation
    {
        const EventName = 'Api.PrepareMethods';

        const responseJSON = 'json';

        const responseXML = 'xml';

        const responseJSONP = 'jsonp';

        const MethodName = '';

        const UploadsFolder = 'api/';

        protected $requiredParams = array();

        protected $requiredFiles = array();

        protected $optionalParams = array();

        protected $paramsData = array();

        protected $requiredACLRights = array();

        /**
         * @var Callable
         */
        protected $callback;

        public function __construct($data = array())
        {
            if (!empty($data)) {
                $this->setParamsData($data);
            }
        }

        public function __get($key)
        {
            return $this->getParam($key);
        }

        protected function checkACL()
        {
            if (!empty($this->requiredACLRights)) {
                try {
                    ACLUser::checkCurrentUserGrants($this->requiredACLRights);
                } catch (Exception $e) {
                    $errorMsg = sprintf('Failed to execute operation:%s. Current user - ',
                        self::MethodName,
                        UsersLogin::isLogined() ? sprintf('%s:%d', UsersLogin::getCurrentSession()->login->getValue(),
                            UsersLogin::getCurrentSession()->id->getValue()) : ''

                    );
                    Record::add('api', $errorMsg);
                    throw $e;
                }

            }
        }

        protected function checkRequiredParams()
        {
            // scan for required params
            foreach ($this->requiredParams as $row) {
                if (!isset($this->paramsData[$row])) {
                    $error = sprintf('Missing parameter: %s', $row);
                    throw new Exception($error);
                }
            }
        }

        protected function checkRequiredFiles()
        {
            // scan for required files
            foreach ($this->requiredFiles as $row) {
                if (!$this->checkFileExists($row)) {
                    $error = sprintf('Missing file: %s', $row);
                    throw new Exception($error);
                }
            }
        }

        public function exec()
        {
            $this->checkACL();
            $this->checkRequiredParams();
            $this->checkRequiredFiles();
            //
            $result = static::action();

            return $result;
        }

        public function match($methodName)
        {
            return 0 == strcmp(strtolower(static::MethodName), strtolower($methodName));
        }

        public function setParamsData($data)
        {
            $this->paramsData = $data;
        }

        public function setCallback($callback)
        {
            if (!is_callable($callback)) {
                throw new Exception('Callback not callable');
            }
            $this->callback = $callback;
        }

        protected function action()
        {
        }

        protected function getParam($paramName, $default = null)
        {
            if (isset($this->paramsData[$paramName])) {
                return $this->paramsData[$paramName];
            } else {
                return $default;
            }
        }

        protected function checkFileExists($fileKey)
        {
            $exists = !empty($_FILES['data'])
                && !empty($_FILES['data']['tmp_name'])
                && !empty($_FILES['data']['tmp_name'][$fileKey]);

            return $exists;
        }

        protected function getFileName($fileKey)
        {
            if (!$this->checkFileExists($fileKey)) {
                throw new \NotFoundException('File not found');
            }

            return $_FILES['data']['name'][$fileKey];
        }

        protected function acceptFile($fileKey)
        {
            if (!$this->checkFileExists($fileKey)) {
                throw new \NotFoundException('File not found');
            }
            // secure file name from
            $tmpPath = $_FILES['data']['tmp_name'][$fileKey];
            $isUploaded = is_uploaded_file($tmpPath);
            if (!$isUploaded) {
                throw new \ForbiddenException('Security error. File not uploaded, file key -' . $fileKey);
            }
            // secure filename
            $fileName = $_FILES['data']['name'][$fileKey];
            $fileName = str_replace(array('..', '\/'), array('', ''), $fileName);
            //
            $path = FILE_PATH . self::UploadsFolder . uniqid('attach') . '__' . $fileName;
            $operationResult = move_uploaded_file($tmpPath, $path);
            if (!$operationResult) {
                throw new \ForbiddenException('Failed to move file. File key - ' . $fileKey);
            }

            return $path;
        }
    }
}