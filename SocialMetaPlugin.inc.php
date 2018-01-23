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

	function register($category, $path) {
		if (parent::register($category, $path)) {
			if ($this->getEnabled()) {
				HookRegistry::register('ArticleHandler::view',array(&$this, 'articleMeta'));
			}
			return true;
		}
		return false;
	}

	function articleMeta($hookName, $args) {
		$request = $args[0];
		$article = $args[2];
		$journal = $request->getContext();
		$locale = $journal->getPrimaryLocale();

		$templateMgr = TemplateManager::getManager($request);

		$templateMgr->addHeader('ogURL', '<meta property="og:url" content="' . $request->url(null, 'article', 'view', array($article->getBestArticleId())) . '"/>');

		$templateMgr->addHeader('ogType', '<meta property="og:type" content="article"/>');

		$templateMgr->addHeader('ogTitle', '<meta property="og:title" content="' . trim(htmlspecialchars($article->getLocalizedTitle())) . '"/>');
		$templateMgr->addHeader("twitterTitle", '<meta name="twitter:title" content="' . trim(htmlspecialchars($article->getLocalizedTitle())) . '"/>');

		if ($article->getAbstract($locale)) {
			$templateMgr->addHeader('ogDescription', '<meta property="og:description" content="' . htmlspecialchars(strip_tags($article->getAbstract($locale))) . '"/>');
			$templateMgr->addHeader('twitterDescription', '<meta name="twitter:description" content="' . htmlspecialchars(strip_tags($article->getAbstract($locale))) . '"/>');
		}

		if ($article->getLocalizedCoverImageUrl()) {
			$templateMgr->addHeader('ogImage', '<meta property="og:image" content="' . $article->getLocalizedCoverImageUrl() . '"/>');
			$templateMgr->addHeader('twitterImage', '<meta name="twitter:image" content="' . $article->getLocalizedCoverImageUrl() . '"/>');
		}

		$templateMgr->addHeader("twitterCard", '<meta name="twitter:card" content="summary_large_image"/>');
	}
}

?>