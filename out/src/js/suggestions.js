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
                /*
                used when html is built server side
                searchSuggestions.innerHTML = xhr.response;
                suggestionContainer.className = 'searchSuggestions border rounded bg-white';
                 */

                //used when html is built client side
                if (isJson(xhr.response)) {
                    let response = JSON.parse(xhr.response);
                    let articles = response.articles;
                    let categories = response.categories;
                    let articleDiv = getArticleDiv(articles);

                    searchSuggestions.innerHTML = '';
                    if (categories.length) {
                        articleDiv.className = 'col-9';
                        let categoriesDiv = getCategoryDiv(categories);
                        searchSuggestions.append(categoriesDiv);
                    } else {
                        articleDiv.className = 'col-12';
                    }
                    searchSuggestions.prepend(articleDiv);

                    suggestionContainer.className = 'searchSuggestions border rounded bg-white';
                } else {
                    suggestionContainer.className = 'd-none';
                }
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

/**
 * Checks whether given string is JSON or not
 *
 * @param string
 * @returns {boolean}
 */
function isJson(string)
{
    try {
        JSON.parse(string)
    } catch (e) {
        return false
    }
    return true
}

/**
 * Builds and returns html div element containing article suggestions
 *
 * @param articles
 * @returns {HTMLDivElement}
 */
function getArticleDiv(articles) {
    let div = document.createElement('div');

    for (let i = 0; i < articles.length; i++) {
        let suggestion = document.createElement('div');
        let suggestionLink = document.createElement('a');
        let thumbContainer = document.createElement('div');
        let thumbnail = document.createElement('img');

        suggestion.className = 'row border rounded justify-content-around';
        suggestionLink.className = 'd-block w-100 align-middle';
        suggestionLink.href = articles[i].url;
        suggestionLink.innerHTML = articles[i].title;
        thumbContainer.className = 'w-25 text-center d-inline-block';
        thumbnail.className = 'suggestionImg';
        thumbnail.src = articles[i].picUrl;

        thumbContainer.append(thumbnail);
        suggestionLink.prepend(thumbContainer);
        suggestion.append(suggestionLink);
        div.append(suggestion)
    }
    return div;
}

/**
 * Builds and returns html div containing category suggestions
 *
 * @param categories
 * @returns {HTMLDivElement}
 */
function getCategoryDiv(categories) {
    let div = document.createElement('div');
    let ul = document.createElement('ul');

    div.className = 'col-3 border-left border-dark';
    ul.className = 'h-100 pt-5';

    for (let i = 0; i < categories.length; i++) {
        let li = document.createElement('li');
        let a = document.createElement('a');

        li.className = 'h-10';
        a.href = categories[i].url;
        a.innerHTML = categories[i].title;

        li.append(a);
        ul.append(li);
    }
    div.append(ul);
    return div;
}