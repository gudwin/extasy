<?php

namespace Extasy\Users\admin;


use Faid\DB;
use Faid\DBSimple;

class GroupPermissions extends \adminPage {
	const Limit = 100;
	protected $aclActionList = [\UserAccount::PermissionName ];
	public function __construct() {
		parent::__construct( );
		$this->addPost('group,permissions','update');
	}
	public function main() {
		$design  =\CMSDesign::getInstance();
		$title = 'Установка прав на группу';
		$begin = [
			'Пользователи' => '../index.php',
			$title => '#'
		];

		$this->outputHeader( $begin, $title );
		$design->header( $title );
		$design->formBegin();
		$design->tableBegin();
		$design->row2cell( 'Группа', $this->getGroupSelectbox());
		$design->row2cell('Права', $this->getGrantControl());
		$design->tableEnd();
		$design->submit('submit','Установить');
		$design->formEnd();
		$this->outputFooter();
		$this->output();
	}
	public function update( $group, $permissions ) {
		if ( empty( $group )) {
			$this->AddAlert('Группа пользователей не выбрана');
			$this->jump('./');
		}
		set_time_limit( 0 );
		$page = 0;

		$groupInfo = DBSimple::get( ACL_TABLE, ['name' => $group ]);

		if ( empty( $groupInfo )) {
			throw new \InvalidArgumentException(sprintf( 'Group "%s" not found, failed to update group permissions', htmlspecialchars($group )));
		}
		$permissions = $this->filterPermissionsByGroup( $group, $permissions );

		do {
			// получаем пользователей
			$sql = <<<SQL
	select u.* from %s as u
	inner join %s as r
	on r.entity = concat("%s", u.id)
	where r.actionId = "%s"
	order by id asc
	limit %d,%d
SQL;
			$sql = sprintf( $sql,
							\UserAccount::getTableName(),
				\ACL_GRANT_TABLE,
				\Faid\DB::escape( \UserAccount::ModelName ),
				$groupInfo['id'],
				$page * self::Limit,
				self::Limit);

			$data = DB::query( $sql );
			// устанавливаем каждому права
			foreach ( $data as $row ) {
				$user = new \UserAccount( $row );
				$tmp = $user->rights->getValue();
				$tmp = array_merge( $tmp, $permissions );
				$user->rights = $tmp;
				
				$user->update();
			}
			$page++;
		} while ( sizeof( $data) != 0 );
		$this->addAlert( 'Права обновлены');
		$this->jump( './');
	}
	protected function filterPermissionsByGroup($group, $permissions ) {
		$result = [];
		foreach ( $permissions as $name=>$value ) {
			$isSame = substr( $name, 0, strlen( $group )) == $group;
			if ( $isSame ) {
				$result[ $name ] = $value;
			}
		}
		return $result;
	}
	protected function getGroupSelectbox() {
		$rights = DBSimple::select(ACL_TABLE, 'parentId = 0','`fullPath` asc');
		foreach ( $rights as $key=>$row) {
			$rights[$key] = ['id' => $row['fullPath'],'name' => $row['fullPath'] ];
		}
		array_unshift( $rights, ['id' => 0, 'name' => 'Не выбрано']);
		$control = new \CSelect();
		$control->name = 'group';
		$control->values = $rights;
		return $control->generate();
	}
	protected function getGrantControl( ) {
		$control         = new \CACLGrant();
		$control->name   = 'permissions';

		$control->entity = '';
		return $control->generate();

	}
}
