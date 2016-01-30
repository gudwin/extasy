<?
use \Extasy\Model\Model as Model;
use \Faid\DBSimple;
use \Extasy\acl\ACLUser;
use \Extasy\Users\UsersModule;

class UserAccount extends Model
{
    const BlockLogName = 'Users.Blocked';
    const ModelName = 'users';
    const TableName = 'users';
    const ModelConfigureKey = 'Users.fieldsInfo';

    const PermissionName = 'Administrator/Users';

    const EmailRegExp = '/.+@.+\..+/i';

    const UpdateEmailConfigName = 'users.updateEmail';
    const UpdatePasswordConfigName = 'users.updatePassword';

    const DefaultAvatarPath = 'users/avatar-blank.jpg';

    public function __construct($data = array())
    {
        if (is_scalar($data)) {
            $data = array('id' => $data);
        }
        parent::__construct($data);
    }

    public function insert()
    {
        $avatarPath = self::DefaultAvatarPath;
        $result = parent::insert();
        $this->avatar = $avatarPath;
        $this->update();

        return $result;
    }

    /**
     *   Обновляет учетную запись
     * @return
     */
    public function update()
    {
        $granted = EventController::callFilter('users_account_before_update', true);
        // if granted then call standart update

        if ($granted) {
            $this->last_activity_date->setValue(date('Y-m-d H:i:s'));
            parent::update();
            EventController::callEvent('users_account_after_update');
            ACLUser::regenerateGuestCache($this);
        }
    }

    /**
     * @param $email
     */
    public function updateEmail($email)
    {
        $pattern = self::EmailRegExp;
        if (!preg_match($pattern, $email)) {
            throw new ForbiddenException('Not an email');
        }
        $found = UsersDBManager::findByEmail($email);
        if (!empty($found)) {
            throw new ForbiddenException('Email already used');
        }
        $this->email_confirmation_code->setValue(md5($this->id . time() . $email));
        $this->new_email->setValue($email);
        $this->update();
        //
        $this->sendUpdateEmail();
    }

    protected function sendUpdateEmail()
    {
        $data = [
            'email' => $this->new_email->getValue(),
            'email_confirmation_code' => $this->email_confirmation_code->getValue(),
        ];
        UsersModule::sendEmail( $data, self::UpdateEmailConfigName );

    }

    /**
     *   Удаляет учетную запись
     * @return
     */
    public function delete()
    {
        $data = UsersDBManager::get($this->id->getValue());
        if (!empty($data['persistent'])) {
            return;
        }
        $granted = EventController::callFilter('users_account_before_delete', $this);
        // if granted then call standart removing
        if (!empty($granted)) {
            parent::delete($granted);
        } else {
            return;
        }
        EventController::callEvent('users_account_after_delete', $this);
    }

    /**
     *   Обновляет дату последней активности
     * @return
     */
    public function updateLastActivityDate()
    {
        $this->last_activity_date->setValue(date('Y-m-d H:i'));
        $this->update();
        \Extasy\Users\login\LoginInfo::setupSession($this);
    }


    /**
     * Возвращает данные о текущей записи для отображения на шаблоне
     */
    public function getParseData()
    {
        if (empty($this->avatar->getViewValue())) {
            $this->avatar->copyFrom(FILE_PATH . self::DefaultAvatarPath);
            $this->update();
        }

        $result = array();
        $skip = ['password', 'confirmation_code', 'email_confirmation_code'];
        foreach ($this->columns as $key => $row) {
            if (!in_array($key, $skip)) {
                $result[$key] = $row->getViewValue();
            }
        }

        return $result;
    }

    public static function activateEmail($code)
    {
        $found = DBSimple::get(
            self::getTableName(),
            array(
                'email_confirmation_code' => $code
            )
        );
        if (empty($found)) {
            throw new NotFoundException('Confirmation code not found ');
        }
        $result = new UserAccount($found);
        $result->email->setValue($result->new_email->getValue());
        $result->new_email->setValue('');
        $result->email_confirmation_code->setValue('');
        $result->update();

        return $result;
    }

    /**
     *
     * @param string $login
     *
     * @return UserAccount
     */
    public static function getByLogin($login)
    {
        $data = UsersDBManager::getByLogin($login);
        $result = new UserAccount($data);

        return $result;
    }

    /**
     * @param $id
     *
     * @return UserAccount
     * @throws NotFoundException
     */
    public static function getById($id)
    {
        $data = UsersDBManager::get($id);
        $result = new UserAccount($data);

        return $result;
    }

    public static function getTableName()
    {
        return self::TableName;
    }

    /**
     * @return array
     */
    public static function getPrivateFields()
    {
        return array(
            'id',
            'login',
            'email',
            'password',
            'confirm_password',
            'email_confirmation_code',
            'last_activity_date',
            'vkontakte_id',
            'facebook_id',
            'rights'
        );
    }

    public function ban()
    {
        $this->confirmation_code = md5(time() - rand(0, 100000));
        $this->update();
    }

    public static function getFieldsInfo()
    {
        return \Faid\Configure\Configure::read(static::ModelConfigureKey);
    }
}

?>