<?php
/**
 * Les fonctions principales des tiers.
 *
 * Le controlleur du modèle Third_Party_Model.
 *
 * @author    Eoxia <dev@eoxia.com>
 * @copyright (c) 2011-2018 Eoxia <dev@eoxia.com>.
 *
 * @license   AGPLv3 <https://spdx.org/licenses/AGPL-3.0-or-later.html>
 *
 * @package   WPshop\Classes
 *
 * @since     2.0.0
 */

namespace wpshop;

defined( 'ABSPATH' ) || exit;

/**
 * Third Party class.
 */
class Transfert_Data_Class extends \eoxia\Singleton_Util {

	protected function construct() {}
}

Transfert_Data_Class::g();
