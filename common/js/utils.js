// Adapted from https://codepen.io/boosmoke/pen/abbMZzb
export const waitForElementToExist = (selector, limit) => {
	return new Promise((resolve, reject) => {
		let count = 0;
		(function waitForFoo() {
			const element = document.querySelector(selector);
			if (element) {
				return resolve(element);
			}
			if (limit && count > limit) {
				reject(new Error('Element not found'));
				return false;
			}
			count += 1;
			setTimeout(waitForFoo, 50);
		}());
	});
};

export function arrayToHtmlCollection(array) {
	const fragment = document.createDocumentFragment();
	array.forEach((child) => {
		fragment.appendChild(child);
	});
	return fragment;
}
