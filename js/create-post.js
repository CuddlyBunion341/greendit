$('.post-tab').click(() => {
    console.log("post-tab");
    $('.post-content [data-tab="0"]').remove("hidden");
    $('.post-content [data-tab="1"]').add("hidden");
})

$('.media-tab').click(() => {
    console.log("media-tab");
    $('.post-content [data-tab="0"]').add("hidden");
    $('.post-content [data-tab="1"]').remove("hidden");
})