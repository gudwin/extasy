<?php
namespace Extasy\errors {
    use \Extasy\Audit\Record;
    use \Extasy\Controllers\ErrorController;

    class Handlers
    {
        public static function onException($exception)
        {
            self::postLogRecord(get_class($exception), $exception->getMessage(), (string)$exception);
            try {
                $controller = new ErrorController();
                $controller->onException($exception);
            } catch (\Exception $e) {

            }

            die;
        }

        public static function onError($errno, $errstr, $errfile = '', $errline = '')
        {
            $fullMessage = self::getFullMessage($errfile, $errline, $errstr);

            self::postLogRecord('Error' . $errno, $errstr, $fullMessage);

            try {
                $controller = new ErrorController();
                $controller->onError($errno, $errstr, $errfile, $errline);
            } catch (\Exception $e) {

            }

            die;
        }


        public static function onFatalError($message, $file, $line)
        {
            $fullMessage = self::getFullMessage($file, $line, $message);

            self::postLogRecord('Fatal error', $message, $fullMessage);

            $error = sprintf('Fatal error "%s"  at : "%s:%d"', $message, $file, $line);
            $exception = new \InternalErrorException($error);
            try {
                $controller = new ErrorController();
                $controller->onException($exception);
            } catch (\Exception $e) {

            }

            die;
        }
        protected static function getFullMessage($file, $line, $message)
        {
            $fullMessage = static::formatErrorMessage($file, $line, $message);
            $fullMessage .= "\r\n";
            $fullMessage .= \Faid\Debug\defaultDebugBackTrace(true);

            return $fullMessage;
        }

        protected static function formatErrorMessage($errfile, $errline, $errstr)
        {
            return sprintf("%s\r\n[%s:%s]", $errstr, $errfile, $errline);
        }

        protected static function postLogRecord($category, $message, $fullMessage)
        {
            try {
                Record::add($category, $message, $fullMessage);
            } catch (\Exception $e) {

            }
        }
    }
}