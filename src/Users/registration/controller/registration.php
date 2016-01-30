<?
use \Faid\DB;
use \SiteMapController;
use \UsersRegistration;
use \Extasy\Users\UsersModule;
//************************************************************//
//                                                            //
//               Контроллер регистрации                       //
//       Copyright (c) 2008 Ext-CMS (http://www.ext-cms.com/) //
//               отдел/сектор                                 //
//       Email:    info@gisma.ru (http://www.gisma.ru/)       //
//                                                            //
//  Разработчик: Gisma (26.01.2009)                           //
//  Модифицирован:  26.01.2009  by Gisma                      //
//                                                            //
//************************************************************//
//


class UsersRegistration_Registrate extends SiteMapController
{
    protected $layout = 'layout/default';

    protected $recaptchaPublicKey = '';
    protected $recaptchaPrivateKey = '';

    /**
     *
     */
    public function __construct($urlInfo = array())
    {
        parent::__construct($urlInfo);
        // confirmation page
        $this->addGet('code', 'confirm');
        $this->addPost('code', 'confirm');
        $this->addGet('email_confirmation_code', 'confirmEmail');

        // after regstration page
        $this->addGet('success', 'showSuccess');
        // submit page
        $this->AddPost('login,password,email', 'signup');

        $register = new SystemRegister('Applications/users');
        $captcha_provider = $register->captcha_provider->value;
        if ('recaptcha' == $captcha_provider) {
            require_once LIB_PATH . '3rdparty/recaptchalib.php';
            $config = \Faid\Configure\Configure::read('Recaptcha');
            $this->recaptchaPublicKey = $config['public_key'];
            $this->recaptchaPrivateKey = $config['private_key'];
        }
    }

    public static function testCaptcha($captcha = '', $captcha_provider = '')
    {
        $register = new SystemRegister('Applications/users');
        if (empty($captcha_provider)) {
            $captcha_provider = $register->captcha_provider->value;
        }

        switch ($captcha_provider) {
            case 'none':
                break;
            case 'recaptcha':
                /**
                 * @todo Избавиться от этой зависимости
                 */
                $recaptcha_challenge_field = isset($_POST['recaptcha_challenge_field']) ? $_POST['recaptcha_challenge_field'] : '';
                $recaptcha_response_field = isset($_POST['recaptcha_response_field']) ? $_POST['recaptcha_response_field'] : '';
                $validator = new \plugins\Recaptcha\Validator($recaptcha_challenge_field, $recaptcha_response_field);

                if (!$validator->isValid()) {
                    throw new \Exception('Incorrect captcha code');

                    return;
                }
                break;
            case 'kcaptcha':
                /**
                 * @todo Избавиться от этой зависимости
                 */
                require_once APPLICATION_PATH . 'kcaptcha/helper.php';
                if (kCaptchaHelper::check($captcha)) {
                } else {
                    // Некорректно
                    throw new \Exception('Incorrect captcha code');
                }

                break;
            //
            default:
                throw new Exception('CAPTCHA provider not found');
                break;

        }


    }

    /**
     * (non-PHPdoc)
     * @see Page::main()
     */
    public function main($aData = array(), $error = '')
    {
        if (UsersLogin::isLogined()) {
            $this->jump('/profile/');
        }

        $this->loadMeta();

        $this->aParse['postData'] = $aData;
        $this->aParse['aProfile'] = $aData;
        $this->aParse['signupFailed'] = htmlspecialchars($error);

        if (!empty($this->recaptchaPublicKey)) {
            $this->aParse['captcha'] = recaptcha_get_html($this->recaptchaPublicKey, $error);
        }

        $this->output('users/registration/form');
    }

    /**
     *
     */
    public function showSuccess()
    {
        $this->loadMeta();
        //
        $this->output('users/registration/success');
    }

    /**
     *
     * @param unknown $login
     * @param unknown $password
     * @param unknown $email
     *
     * @throws Exception
     */
    public function signup($login, $password, $email)
    {
        try {
            self::testCaptcha(isset($_POST['kcaptcha']) ? $_POST['kcaptcha'] : '');
            $userId = UsersRegistration::signup($login, $password, $email, $_POST);
            $user = \UserAccount::getById($userId);
            $avatar = $this->acceptFile('avatar');
            if (!empty($avatar)) {
                $user->avatar->copyFrom($avatar);
                $user->update();
                if (file_exists($avatar)) {
                    unlink($avatar);
                }
            }

            $this->jump('/signup/?success=1');
        } catch (Exception $e) {
            CMSLog::addMessage('registration', $e);
            $this->main($_POST, $e->getMessage());
        }
    }

    protected function acceptFile($fileKey)
    {
        $exists = !empty($_FILES)
            && !empty($_FILES)
            && !empty($_FILES[$fileKey])
            && empty($_FILES[$fileKey]['error']);

        if (!$exists) {
            return;
        }
        // secure file name from
        $tmpPath = $_FILES[$fileKey]['tmp_name'];
        $isUploaded = is_uploaded_file($tmpPath);
        if (!$isUploaded) {
            throw new \ForbiddenException('Security error. File not uploaded, file key -' . $tmpPath . print_r($_FILES,
                    true));
        }
        // secure filename
        $fileName = $_FILES[$fileKey]['name'];
        $fileName = str_replace(array('..', '\/'), array('', ''), $fileName);
        //
        $path = FILE_PATH . \Extasy\Api\ApiOperation::UploadsFolder . uniqid('attach') . '__' . $fileName;
        $operationResult = move_uploaded_file($tmpPath, $path);
        if (!$operationResult) {
            throw new \ForbiddenException('Failed to move file. File key - ' . $fileKey);
        }

        return $path;
    }

    protected static function getByCode($code)
    {
        $sql = 'SELECT * FROM `%s` WHERE STRCMP(`confirmation_code`,"%s") = 0';
        $sql = sprintf(
            $sql,
            USERS_TABLE,
            \Faid\DB::$connection->real_escape_string($code)
        );
        $modelData = DB::get($sql);
        if (!empty($modelData)) {
            return new UserAccount($modelData);
        } else {
            return null;
        }
    }

    /**
     *
     * @param unknown $code
     */
    public function confirm($code)
    {
        if (!empty($code)) {
            $oAccount = self::getByCode($code);
        }

        if (!empty($oAccount)) {
            $oAccount->confirmation_code = '';
            $oAccount->update();
            UsersLogin::forceLogin($oAccount);
            EventController::callEvent('users_registration_after_confirm', $oAccount);
            $bFailed = false;

            $this->addAlert('Аккаунт активирован');

            $this->jump('/');
        } else {
            $bFailed = true;
        }
        $this->aParse['pageMeta'] = UsersModule::getMeta( UsersRegistration::RegistrationConfigName );
        $this->aParse['isCodeEmpty'] = empty($code);
        $this->aParse['bFailed'] = $bFailed;

        $this->output('users/registration/confirm');
    }

    /**
     *
     * @param unknown $code
     */
    public function confirmEmail($code)
    {
        try {
            $acc = UserAccount::activateEmail($code);
            UsersLogin::logout();
            UsersLogin::forceLogin($acc);
            $this->set('success', '1');

        } catch (Exception $e) {
            $this->set('error', $e->getMessage());
        }
        $this->output('users/registration/confirmEmail');
    }

    protected function loadMeta()
    {

        require_once SETTINGS_PATH . 'users/registration/page.cfg.php';
        $this->aParse['aMeta'] = array(
            'title' => PAGE_TITLE,
            'description' => PAGE_DESCRIPTION,
            'keywords' => PAGE_KEYWORDS,

        );
    }

}

?>