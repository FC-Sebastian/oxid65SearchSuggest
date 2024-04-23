<?php

namespace FATCHIP\SearchSuggest\Application\Controller;

class SearchSuggestionsAjax extends \OxidEsales\Eshop\Application\Controller\FrontendController
{
    /**
     * Gets matching articles and categories via search request param,
     * then builds and echoes suggestions html string
     *
     * @return void
     */
    public function render()
    {
        $oRequest = \OxidEsales\Eshop\Core\Registry::getRequest();
        $oArticle = oxNew(\OxidEsales\Eshop\Application\Model\Article::class);
        $oCategory = oxNew(\OxidEsales\Eshop\Application\Model\Category::class);
        $sSearch = $oRequest->getRequestParameter('search');

        $aArticles = $oArticle->fcGetRecommendedArticles($sSearch);
        $aCategories = $oCategory->fcGetRecommendedCategories($sSearch);

        if (!empty($aArticles) || !empty($aCategories)) {
            //used when html is build server side
            //$this->getHtml($aArticles, $aCategories);

            echo json_encode(['articles' => $aArticles, 'categories' => $aCategories]);
        }
        die();
    }

    /**
     * Builds and echoes suggestions html from article and categories
     *
     * @param $aArticles
     * @param $aCategories
     * @return void
     */
    protected function getHtml($aArticles, $aCategories)
    {
        if (count($aCategories)) {
            $sDiv = '<div class="col-9">';
        } else {
            $sDiv = '<div class="col-12">';
        }

        $sDiv .= $this->getArticleSuggestions($aArticles) . '</div>';

        if (count($aCategories)) {
            $sDiv .= $this->getCategorySuggestions($aCategories);
        }

        echo $sDiv;
    }

    /**
     * Returns html-string containing matching articles
     *
     * @param $aArticles
     * @return string
     */
    protected function getArticleSuggestions($aArticles)
    {
        $sDiv = '';
        if (!empty($aArticles)) {
            foreach ($aArticles as $aArticle) {
                $sDiv .= "<div class='row border rounded justify-content-around'><a class='d-block w-100 align-middle' href='{$aArticle['url']}'><div class='w-25 text-center d-inline-block'><img class='suggestionImg' src='{$aArticle['picUrl']}'></div>{$aArticle['title']}</a></div>";
            }
        }
        return $sDiv;
    }

    /**
     * Returns html-string containing matching categories
     *
     * @param $aCategories
     * @return string
     */
    protected function getCategorySuggestions($aCategories)
    {
        $sDiv = '<div class="col-3 border-left border-dark"><ul class="h-100 pt-5">';

        foreach ($aCategories as $aCategory) {
            $sDiv .= "<li class='h-10'><a href='{$aCategory["url"]}'>{$aCategory["title"]}</a></li>";
        }
        return $sDiv . '</ul></div>';
    }
}