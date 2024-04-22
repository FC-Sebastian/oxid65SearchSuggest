[{$smarty.block.parent}]
<div id="searchSuggestionDiv"></div>
<script>
    const BaseUrl = "[{$oConfig->getShopUrl()}]";
</script>
[{oxscript include=$oViewConf->getModuleUrl('fcsearchsuggest', 'out/src/js/suggestions.js')}]