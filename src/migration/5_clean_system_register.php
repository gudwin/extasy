<?php
$register = new SystemRegister('/System/CMS');
$register->delete('Menu');
$register->delete('routes');

SystemRegisterSample::createCache();