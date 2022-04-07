<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Class for converting files between different file formats using flask rest server.
 *
 * @package    fileconverter_flasksoffice
 * @copyright  2020 Mirko Otto
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace fileconverter_flasksoffice;

defined('MOODLE_INTERNAL') || die();

use stored_file;
use moodle_exception;
use moodle_url;
use coding_exception;
use curl;
use \core_files\conversion;

/**
 * Class for converting files between different formats using flask rest server.
 *
 * @package    fileconverter_flasksoffice
 * @copyright  2020 Mirko Otto
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class converter implements \core_files\converter_interface {

    /** @var array $imports List of supported import file formats */
    private static $imports = [
        // Document file formats.
        'doc' => 'application/msword',
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'rtf' => 'application/rtf',
        'odt' => 'application/vnd.oasis.opendocument.text',
        'html' => 'text/html',
        'txt' => 'text/plain',
        // Spreadsheet file formats.
        'xls' => 'application/vnd.ms-excel',
        'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
        // Presentation file formats.
        'ppt' => 'application/vnd.ms-powerpoint',
        'pptx' => 'application/vnd.ms-powerpoint',
        'odp' => 'application/vnd.oasis.opendocument.presentation',
    ];

    /** @var array $export List of supported export file formats */
    private static $exports = [
        'pdf' => 'application/pdf'
    ];

    /**
     *
     * @var object Plugin configuration.
     */
    private $config;

    /**
     * @var string The base API URL
     */
    private $baseurl;

    /**
     * Class constructor
     */
    public function __construct() {
        $this->config = get_config('fileconverter_flasksoffice');

        if ($this->baseurl == null) {
            $this->baseurl = $this->config->flasksofficeurl;
        }
    }

    /**
     * Check if the plugin has the required configuration set.
     *
     * @param \fileconverter_flasksoffice\converter $converter
     * @return boolean $isset Is all configuration options set.
     */
    private static function is_config_set(\fileconverter_flasksoffice\converter $converter) {
        $iscorrect = true;

        if (empty($converter->config->flasksofficeurl)) {
            $iscorrect = false;
        }

        return $iscorrect;
    }

    /**
     * Whether the plugin is configured and requirements are met.
     *
     * @return  bool
     */
    public static function are_requirements_met() {
        $converter = new \fileconverter_flasksoffice\converter();

        // First check that we have the basic configuration settings set.
        if (!self::is_config_set($converter)) {
            debugging('fileconverter_flasksoffice configuration not set');
            return false;
        }

        return true;
    }


    /**
     * Convert a document to a new format and return a conversion object relating to the conversion in progress.
     *
     * @param   \core_files\conversion $conversion The file to be converted
     * @return  this
     */
    public function start_document_conversion(\core_files\conversion $conversion) {
        global $CFG;

        $file = $conversion->get_sourcefile();
        $contenthash = $file->get_contenthash();

        $originalname = $file->get_filename();
        if (strpos($originalname, '.') === false) {
            $conversion->set('status', conversion::STATUS_FAILED);
            return $this;
        }

        // Test server, if available.
        $curl = curl_init();
        $location = $this->baseurl;
        curl_setopt($curl, CURLOPT_URL, $location);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);
        $fserverrespond = curl_exec($curl);
        curl_close($curl);
        if ($fserverrespond != 'OK') {
            throw new coding_exception('The document conversion server is not accessible at the URL '.$location);
        }

        // Post/upload file to doc-server.
        $fs = get_file_storage();
        $filesystem = $fs->get_file_system();
        if ($filesystem->is_file_readable_locally_by_storedfile($file)) {
            $localpath = $filesystem->get_local_path_from_storedfile($file);
        } else if ($filesystem->is_file_readable_remotely_by_storedfile($file)) {
            $remotepath = $filesystem->get_remote_path_from_storedfile($file);
        } else if ($filesystem->is_file_readable_locally_by_storedfile($file, true)) {
            $localpath = $filesystem->get_local_path_from_storedfile($file, true);
        } else {
            $conversion->set('status', conversion::STATUS_FAILED);
            return $this;
        }

        $filepath = $localpath ?? $remotepath;
        $type = '';
        $filename = $file->get_filename();
        $contenthashf7 = substr($contenthash, 0, 7);
        $data = array('file' => curl_file_create($filepath, $type, $contenthashf7.$filename));

        $location = $this->baseurl . '/upload';
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $location);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: multipart/form-data'));
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        $response = curl_exec($curl);

        if (curl_errno($curl)) {
            $errormsg = curl_error($curl);
        }
        curl_close($curl);
        if (isset($errormsg)) {
            throw new coding_exception($errormsg);
        }

        $json = json_decode($response, true);
        if (!empty($json->error)) {
            throw new coding_exception($json->error->code . ': ' . $json->error->message . '. Response was: '.$response);
        }
        if (isset($json['result']['doc-conv-failed'])) {
            if ($json['result']['doc-conv-failed'] == 'TimeoutExpired') {
                $conversion->set('status', conversion::STATUS_FAILED);
                $conversion->update();
                return $this;
            }
        }
        if (!isset($json['result']['pdf']) OR is_null($json)) {
            throw new coding_exception('Response was: '.$response);
        }

        $strarray = explode('/', $json['result']['pdf']);
        $lastelement = end($strarray);

        // Download file from doc-server.
        $client = new curl();
        $sourceurl = new moodle_url($this->baseurl . $json['result']['pdf']);
        $source = $sourceurl->out(false);

        $tmp = make_request_directory();
        $downloadto = $tmp . '/' . ltrim($lastelement, $contenthashf7);

        $options = ['filepath' => $downloadto, 'timeout' => 15, 'followlocation' => true, 'maxredirs' => 5];
        $success = $client->download_one($source, null, $options);
        if ($client->errno != 0) {
            throw new coding_exception($client->error, $client->errno);
        }
        if ($success) {
            $conversion->store_destfile_from_path($downloadto);
            $conversion->set('status', conversion::STATUS_COMPLETE);
            $conversion->update();
        } else {
            $conversion->set('status', conversion::STATUS_FAILED);
        }

        // Trigger event.
        list($context, $course, $cm) = get_context_info_array($file->get_contextid());
        // Only it is related to a course. Config test excluded.
        if (!is_null($course)) {
            $eventinfo = array(
                'context' => $context,
                'courseid' => $course->id,
                'other' => array(
                    'sourcefileid' => $conversion->get('sourcefileid'),
                    'targetformat' => $conversion->get('targetformat'),
                    'id' => $conversion->get('id'),
                    'status' => $conversion->get('status')
                ));
            $event = \fileconverter_flasksoffice\event\document_conversion::create($eventinfo);
            $event->trigger();
        }

        return $this;
    }

    /**
     * Workhorse method: Poll an existing conversion for status update. If conversion has succeeded, download the result.
     *
     * @param   conversion $conversion The file to be converted
     * @return  $this;
     */
    public function poll_conversion_status(conversion $conversion) {

        // If conversion is complete or failed return early.
        if ($conversion->get('status') == conversion::STATUS_COMPLETE
            || $conversion->get('status') == conversion::STATUS_FAILED) {
            return $this;
        }
        return $this->start_document_conversion($conversion);
    }

    /**
     * Generate and serve the test document.
     *
     * @return  stored_file
     */
    public function serve_test_document() {
        global $CFG;
        require_once($CFG->libdir . '/filelib.php');

        $format = 'pdf';

        $filerecord = [
            'contextid' => \context_system::instance()->id,
            'component' => 'test',
            'filearea' => 'fileconverter_flasksoffice',
            'itemid' => 0,
            'filepath' => '/',
            'filename' => 'source.docx'
        ];

        // Get the fixture doc file content and generate and stored_file object.
        $fs = get_file_storage();
        $testdocx = $fs->get_file($filerecord['contextid'], $filerecord['component'], $filerecord['filearea'],
                $filerecord['itemid'], $filerecord['filepath'], $filerecord['filename']);

        if (!$testdocx) {
            $fixturefile = dirname(__DIR__) . '/tests/fixtures/source.docx';
            $testdocx = $fs->create_file_from_pathname($filerecord, $fixturefile);
        }

        $conversion = new \core_files\conversion(0, (object) [
            'targetformat' => 'pdf',
        ]);

        $conversion->set_sourcefile($testdocx);
        $conversion->create();

        // Convert the doc file to pdf and send it direct to the browser.
        $this->start_document_conversion($conversion);

        $testfile = $conversion->get_destfile();
        readfile_accel($testfile, 'application/pdf', true);
    }

    /**
     * Whether a file conversion can be completed using this converter.
     *
     * @param   string $from The source type
     * @param   string $to The destination type
     * @return  bool
     */
    public static function supports($from, $to) {
        // This is not a one-liner because of php 5.6.
        $imports = self::$imports;
        $exports = self::$exports;
        return isset($imports[$from]) && isset($exports[$to]);
    }

    /**
     * A list of the supported conversions.
     *
     * @return  string
     */
    public function get_supported_conversions() {
        $conversions = array(
            // Document file formats.
            'doc', 'docx', 'rtf', 'odt', 'html', 'txt',
            // Spreadsheet file formats.
            'xls', 'xlsx', 'ods', 'csv',
            // Presentation file formats.
            'ppt', 'pptx', 'odp',
            );
        return implode(', ', $conversions);
    }
}
