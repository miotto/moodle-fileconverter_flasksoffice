ABOUT
==========
This is a tool that enables Moodle to use a separat Linux server with
LibreOffice for converting documents. For example, this is useful in
assignment submissions. In combination with a Linux Ubuntu server with
LibreOffice, submitted text documents, spreadsheets, and presentations
are automatically converted to PDF to simplify the grading workflow.

The plugin uses a Flask rest interface to a LibreOffice on a Linux
server. The installation and configuration of the Flask rest interface
is described in the associated repository.

It is based on the Google Drive document converter plugin.

This module may be distributed under the terms of the General Public License
(see http://www.gnu.org/licenses/gpl.txt for details)

PURPOSE
==========
An alternative document conversion plugin that makes use of Flask and LibreOffice.

INSTALLATION
==========
The Flask rest server document converter follows the standard installation procedure of file converters.

1. Create folder \<path to your moodle dir\>/files/converter/flasksoffice.
2. Extract files from downloaded archive to the created folder.
3. In Moodle: Visit Site administration -> PlugIns -> Additional Plugins -> Flask soffice -> Settings
4. Enter the URL of your Flask Server runnig soffice
5. Optional: To test plugin working properly: Click on "Test this converter is working properly." This will check the plugin. On the next page click on "Test document conversion" this will test a document conversion. If everything works properly a test PDF document is created/opened.

ENABLING THE CONVERTER
==========
Visit Site administration ► Plugins ► Document converters ► Manage document converters to enable the plugin

You will need to ensure that it is:

1. Configured to use a [Flask LibreOffice rest server](https://github.com/miotto/server_fileconverter_flasksoffice). A *new* alternative is a [FastAPI LibreOffice rest server](https://github.com/miotto/server_fileconverter_fastapisoffice).
2. Working by using the 'Test this converter is working properly' link on the settings page.
