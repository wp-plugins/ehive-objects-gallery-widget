<?php
/*
	Plugin Name: eHive Objects Gallery widget
	Plugin URI: http://developers.ehive.com/wordpress-plugins/
	Author: Vernon Systems limited
	Description: A widget that displays a gallery of objects by catalogue type in eHive. The <a href="http://developers.ehive.com/wordpress-plugins#ehiveaccess" target="_blank">eHiveAccess plugin</a> must be installed.
	Version: 2.1.0
	Author URI: http://vernonsystems.com
	License: GPL2+
*/
/*
	Copyright (C) 2012 Vernon Systems Limited

	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.
	
	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
	
	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/
add_action( 'widgets_init', 'ehive_objects_gallery_widget' );

function ehive_objects_gallery_widget() {
	return register_widget( 'EHiveObjectsGallery_Widget' );
}

class EHiveObjectsGallery_Widget extends WP_Widget {
	public $eHiveApiErrorMessage;
	
	public function __construct() {
		parent::__construct('ehiveobjectsgallery_widget',
				'eHive Objects Gallery',
				array( 'description' => __('A widget that displays a gallery of eHive objects by catalouge type.', 'text_domain'))
		);
	}

	public function widget($args, $instance) {
		
		if (isset($instance['widget_css_enabled'])) {
			wp_register_style($handle = 'eHiveObjectsGalleryWidgetCSS', $src = plugins_url('eHiveObjectsGallery_Widget.css', '/ehive-objects-gallery-widget/css/EHiveObjectsGallery_Widget.css'), $deps = array(), $ver = '0.0.1', $media = 'all');
			wp_enqueue_style( 'eHiveObjectsGalleryWidgetCSS');
		}	
		
		global $post, $eHiveAccess;
		
		extract( $args );
		
		$displayOnPage = $instance['display_on_page'];

		if ( $displayOnPage == -1 || ($displayOnPage == $eHiveAccess->getAccountDetailsPageId() && $displayOnPage == $post->ID )) {
				
			$title = apply_filters('widget_title', $instance['title']);
			
			echo $before_widget;
			
			if (! empty( $title) ) {
				echo $before_title . $title . $after_title;
			}
			
			if ($instance['css_class'] == "") {
				echo '<div class="ehive-objects-gallery-widget">';
			} else {
				echo '<div class="ehive-objects-gallery-widget '.$instance['css_class'].'">';
			}
			
			try {
				$siteType = $eHiveAccess->getSiteType();
				$siteAccountId = $eHiveAccess->getAccountId();
				$siteCommunityId = $eHiveAccess->getCommunityId();
					
				$galleryType = $instance['gallery_type'];
				
				$fixedAccountId = $instance['account_id'];
				$fixedCommunityId = $instance['community_id'];
							
				
				$objectFilter = $instance['object_filter'];
							
				$imageColumns = $instance['image_columns'];
				$imageRows = $instance['image_rows'];
				$limit = $imageColumns * $imageRows;
				
	
				$accountId = null;
				if ($fixedAccountId != '') {
					$accountId = $fixedAccountId;
				} else {				
					if ($displayOnPage == $post->ID) {
						$accountId = ehive_get_var('ehive_account_id');
					} else {
						$accountId = $eHiveAccess->getAccountId();
					}
				}
				
				$communityId = null;
				if ($fixedCommunityId != '') {
					$communityId = $fixedCommunityId;				
				} else {
					$communityId = $eHiveAccess->getCommunityId();
				}
					
				$objectRecordsCollectionArchives = null;
				$objectRecordsCollectionArchaeology = null;
				$objectRecordsCollectionArt = null;
				$objectRecordsCollectionHistory = null;
				$objectRecordsCollectionLibrary = null;
				$objectRecordsCollectionNaturalScience = null;
				$objectRecordsCollectionPhotography = null;
				
				$archivesEnabled = $instance['archives_enabled'];
				$archaeologyEnabled = $instance['archaeology_enabled'];
				$artEnabled = $instance['art_enabled'];
				$historyEnabled = $instance['history_enabled'];
				$libraryEnabled = $instance['library_enabled'];
				$naturalScienceEnabled = $instance['natural_science_enabled'];
				$photographyEnabled = $instance['photography_enabled'];
				
								
				$siteType = $eHiveAccess->getSiteType();
				$siteAccountId = $eHiveAccess->getAccountId();
				$siteCommunityId = $eHiveAccess->getCommunityId();
								
				$eHiveApi = $eHiveAccess->eHiveApi();
	
				
				if ( $galleryType == 'fixed_community_id' ) {
					switch($objectFilter) {
						case 'interesting':
							if ($archivesEnabled)		$objectRecordsCollectionArchives		= $eHiveApi->getInterestingObjectRecordsInCommunity($communityId, 'archives',		false, 0, $limit);
							if ($archaeologyEnabled)	$objectRecordsCollectionArchaeology		= $eHiveApi->getInterestingObjectRecordsInCommunity($communityId, 'archaeology',	false, 0, $limit);
							if ($artEnabled)			$objectRecordsCollectionArt				= $eHiveApi->getInterestingObjectRecordsInCommunity($communityId, 'art',			false, 0, $limit);
							if ($historyEnabled)		$objectRecordsCollectionHistory			= $eHiveApi->getInterestingObjectRecordsInCommunity($communityId, 'history',		false, 0, $limit);
							if ($libraryEnabled)		$objectRecordsCollectionLibrary			= $eHiveApi->getInterestingObjectRecordsInCommunity($communityId, 'library',		false, 0, $limit);
							if ($naturalScienceEnabled)	$objectRecordsCollectionNaturalScience	= $eHiveApi->getInterestingObjectRecordsInCommunity($communityId, 'natural_science', false, 0, $limit);
							if ($photographyEnabled)	$objectRecordsCollectionPhotography		= $eHiveApi->getInterestingObjectRecordsInCommunity($communityId, 'photography', 	false, 0, $limit);
							break;
						case 'popular':
							if ($archivesEnabled)		$objectRecordsCollectionArchives		= $eHiveApi->getPopularObjectRecordsInCommunity($communityId, 'archives',		false, 0, $limit);
							if ($archaeologyEnabled)	$objectRecordsCollectionArchaeology		= $eHiveApi->getPopularObjectRecordsInCommunity($communityId, 'archaeology',	false, 0, $limit);
							if ($artEnabled)			$objectRecordsCollectionArt				= $eHiveApi->getPopularObjectRecordsInCommunity($communityId, 'art',			false, 0, $limit);
							if ($historyEnabled)		$objectRecordsCollectionHistory			= $eHiveApi->getPopularObjectRecordsInCommunity($communityId, 'history',		false, 0, $limit);
							if ($libraryEnabled)		$objectRecordsCollectionLibrary			= $eHiveApi->getPopularObjectRecordsInCommunity($communityId, 'library',		false, 0, $limit);
							if ($naturalScienceEnabled)	$objectRecordsCollectionNaturalScience	= $eHiveApi->getPopularObjectRecordsInCommunity($communityId, 'natural_science',	false, 0, $limit);
							if ($photographyEnabled)	$objectRecordsCollectionPhotography		= $eHiveApi->getPopularObjectRecordsInCommunity($communityId, 'photography',	false, 0, $limit);
							break;
						case 'recent':
							if ($archivesEnabled)		$objectRecordsCollectionArchives		= $eHiveApi->getRecentObjectRecordsInCommunity($communityId, 'archives',		false, 0, $limit);
							if ($archaeologyEnabled)	$objectRecordsCollectionArchaeology		= $eHiveApi->getRecentObjectRecordsInCommunity($communityId, 'archaeology',		false, 0, $limit);
							if ($artEnabled)			$objectRecordsCollectionArt				= $eHiveApi->getRecentObjectRecordsInCommunity($communityId, 'art',				false, 0, $limit);
							if ($historyEnabled)		$objectRecordsCollectionHistory			= $eHiveApi->getRecentObjectRecordsInCommunity($communityId, 'history',			false, 0, $limit);
							if ($libraryEnabled)		$objectRecordsCollectionLibrary			= $eHiveApi->getRecentObjectRecordsInCommunity($communityId, 'library',			false, 0, $limit);
							if ($naturalScienceEnabled)	$objectRecordsCollectionNaturalScience	= $eHiveApi->getRecentObjectRecordsInCommunity($communityId, 'natural_science',	false, 0, $limit);
							if ($photographyEnabled)	$objectRecordsCollectionPhotography		= $eHiveApi->getRecentObjectRecordsInCommunity($communityId, 'photography',		false, 0, $limit);
							break;
					}
				}		
				
				if ( $galleryType == 'query_attribute_account_id' || $galleryType == "fixed_account_id" ) {				
					if ( $siteType == 'Community') {
						switch($objectFilter) {
							case 'interesting':
								if ($archivesEnabled)		$objectRecordsCollectionArchives		= $eHiveApi->getInterestingObjectRecordsInAccountInCommunity($communityId, $accountId, 'archives',		false, 0, $limit);
								if ($archaeologyEnabled)	$objectRecordsCollectionArchaeology		= $eHiveApi->getInterestingObjectRecordsInAccountInCommunity($communityId, $accountId, 'archaeology',	false, 0, $limit);
								if ($artEnabled)			$objectRecordsCollectionArt				= $eHiveApi->getInterestingObjectRecordsInAccountInCommunity($communityId, $accountId, 'art',			false, 0, $limit);
								if ($historyEnabled)		$objectRecordsCollectionHistory			= $eHiveApi->getInterestingObjectRecordsInAccountInCommunity($communityId, $accountId, 'history',		false, 0, $limit);
								if ($libraryEnabled)		$objectRecordsCollectionLibrary			= $eHiveApi->getInterestingObjectRecordsInAccountInCommunity($communityId, $accountId, 'library',		false, 0, $limit);
								if ($naturalScienceEnabled)	$objectRecordsCollectionNaturalScience	= $eHiveApi->getInterestingObjectRecordsInAccountInCommunity($communityId, $accountId, 'natural_science',false, 0, $limit);
								if ($photographyEnabled)	$objectRecordsCollectionPhotography		= $eHiveApi->getInterestingObjectRecordsInAccountInCommunity($communityId, $accountId, 'photography',	false, 0, $limit);
								break;
							case 'popular':
								if ($archivesEnabled)		$objectRecordsCollectionArchives		= $eHiveApi->getPopularObjectRecordsInAccountInCommunity($communityId, $accountId, 'archives',		false, 0, $limit);
								if ($archaeologyEnabled)	$objectRecordsCollectionArchaeology		= $eHiveApi->getPopularObjectRecordsInAccountInCommunity($communityId, $accountId, 'archaeology',	false, 0, $limit);
								if ($artEnabled)			$objectRecordsCollectionArt				= $eHiveApi->getPopularObjectRecordsInAccountInCommunity($communityId, $accountId, 'art',			false, 0, $limit);
								if ($historyEnabled)		$objectRecordsCollectionHistory			= $eHiveApi->getPopularObjectRecordsInAccountInCommunity($communityId, $accountId, 'history',		false, 0, $limit);
								if ($libraryEnabled)		$objectRecordsCollectionLibrary			= $eHiveApi->getPopularObjectRecordsInAccountInCommunity($communityId, $accountId, 'library', 		false, 0, $limit);
								if ($naturalScienceEnabled)	$objectRecordsCollectionNaturalScience	= $eHiveApi->getPopularObjectRecordsInAccountInCommunity($communityId, $accountId, 'natural_science',false, 0, $limit);
								if ($photographyEnabled)	$objectRecordsCollectionPhotography		= $eHiveApi->getPopularObjectRecordsInAccountInCommunity($communityId, $accountId, 'photography',	false, 0, $limit);
								break;
							case 'recent':
								if ($archivesEnabled)		$objectRecordsCollectionArchives		= $eHiveApi->getRecentObjectRecordsInAccountInCommunity($communityId, $accountId, 'archives',	   false, 0, $limit);
								if ($archaeologyEnabled)	$objectRecordsCollectionArchaeology		= $eHiveApi->getRecentObjectRecordsInAccountInCommunity($communityId, $accountId, 'archaeology',   false, 0, $limit);
								if ($artEnabled)			$objectRecordsCollectionArt				= $eHiveApi->getRecentObjectRecordsInAccountInCommunity($communityId, $accountId, 'art',		   false, 0, $limit);
								if ($historyEnabled)		$objectRecordsCollectionHistory			= $eHiveApi->getRecentObjectRecordsInAccountInCommunity($communityId, $accountId, 'history',	   false, 0, $limit);
								if ($libraryEnabled)		$objectRecordsCollectionLibrary			= $eHiveApi->getRecentObjectRecordsInAccountInCommunity($communityId, $accountId, 'library',	   false, 0, $limit);
								if ($naturalScienceEnabled)	$objectRecordsCollectionNaturalScience	= $eHiveApi->getRecentObjectRecordsInAccountInCommunity($communityId, $accountId, 'natural_science',false, 0, $limit);
								if ($photographyEnabled)	$objectRecordsCollectionPhotography		= $eHiveApi->getRecentObjectRecordsInAccountInCommunity($communityId, $accountId, 'photography',   false, 0, $limit);
								break;
						}
						
					} else if ( $siteType == 'Account' ) {
						switch($objectFilter) {
							case 'interesting':
								if ($archivesEnabled)		$objectRecordsCollectionArchives		= $eHiveApi->getInterestingObjectRecordsInAccount($accountId, 'archives',		false, 0, $limit);
								if ($archaeologyEnabled)	$objectRecordsCollectionArchaeology		= $eHiveApi->getInterestingObjectRecordsInAccount($accountId, 'archaeology',	false, 0, $limit);
								if ($artEnabled)			$objectRecordsCollectionArt				= $eHiveApi->getInterestingObjectRecordsInAccount($accountId, 'art',			false, 0, $limit);
								if ($historyEnabled)		$objectRecordsCollectionHistory			= $eHiveApi->getInterestingObjectRecordsInAccount($accountId, 'history',		false, 0, $limit);
								if ($libraryEnabled)		$objectRecordsCollectionLibrary			= $eHiveApi->getInterestingObjectRecordsInAccount($accountId, 'library',		false, 0, $limit);
								if ($naturalScienceEnabled)	$objectRecordsCollectionNaturalScience	= $eHiveApi->getInterestingObjectRecordsInAccount($accountId, 'natural_science',	false, 0, $limit);
								if ($photographyEnabled)	$objectRecordsCollectionPhotography		= $eHiveApi->getInterestingObjectRecordsInAccount($accountId, 'photography',	false, 0, $limit);
								break;
							case 'popular':
								if ($archivesEnabled)		$objectRecordsCollectionArchives		= $eHiveApi->getPopularObjectRecordsInAccount($accountId, 'archives',		false, 0, $limit);
								if ($archaeologyEnabled)	$objectRecordsCollectionArchaeology		= $eHiveApi->getPopularObjectRecordsInAccount($accountId, 'archaeology',	false, 0, $limit);
								if ($artEnabled)			$objectRecordsCollectionArt				= $eHiveApi->getPopularObjectRecordsInAccount($accountId, 'art',			false, 0, $limit);
								if ($historyEnabled)		$objectRecordsCollectionHistory			= $eHiveApi->getPopularObjectRecordsInAccount($accountId, 'history',		false, 0, $limit);
								if ($libraryEnabled)		$objectRecordsCollectionLibrary			= $eHiveApi->getPopularObjectRecordsInAccount($accountId, 'library',		false, 0, $limit);
								if ($naturalScienceEnabled)	$objectRecordsCollectionNaturalScience	= $eHiveApi->getPopularObjectRecordsInAccount($accountId, 'natural_science',	false, 0, $limit);
								if ($photographyEnabled)	$objectRecordsCollectionPhotography		= $eHiveApi->getPopularObjectRecordsInAccount($accountId, 'photography',	false, 0, $limit);
								break;
							case 'recent':
								if ($archivesEnabled)		$objectRecordsCollectionArchives		= $eHiveApi->getRecentObjectRecordsInAccount($accountId, 'archives',		false, 0, $limit);
								if ($archaeologyEnabled)	$objectRecordsCollectionArchaeology		= $eHiveApi->getRecentObjectRecordsInAccount($accountId, 'archaeology',		false, 0, $limit);
								if ($artEnabled)			$objectRecordsCollectionArt				= $eHiveApi->getRecentObjectRecordsInAccount($accountId, 'art',				false, 0, $limit);
								if ($historyEnabled)		$objectRecordsCollectionHistory			= $eHiveApi->getRecentObjectRecordsInAccount($accountId, 'history',			false, 0, $limit);
								if ($libraryEnabled)		$objectRecordsCollectionLibrary			= $eHiveApi->getRecentObjectRecordsInAccount($accountId, 'library',			false, 0, $limit);
								if ($naturalScienceEnabled)	$objectRecordsCollectionNaturalScience	= $eHiveApi->getRecentObjectRecordsInAccount($accountId, 'natural_science',	false, 0, $limit);
								if ($photographyEnabled)	$objectRecordsCollectionPhotography		= $eHiveApi->getRecentObjectRecordsInAccount($accountId, 'photography',		false, 0, $limit);
								break;
						}
												
					} else if ( $siteType == 'ehive' ) {
						switch($objectFilter) {
							case 'interesting':
								if ($archivesEnabled)		$objectRecordsCollectionArchives		= $eHiveApi->getInterestingObjectRecordsInEHive('archives',		false, 0, $limit);
								if ($archaeologyEnabled)	$objectRecordsCollectionArchaeology		= $eHiveApi->getInterestingObjectRecordsInEHive('archaeology',	false, 0, $limit);
								if ($artEnabled)			$objectRecordsCollectionArt				= $eHiveApi->getInterestingObjectRecordsInEHive('art',			false, 0, $limit);
								if ($historyEnabled)		$objectRecordsCollectionHistory			= $eHiveApi->getInterestingObjectRecordsInEHive('history',		false, 0, $limit);
								if ($libraryEnabled)		$objectRecordsCollectionLibrary			= $eHiveApi->getInterestingObjectRecordsInEHive('library',		false, 0, $limit);
								if ($naturalScienceEnabled)	$objectRecordsCollectionNaturalScience	= $eHiveApi->getInterestingObjectRecordsInEHive('natural_science',	false, 0, $limit);
								if ($photographyEnabled)	$objectRecordsCollectionPhotography		= $eHiveApi->getInterestingObjectRecordsInEHive('photography',	false, 0, $limit);
								break;
							case 'popular':
								if ($archivesEnabled)		$objectRecordsCollectionArchives		= $eHiveApi->getPopularObjectRecordsInEHive('archives',		false, 0, $limit);
								if ($archaeologyEnabled)	$objectRecordsCollectionArchaeology		= $eHiveApi->getPopularObjectRecordsInEHive('archaeology',	false, 0, $limit);
								if ($artEnabled)			$objectRecordsCollectionArt				= $eHiveApi->getPopularObjectRecordsInEHive('art',			false, 0, $limit);
								if ($historyEnabled)		$objectRecordsCollectionHistory			= $eHiveApi->getPopularObjectRecordsInEHive('history',		false, 0, $limit);
								if ($libraryEnabled)		$objectRecordsCollectionLibrary			= $eHiveApi->getPopularObjectRecordsInEHive('library',			false, 0, $limit);
								if ($naturalScienceEnabled)	$objectRecordsCollectionNaturalScience	= $eHiveApi->getPopularObjectRecordsInEHive('natural_science',	false, 0, $limit);
								if ($photographyEnabled)	$objectRecordsCollectionPhotography		= $eHiveApi->getPopularObjectRecordsInEHive('photography',		false, 0, $limit);
								break;
							case 'recent':
								if ($archivesEnabled)		$objectRecordsCollectionArchives		= $eHiveApi->getRecentObjectRecordsInEHive('archives',		false, 0, $limit);
								if ($archaeologyEnabled)	$objectRecordsCollectionArchaeology		= $eHiveApi->getRecentObjectRecordsInEHive('archaeology',	false, 0, $limit);
								if ($artEnabled)			$objectRecordsCollectionArt				= $eHiveApi->getRecentObjectRecordsInEHive('art',			false, 0, $limit);
								if ($historyEnabled)		$objectRecordsCollectionHistory			= $eHiveApi->getRecentObjectRecordsInEHive('history',		false, 0, $limit);
								if ($libraryEnabled)		$objectRecordsCollectionLibrary			= $eHiveApi->getRecentObjectRecordsInEHive('library',		false, 0, $limit);
								if ($naturalScienceEnabled)	$objectRecordsCollectionNaturalScience	= $eHiveApi->getRecentObjectRecordsInEHive('natural_science',false, 0, $limit);
								if ($photographyEnabled)	$objectRecordsCollectionPhotography		= $eHiveApi->getRecentObjectRecordsInEHive('photography',	false, 0, $limit);
								break;
						}
						break;
					}
				}
						
				if ( $instance['summary_enabled'] ) {
					$totalObjects = 0;
					if ($objectRecordsCollectionArchives != null) {
						$totalObjects = $totalObjects + $objectRecordsCollectionArchives->totalObjects;
					}
					if ($objectRecordsCollectionArchaeology != null) {
						$totalObjects = $totalObjects + $objectRecordsCollectionArchaeology->totalObjects;
					}
					if ($objectRecordsCollectionArt != null) {
						$totalObjects = $totalObjects + $objectRecordsCollectionArt->totalObjects;
					}
					if ($objectRecordsCollectionHistory != null) {
						$totalObjects = $totalObjects + $objectRecordsCollectionHistory->totalObjects;
					}
					if ($objectRecordsCollectionLibrary != null) {
						$totalObjects = $totalObjects + $objectRecordsCollectionLibrary->totalObjects;
					}
					if ($objectRecordsCollectionNaturalScience != null) {
						$totalObjects = $totalObjects + $objectRecordsCollectionNaturalScience->totalObjects;
					}
					if ($objectRecordsCollectionPhotography != null) {
						$totalObjects = $totalObjects + $objectRecordsCollectionPhotography->totalObjects;
					}
				}
				
					
				if ( $instance['summary_enabled'] ) {
					$this->summaryTitle( $communityId, $accountId, $totalObjects, $instance['summary_title'], $instance['summary_link_enabled'], $galleryType );
				}
				
				if ($objectRecordsCollectionArchives != null) {
					$this->catalogueTitle( $communityId, $accountId, 'archives', $objectRecordsCollectionArchives->totalObjects, $instance['archives_title'], $instance['catalogue_type_link_enabled'], $instance['hide_catalogue_title_when_zero'], $galleryType );
					$this->imageGrid( $objectRecordsCollectionArchives, $imageRows, $imageColumns, $instance['image_size'], $instance['image_link_enabled'], $instance['image_border_colour'], $instance['image_background_colour']);
				}
				if ($objectRecordsCollectionArchaeology != null) {				
					$this->catalogueTitle( $communityId, $accountId, 'archaeology', $objectRecordsCollectionArchaeology->totalObjects, $instance['archaeology_title'], $instance['catalogue_type_link_enabled'], $instance['hide_catalogue_title_when_zero'], $galleryType );				
					$this->imageGrid( $objectRecordsCollectionArchaeology, $imageRows, $imageColumns, $instance['image_size'], $instance['image_link_enabled'], $instance['image_border_colour'], $instance['image_background_colour'] );
				}
				if ($objectRecordsCollectionArt != null) {
					$this->catalogueTitle( $communityId, $accountId, 'art', $objectRecordsCollectionArt->totalObjects, $instance['art_title'], $instance['catalogue_type_link_enabled'], $instance['hide_catalogue_title_when_zero'], $galleryType );				
					$this->imageGrid( $objectRecordsCollectionArt, $imageRows, $imageColumns, $instance['image_size'], $instance['image_link_enabled'], $instance['image_border_colour'], $instance['image_background_colour'] );
				}				
				if ($objectRecordsCollectionHistory != null) {
					$this->catalogueTitle( $communityId, $accountId, 'history', $objectRecordsCollectionHistory->totalObjects, $instance['history_title'], $instance['catalogue_type_link_enabled'], $instance['hide_catalogue_title_when_zero'], $galleryType  );				
					$this->imageGrid( $objectRecordsCollectionHistory, $imageRows, $imageColumns, $instance['image_size'], $instance['image_link_enabled'], $instance['image_border_colour'], $instance['image_background_colour'] );
				}
				if ($objectRecordsCollectionLibrary != null) {
					$this->catalogueTitle( $communityId, $accountId, 'library', $objectRecordsCollectionLibrary->totalObjects, $instance['library_title'], $instance['catalogue_type_link_enabled'], $instance['hide_catalogue_title_when_zero'], $galleryType  );				
					$this->imageGrid( $objectRecordsCollectionLibrary, $imageRows, $imageColumns, $instance['image_size'], $instance['image_link_enabled'], $instance['image_border_colour'], $instance['image_background_colour'] );
				}
				if ($objectRecordsCollectionNaturalScience != null) {
					$this->catalogueTitle( $communityId, $accountId, 'naturalscience', $objectRecordsCollectionNaturalScience->totalObjects, $instance['natural_science_title'], $instance['catalogue_type_link_enabled'], $instance['hide_catalogue_title_when_zero'], $galleryType  );
					$this->imageGrid( $objectRecordsCollectionNaturalScience, $imageRows, $imageColumns, $instance['image_size'], $instance['image_link_enabled'], $instance['image_border_colour'], $instance['image_background_colour'] );
				}
				if ($objectRecordsCollectionPhotography != null) {
					$this->catalogueTitle( $communityId, $accountId, 'photography', $objectRecordsCollectionPhotography->totalObjects, $instance['photography_title'], $instance['catalogue_type_link_enabled'], $instance['hide_catalogue_title_when_zero'], $galleryType  );				
					$this->imageGrid( $objectRecordsCollectionPhotography, $imageRows, $imageColumns, $instance['image_size'], $instance['image_link_enabled'], $instance['image_border_colour'], $instance['image_background_colour'] );
				}
								
				echo "</div>";				
				echo $args['after_widget'];				
			} catch (Exception $exception) {
				error_log('EHive Object gallery widget returned and error while accessing the eHive API: ' . $exception->getMessage());
				$eHiveApiErrorMessage = " ";
				if ($eHiveAccess->getIsErrorNotificationEnabled()) {
					$eHiveApiErrorMessage = $eHiveAccess->getErrorMessage();
				}
				// echo "<div><p class='ehive-error-message ehive-account-details-error'>$eHiveApiErrorMessage</p></div>";
			}
		}
	}

	private function summaryTitle( $communityId, $accountId, $total, $title, $linkEnabled, $galleryType ) {
		
		global $eHiveAccess;
	
		if ($linkEnabled == true) {
			
			if ($accountId != null) {				
				if ( $galleryType == 'query_attribute_account_id' ) {
					$link = $eHiveAccess->getSearchPageLink( '?a='.$accountId.'&all=true' );
				} else {
					$link = $eHiveAccess->getSearchPageLink('?all=true');
				}
				
			} elseif ($communityId != null) {
				$link = $eHiveAccess->getSearchPageLink( '?c='.$communityId.'&all=true' );
			} else {
				$link = $eHiveAccess->getSearchPageLink('?all=true' );
			}
		}
	
		$linkText = str_replace ('{total}', $total, $title);
	
		echo '<div class="ehive-catalogue-title ehive-summary">';
		if ($linkEnabled == true) {
			echo '<a href="'.$link.'">'.$linkText.'</a>';
		} else {
			echo $linkText;
		}
		echo '</div>';
	}
		
	private function catalogueTitle( $community, $account, $catalogueType, $total, $title, $linkEnabled, $hideIfZero, $galleryType ) {		

		if ($hideIfZero == true and $total == 0) {
			return;
		}
		
		global $eHiveAccess, $eHiveSearch;
		$searchOptions = $eHiveSearch->getSearchOptions();

		if ($linkEnabled == true) {
			if ($account != null) {
				if ( $galleryType == 'query_attribute_account_id' ) {
					$link = $eHiveAccess->getSearchPageLink( '?'.$searchOptions['query_var'].'=cat_type:'.$catalogueType.'&a='.$account );
				} else {
					$link = $eHiveAccess->getSearchPageLink( '?'.$searchOptions['query_var'].'=cat_type:'.$catalogueType );
				}
				
			} elseif ($community != null) {
				$link = $eHiveAccess->getSearchPageLink( '?'.$searchOptions['query_var'].'=cat_type:'.$catalogueType.'&c='.$communityId );
			} else {
				$link = $eHiveAccess->getSearchPageLink( '?'.$searchOptions['query_var'].'=cat_type:'.$catalogueType );
			}
		}		
		
		$linkText = str_replace ('{total}', $total, $title);
				
		echo '<div class="ehive-catalogue-title ehive-'.$catalogueType.'" >';
		if ($linkEnabled == true) {
			echo '<a href="'.$link.'">'.$linkText.'</a>';
		} else {
			echo $linkText;
		}
		echo '</div>';
	}
	
	private function imageGrid( $objectRecordsCollection, $rows, $columns, $imageSize, $imageLinkEnabled, $imageBorderColour, $imageBackgroundColour) {
		
		if ( count($objectRecordsCollection->objectRecords) == 0 ) {
			return;
		}
		
		global $eHiveAccess;
		
		echo '<div class="ehive-image-grid">';
		$column = 0;
		
		foreach($objectRecordsCollection->objectRecords as $objectRecord) {
					
			if ($column == 0) {
				echo '<div class="ehive-row">';
			}
			$column = $column + 1;
		
			$imageMediaSet = $objectRecord->getMediaSetByIdentifier('image');
			if (isset($imageMediaSet)){
		
				$mediaRow = $imageMediaSet->mediaRows[0];
				$imageMedia = $mediaRow->getMediaByIdentifier($imageSize);
		
				if ($imageLinkEnabled) {
					echo '<a class="ehive-image-link" href="'. $eHiveAccess->getObjectDetailsPageLink($objectRecord->objectRecordId) .'">';
				}
				
				echo '<img class="ehive-image" src="'.$imageMedia->getMediaAttribute('url').'" style="border: 2px solid '.$imageBorderColour.'; background-color: '.$imageBackgroundColour.';"/>';
				
				if ($imageLinkEnabled) {
					echo '</a>';
				}
			}
			if ($column == $columns ) {  // End of an image row.
				echo '</div>';
				$column = 0;
			}
		}
		if ($column > 0 && $column < $columns) {  // End of an image row where the number of columns does not divide evenly. A short row.
			echo "</div>";
		}
		echo "</div>";		
	}
	

	public function update($new_instance, $old_instance) {
		$instance = $old_instance;

		$instance['title'] = strip_tags( $new_instance['title']);
		
		$instance['gallery_type'] = strip_tags($new_instance['gallery_type']);
		
		$instance['fixed_account_id'] = strip_tags($new_instance['fixed_account_id']);
		$instance['fixed_community_id'] = strip_tags($new_instance['fixed_community_id']);
		
		$instance['display_on_page'] = strip_tags($new_instance['display_on_page']);
				
		$instance['summary_enabled'] = strip_tags($new_instance['summary_enabled']);
		$instance['summary_link_enabled'] = strip_tags($new_instance['summary_link_enabled']);
		$instance['summary_title'] = $new_instance['summary_title'];		
			
		$instance['archives_enabled'] = strip_tags($new_instance['archives_enabled']);
		$instance['archaeology_enabled'] = strip_tags($new_instance['archaeology_enabled']);
		$instance['art_enabled'] = strip_tags($new_instance['art_enabled']);
		$instance['history_enabled'] = strip_tags($new_instance['history_enabled']);
		$instance['library_enabled'] = strip_tags($new_instance['library_enabled']);
		$instance['natural_science_enabled'] = strip_tags($new_instance['natural_science_enabled']);
		$instance['photography_enabled'] = strip_tags($new_instance['photography_enabled']);
		
		$instance['hide_catalogue_title_when_zero'] = strip_tags($new_instance['hide_catalogue_title_when_zero']);
		$instance['catalogue_type_link_enabled'] = strip_tags($new_instance['catalogue_type_link_enabled']);
				
		$instance['archives_title'] = $new_instance['archives_title'];		
		$instance['archaeology_title'] = $new_instance['archaeology_title'];
		$instance['art_title'] = $new_instance['art_title'];
		$instance['history_title'] = $new_instance['history_title'];		
		$instance['library_title'] = $new_instance['library_title'];
		$instance['natural_science_title'] = $new_instance['natural_science_title'];		
		$instance['photography_title'] = $new_instance['photography_title'];

		$instance['object_filter'] = strip_tags($new_instance['object_filter']);		
		
		$instance['image_rows'] = strip_tags($new_instance['image_rows']);
		$instance['image_columns'] = strip_tags($new_instance['image_columns']);		
		$instance['image_size'] = strip_tags($new_instance['image_size']);
		$instance['image_link_enabled'] = strip_tags($new_instance['image_link_enabled']);
				
		$instance['widget_css_enabled'] = $new_instance['widget_css_enabled'];
		$instance['css_class'] = $new_instance['css_class'];
				
		$instance['image_border_colour'] = $new_instance['image_border_colour'];
		$instance['image_background_colour'] = $new_instance['image_background_colour'];
		
		return $instance;
	}

	public function form($instance) {

		$defaults = array( 
				'title' => 'Explore Collection',
				'gallery_type' => 'fixed_account_id',
				'fixed_account_id' => '',
				'fixed_community_id' => '',
				'object_filter' => 'interesting',
				'display_on_page' => '-1',
				
				'summary_enabled' => true, 
				'summary_link_enabled' => true,
				'summary_title' => 'Entire Collection ({total})',
				
				'archives_enabled' => true, 'archives_title' => 'Archives ({total})',
				'archaeology_enabled' => true, 'archaeology_title' => 'Archaeology ({total})',
				'art_enabled' => true, 'art_title' => 'Art ({total})',
				'history_enabled' => true, 'history_title' => 'History ({total})',
				'library_enabled' => true, 'library_title' => 'Library ({total})',
				'natural_science_enabled' => true, 'natural_science_title' => 'Natural Science ({total})',
				'photography_enabled' => true, 'photography_title' => 'Photography and Multimedia ({total})',
				
				'hide_catalogue_title_when_zero' => true,
				'catalogue_type_link_enabled' => true,
																
				'image_rows' => '1',
				'image_columns' => '6',
				'image_size' => 'ns',
				'image_link_enabled' => true,
				'widget_css_enabled' => true,
				'css_class' => '',
				'image_border_colour' => '#f3f3f3',
				'image_background_colour' => '#ffffff');
		
		$instance = wp_parse_args( $instance, $defaults );	

		$galleryType = esc_attr($instance['gallery_type']);
		$objectFilter = esc_attr($instance['object_filter']);
		$displayOnPage = esc_attr($instance['display_on_page']);
		$imageSize = esc_attr($instance['image_size']);
		?>
		<script type="text/javascript">
			//<![CDATA[
				jQuery(document).ready(function()
				{
					jQuery('.image_background_colourpicker').hide();
					
					var imageBackgroundColourpicker;
				    jQuery('.image_background_colourpicker').each(function() {
				    	var me = jQuery(this), id = me.attr('rel');	 
						me.farbtastic('#' + id);
						imageBackgroundColourpicker = jQuery('#' + id);
				    });	

				    var imageBackgroundColourpickerHidden = true;
				    jQuery(imageBackgroundColourpicker).click(function() {
					    if (imageBackgroundColourpickerHidden){
			    			jQuery('.image_background_colourpicker').slideDown();
			    			imageBackgroundColourpickerHidden = false;
					    } else {
					    	jQuery('.image_background_colourpicker').slideUp();
					    	imageBackgroundColourpickerHidden = true;
						}
			    	});

				    jQuery('.image_border_colourpicker').hide();

				    var imageBorderColourpicker;
				    jQuery('.image_border_colourpicker').each(function() {
				    	var me = jQuery(this), id = me.attr('rel');	 
						me.farbtastic('#' + id);
						imageBorderColourpicker = jQuery('#' + id);
				    });

			    	var imageBorderColourpickerHidden = true;
				    jQuery(imageBorderColourpicker).click(function() {
					    if (imageBorderColourpickerHidden){
			    			jQuery('.image_border_colourpicker').slideDown();
			    			imageBorderColourpickerHidden = false;
					    } else {
					    	jQuery('.image_border_colourpicker').slideUp();
					    	imageBorderColourpickerHidden = true;
						}
			    	});
				});
			//]]>   
		</script>	
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
			<input class="widefat" type="text" value="<?php echo $instance['title']; ?>" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" />
		</p>		
		<p>
			<label for="<?php echo $this->get_field_id('gallery_type'); ?>"><?php _e('Gallery type:'); ?></label>
	        <select name="<?php echo $this->get_field_name('gallery_type'); ?>" id="<?php echo $this->get_field_id('gallery_type'); ?>">
	            <?php
	            echo '<option value="fixed_account_id"', $galleryType == 'fixed_account_id' ? ' selected="selected"' : '', '>', "Fixed Account Id", '</option>';	             
	            echo '<option value="fixed_community_id"', $galleryType == 'fixed_community_id' ? ' selected="selected"' : '', '>', "Fixed Community Id", '</option>';	            
	            echo '<option value="query_attribute_account_id"', $galleryType == 'query_attribute_account_id' ? ' selected="selected"' : '', '>', "Account Id from query attribute", '</option>';	            
	            ?>
	        </select>
		</p>						
		<p>
			<label for="<?php echo $this->get_field_id( 'fixed_account_id' ); ?>"><?php _e( 'Fixed Account Id:' ); ?></label>
			<input type="text" value="<?php echo $instance['fixed_account_id']; ?>" id="<?php echo $this->get_field_id( 'fixed_account_id' ); ?>" name="<?php echo $this->get_field_name( 'fixed_account_id' ); ?>" size="8" />
			<br/>	
			<label for="<?php echo $this->get_field_id( 'fixed_community_id' ); ?>"><?php _e( 'Fixed Community Id:' ); ?></label>
			<input type="text" value="<?php echo $instance['fixed_community_id']; ?>" id="<?php echo $this->get_field_id( 'fixed_community_id' ); ?>" name="<?php echo $this->get_field_name( 'fixed_community_id' ); ?>" size="8" />
		</p>		
		<p>
			<label for="<?php echo $this->get_field_id('object_filter'); ?>"><?php _e('Object filter:'); ?></label>
	        <select name="<?php echo $this->get_field_name('object_filter'); ?>" id="<?php echo $this->get_field_id('object_filter'); ?>">
	            <?php
	            echo '<option value="interesting"', $objectFilter == 'interesting' ? ' selected="selected"' : '', '>', "Interesting", '</option>';	             
	            echo '<option value="popular"', $objectFilter == 'popular' ? ' selected="selected"' : '', '>', "Popular", '</option>';	            
	            echo '<option value="recent"', $objectFilter == 'recent' ? ' selected="selected"' : '', '>', "Recent", '</option>';	            
	            ?>
	        </select>
		</p>								
		<p>
        <label for="<?php echo $this->get_field_id('display_on_page'); ?>"><?php _e('Page to display on:'); ?></label>
        <select name="<?php echo $this->get_field_name('display_on_page'); ?>" id="<?php echo $this->get_field_id('display_on_page'); ?>" class="widefat">
            <?php
            $pages = get_pages();
            echo '<option value="-1" id="-1"', $displayOnPage == -1 ? ' selected="selected"' : '', '>', "- All Pages -", '</option>';
            foreach ($pages as $page) {
                echo '<option value="' . $page->ID . '" id="' . $page->ID . '"', $displayOnPage == $page->ID ? ' selected="selected"' : '', '>', $page->post_title, '</option>';
            }
            ?>
        </select>
	    </p>					
				
		<hr class="div"/>
		<p>
			<strong>Summary</strong><br/>
	        <input class="checkbox" type="checkbox" value="1" <?php checked( $instance['summary_enabled'], true ); ?> id="<?php echo $this->get_field_id('summary_enabled'); ?>" name = "<?php echo $this->get_field_name('summary_enabled'); ?>" />
			<label for="<?php echo $this->get_field_id('summary_enabled'); ?>"><?php _e( 'Summary enabled' ); ?></label>        
	        <br/>
			<input class="checkbox" type="checkbox" value="1" <?php checked( $instance['summary_link_enabled'], true ); ?> id="<?php echo $this->get_field_id('summary_link_enabled'); ?>" name = "<?php echo $this->get_field_name('summary_link_enabled'); ?>" />
			<label for="<?php echo $this->get_field_id('summary_link_enabled'); ?>"><?php _e( 'Sumamry title link enabled' ); ?></label>        
	        <br/>
		</p>
	    <p>
			<label for="<?php echo $this->get_field_id( 'summary_title' ); ?>"><?php _e( 'Summary title:' ); ?></label>
			<input class="widefat" type="text" value="<?php echo $instance['summary_title']; ?>" id="<?php echo $this->get_field_id( 'summary_title' ); ?>" name="<?php echo $this->get_field_name( 'summary_title' ); ?>" />
		</p>
	    
	    
		<hr class="div"/>
		<p>
			<strong>Catalogue Types</strong><br/>	        		
			<input class="checkbox" type="checkbox" value="1" <?php checked( $instance['archives_enabled'], true ); ?> id="<?php echo $this->get_field_id('archives_enabled'); ?>" name = "<?php echo $this->get_field_name('archives_enabled'); ?>" />
			<label for="<?php echo $this->get_field_id('archives_enabled'); ?>"><?php _e( 'Archives enabled' ); ?></label>        
	        <br/>
			
	        <input class="checkbox" type="checkbox" value="1" <?php checked( $instance['archaeology_enabled'], true ); ?> id="<?php echo $this->get_field_id('archaeology_enabled'); ?>" name = "<?php echo $this->get_field_name('archaeology_enabled'); ?>" />
			<label for="<?php echo $this->get_field_id('archaeology_enabled'); ?>"><?php _e( 'Archaeology enabled' ); ?></label>        			
	        <br/>
			
	        <input class="checkbox" type="checkbox" value="1" <?php checked( $instance['art_enabled'], true ); ?> id="<?php echo $this->get_field_id('art_enabled'); ?>" name = "<?php echo $this->get_field_name('art_enabled'); ?>" />
			<label for="<?php echo $this->get_field_id('art_enabled'); ?>"><?php _e( 'Art enabled' ); ?></label>        			
	        <br/>
			
	        <input class="checkbox" type="checkbox" value="1" <?php checked( $instance['history_enabled'], true ); ?> id="<?php echo $this->get_field_id('history_enabled'); ?>" name = "<?php echo $this->get_field_name('history_enabled'); ?>" />
			<label for="<?php echo $this->get_field_id('history_enabled'); ?>"><?php _e( 'History enabled' ); ?></label>        			
	        <br/>
			
	        <input class="checkbox" type="checkbox" value="1" <?php checked( $instance['library_enabled'], true ); ?> id="<?php echo $this->get_field_id('library_enabled'); ?>" name = "<?php echo $this->get_field_name('library_enabled'); ?>" />
			<label for="<?php echo $this->get_field_id('library_enabled'); ?>"><?php _e( 'Library enabled' ); ?></label>        			
	        <br/>
			
	        <input class="checkbox" type="checkbox" value="1" <?php checked( $instance['natural_science_enabled'], true ); ?> id="<?php echo $this->get_field_id('natural_science_enabled'); ?>" name = "<?php echo $this->get_field_name('natural_science_enabled'); ?>" />
			<label for="<?php echo $this->get_field_id('natural_science_enabled'); ?>"><?php _e( 'Natural Science enabled' ); ?></label>        
	        <br/>
			
	        <input class="checkbox" type="checkbox" value="1" <?php checked( $instance['photography_enabled'], true ); ?> id="<?php echo $this->get_field_id('photography_enabled'); ?>" name = "<?php echo $this->get_field_name('photography_enabled'); ?>" />
			<label for="<?php echo $this->get_field_id('photography_enabled'); ?>"><?php _e( 'Photography and Multimedia enabled' ); ?></label>        
		</p>
								
		<p>
			<input class="checkbox" type="checkbox" value="1" <?php checked( $instance['hide_catalogue_title_when_zero'], true ); ?> id="<?php echo $this->get_field_id('hide_catalogue_title_when_zero'); ?>" name = "<?php echo $this->get_field_name('hide_catalogue_title_when_zero'); ?>" />
			<label for="<?php echo $this->get_field_id('hide_catalogue_title_when_zero'); ?>"><?php _e( 'Hide title if no objects' ); ?></label>        
			<br/>
			<input class="checkbox" type="checkbox" value="1" <?php checked( $instance['catalogue_type_link_enabled'], true ); ?> id="<?php echo $this->get_field_id('catalogue_type_link_enabled'); ?>" name = "<?php echo $this->get_field_name('catalogue_type_link_enabled'); ?>" />
			<label for="<?php echo $this->get_field_id('catalogue_type_link_enabled'); ?>"><?php _e( 'Title link enabled' ); ?></label>        
	        <br/>
		</p>
        <p>
			<label for="<?php echo $this->get_field_id( 'archives_title' ); ?>"><?php _e( 'Archives title:' ); ?></label>
			<input class="widefat" type="text" value="<?php echo $instance['archives_title']; ?>" id="<?php echo $this->get_field_id( 'archives_title' ); ?>" name="<?php echo $this->get_field_name( 'archives_title' ); ?>" />
		<p>
			<label for="<?php echo $this->get_field_id( 'archaeology_title' ); ?>"><?php _e( 'Archaeology title:' ); ?></label>
			<input class="widefat" type="text" value="<?php echo $instance['archaeology_title']; ?>" id="<?php echo $this->get_field_id( 'archaeology_title' ); ?>" name="<?php echo $this->get_field_name( 'archaeology_title' ); ?>" />
		</p>
        <p>
			<label for="<?php echo $this->get_field_id( 'art_title' ); ?>"><?php _e( 'Art title:' ); ?></label>
			<input class="widefat" type="text" value="<?php echo $instance['art_title']; ?>" id="<?php echo $this->get_field_id( 'art_title' ); ?>" name="<?php echo $this->get_field_name( 'art_title' ); ?>" />
		</p>
        <p>
			<label for="<?php echo $this->get_field_id( 'history_title' ); ?>"><?php _e( 'History title:' ); ?></label>
			<input class="widefat" type="text" value="<?php echo $instance['history_title']; ?>" id="<?php echo $this->get_field_id( 'history_title' ); ?>" name="<?php echo $this->get_field_name( 'history_title' ); ?>" />
		</p>
        <p>
			<label for="<?php echo $this->get_field_id( 'library_title' ); ?>"><?php _e( 'Library title:' ); ?></label>
			<input class="widefat" type="text" value="<?php echo $instance['library_title']; ?>" id="<?php echo $this->get_field_id( 'library_title' ); ?>" name="<?php echo $this->get_field_name( 'library_title' ); ?>" />
		</p>
        <p>
			<label for="<?php echo $this->get_field_id( 'natural_science_title' ); ?>"><?php _e( 'Natural Science title:' ); ?></label>
			<input class="widefat" type="text" value="<?php echo $instance['natural_science_title']; ?>" id="<?php echo $this->get_field_id( 'natural_science_title' ); ?>" name="<?php echo $this->get_field_name( 'natural_science_title' ); ?>" />
			<br/>
		</p>
        <p>
			<label for="<?php echo $this->get_field_id( 'photography_title' ); ?>"><?php _e( 'Photography and Multimedia title:' ); ?></label>
			<input class="widefat" type="text" value="<?php echo $instance['photography_title']; ?>" id="<?php echo $this->get_field_id( 'photography_title' ); ?>" name="<?php echo $this->get_field_name( 'photography_title' ); ?>" />
		</p>
				
		<hr class="div"/>		
		<p>
			<strong>Image Grid</strong><br/>
			<label for="<?php echo $this->get_field_id( 'image_rows' ); ?>"><?php _e( 'image rows:' ); ?></label>
			<input class="small-text" type="number" value="<?php echo $instance['image_rows']; ?>" id="<?php echo $this->get_field_id( 'image_rows' ); ?>" name="<?php echo $this->get_field_name( 'image_rows' ); ?> "/>
			<br/>
			<label for="<?php echo $this->get_field_id( 'image_columns' ); ?>"><?php _e( 'image columns:' ); ?></label>
			<input class="small-text" type="number" value="<?php echo $instance['image_columns']; ?>" id="<?php echo $this->get_field_id( 'image_columns' ); ?>" name="<?php echo $this->get_field_name( 'image_columns' ); ?>"/>
			<br/>
			<label for="<?php echo $this->get_field_id('image_size'); ?>"><?php _e('Image Size:'); ?></label>
	        <select name="<?php echo $this->get_field_name('image_size'); ?>" id="<?php echo $this->get_field_id('image_size'); ?>">
	            <?php
	            echo '<option value="image_ns"', $imageSize == 'image_ns' ? ' selected="selected"' : '', '>', "Nano Square (45x45)", '</option>';
	            echo '<option value="image_ts"', $imageSize == 'image_ts' ? ' selected="selected"' : '', '>', "Tiny Square (75x75)", '</option>';
	            echo '<option value="image_t"', $imageSize == 'image_t' ? ' selected="selected"' : '', '>', "Tiny (75xN)|(Nx75)", '</option>';
	            echo '<option value="image_s"', $imageSize == 'image_s' ? ' selected="selected"' : '', '>', "Small (150xN)|(Nx150)", '</option>';
	            ?>
	        </select>
			<input class="checkbox" type="checkbox" value="1" <?php checked( $instance['image_link_enabled'], true ); ?> id="<?php echo $this->get_field_id('image_link_enabled'); ?>" name = "<?php echo $this->get_field_name('image_link_enabled'); ?>" />
			<label for="<?php echo $this->get_field_id('image_link_enabled'); ?>"><?php _e( 'image link enabled:' ); ?></label>        
			<br/>
	    </p>					
				
		<hr class="div"/>	
		<strong>CSS Stylesheet</strong><br/>			
        <p>
	        <input class="checkbox" type="checkbox" value="1" <?php checked( $instance['widget_css_enabled'], true ); ?> id="<?php echo $this->get_field_id('widget_css_enabled'); ?>" name = "<?php echo $this->get_field_name('widget_css_enabled'); ?>" />
			<label for="<?php echo $this->get_field_id('widget_css_enabled'); ?>"><?php _e( 'Use widget css' ); ?></label>        
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'css_class' ); ?>"><?php _e( 'Custom CSS Class:' ); ?></label>
			<input class="widefat" type="text" value="<?php echo $instance['css_class']; ?>" id="<?php echo $this->get_field_id( 'css_class' ); ?>" name="<?php echo $this->get_field_name( 'css_class' ); ?>" />
		</p>	
		
		<hr class="div"/>	
		<strong>Inline CSS</strong><br/>
		<div>
			<label for="<?php echo $this->get_field_id( 'image_border_colour' ); ?>"><?php _e( 'Image border colour: ' ); ?></label>
			<input class='medium-text' id='<?php echo $this->get_field_id('image_border_colour'); ?>' name='<?php echo $this->get_field_name('image_border_colour'); ?>' type='text' value='<?php echo $instance['image_border_colour']; ?>' />
			<div class="image_border_colourpicker" rel="<?php echo $this->get_field_id('image_border_colour'); ?>"></div>
		</div>
		<div>
			<label for="<?php echo $this->get_field_id( 'css_class' ); ?>"><?php _e( 'Image background colour: ' ); ?></label>
			<input class='medium-text' id='<?php echo $this->get_field_id('image_background_colour'); ?>' name='<?php echo $this->get_field_name('image_background_colour'); ?>' type='text' value='<?php echo $instance['image_background_colour']; ?>' />
			<div class="image_background_colourpicker" rel="<?php echo $this->get_field_id('image_background_colour'); ?>"></div>
		</div>			
		<?php
	}
}

function ehive_object_gallery_script() {
	wp_enqueue_script('farbtastic');
}
function ehive_object_gallery_style() {
	wp_enqueue_style('farbtastic');
}

//add_action('admin_print_scripts-widgets.php', 'sample_load_color_picker_script');
//add_action('admin_print_styles-widgets.php', 'sample_load_color_picker_style');

?>