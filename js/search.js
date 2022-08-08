function search(query) {
	$.get(`request/search.php?q=${query}`, (response) => {
		$("#search-result__content").html(response);
		$("#search-result__wrapper").remove("hidden");
	});
}
$("#search").on("input", function () {
	const query = this.value;
	search(query);
});
$("#search").on("focus", () => {
	$("#search-result__wrapper").remove("hidden");
	search($("#search").value());
});
$("#search").on("focusout", () => {
	setTimeout(() => {
		$("#search-result__wrapper").add("hidden");
	}, 200);
});
document.addEventListener("keyup", (e) => {
	if (e.code == "Escape") {
		$("#search-result__wrapper").add("hidden");
	}
});
