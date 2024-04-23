[{$smarty.block.parent}]
<div id="suggestionContainer" class="d-none">
    <div id="searchSuggestionDiv" class="row">

    </div>
</div>
<script>
    [{assign var="oConf" value=$oViewConf->getConfig()}]
    const BaseUrl = "[{$oConf->getShopUrl()}]";
</script>
[{oxscript include=$oViewConf->getModuleUrl('fcsearchsuggest', 'out/src/js/suggestions.js')}]
[{oxstyle include=$oViewConf->getModuleUrl('fcsearchsuggest', 'out/src/css/suggestions.css')}]