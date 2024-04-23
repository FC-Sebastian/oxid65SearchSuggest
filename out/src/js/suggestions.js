const searchBar = document.getElementById('searchParam');
const searchSuggestions = document.getElementById('searchSuggestionDiv');
const suggestionContainer = document.getElementById('suggestionContainer');

searchBar.addEventListener('keyup', getSearchSuggestions);
document.addEventListener('click', function (e){
    if (!suggestionContainer.contains(e.target)) {
        suggestionContainer.className = 'd-none';
    }
})

function getSearchSuggestions()
{
    if (searchBar.value.length > 2) {
        let xhr = new XMLHttpRequest();
        xhr.onload = function () {
            if (xhr.response.length) {
                searchSuggestions.innerHTML = xhr.response;
                suggestionContainer.className = 'searchSuggestions border rounded bg-white';
            } else {
                suggestionContainer.className = 'd-none';
            }
        };

        xhr.open('GET', BaseUrl+'?cl=fc_search_suggest&search='+encodeURIComponent(searchBar.value));
        xhr.send();
    } else {
        suggestionContainer.className = 'd-none';
    }
}