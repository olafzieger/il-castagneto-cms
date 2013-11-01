/**
 * @version SVN: $Id: stuffer.js 456 2011-07-23 07:56:59Z elkuku $
 * @package    EasyCreator
 * @subpackage Javascript
 * @author     Nikolai Plath {@link http://www.nik-it.de}
 * @author     Created on 31-May-2010
 * @license GNU/GPL, see JROOT/LICENSE.php
 */

function createFile(type1, type2)
{
    document.id('type1').value = type1;
    document.id('type2').value = type2;

    submitbutton('create_install_file');
}//function

function addUpdateServer(name, url, type, priority)
{
	var container = document.id('updateServers');
	var div = new Element('div', {'style' : 'border: 1px dashed gray; padding: 0.4em; margin: 0.2em;'});
	var html = '';

	html += jgettext('Name')+': <input type="text" name="updateServers[name][]" value="'+name+'" /><br />';
	html += jgettext('URL')+': <input type="text" name="updateServers[url][]" value="'+url+'" /><br />';
	html += jgettext('Priority')+': <input type="text" size="2" name="updateServers[priority][]" value="'+priority+'" /> ';
	html += jgettext('Type')+': <input type="text" size="8" name="updateServers[type][]" value="'+type+'" /><br />';

	html += '<br /><span class="ecr_button" onclick="this.getParent().dispose();">';
	html += jgettext('Delete');
	html += '</span>';

	div.set('html', html);

	div.inject(container);
}//function

function submitStuffer(task)
{
    if (document.id('package-to') == null)
    {
        submitbutton(task);

        return;
    }

    var elements = document.id('package-to').getElements('li');

    var inserts = new Array();

    elements.each(function(el)
    {
        inserts.push(el.id);
    });

    document.id('packageElements').value = inserts.join(',');

    submitbutton(task);
}

window.addEvent('domready', function()
{
    var mySortables = new Sortables('#package-from, #package-to', {
        constrain : false,
        clone : true,
        revert : true
    });
});
