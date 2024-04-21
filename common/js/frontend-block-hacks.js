document.addEventListener('DOMContentLoaded', function() {
	addClassesToLatestPostsBlock();
});

function addClassesToLatestPostsBlock() {
	const latestPostsItems = document.querySelectorAll('.wp-block-latest-posts__list li');
	if (latestPostsItems.length > 0) {
		latestPostsItems.forEach(function(item) {
			item.classList.add('card-wrapper');
			item.classList.add('has-white-background-color');
			item.classList.add('entry-content');
		});
	}

	const latestPostsButtons = document.querySelectorAll('.wp-block-latest-posts__post-excerpt a');
	if (latestPostsButtons.length > 0) {
		latestPostsButtons.forEach(function(button) {
			button.classList.add('btn');
			button.classList.add('btn--primary');
			button.classList.add('btn--small');
		});
	}
}
