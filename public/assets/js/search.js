function Search(SearchId,SearchList,SearchItem,SearchInfo) {
    var input, filter, ul, li, a, i, txtValue;
    input = document.getElementById(SearchId);
    filter = input.value.toUpperCase();
    ul = document.getElementById(SearchList);
    li = ul.getElementsByClassName(SearchItem);
    for (i = 0; i < li.length; i++) {
        a = li[i].getElementsByClassName(SearchInfo)[0];
        txtValue = a.textContent || a.innerText;
        if (txtValue.toUpperCase().indexOf(filter) > -1) {
            li[i].style.display = "";
        } else {
            li[i].style.display = "none";
        }
    }
}