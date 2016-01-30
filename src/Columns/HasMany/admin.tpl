<?php

foreach ( $values as $key=>$row ) {
	$checkbox = new CCheckbox();
	$checkbox->name = $name . '[]';;
	$checkbox->id = $name . '_' . $row['model']['id'];
	$checkbox->title = $row['model']['name'];
	$checkbox->value = $row['model']['id'];
	$checkbox->checked = $row['checked'];
	printf('<div>%s</div>', $checkbox);
}