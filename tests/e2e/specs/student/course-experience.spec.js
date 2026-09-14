/**
 * Test the responsive single-course experience and syllabus interaction.
 *
 * @since [version]
 */

// eslint-disable-next-line import/no-extraneous-dependencies -- Provided by @wordpress/scripts, as in the existing E2E suite.
import { test, expect } from '@wordpress/e2e-test-utils-playwright';
import {
	enrollInFreeCourse,
	loginStudent,
	logoutUser,
} from '../../utils/index.js';

const courseUrl = '/course/free-course/';
const viewports = [
	{ width: 1440, height: 1000 },
	{ width: 1024, height: 900 },
	{ width: 768, height: 1024 },
	{ width: 480, height: 900 },
	{ width: 375, height: 812 },
];

test.describe( 'CourseExperience', () => {
	test.beforeEach( async ( { page } ) => {
		await logoutUser( page );
		await page.goto( courseUrl );
	} );

	test( 'keeps the syllabus accessible with collapsible section controls', async ( {
		page,
	} ) => {
		const toggle = page.locator( '.llms-section-toggle' ).first();
		const panelId = await toggle.getAttribute( 'aria-controls' );
		const panel = page.locator( `#${ panelId }` );

		await expect( toggle ).toBeVisible();
		await expect( toggle ).toHaveAttribute( 'aria-expanded', 'true' );
		await expect( toggle ).toContainText( '1 lesson' );
		await expect( panel ).toBeVisible();
		await expect( panel.locator( '.llms-lesson-preview' ) ).toHaveCount(
			1
		);

		await toggle.click();
		await expect( toggle ).toHaveAttribute( 'aria-expanded', 'false' );
		await expect( panel ).toBeHidden();

		await toggle.focus();
		await page.keyboard.press( 'Enter' );
		await expect( toggle ).toHaveAttribute( 'aria-expanded', 'true' );
		await expect( panel ).toBeVisible();
	} );

	test( 'recomposes without horizontal overflow at supported widths', async ( {
		page,
	} ) => {
		const experience = page.locator( '.llms-course-experience' );

		for ( const viewport of viewports ) {
			await page.setViewportSize( viewport );
			await page.reload();
			await expect( experience ).toBeVisible();

			const layout = await experience.evaluate( ( element ) => ( {
				display: window.getComputedStyle( element ).display,
				overflows:
					document.documentElement.scrollWidth >
					document.documentElement.clientWidth,
			} ) );

			expect( layout.overflows ).toBe( false );
			expect( layout.display ).toBe(
				viewport.width > 768 ? 'grid' : 'flex'
			);
		}
	} );

	test( 'shows access information instead of progress to a visitor', async ( {
		page,
	} ) => {
		await expect( page.locator( '.llms-course-progress' ) ).toHaveCount(
			0
		);
		await expect( page.locator( '.llms-access-plans' ) ).toBeVisible();
		await expect( page.locator( '.llms-syllabus-wrapper' ) ).toBeVisible();
		await expect( page.locator( '.llms-lesson-locked' ) ).toBeVisible();
		await expect(
			page.locator( '.type-course > .wp-post-image' )
		).toHaveCount( 0 );
	} );

	test( 'prioritizes progress for an enrolled student', async ( {
		page,
	} ) => {
		await loginStudent( page, 'validcreds@email.tld', 'password' );
		await page.goto( courseUrl );

		if ( await page.locator( '.llms-free-enroll-form' ).count() ) {
			await enrollInFreeCourse( page, 'free-course' );
		}

		await page.goto( courseUrl );

		const progress = page.locator( '.llms-course-progress' );
		await expect( progress ).toBeVisible();
		await expect(
			progress.locator( '.llms-course-card-title' )
		).toHaveText( 'Course Progress' );
		await expect(
			progress.locator( '.progress-bar-complete' )
		).toHaveAttribute( 'data-progress', /^(0|100)%$/ );
		await expect( page.locator( '.llms-access-plans' ) ).toHaveCount( 0 );
		await expect(
			page.locator( '.llms-lesson-link' ).first()
		).toHaveJSProperty( 'tagName', 'A' );
	} );
} );
