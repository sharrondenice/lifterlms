<?php
/**
 * Tests for template functions
 *
 * @group functions_templates
 *
 * @since 3.15.0
 * @version 3.37.0
 */
class LLMS_Functions_Templates extends LLMS_UnitTestCase {

	/**
	 * Test the single course progress template structure.
	 *
	 * @since [version]
	 *
	 * @return void
	 */
	public function test_single_course_progress_structure() {

		global $post;

		$course_id = $this->generate_mock_courses( 1, 1, 1, 0 )[0];
		$student   = $this->get_mock_student();
		$post      = get_post( $course_id );

		wp_set_current_user( $student->get_id() );
		llms_enroll_student( $student->get_id(), $course_id );

		$output = $this->get_output( 'lifterlms_template_single_course_progress' );

		$this->assertStringContainsString( '<section class="llms-course-progress llms-course-card">', $output );
		$this->assertStringContainsString( '<h3 class="llms-course-card-title">Course Progress</h3>', $output );
		$this->assertStringContainsString( '<div class="llms-course-progress-action">', $output );

		$post = null;

	}

	/**
	 * Test the single course syllabus template structure.
	 *
	 * @since [version]
	 *
	 * @return void
	 */
	public function test_single_course_syllabus_structure() {

		global $post;

		$course_id = $this->generate_mock_courses( 1, 1, 1, 0 )[0];
		$post      = get_post( $course_id );
		$output    = $this->get_output( 'lifterlms_template_single_syllabus' );

		$this->assertStringContainsString( '<div class="llms-syllabus-section">', $output );
		$this->assertStringContainsString( '<header class="llms-section-header">', $output );
		$this->assertStringContainsString( 'class="llms-section-toggle"', $output );
		$this->assertStringContainsString( 'aria-expanded="true"', $output );
		$this->assertStringContainsString( 'aria-controls="llms-syllabus-', $output );
		$this->assertStringContainsString( 'class="llms-section-lessons"', $output );
		$this->assertStringContainsString( '<div class="llms-lesson-preview', $output );

		$post = null;

	}

	/**
	 * Test lifterlms_course_continue_button() func
	 *
	 * @since 3.15.0
	 *
	 * @return   void
	 */
	public function test_lifterlms_course_continue_button() {

		global $post;
		$func = 'lifterlms_course_continue_button';

		// student to use
		$student = $this->get_mock_student();

		// course to use
		$course_id = $this->generate_mock_courses()[0];
		$course = llms_get_post( $course_id );

		// blog post to test globals against
		$post_id = $this->factory->post->create( array(
			'post_title' => 'Test Post',
		) );


		// call function with no parameters (using only defaults)
		// no student and no post set right now
		$this->assertEmpty( $this->get_output( $func ) );

		// set the global post to be a blog post
		$post = get_post( $post_id );

		// call function with no parameters (using only defaults)
		// post is a blog post & no student
		$this->assertEmpty( $this->get_output( $func ) );

		// set global to be a course but still no student
		$post = get_post( $course_id );
		$this->assertEmpty( $this->get_output( $func ) );

		// set the current student (should display a continue button)
		wp_set_current_user( $student->get_id() );

		// student setup but no enrollment
		$this->assertEmpty( $this->get_output( $func ) );

		// enroll student
		llms_enroll_student( $student->get_id(), $course_id );

		// 0 progress, "Get Started" text displays in button
		$this->assertTrue( ( false !== strpos( $this->get_output( $func ), 'Get Started' ) ) );

		// Progress > 0, "Continue" text displays in button
		$this->complete_courses_for_student( $student->get_id(), array( $course_id ), 85 );
		$this->assertTrue( ( false !== strpos( $this->get_output( $func ), 'Continue' ) ) );

		// 100% progress, "Course Complete" text displays
		$this->complete_courses_for_student( $student->get_id(), array( $course_id ), 100 );
		$this->assertTrue( ( false !== strpos( $this->get_output( $func ), 'Course Complete' ) ) );

		// use a lesson, same result as last
		$post = get_post( $course->get_lessons( 'ids' )[0] );
		$this->assertTrue( ( false !== strpos( $this->get_output( $func ), 'Course Complete' ) ) );

		// reset global
		$post = null;

	}

}
