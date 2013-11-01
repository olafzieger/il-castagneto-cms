/**
 * @version SVN: $Id: easycreator.js 418 2011-06-13 04:01:43Z elkuku $
 * @package		EasyCreator
 * @subpackage	Frontend-JavaScript
 * @author		Nikolai Plath {@link http://www.nik-it.de}
 * @author		Created on 24-Sep-2008
 */

function drawProject(project)
{
	frm = document.adminForm;
	frm.ebc_project.value=project;
	frm.submit();
}//function