<?php
namespace Extasy\Schedule {
	use \Faid\DBSimple;

	class Job extends \Extasy\Model\Model {
		const ModelName = '\\Extasy\\Schedule\\Job';
		const TableName = 'schedule_job';

		const NewStatus      = 0;
		const ActiveStatus   = 1;
		const FinishedStatus = 3;
		const ErrorStatus    = 2;
		const CanceledStatus = 4;

		public function __construct( $initialData = array() ) {
			if ( empty( $initialData ) ) {
				$initialData[ 'dateCreated' ] = date( 'Y-m-d H:i:s' );
			}
			parent::__construct( $initialData );
		}

		public function run() {

			if ( self::NewStatus != $this->status->getValue() ) {
				throw new \LogicException( 'Only tasks with new status allowed' );
			}
			$this->status = self::ActiveStatus;
			$this->update();

			try {
				$this->action();
				$this->status = self::FinishedStatus;
			}
			catch ( \Exception $e ) {
				$this->status = self::ErrorStatus;
				throw $e;
			}
			finally {
				$this->update();
			}
		}

		protected function action() {

		}

		public function insert() {
			$condition = array(
				'class'  => $this->class->getValue(),
				'hash'   => $this->hash->getValue(),
				'status' => self::NewStatus
			);
			$found = DBSimple::get( self::TableName, $condition );

			if ( !empty( $found ) ) {
				$this->setData( $found );
			} else {
				parent::insert();
			}
		}
		public static function factory( $dbRow ) {
			$className = $dbRow[ 'class' ];
			$job       = new $className( $dbRow );
			return $job;
		}
		public static function factoryById( $id ) {
			$found = DBSimple::get( self::TableName, array(
				'id' => intval( $id )
			));
			if ( empty( $found )) {
				throw new \NotFoundException('Job not found');
			}
			return self::factory( $found );
		}

		public static function getFieldsInfo() {
			return array(
				'table'  => static::TableName,
				'fields' => array(
					'id'          => ['class' => '\\Extasy\\Columns\\Index','preview_field' => 1],
					'status'      => array(
						'class'         => '\\Extasy\\Columns\\StaticSelect',
						'values'        => array(
							self::NewStatus      => 'New',
							self::ActiveStatus   => 'Processing',
							self::ErrorStatus    => 'Error',
							self::FinishedStatus => 'Finished',
							self::CanceledStatus => 'Canceled'
						),
						'preview_field' => 1
					),
					'class'       => [ 'class' => '\\Extasy\\Schedule\\Columns\\ClassName', 'preview_field' => 1 ],
					'dateCreated' => [ 'class' => '\\Extasy\\Columns\\Datetime', 'preview_field' => 'getValue' ],
					'actionDate'  => [ 'class' => '\\Extasy\\Schedule\\Columns\\ActionDate', 'preview_field' => 'getValue' ],
					'hash'        => [ 'class' => '\\Extasy\\Columns\\Input', 'preview_field' => 1 ],
					'data'        => [ 'class' => '\\Extasy\\Columns\\Serializeable', ],
					'result'      => [ 'class' => '\\Extasy\\Columns\\Serializeable', ]
				)
			);
		}

	}
}