<?php

/**
 * A Decorator which adds a small bar to the page which allows a logged-in CMS
 * user to edit the current page.
 *
 * To use, add the following code to your template (Page.ss), probably at the bottom
 * of the page just before the </body>:
 *
 * <code>
 * 	MiniAdminBar
 * </code>
 *
 * If you also want some default CSS, then you can add (which includes the full
 * <style> element):
 *
 * <code>
 * 	MiniAdminBarCSS
 * </code>
 *
 * @todo		Figure out how it works with things other than pages
 * @package		miniadminbar
 * @author		Mark James <mail@mark.james.name>
 * @copyright	2011 - Mark James
 * @license		New BSD License
 * @link		http://github.com/markjames/silverstripe-miniadminbar
 * 
 * Copyright (c) 2011, Mark James
 * All rights reserved.
 * 
 * Redistribution and use in source and binary forms, with or without modification,
 * are permitted provided that the following conditions are met:
 * 
 *     * Redistributions of source code must retain the above copyright notice,
 *       this list of conditions and the following disclaimer.
 * 
 *     * Redistributions in binary form must reproduce the above copyright notice,
 *       this list of conditions and the following disclaimer in the documentation
 *       and/or other materials provided with the distribution.
 * 
 *     * Neither the name of Zend Technologies USA, Inc. nor the names of its
 *       contributors may be used to endorse or promote products derived from this
 *       software without specific prior written permission.
 * 
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR
 * ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
 * ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

class MiniAdminBarDecorator extends Extension {

	/**
	 * Returns the HTML for the mini admin bar if the user
	 * is logged-in and has CMS access
	 *
	 * @return string The HTML for the miniadminbar
	 */
	public function MiniAdminBar() {

		// Get current logged-in member
		$member = Member::currentUser();
		
		if(!$member) {
			return;
		}

		// Does the user have access to the CMS?
		if( !Permission::check('CMS_ACCESS_CMSMain') ) {
			return;
		}

		// Does the user have permission to view CMS pages?
		if( !Permission::check('VIEW_DRAFT_CONTENT') ) {
			return;
		}

		// Get the current page
		$page = $this->owner->data();
		if( !$page ) {
			return;
		}

		$cmsLink = Director::baseURL() . 'admin/show/' . $page->ID;
		$stageLink = Controller::join_links($page->AbsoluteLink(), '?stage=Stage');
		$liveLink = Controller::join_links($page->AbsoluteLink(), '?stage=Live');

		switch( strtolower(Versioned::current_stage()) ) {

			case 'stage':
				$currentStage = 'stage';
				$currentStageName = _t('ContentController.DRAFTSITE', 'Draft Site');
				break;

			case 'live':
				$currentStage = 'live';
				$currentStageName = _t('ContentController.PUBLISHEDSITE', 'Published Site');
				break;

			default:
				return '';

		}

		$output = '';
		$output .= '<div id="miniadminbar" class="miniadminbar-current-stage-'.$currentStage.'">';
		$output .= '<strong>'.$currentStageName.'</strong>';
		$output .= '<div class="options">';
		$output .= '<a href="'.Convert::raw2xml($cmsLink).'" class="miniadminbar-cms first">' . _t('ContentController.CMS', 'CMS') . '</a>';
		if( $currentStage != 'live' ) {
			$output .= '<a href="'.Convert::raw2xml($liveLink).'" class="miniadminbar-live">' . _t('ContentController.PUBLISHEDSITE', 'Published Site') . '</a>';
		}
		if( $currentStage != 'stage' ) {
			$output .= '<a href="'.Convert::raw2xml($stageLink).'" class="miniadminbar-stage">' . _t('ContentController.DRAFTSITE', 'Draft Site') . '</a>';
		}
		$output .= '</div>';
		$output .= '</div>';

		return $output;

	}

	/**
	 * Returns the HTML for the CSS styles for the mini
	 * admin bar
	 *
	 * @return string The HTML for the miniadminbar styles
	 */
	public function MiniAdminBarCSS() {

		// Get current logged-in member
		$member = Member::currentUser();
		
		if(!$member) {
			return;
		}

		// Does the user have access to the CMS?
		if( !Permission::check('CMS_ACCESS_CMSMain') ) {
			return;
		}

		// Does the user have permission to view CMS pages?
		if( !Permission::check('VIEW_DRAFT_CONTENT') ) {
			return;
		}

		// Get the current page
		$page = $this->owner->data();
		if( !$page ) {
			return;
		}

		return <<<HTML
			<style type="text/css">
			#miniadminbar {
				margin: 0;
				padding: 0;
				border: 0;
				font-size: 100%;
				font: inherit;
				vertical-align: baseline;
				position: fixed;
				bottom: 0;
				right: 0;
				width: 10em;
				background: green;
				z-index: 2000;
			}
			#miniadminbar strong {
				cursor: default;
			}
			#miniadminbar strong,
			#miniadminbar a {
				display: block;
				background: rgba(0,0,0,0.8);
				padding: 0.5em 0.75em;
				line-height: 1;
				color: #EEE;
				text-decoration: none;
			}
			#miniadminbar a:hover {
				color: #FFF;
				text-decoration: none;
				background: #444;
			}
			#miniadminbar .options {
				margin: 0;
				padding: 0;
				border: 0;
				font-size: 100%;
				font: inherit;
				vertical-align: baseline;
				position: absolute;
				bottom: 100%;
				background: #FFF;
				width: 12em;
				text-shadow: 0 -1px 1px rgba(0,0,0,1);
				opacity: 0;
				-webkit-transition: opacity 0.1s linear;
			}
			#miniadminbar.miniadminbar-current-stage-live {
				border-bottom: 3px solid #1CAE00;
			}
			#miniadminbar.miniadminbar-current-stage-stage {
				border-bottom: 3px solid #9B1400;
			}
			#miniadminbar:hover .options {
				opacity: 1;
			}
			</style>
HTML;

	}
}