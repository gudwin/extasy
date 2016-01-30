<?php
namespace Extasy\tests\Bootstrap {
	class TestScript extends \SitemapController {

		public function process() {
			SitemapRouteTest::sitemapScriptCalled();
		}
	}

	return new TestScript();
}