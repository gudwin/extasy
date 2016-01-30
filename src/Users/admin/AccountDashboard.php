<?
namespace Extasy\Users\admin;

//************************************************************//
//                                                            //
//  Copyright (c) 2008 Ext-CMS                                //
//  Разработчик: Gisma (23.01.2009)                           //
//************************************************************//

use \UserAccount;
class AccountDashboard extends Page
{
    protected $aclActionList = array(
        UserAccount::PermissionName
    );
    const msgNewAccountCreated = 'Новый аккаунт: "%s" Успешно создан ';
    const msgUpdatedSuccessfully = 'Аккаунт пользователя "%s" успешно обновлен';

    public function __construct()
    {
        parent::__construct();
        $this->addGet('insert', 'insert');
        $this->AddGet('id', 'showAdminUpdate');
        $this->addPost('id,submit', 'update');
        $this->addPost('id,fieldName,action', 'ajaxEditColumn');
        $this->addPost('id,fieldnName,action,data', 'ajaxEditColumn');
    }

    /**
     *
     */
    protected function ajaxEditColumn($userId, $columnName, $action, $data = array())
    {
        $user = $this->getUser($userId);
        $column = $user->attr($columnName, true);
        $result = $column->ajaxCall($action, $data);
        print json_encode($result);
        die();
    }

    /**
     *   Метод выводит форму редактирования существующего аккаунта
     * @return
     */
    public function showAdminUpdate($id)
    {
        try {

            $oAccount = $this->getUser($id);

            // Готовим данные для вывода
            $szTitle = 'Просмотр и редактирование профиля "' . htmlspecialchars($oAccount->login) . '"';
            $aBegin = array(
                'Пользователи' => 'index.php',
                $szTitle => '#',
            );
            $tabSheets = array(
                array('id' => 'tab_main', 'title' => 'Авторизация'),
                array('id' => 'tab_additional', 'title' => 'Доп. данные'),
            );
            // Начало вывода
            $design = \CMSDesign::getInstance();
            $this->outputHeader($aBegin, $szTitle);
            $design->formBegin();
            $design->submit('submit', 'Сохранить');
            $design->tabs->sheetsBegin($tabSheets);
            $design->tabs->contentBegin($tabSheets[0]['id']);
            $design->tableBegin();

            // Обязательные поля
            $design->row2cell('Логин пользователя', $oAccount->login->getAdminFormValue());
            $design->row2cell('Зарегистрирован', $oAccount->registered->getCyrilicViewValue());
            $design->row2cell('Последняя активность',
                \DateHelper::getCyrilicViewValue($oAccount->last_activity_date->getValue()));
            $design->row2cell('Пароль', $oAccount->password->getAdminFormValue());
            $design->row2cell('Код подтверждения авторизации', $oAccount->confirmation_code->getAdminFormValue());
            $design->row2cell('E-mail', $oAccount->email->getAdminFormValue());
            $design->row2cell('Права пользователя', $oAccount->rights->getAdminFormValue());
            $design->row2cell('Время доступа', $oAccount->time_access->getAdminFormValue());

            $design->tableEnd();
            $design->tabs->contentEnd();

            $design->tabs->contentBegin($tabSheets[1]['id']);
            $design->tableBegin();
            \EventController::callEvent('users_admin_update_form', $oAccount, $design);
            $design->tableEnd();
            $design->tabs->contentEnd();
            $design->tabs->sheetsEnd();
            // Завершаем вывод

            $design->hidden('id', $oAccount->id->getValue());
            $design->hidden('typeName', UserAccount::ModelName);
            $design->submit('submit', 'Сохранить');
            $design->formEnd();
            $design->documentEnd();
            $design->end();
            $this->output();

        } catch (\Exception $e) {

            $this->addError('Профайл не был найден');
            $this->jumpBack();
        }
    }

    public function insert()
    {
        $user = new UserAccount();
        $user->login = uniqid('login_');
        $user->insert();
        $message = sprintf(self::msgNewAccountCreated, $user->login->getValue());
        \CMSLog::addMessage(__CLASS__, sprintf('New user - (%s,%d) ', $user->login->getValue(), $user->id->getValue()));
        $this->goToEditPage($message, $user->id->getValue());
    }

    public function update($id)
    {
        $aData = $_POST;
        $user = new UserAccount();
        $found = $user->get($id);
        if (empty($found)) {
            throw new \Exception("User not found");
        }
        \CMSLog::addMessage(__CLASS__, sprintf('User account `%s` updated', $user->login->getValue()));
        // Вызов проверки форм
        $result = \EventController::callFilter('users_admin_profile_check', $aData);
        if ($result) {
            $user->updateFromPost($result);
        }

        $msg = sprintf(self::msgUpdatedSuccessfully, htmlspecialchars($user->login->getValue()));
        $this->goToEditPage($msg, $user->id->getValue());
    }

    protected function getUser($id)
    {
        $oAccount = new UserAccount();
        $found = $oAccount->get($id);
        if (!$found) {
            throw new \Exception('Profile not found');
        }

        return $oAccount;
    }

    /**
     *
     */
    protected function goToEditPage($msg, $id)
    {
        $this->addAlert($msg);
        $goTo = sprintf('manage?id=%d', $id);
        $this->jump($goTo);
    }
}

?>