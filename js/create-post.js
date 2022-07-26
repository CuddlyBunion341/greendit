$(".post-tab").click(() => {
	console.log("post-tab");
	$('.post-content [data-tab="0"]').remove("hidden");
	$('.post-content [data-tab="1"]').add("hidden");
	$('#tab-val').value("0");
});

$(".media-tab").click(() => {
	console.log("media-tab");
	$('.post-content [data-tab="0"]').add("hidden");
	$('.post-content [data-tab="1"]').remove("hidden");
	$('#tab-val').value("1");
});

$("#media").on("change", () => {
	const preview = $("#preview").element;
	while (preview.firstChild) {
		preview.removeChild(preview.firstChild);
	}
	const input = $("#media").element;
	for (const file of input.files) {
		const url = URL.createObjectURL(file);
		const img = $.createElementFromHTML(`<img src="${url}">`);
		$(preview).append(img);
	}
});
