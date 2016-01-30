<?php
namespace Extasy\Users\Columns {
    use \Faid\DB;
    use \UserAccount;
    use \DAO;
    use \Exception;

    class Username extends \Extasy\Columns\BaseColumn
    {
        function Insert(\Extasy\ORM\QueryBuilder $query)
        {
            $query->setSet($this->szFieldName, $this->aValue);
        }

        function Update(\Extasy\ORM\QueryBuilder $query)
        {
            $query->setSet($this->szFieldName, $this->aValue);
        }
        public function getUser() {
            if ( !empty( $this->aValue )) {
                $user = UserAccount::getById( $this->aValue );
                return $user->getParseData();
            }
            return null;
        }

        /**
         *
         * @param unknown $dbData
         */
        public function onAfterSelect($dbData)
        {
            if (isset($dbData[$this->szFieldName])) {
                $this->aValue = $dbData[$this->szFieldName];
            }
        }

        public function getViewValue()
        {
            try {
                $user = new UserAccount($this->aValue);
                $result = array(
                    'id' => $user->id->getValue(),
                    'login' => $user->login->getValue(),
                );

                return $result;

            } catch (Exception $e) {
                return array(
                    'id' => 0,
                    'login' => '',
                );
            }
        }

        public function getAdminViewValue()
        {
            try {
                $user = UserAccount::getById($this->aValue);
                $result = self::outputLink($user->id->getValue(), $user->login->getValue());

                return $result;
            } catch (Exception $e) {
                return 'Пользователя не определен';
            }
        }

        public function getAdminFormValue()
        {
            $control = new \CUserSelect();
            $control->name = $this->szFieldName;
            $control->current = $this->aValue;
            return $control->generate();
        }

        public static function outputLink($userId, $userLogin)
        {
            $result = sprintf('<a href="%susers/manage?id=%d" target="_blank">%s</a>',
                \Extasy\CMS::getDashboardWWWRoot(),
                $userId,
                $userLogin);

            return $result;
        }


        public function onCreateTable(\Extasy\ORM\QueryBuilder $queryBuilder)
        {
            $queryBuilder->addFields(sprintf('`%s` int not null default 0', $this->szFieldName));
            $queryBuilder->addFields(sprintf(' index `search_%s` (`%s`)', $this->szFieldName, $this->szFieldName));
        }
    }
}