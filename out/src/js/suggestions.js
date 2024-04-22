const searchBar = document.getElementById('searchParam');

searchBar.addEventListener('change', getSearchSuggestions);

function getSearchSuggestions()
{
    console.log(searchBar.value);
    let xhr = new XMLHttpRequest();
    xhr.onload = function () {
        console.log(xhr.response);
    };

    xhr.open('GET', BaseUrl+"?cl=fc_search_suggest");
    xhr.send();
}