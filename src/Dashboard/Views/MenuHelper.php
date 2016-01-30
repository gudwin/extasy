<?php
namespace Extasy\Dashboard\Views;

class MenuHelper {
    public function renderMenuItemsRecursive( $menuItems, $firstLevel = true ) {

        foreach ( $menuItems as $row ) {
            if ( !empty( $row['children'])) {
                printf('<li class="menu-item dropdown %s"><a href="#" class="dropdown-toggle" data-toggle="dropdown">%s%s</a>',
                    $firstLevel ? '' : 'dropdown-submenu',
                    $row['name'],
                    $firstLevel ? '<b class="caret"></b>' : ''
                );
                printf('<ul class="dropdown-menu">');
                $this->renderMenuItemsRecursive( $row['children'], false);
                printf('</ul></li>');
            } else {
                if ( !empty( $row['code'] )) {
                    print $row['code'];
                } else {
                    printf('<li><a href="%s" class="button">%s</a></li>',$row['link'],$row['name']);
                }

            }
        }
    }
} 