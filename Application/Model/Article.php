<?php

namespace FATCHIP\SearchSuggest\Application\Model;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;

class Article extends Article_parent
{
    protected int $iNumOfArticleSuggestions = 5;
    protected $iTplLang;

    /**
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Doctrine\DBAL\Exception
     */
    public function fcGetRecommendedArticles($sSearch)
    {
        $oLang = Registry::getLang();
        $this->iTplLang = $oLang->getTplLanguage();

        $aArticles = $this->fcGetTitleResults($sSearch);

        $aAppend = [];
        if (count($aArticles) < $this->iNumOfArticleSuggestions) {
            $aAppend = $this->fcGetLongDescResults($sSearch, $this->iNumOfArticleSuggestions - count($aArticles));
        }
        $aArticles = array_merge($aArticles, $aAppend);

        $aReturn = [];
        foreach ($aArticles as $aRow) {
            $oArticle = oxNew(self::class);
            $oArticle->load($aRow['oxid']);

            $aNext['url'] = $oArticle->getLink($this->iTplLang);
            $aNext['picUrl'] = $oArticle->getThumbnailUrl();
            $aNext['title'] = $this->fcGetBoldTitle($oArticle, $sSearch);

            $aReturn[] = $aNext;
        }

        Registry::getLogger()->error(json_encode($aReturn));

        return $aReturn;
    }

    /**
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Doctrine\DBAL\Exception
     */
    protected function fcGetTitleResults($sSearch): array
    {
        if ($this->iTplLang > 0) {
            $sTitleColumn = 'oxtitle'.$this->iTplLang;
        } else {
            $sTitleColumn = 'oxtitle';
        }

        $oQueryBuilder =
            ContainerFactory::getInstance()
                ->getContainer()
                ->get(QueryBuilderFactoryInterface::class)
                ->create();
        $oQueryBuilder
            ->select('oxid')
            ->from('oxarticles')
            ->where($sTitleColumn . ' LIKE ?')
            ->setMaxResults($this->iNumOfArticleSuggestions)
            ->setParameter(0, '%'.$sSearch.'%');
        $oResult = $oQueryBuilder->execute();

        return $oResult->fetchAllAssociative();
    }

    /**
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Doctrine\DBAL\Exception
     */
    protected function fcGetLongDescResults($sSearch, $iLimit): array
    {
        /**
         * @var $oQueryBuilder \Doctrine\DBAL\Query\QueryBuilder
         */
        if ($this->iTplLang > 0) {
            $sLongDesc = 'oxlongdesc'.$this->iTplLang;
            $sTitle = 'oxtitle'.$this->iTplLang;
        } else {
            $sLongDesc = 'oxlongdesc';
            $sTitle = 'oxtitle';
        }

        $oQueryBuilder = ContainerFactory::getInstance()
                ->getContainer()
                ->get(QueryBuilderFactoryInterface::class)
                ->create();
        $oQueryBuilder
            ->select('ae.oxid', 'a.oxtitle')
            ->from('oxarticles', 'a')
            ->leftjoin('a', 'oxartextends', 'ae', 'a.oxid = ae.oxid')
            ->where('ae.'.$sLongDesc.' LIKE ?')
            ->andWhere('a.'.$sTitle.' NOT LIKE ?')
            ->andWhere('a.oxparentid = ""')
            ->setMaxResults($iLimit)
            ->setParameter(0, '%'.$sSearch.'%')
            ->setParameter(1, '%'.$sSearch.'%');

        $oResult = $oQueryBuilder->execute();

        return $oResult->fetchAllAssociative();
    }

    protected function fcGetBoldTitle($oArticle, $sSearch)
    {
        $sLowTitle = strtolower($oArticle->oxarticles__oxtitle->value);
        $sLowSearch = strtolower($sSearch);

        $iOffset = strpos($sLowTitle, $sLowSearch);

        if ($iOffset !== false) {
            return substr($oArticle->oxarticles__oxtitle->value, 0, $iOffset)
                . '<b>'
                . substr($oArticle->oxarticles__oxtitle->value, $iOffset, strlen($sSearch))
                . '</b>'
                . substr($oArticle->oxarticles__oxtitle->value, $iOffset + strlen($sSearch));
        } else {
            return $oArticle->oxarticles__oxtitle->value;
        }
    }
}