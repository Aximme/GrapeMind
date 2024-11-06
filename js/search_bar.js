
    function fetchSuggestions(query) {
    if (query.length < 2) {
    document.getElementById("search-suggestions").innerHTML = "";
    return;
}

    fetch(`/components/wine_map/search_bar_server.php?query=${encodeURIComponent(query)}`)
    .then(response => response.json())
    .then(data => {
    let suggestions = "";
    data.slice(0, 4).forEach(item => {
    suggestions += `
                        <div class='search-suggestion-item' onclick="selectSuggestion('${item.name}')">
                            <img src="${item.thumb}" alt="${item.name}" style="width: 30px; height: 30px; margin-right: 10px;">
                            ${item.name}
                        </div>`;
});
    document.getElementById("search-suggestions").innerHTML = suggestions;
});
}

    function selectSuggestion(value) {
    document.querySelector('.search-bar').value = value;
    document.getElementById("search-suggestions").innerHTML = "";
}
