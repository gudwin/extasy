<?
printf("<ul>");
foreach ($map as $row ) {
	printf('<li><b>%s</b> - %s </li>', $row['title'], $row['value']);
}
printf('</ul>');
?>