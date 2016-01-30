<?php
/**
 * 
 * @author Gisma
 *
 */
class extasytestAdminLauncher extends adminPage {
	public function __construct( ) {
		parent::__construct( );
		$this->addPost( 'testId', 'test');
	}
	/**
	 * (non-PHPdoc)
	 * @see adminPage::main()
	 */
	public function main( ) {
		$list = extasyTestModel::selectAll();
		$title = 'Тестирование сайта';
		$path = array( 
				$title 			=> './index.php',
				);
		$linkList = array(
				'Стартовать тестирование'	=> array(
						'id'		=> 'launchTests',
						'value'		=> '#',
						),
				'Выделить все'				=> array(
						'id'		=> 'extasyTestLauncherSelectAll',
						'value'		=> '#'
						),
				'Быстрое добавление'   => './quick_add',
				'Отредактировать тесты'		=> './list.php'
				);
		$tableHeader = array(
				array('&nbsp',3),
				array( 'Оттестировано', 17),
				array( 'URL', 25),
				array( 'Комментарий', 25),
				array( 'Пояснение к ошибке', 30 )
				);
		
		$this->outputHeader( $path, $title );
		$design = CMSDesign::getInstance( );
		$design->decor->buttons( $linkList );

		$design->table->begin('testListTable');
		$design->table->header( $tableHeader );
		foreach ( $list as $row ) {
			$design->table->rowBegin( );
			$design->table->listCell( sprintf( '<input type="checkbox" name="id[]" class="testSelector" value="%d" />', $row->id->getValue() ));
			$design->table->listCell( sprintf( '<span class="testDate">%s</span><a href="#" class="testMe ui-icon ui-icon-refresh"></a>', $row->lastTestDate) );
			$design->table->cellBegin();
			?>
			<span>
				<?php print $row->url?>
				<a href="<?php print $row->url?>" target="_blank" class="targetUrl ui-icon ui-icon-extlink"><!--  --></a>
				</span>
				
				<div class="requestInfo">
					<span class="method"><?php print $row->obj_method->getViewValue()?></span>
					<?php
					$isPost = 1 == $row->method->getValue() ;
					if ( $isPost ):
						$postLabel = preg_replace( '\s', '', $row->parameters);
						if ( sizeof( $postLabel ) > 16 ) {
							$postLabel = mb_substr( $postLabel, 0, 16,'utf-8').'...';
						}
					?>
					<span class="params"><a href="#" class="moreParams"><?php print $postLabel?></a><a href="#" class="moreParams ui-icon ui-icon-zoomin"><!--  --></a></span>
					<div class="fullParams">
					<pre><?php print $row->parameters?></pre>
					</div>
					<?php endif?>
				</div>
			<?php 
			$design->table->cellEnd();
			$design->table->listCell( $row->comment );
			$design->table->listCell( $row->obj_lastResult->getAdminViewValue() );
			$design->table->rowEnd( );
		}
		$design->table->end( );
		 
		$this->outputFooter();
		$this->output( );
	}
	public function test( $testId ) {
		Trace::setDisabled();
		$test = new extasyTestModel();
		$found = $test->get( $testId );
			if ( $found ) {
				$test->execute();
				$result = array(
					'lastTestDate'  => $test->lastTestDate->getValue(),
					'testResult'	=> $test->obj_lastResult->getAdminViewValue( )
				);
				die( json_encode( $result ));
			} else {
 			throw new Exception( 'test model not found '); 
		}
		
		
	}
} 