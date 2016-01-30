<?
namespace Extasy\Columns {
	use \Faid\DB;
	use \DAO;

//************************************************************//
//                                                            //
//              DAO-тип    Text                               //
//       Copyright (c) 2006  ��� SmartDesign                  //
//               �����/������                                 //
//       Email: support@smartdesign.by                        //
//                                                            //
//  Разработчик: Gisma  (info@gisma.ru)                       //
//  Модифичирован:   2006.02.20 by Gisma                      //
//                                                            //
//************************************************************//
//  Модифичирован:   19.09.2008 by Gisma                      //
//  Удалена поддержка конфигурации required                   //
//************************************************************//

	class Text extends BaseColumn {
		function __construct( $szFieldName, $szFieldInfo, $Value ) {
			parent::__construct( $szFieldName, $szFieldInfo, $Value );
			if ( !empty( $this->fieldInfo[ 'no_rn' ] ) ) {
				$this->aValue = str_replace( "\n", '', $this->aValue );
			}
		}

		public function onAfterSelect( $dbData ) {
			if ( isset( $dbData[ $this->szFieldName ] ) ) {
				if ( !empty( $this->fieldInfo[ 'no_rn' ] ) ) {
					$this->aValue = str_replace( "\n", '', $this->aValue );
				} else {
					$this->aValue = $dbData[ $this->szFieldName ];
				}
			}
		}

		function getValue() {
			return $this->aValue;
		}

		function getViewValue() {
			return nl2br( htmlspecialchars( $this->aValue ) );
		}

		function getRN2BRViewValue() {
			return rn2br( htmlspecialchars( $this->aValue ) );
		}

		/**
		 * @desc Показывает формовый элемент
		 * @return
		 */
		function getFormValue() {
			if ( is_array( $this->fieldInfo ) && isset( $this->fieldInfo[ 'disabled' ] ) &&
				!empty( $this->aValue )
			) {
				return $this->getViewValue();
			}

			$view = new \Faid\View\View( __DIR__ . '/Views/textAdminForm.tpl' );
			$view->set( 'name', $this->szFieldName );
			$view->set( 'value', $this->aValue );
			$view->set( 'style', !empty( $this->fieldInfo[ 'style' ] ) ? $this->fieldInfo[ 'style' ] : '' );
			$view->set( 'class', !empty( $this->fieldInfo[ 'class' ] ) ? $this->fieldInfo[ 'class' ] : '' );
			$view->set( 'title',
						!empty( $this->fieldInfo[ 'title' ] ) ? $this->fieldInfo[ 'title' ] : $this->szFieldName );
			$view->set( 'formEdit', !empty( $this->fieldInfo[ 'form_edit' ] ) ? $this->fieldInfo[ 'form_edit' ] : '' );
			if ( !empty( $this->fieldInfo[ 'required' ] ) ) {
				$view->set( 'requiredField', true );
			}
			return $view->render();
		}


		public function onCreateTable( \Extasy\ORM\QueryBuilder $queryBuilder ) {
			$queryBuilder->addFields( sprintf( '`%s` text null', $this->szFieldName ) );
		}
	}
}
?>