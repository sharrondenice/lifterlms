<?php
/**
 * Template for the Course Syllabus Displayed on individual course pages
 *
 * @author LifterLMS
 * @package LifterLMS/Templates
 *
 * @since 1.0.0
 * @since 3.24.0 Unknown.
 * @since 4.4.0 Pass the progressive lesson order value to the lesson-preview template.
 * @since 7.1.3 Add paragraph tag to wrap message when sections or lessons are empty.
 * @since [version] Group sections and their lessons for course layout and navigation.
 * @since [version] Add accessible, progressively enhanced section controls.
 * @version [version]
 */
defined( 'ABSPATH' ) || exit;
global $post;
$course   = new LLMS_Course( $post );
$sections = $course->get_sections();

static $syllabus_instance = 0;
++$syllabus_instance;
?>

<div class="clear"></div>

<div class="llms-syllabus-wrapper">

	<?php if ( ! $sections ) : ?>

		<p><?php esc_html_e( 'This course does not have any sections.', 'lifterlms' ); ?></p>

	<?php else : ?>

		<?php foreach ( $sections as $section ) : ?>

			<?php
			$lesson_order          = 0;
			$display_section_title = apply_filters( 'llms_display_outline_section_titles', true );
			$lessons               = $section->get_lessons();
			$section_id            = sprintf( 'llms-syllabus-%1$d-%2$d-%3$d', $course->get( 'id' ), $syllabus_instance, $section->get( 'id' ) );
			?>

			<div class="llms-syllabus-section">

			<?php if ( $display_section_title ) : ?>
				<header class="llms-section-header">
					<h3 class="llms-h3 llms-section-title">
						<button
							class="llms-section-toggle"
							type="button"
							aria-controls="<?php echo esc_attr( $section_id ); ?>"
							aria-expanded="true"
							id="<?php echo esc_attr( $section_id ); ?>-toggle"
						>
							<span class="llms-section-name"><?php echo esc_html( get_the_title( $section->get( 'id' ) ) ); ?></span>
							<span class="llms-section-count">
								<?php
								printf(
									/* translators: %d: Number of lessons in a course section. */
									esc_html( _n( '%d lesson', '%d lessons', count( $lessons ), 'lifterlms' ) ),
									absint( count( $lessons ) )
								);
								?>
							</span>
							<span class="llms-section-caret" aria-hidden="true"></span>
						</button>
					</h3>
				</header>
			<?php endif; ?>

			<div
				class="llms-section-lessons"
				id="<?php echo esc_attr( $section_id ); ?>"
				<?php if ( $display_section_title ) : ?>
					aria-labelledby="<?php echo esc_attr( $section_id ); ?>-toggle"
				<?php endif; ?>
			>
			<?php if ( $lessons ) : ?>

				<?php foreach ( $lessons as $lesson ) : ?>

					<?php
					llms_get_template(
						'course/lesson-preview.php',
						array(
							'lesson'        => $lesson,
							'total_lessons' => count( $lessons ),
							'order'         => ++$lesson_order,
						)
					);
					?>

				<?php endforeach; ?>

			<?php else : ?>

				<p><?php esc_html_e( 'This section does not have any lessons.', 'lifterlms' ); ?></p>

			<?php endif; ?>
			</div>
			</div>

		<?php endforeach; ?>

	<?php endif; ?>

	<div class="clear"></div>

</div>
