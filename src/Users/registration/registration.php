<?
use \Faid\DB;
use \CConfig;
use \Extasy\Users\UsersModule;

class UsersRegistration
{
    const RegistrationPageConfigName = 'users.registration.pageConfig';
    const RegistrationAcceptedConfigName = 'users.registration.acceptedEmail';
    const RegistrationConfirmationConfigName = 'users.registration.confirmationEmail';

    public static function getConfirmationCode()
    {
        return md5(md5(time()) . rand(1, 1024));
    }

    public static function validateLogin($login)
    {
        if (!preg_match('#^[0-9A-Z\_\@\.\-]+$#i', $login)) {
            throw new Exception('Login incorrect');
        }
    }

    public static function validateEmail($email)
    {
        $register = new SystemRegister('Applications/users/');
        // Переменная-флаг, если true, то будет осуществляться проверка email на уникальность
        $checkEmail = $register->registration_need_email->value;

        if (!empty($email)) {
            $emailPattern = UserAccount::EmailRegExp;

            if (!preg_match($emailPattern, $email)) {
                throw new Exception('Email incorrect:' . $email);
            }
        } elseif (!empty($checkEmail)) {
            throw new Exception('Email incorrect:' . $email);
        }
    }

    public static function signup($login, $password, $email, $aData)
    {
        self::validateLogin($login);

        self::validateEmail($email);

        // Проверка данных
        EventController::callEvent('users_registration_check_data', $login, $password, $email, $aData);
        // Проверка существования логина и почты

        self::checkLoginOrEmailExists($login, $email);

        $register = new SystemRegister('Applications/users/front-end/');
        // Если требуется подтвержденте

        if ($register->account_confirmation->value) {
            // Код подтверждения
            $aData['confirmation_code'] = self::getConfirmationCode();
        }
        //
        $aData['login'] = $login;
        $aData['email'] = $email;

        $user = new UserAccount($aData);
        $user->obj_password->setValue($password);
        $user->insert();
        // Добавлени в базу
        $aData['id'] = $user->id->getValue();
        $aData['password'] = $password;
        // Вызов события
        EventController::callEvent('users_registration_after_signup', $aData, $user);

        //
        // Если требуется подтверждение
        if ($register->account_confirmation->value) {
            // Высылка письма
            self::sendEmailConfirmation( $aData );
        } else {
            UsersLogin::login($login, $password);

        }
        if ($register->account_registration_success_email->value) {
            // Высылка письма
            self::sendEmailRegistration( $aData );
        }


        return $aData['id'];
    }

    protected static function sendEmailConfirmation($aData)
    {
        UsersModule::sendEmail($aData, self::RegistrationConfirmationConfigName);
    }

    protected static function sendEmailRegistration($aData)
    {
        UsersModule::sendEmail($aData, self::RegistrationAcceptedConfigName);

    }

    public static function checkLoginOrEmailExists($login, $email)
    {
        $sql = 'SELECT * FROM `%s` WHERE STRCMP(`login`,"%s") = 0 or (LENGTH("%s") > 0 and STRCMP(`email`,"%s") = 0) ';
        $sql = sprintf($sql,
            USERS_TABLE,
            DB::escape($login),
            DB::escape($email),
            DB::escape($email));
        $aResult = DB::query($sql);
        if (!empty($aResult)) {
            throw new Exception('Login or email already registered. Login - ' . $login . ' Email:' . $email);
        }
    }

    protected static function insertIntoDB($aData)
    {
        $nId = UsersDBManager::insertProfile($aData['login'], $aData);

        return $nId;
    }


}

?>