<?php
/*
Plugin Name: Gettext override translations
Description: Lets you override default texts from your admin panel.
Version: 2.0.2
License: GPL2
Author: Ramon Fincken
Author URI: https://www.managedwphosting.nl/
*/

/*  Copyright 2015 Ramon Fincken

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

define( 'MP_GETTEXT_OVERRIDE_OPTION', 'mp_gettext_override_translations' );

if( is_admin() ) {
	require_once( 'php/backend.php' );
	$MP_Gettext_Override_Admin = new MP_Gettext_Override_Admin();
} else {
	require_once( 'php/frontend.php' );
	$MP_Gettext_Override = new MP_Gettext_Override();
}
