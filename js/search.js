$("#search").on('input', function() {
    console.log(this.value);
    const query = this.value;
    $.get(`request/search.php?q=${query}`,(response) => {
        $("#search-result__content").html(response);
        $("#search-result__wrapper").remove("hidden");
    })
})
$("#search").on('focus', () => {
    $("#search-result__wrapper").remove("hidden");
})
document.body.addEventListener('click', (e) => {
    const wrapper = $("#search-result__content").element;

    if (!wrapper.contains(e.target)) {
        console.log("NOT");
        $("#search-result__wrapper").add("hidden");
    } else {
        console.log("YES");
    }
})
document.addEventListener('keyup', (e) => {
    if (e.code == "Escape") {
        $("#search-result__wrapper").add("hidden");
    }
})
