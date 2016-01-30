<?php
$register = new SystemRegister( '/System/CMS/routes/administrate');
$register->insert('audit','class://Extasy\\Audit\\Controllers\\Audit/startup');

SystemRegisterSample::createCache();