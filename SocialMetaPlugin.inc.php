<?php

/**
 * @file plugins/generic/socialMeta/SocialMetaPlugin.inc.php
 *
 * Copyright (c) 2018 Vitaliy Bezsheiko, MD
 * Distributed under the GNU GPL v3.
 *
 * @brief Inject Open Graph and Twitter meta tags into article.
 */

class SocialMetaPlugin extends GenericPlugin {

	function getDisplayName() {
		return __('plugins.generic.socialMeta.name');
	}

	function getContextSpecificPluginSettingsFile() {
		return $this->getPluginPath() . '/settings.xml';
	}

	function getDescription() {
		return __('plugins.generic.socialMeta.description');
	}

	function register($category, $path, $mainContextId = null) {
		if (parent::register($category, $path, $mainContextId)) {
			if ($this->getEnabled()) {
				HookRegistry::register('ArticleHandler::view',array(&$this, 'articleMeta'));
			}
			return true;
		}
		return false;
	}

	function articleMeta($hookName, $args) {
		/**
		 * @var $article Submission
		 * @var $publication Publication
		 * @var $journal Journal
		 */
		$request = $args[0];
		$article = $args[2];
		$publication = $args[3];
		$journal = $request->getContext();

		$templateMgr = TemplateManager::getManager($request);

		$publicationUrl = $publication->getId() !== $article->getCurrentPublication()->getId() ?
			$request->url(null, 'article', 'view', array('version', $publication->getId())) :
			$request->url(null, 'article', 'view', array($article->getId()));
		$templateMgr->addHeader('ogURL', '<meta property="og:url" content="' . $publicationUrl . '"/>');

		$templateMgr->addHeader('ogType', '<meta property="og:type" content="article"/>');

		$title = $publication->getLocalizedTitle();
		$templateMgr->addHeader('ogTitle', '<meta property="og:title" content="' . trim(htmlspecialchars($title)) . '"/>');
		$templateMgr->addHeader("twitterTitle", '<meta name="twitter:title" content="' . trim(htmlspecialchars($title)) . '"/>');

		$abstract = $publication->getLocalizedData('abstract');
		if ($abstract) {
			$templateMgr->addHeader('ogDescription', '<meta property="og:description" content="' . htmlspecialchars(strip_tags($abstract)) . '"/>');
			$templateMgr->addHeader('twitterDescription', '<meta name="twitter:description" content="' . htmlspecialchars(strip_tags($abstract)) . '"/>');
		}

		$imageUrl = $publication->getLocalizedCoverImageUrl($journal->getId());
		if (!empty($imageUrl)) {
			$templateMgr->addHeader('ogImage', '<meta property="og:image" content="' . $imageUrl . '"/>');
			$templateMgr->addHeader('twitterImage', '<meta name="twitter:image" content="' . $imageUrl . '"/>');
		}

		$templateMgr->addHeader("twitterCard", '<meta name="twitter:card" content="summary_large_image"/>');
	}
}

?>
