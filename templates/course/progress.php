<?php
/**
 * Display a course progress bar and
 * a button for the next incomplete lesson in the course
 *
 * @since    1.0.0
 * @version  3.11.1
 */

defined( 'ABSPATH' ) || exit;

global $post;

if ( ! llms_is_user_enrolled( get_current_user_id(), $post->ID ) ) {
	return;
}

$student  = new LLMS_Student();
$progress = $student->get_progress( $post->ID, 'course' );
?>

<section class="llms-course-progress llms-course-card">

	<h3 class="llms-course-card-title"><?php esc_html_e( 'Course Progress', 'lifterlms' ); ?></h3>

	<?php if ( apply_filters( 'lifterlms_display_course_progress_bar', true ) ) : ?>

		<?php lifterlms_course_progress_bar( $progress, false, false ); ?>

	<?php endif; ?>

	<div class="llms-course-progress-action">
		<?php lifterlms_course_continue_button( $post->ID, $student, $progress ); ?>
	</div>

</section>
