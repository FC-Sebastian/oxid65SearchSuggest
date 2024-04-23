<?php

namespace FATCHIP\SearchSuggest\Application\Model;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;

class Category extends Category_parent
{
    protected $iTplLang;
    protected $iNumOfCategorySuggestions = 10;

    public function fcGetRecommendedCategories($sSearch)
    {
        $oLang = Registry::getLang();
        $this->iTplLang = $oLang->getTplLanguage();

        $aCategories = $this->fcGetTitleResults($sSearch);

        $aReturn = [];
        foreach ($aCategories as $aRow) {
            $oCategory = oxNew(self::class);
            $oCategory->load($aRow['oxid']);

            $aNext['url'] = $oCategory->getLink($this->iTplLang);
            $aNext['title'] = $this->fcGetBoldTitle($oCategory, $sSearch);

            $aReturn[] = $aNext;
        }

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
            ->from('oxcategories')
            ->where($sTitleColumn . ' LIKE ?')
            ->setMaxResults($this->iNumOfCategorySuggestions)
            ->setParameter(0, '%'.$sSearch.'%');
        $oResult = $oQueryBuilder->execute();

        return $oResult->fetchAllAssociative();
    }

    protected function fcGetBoldTitle($oCategory, $sSearch)
    {
        $sLowTitle = strtolower($oCategory->oxcategories__oxtitle->value);
        $sLowSearch = strtolower($sSearch);

        $iOffset = strpos($sLowTitle, $sLowSearch);

        return substr($oCategory->oxcategories__oxtitle->value, 0, $iOffset)
            . '<b>'
            . substr($oCategory->oxcategories__oxtitle->value, $iOffset, strlen($sSearch))
            . '</b>'
            . substr($oCategory->oxcategories__oxtitle->value, $iOffset + strlen($sSearch));

    }
}