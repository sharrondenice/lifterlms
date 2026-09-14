/**
 * Progressively enhance the course syllabus with collapsible sections.
 *
 * @package LifterLMS/Scripts
 *
 * @since [version]
 */

LLMS.CourseSyllabus = {

	/**
	 * Initialize course syllabus controls.
	 *
	 * @return {void}
	 */
	init: function() {

		$( '.llms-syllabus-wrapper .llms-section-toggle:not([data-llms-bound])' ).each( function() {

			var $toggle = $( this ),
				$section = $toggle.closest( '.llms-syllabus-section' ),
				$panel = $( '#' + $toggle.attr( 'aria-controls' ) );

			if ( ! $panel.length ) {
				return;
			}

			$toggle.attr( 'data-llms-bound', 'true' );
			$section.addClass( 'llms-section--open' );

			$toggle.on( 'click', function() {

				var is_expanded = 'true' === $toggle.attr( 'aria-expanded' );

				$toggle.attr( 'aria-expanded', String( ! is_expanded ) );
				$panel.prop( 'hidden', is_expanded );
				$section.toggleClass( 'llms-section--open', ! is_expanded );
				$section.toggleClass( 'llms-section--closed', is_expanded );

			} );

		} );

	},

};
