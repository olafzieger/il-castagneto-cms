/**
 * @version SVN: $Id: versions.js 412 2011-06-07 19:53:56Z elkuku $
 * @package EasyCreator
 * @subpackage Javascript
 * @author Nikolai Plath {@link http://www.nik-it.de}
 * @author Created on 11-Oct-2009
 * @license GNU/GPL, see JROOT/LICENSE.php
 */

function showVersion(number) {
    frm = document.adminForm;
    frm.selected_version.value = number;
    submitform('show_version');
}
