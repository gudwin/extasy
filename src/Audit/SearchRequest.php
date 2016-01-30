<?
namespace Extasy\Audit;

use \ForbiddenException;
use \DateHelper;
class SearchRequest {
	const OrderAsc = 'asc';
	const OrderDesc = 'desc';
	const FieldId = 'id';
	const FieldEventName = 'event';
	const FieldDate = 'date';
	const FieldIP = 'ip';
	const FieldUser = 'user_login';
	const MaxLimit = 1000;

	public $sort_by = 'id';

	public $order = 'desc';

	public $search_phrase = '';

	public $user = '';

	public $page = 0;

	public $limit = 50;

	public $date_from = '0000-00-00 00:00:00';

	public $date_to = '';
	public function __construct( ) {
		$this->date_to = date('Y-m-d H:i:s');
	}
	public function validate( ) {
		$this->validateDates();
		$this->validatePaging( );
		$this->validateSortBy( );
	}
	protected function validateDates( ) {
		$this->validateDate( $this->date_from );
		$this->validateDate( $this->date_to );
		if ( $this->date_from > $this->date_to ) {
			throw new ForbiddenException('Field `date_from` can`t be bigger than `date_to`');
		}
	}

	protected function validatePaging( ) {
		$this->limit = intval( $this->limit );
		$this->page  = intval( $this->page );
		if  ( self::MaxLimit < $this->limit ) {
			throw new ForbiddenException('Limit max border reached. Max limit value:' . self::MaxLimit);
		}
		if  ( 0 >= $this->limit ) {
			throw new ForbiddenException('Limit value can`t be empty or negative');
		}
		if ( $this->page < 0 ) {
			throw new ForbiddenException('Field `page` must have zero or positive value');
		}
	}
	protected function validateDate( $date ) {
		$valid = DateHelper::isCorrectDateTime( $date );
		if ( !$valid ) {
			throw new ForbiddenException('Failed to validate date');
		}
	}
	protected function validateSortBy( ) {
		$allowedFields = array(
			self::FieldId,
			self::FieldIP,
			self::FieldDate,
			self::FieldEventName,
			self::FieldUser
		);
	}
}