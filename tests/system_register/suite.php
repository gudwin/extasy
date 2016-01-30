<?

class SystemRegisterAllTests
{
	public static function suite()
	{

		$suite = new PHPUnit_Framework_TestSuite('Property Project');
		$suite->addTestSuite('TestSystemRegisterManaging');
		return $suite;
		//$this->addTest('TestSystemRegisterManaging');
		//$this->addTestFile(dirname(__FILE__).'/select.php');
		/*,
			array(dirname(__FILE__).'/managing.php'),
			array(dirname(__FILE__).'/caching.php'),
			array(dirname(__FILE__).'/isset.php'),
			array(dirname(__FILE__).'/additional_property.php'));*/

	}
}
?>