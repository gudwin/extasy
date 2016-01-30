<?
use \Faid\DB;
use \UsersLogin;
use \Extasy\Columns\Password as passwordColumn;
use \Extasy\Users\UsersModule;
use \Extasy\Users\login\controller\AuthorizationRequired;


class UserProfilePage extends AuthorizationRequired
{
    const UpdateProfileConfigPage = 'users.profile.configPage';
    /**
     * @var array Массив данных на парсинг
     */
    protected $aParse = array();

    protected $register = null;

    public function __construct($urlInfo = array())
    {
        parent::__construct($urlInfo);
        $this->addPost('change', 'change');
        $this->addGet('deleteProfile', 'deleteProfile');

        $this->register = new SystemRegister('Applications/users/');
    }

    public function deleteProfile($code)
    {
        $valid = $this->aProfile->password->getValue() == $code;
        if ($valid) {
            $this->aProfile->ban();
            UsersLogin::logout();
        }

        $this->addAlert('Профиль удален');
        $this->jump('/');
    }

    public function change()
    {
        $old_password = '';
        $password = '';
        // Если установлена проверка старого пароля
        if ($this->register->get('front-end')->ignore_password_field->value == 0) {

            $password = !empty($_POST['password']) ? $_POST['password'] : '';
            $old_password = !empty($_POST['old_password']) ? $_POST['old_password'] : '';

        } else {
            $old_password = $this->aProfile->password->getValue();
        }

        if (!empty($_POST['password'])) {
            $password = $_POST['password'];
            //
            try {
                $this->changePassword($old_password, $password);
            } catch (Exception $e) {
                $this->jumpBack();
            }
            try {

                $this->aProfile = UsersLogin::login($this->aProfile->login->getValue(), $password);
            } catch (Exception $e) {
                UsersLogin::logout();
                $this->addAlert('Ошибка при изменении пароля');
                $this->jump('/');
            }
            // Для того, чтобы смена пароля отработала корректно
            $old_password = $password;
        }
        // Если передан email, то заменяем его
        if (!empty($_POST['email'])) {
            $this->changeEmail($old_password, $_POST['email']);
        }
        if (!empty($_POST['login'])) {
            $this->changeLogin($old_password, $_POST['login']);
        }

        $oUser = UsersLogin::getCurrentSession();
        // Получаем список полей, которые запрещены к обновлению
        $fields = $this->register->get('front-end')->blocked_fields->value;
        $fields = explode(',', $fields);
        foreach ($_POST as $key => $row) {
            try {
                if (!(in_array($key, $fields))) {
                    $oUser->$key->setValue($row);
                }
            } catch (Exception $e) {

            }
        }
        if (!empty($_FILES)) {
            foreach ($_FILES as $key => $row) {
                try {
                    $oUser->$key->setValue('');
                } catch (Exception $e) {

                }
            }
        }
        $oUser->update();
        EventController::callEvent('users_after_update_profile', $oUser, $_POST);
        $this->addSuccess('updateProfile');
        if (!empty($_POST['redirectTo'])) {
            $this->jump($_POST['redirectTo']);
        }
        $this->jump('./');
    }


    public function showChangeForm()
    {
        $this->aParse['aMeta'] = UsersModule::getMeta(self::UpdateProfileConfigPage);
        $this->aParse['aMeta']['title'] .= ' - ' . $this->aProfile->login->getValue();
        $this->aParse['aProfile'] = $this->aProfile->getParseData();
        $this->output('users/profile/form', $this->aParse, array('get_profileform_data'));
    }

    public function main()
    {
        $this->showChangeForm();
    }

    protected function changePassword($oldpassword, $password)
    {
        $oUser = UsersLogin::getCurrentSession();

        $isOldPasswordSame = $this->checkPassword($oldpassword, $oUser);

        if (!$isOldPasswordSame) {
            $this->addValidationError('updatePassword');
            throw new Exception('old password incorrect');
        }

        $oUser->password->setValue($password);
        $oUser->update();
        $this->addSuccess('updatePassword');
    }

    protected function changeEmail($password, $email)
    {
        $email = \Faid\DB::$connection->real_escape_string($email);
        $oUser = UsersLogin::getCurrentSession();
        if ($email == $oUser->email->getValue()) {
            return;
        }
        if ($this->register->get('front-end')->ignore_password_field->value == 0) {
            $isPasswordSame = $this->checkPassword($password, $oUser);
            if (!$isPasswordSame) {
                $this->addValidationError('updatePassword');

                return;
            }
        }
        $sql = 'SELECT * FROM `%s` WHERE `email` = "%s" and `id` <> %d';
        $sql = sprintf($sql, USERS_TABLE, $email, $oUser->id->getValue());
        //
        $aGet = DB::get($sql);
        // Ого! уже есть акк с таким емейлом, навиду подстава! ;)
        if (!empty($aGet)) {
            $register = new SystemRegister('Applications/users/');
            $checkEmail = $register->registration_need_email->value;
            if (!empty($checkEmail)) {
                $this->addValidationError('updateEmail');

                return;
            }
        }
        $old_email = $oUser->email->getValue();
        $oUser->email->SetValue($email);
        $oUser->update();

        EventController::callEvent('users_after_update_email', $oUser, $old_email, $email);

        $this->addSuccess('updateEmail');
    }

    protected function changeLogin($password, $login)
    {
        $login = \Faid\DB::$connection->real_escape_string($login);
        $oUser = UsersLogin::getCurrentSession();
        if ($login == $oUser->login->getValue()) {
            return;
        }
        if ($this->register->get('front-end')->ignore_password_field->value == 0) {
            $isSamePassword = $this->checkPassword($password, $oUser);
            if (!$isSamePassword) {
                $this->addValidationError('updatePassword');

                return;
            }
        }
        $sql = 'SELECT * FROM `%s` WHERE `login` = "%s" and `id` <> %d';
        $sql = sprintf($sql, USERS_TABLE, $login, $oUser->id->getValue());
        //
        $aGet = DB::get($sql);
        // Ого! уже есть акк с таким емейлом, навиду подстава! ;)
        if (!empty($aGet)) {
            $this->addValidationError('updateLogin');

            return;
        }
        $old_login = $oUser->email->getValue();
        $oUser->login - setValue($login);
        $oUser->update();

        EventController::callEvent('users_after_update_login', $oUser, $old_login, $login);

        $this->addSuccess('updateLogin');
    }

    protected function addValidationError($key)
    {
        switch ($key) {
            case 'updatePassword':
                $this->AddAlert('ОШИБКА: старый пароль указан неверно');
                break;
            case 'updateEmail':
                $this->AddAlert('ОШИБКА: e-mail уже существует в базе');
                break;
            case 'updateLogin':
                $this->AddAlert('ОШИБКА: login уже занят');
                break;
        }
    }

    protected function addSuccess($key)
    {
        if (empty($this->aParse['aSuccess'])) {
            $this->aParse['aSuccess'] = array();
        }
        $this->aParse['aSuccess'][$key] = 1;
    }

    protected function checkPassword($password, $user)
    {
        return passwordColumn::hash($password) == $user->password->getValue();
    }
}

?>