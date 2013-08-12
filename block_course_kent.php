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
 * This is a a block that diplays all courses
 *
 * The block displays the shortname and fullname of every course
 * sorted in alphabetical order by shortname.
 *
 * @package    block_course_kent
 * @category   navigation
 * @copyright  2013 Andy Ekins
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class block_course_kent extends block_base {

    function init() {
        $this->title = get_string('pluginname', 'block_course_kent');
    }

    function has_config() {
        return false;
    }

    function applicable_formats() {
        return array('all' => true, 'my' => false, 'tag' => false);
    }

    function instance_allow_config() {
        return true;
    }

    // The following function gets all courses from the course db table and
    // displays them witin the block
    function get_content() {
      global $CFG, $DB, $OUTPUT;

        if ($this->content !== NULL) {
            return $this->content;
        }

        // This is the course icon displayed by every course  
	$icon = '<img src="'.$OUTPUT->pix_url('i/course') . '" class="icon" alt="" />';

        $this->content = new stdClass();

        // These are the parameters used withn the SQL statement
	$params = array(SITEID);
        $where = 'id != ?';
	$sort = 'shortname';

        // Testing to see if we bring any data back from the SQL statement.
        // If data returned it is put into the $courses array 
        if ($courses = $DB->get_records_select('course', $where, $params, $sort)) {
            // Start the Unordered list tag
            $list_contents = html_writer::start_tag('ul');
            // Iterate over each line in the $courses array
            foreach ($courses as $course) {
                // Create a string consisting of the course icon, the shortname,
                // and the fullname. Wrap this as a hyperlink to the course
                $url = new moodle_url('/course/view.php', array('id' => $course->id));
                $link = html_writer::tag('a',
                        $icon.format_string($course->shortname.' - '.$course->fullname),
                        array('href' => $url->out())
                );
                // Ensure it displays in a nice list format
                $li = html_writer::tag('li', $link);
                $list_contents .= $li;
            }
            // Close the Unordered list tag
            $list_contents .= html_writer::end_tag('ul');
        }

        // Ensure the contents is returned as text
        $this->content->text = $list_contents;

        // No footer to display.
        $this->content->footer = '';

        // Return the content object.
        return $this->content;
    }

    // Returns the role that best describes the blog menu block.
    public function get_aria_role() {
        return 'navigation';
    }
}
