<?php
$register = new SystemRegister('System/CMS');
$register->delete('display_administrate_scripts');

SystemRegisterSample::clearCache();