<?php
use \Faid\DB;
class BasicDocumentTest extends PHPUnit_Framework_TestCase {
	public function setUp() {
		include dirname( __FILE__ ) . '/import.php';
	}
	/**
	 * В данном тесте мы проверяем, что метод бросит исключение, если произойдет обращение к неизвестному свойству документа
	 * @expectedException Exception
	 */
	public function testGetUnknownAttr() {
		
		$doc = new basicTestDocument();
		$doc->attr('unknown_field');
	}
	/**
	 * В данном тесте, я проверяю, что метод attr() вернет null, если поле существует в описании документа, но его значения нету в самом документе
	 */
	public function testGetEmptyAttr() {
		$doc = new basicTestDocument();
		$value = $doc->attr('name');
		$this->assertEquals($value->getValue(),null);
	}
	/**
	 * Проверяем, что значение колонки возвращается коррректно
	 */
	public function testGetAttr() {
		$document = new basicTestDocument(array(
			'name' => 'value1',
			'content' => 'value2',
			'id' => '2',
			));
		$this->assertEquals('value2',$document->attr('content'));
	}
	/**
	 * Получаем колонку как объект
	 */
	public function testGetAttrLikeObject() {
		$document = new basicTestDocument();
		$document->get(1);
		$nameField = $document->attr('name',true);
		$this->assertEquals($nameField->getValue(),'test name');	
	}
	/**
	 * Проверяем получение колонки документа, как обычное св-во объекта
	 */
	public function testGetAttrLikeProperty() {
		$document = new basicTestDocument();
		$document->get(1);
		$this->assertEquals($document->name,'test name');
	}
	/**
	 * Пробуем загрузить документ, информации о котором в бд нету
	 */
	public function testGetFailed() {
		$doc = new basicTestDocument();
		$result = $doc->get(-1);
		$this->assertEquals(false,$result);
	}
	/**
	 * Проверяем загрузку документа
	 */
	public function testGet() {
		$doc = new basicTestDocument();
		$doc->get(1);
		$this->assertEquals('test name',$doc->attr('name'));
		$this->assertEquals('test content',$doc->attr('content'));
	}
	/**
	 * Проверяем, что документ позволяет устанавливать ему свой-ства
	 */
	public function testSet() {
		$doc = new basicTestDocument();
		$doc->content = 'WOW';
		$this->assertEquals($doc->content,'WOW');
		
	}
	/**
	 * Проверяем, что документ добавляется в таблицу БД
	 */
	public function testInsert() {
		$doc = new basicTestDocument();
		$doc->name = 'new name';
		$doc->content = 'new content';
		$doc->insert();
		// Проверяем на уровне бд 
		$sql = 'select * from `basic_document` where `id`="%d" ';
		$sql = sprintf($sql,2);
		$result = DB::get($sql);
		$this->assertEquals($result['name'],'new name');
		$this->assertEquals($result['content'],'new content');
		// Проверяем количество рядов в БД (должно быть 2)
		$sql = 'select count(*) as `count` from `basic_document`';
		$result = DB::getField($sql,'count');
		$this->assertEquals($result,2);
	}
	/**
	 * Проверяем, что документ обновляет информацию о себе в таблице БД
	 */
	public function testUpdate() {
		$model = new basicTestDocument();
		$model->get(1);
		$model->name = 'name updated';
		$model->update();

		//
		$model2 = new basicTestDocument();
		$model2->get(1);
		// 
		$this->assertEquals($model2->name,'name updated');
		// А поле content должно оставаться неизменным
		$this->assertEquals($model2->content,'test content');
	}
	/**
	 * Проверяем, что документ удаляет себя из бд
	 */
	public function testDelete() {
		//
		$doc = new basicTestDocument();
		$doc->name = 'new name';
		$doc->content = 'new content';
		$newId = $doc->insert();
		//
		$doc->delete();
		// 
		// Проверяем количество рядов в таблице
		$sql = 'select count(*) as `count` from `basic_document` ';
		$result = DB::getField($sql,'count');
		//
		$this->assertEquals($result,1);
	}
	/** 
	 * Только что созданный документ при обращении к его индексу возвращает 0 
	 */
	public function testGetEmptyId() {
		$doc = new basicTestDocument();
		$this->assertEquals(0,$doc->getId());
	}
	/**
	 * Проверяем, что документ возвращает корректную информацию об индексе
	 */
	public function testGetId() {
		$doc = new basicTestDocument();
		$doc->get(1);
		$this->assertEquals(1,$doc->getId());
	}
	/**
	 * Проверяем, что после вставки в БД документ обновляет информацию о своем индексе
	 */
	public function testGetIdCorrentAfterInsert() {
		$doc = new basicTestDocument();
		$doc->name = 'name1';
		$doc->content = 'content2';
		$doc->insert();
		$this->assertEquals(2,$doc->getId());
	}
	/**
	 * Проверяем работу магического метода __isset
	 */
	public function testIsset() {
		$doc = new basicTestDocument();
		// Проверяем, что id всегда доступен, даже для пустого документа
		$this->assertEquals(true,isset($doc->id));
		$this->assertEquals(true,isset($doc->name));
		$this->assertEquals(false,isset($doc->unknown_field));
		// Проверяем, что после загрузки документа поле станет доступным
		$doc->get(1);
		$this->assertEquals(true,isset($doc->name));

	}
	/**
	 * Проверяем, что документ корректно возвращает имя схемы данных
	 */
	public function testGetModelName() {
		$doc = new basicTestDocument();
		$this->assertEquals(basicTestDocument::getModelName(),$doc->getModelName());
	}
	/**
	 * Проверяем, что документ возвращает все данные
	 */
	public function testGetData() {
		$doc = new basicTestDocument();
		$doc->get(1);
		$expectedData = array(
			'id' => '1',
			'name' => 'test name',
			'content' => 'test content',
			);
		$this->assertEquals($expectedData,$doc->getData());
	}
	/**
     *
	 */ 
	public function testSetData() {
		$doc = new basicTestDocument();
		$doc->get(1);
		$expectedData = array(
			'id' => '2',
			'name' => 'test name2',
			'content' => 'test content2',
		);
		$doc->setData($expectedData);
		// ничего не произойдет
		$doc->update();
		//
		$sql = 'select count(*) as `count` from `basic_document`';
		$count = DB::getField($sql,'count');
		$this->assertEquals(1,$count);


		// Проверяем, что предыдущий update ни к чему не привел, т.к. мы заменили id документа
		$doc->get(1);
		$this->assertEquals('1',$doc->getId());
		$this->assertEquals('test name',$doc->name->getValue());
		// 
		$doc->setData(array(
			'content' => 'its works!'
			));
		$doc->update();

		$doc = new basicTestDocument();
		$doc->get(1);

		//
		$doc2 = new basicTestDocument();
		$doc2->get(1);
		$this->assertEquals('its works!',$doc2->content->getValue());
	}
}
?>