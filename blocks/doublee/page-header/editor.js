/* global wp */

import { waitForElementToExist } from '../../../common/js/utils.js';

wp.domReady(async() => {
	const title = await waitForElementToExist('.editor-post-title__input');
	if (title) {
		title.addEventListener('blur', (e) => {
			const heading = document.querySelector('.block__page-header h1');
			if (heading) {
				heading.innerHTML = e.target.textContent;
			}
		});
	}
});
