<?php
require_once('config.php');

$course = get_course(1);
$courses = get_courses();
$datas = $handler->get_instance_data($courseid, true);

$coursele = new \core_course_list_element($course);

if ($coursele->has_custom_fields()) {

   $fields = $courseele->get_custom_fields();

   foreach ($fields as $key => $value) {

      echo '<p>key: '.$key.' = '.$value.'</p>';

   }

} else {
  echo '<p>no custom fields found.</p>';

  echo '<p>Courses:</p><pre>',print_r($courses),'</pre>';
}






// $chelper = new \coursecat_helper();

// // when COURSECAT_SHOW_COURSES_EXPANDED is set, the option 'customfields' is set to true, which efficiently populates these fields
// $chelper->set_show_courses(self::COURSECAT_SHOW_COURSES_EXPANDED)->set_courses_display_options(['recursive' => true]);

// echo '<p>chelper:</p><pre>',print_r($chelper),'</pre>';

// exit;

// $courses = \core_course_category::top()->get_courses($chelper->get_courses_display_options());

// // Filter out courses from the iterator when they have a custom field called 'invisible' set to the value '1' (e.g. a checkbox is ticked).
// foreach ($courses as $index => $course) {

//   echo '<p>Course:</p><pre>',print_r($course),'</pre>';

//   foreach ($course->customfields as $f) {
//     // if ($f->get_field()->get('shortname') === 'invisible' && $f->get_value() === '1') {
//     //   unset($courses[$index]);
//     // }
//     echo '<p>Custom field: '.$f->get_field()->get('shortname').' = '.$f->get_value().'</p>';
//   }
// }








// $courses = get_courses();

// function get_course_metadata($courseid) {
//     $course = get_course($courseid);
//     $courseelement = new \core_course_list_element($course);

//     echo '<p>Course:</p><pre>',print_r($course),'</pre>';
//     echo '<p>Course Element:</p><pre>',print_r($courseelement),'</pre>';

//     if ($courseelement->has_custom_fields()) {

//       echo '<p>HAS CUSOM FIELDS</p>';

//         $fields = $courseelement->get_custom_fields();
//         $metadata = [];
//         foreach ($fields as $field) {
//             if (empty($field->get_value())) {
//                 echo "<p>Empty field</p>";
//                 continue;
//             }
//             $cat = $field->get_field()->get_category()->get('name');
//             $metadata[$field->get_field()->get('shortname')] = $cat . ': ' . $field->get_value();
//         }

//         return $metadata;
//     } else {

//       echo '<p>NO CUSOM FIELDS</p>';

//         return false;
//     }




  // $handler = \core_customfield\handler::get_handler('core_course', 'course');
  // // This is equivalent to the line above.
  // //$handler = \core_course\customfield\course_handler::create();
  // $datas = $handler->get_instance_data($courseid, true);
  // echo '<p>',print_r($datas),'</p>';
  // $metadata = [];
  // foreach ($datas as $data) {
  //     if (empty($data->get_value())) {
  //         continue;
  //     }
  //     $cat = $data->get_field()->get_category()->get('name');
  //     $metadata[$data->get_field()->get('shortname')] = $cat . ': ' . $data->get_value();
  // }
  // return $metadata;
// }

// foreach ($courses as $course) {
//     echo '<p>Course ID: '.$course->id.'<br />Metadata:</p>';
//     $metadata = get_course_metadata($course->id);

//     if ($metadata !== false && count($metadata) > 0) {
//         foreach ($metadata as $key => $value) {
//             echo '<p>'.$key.': '.$value.'</p>';
//         }
//     } else {
//         echo '<p>Metadata not found</p>';
//     }
// }

exit;
