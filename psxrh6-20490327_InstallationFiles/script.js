// To make the dropdown list in forms searchable - code taken from this StackOverflow post: https://stackoverflow.com/questions/18796221/creating-a-select-box-with-a-search-option  
// The function uses Selectize, a JS library: https://selectize.dev/

$(document).ready(function () {
    $('select').selectize({
        sortField: 'text'
    });
});
