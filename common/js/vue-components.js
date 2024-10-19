import * as Vue from './vendor/vue-esm-browser.js';

const themeUrl = '/wp-content/themes/starter-kit-classic';

const vueSfcLoaderOptions = {
	moduleCache: { vue: Vue },
	getFile: async(url) => {
		const res = await fetch(url);
		if (!res.ok) {
			throw Object.assign(new Error(res.statusText + ' ' + url), { res });
		}

		return {
			getContentData: () => {
				return res.text().then((content) => {
					// Filter out the <style> tags from the component as they need to be processed separately
					const dom = new DOMParser().parseFromString(content, 'text/html');
					return Array.from(dom.head.children)
						.filter((element) => element.tagName !== 'STYLE')
						.map((element) => element.outerHTML)
						.join('\n');
				});
			},
		};
	},
	addStyle: async(fileUrl) => {
		const res = await fetch(fileUrl);
		const dom = new DOMParser().parseFromString(await res.text(), 'text/html');
		const css = Array.from(dom.head.children).find((element) => element.tagName === 'STYLE');
		if (css?.textContent) {
			const style = document.createElement('style');
			style.setAttribute('data-vue-component', fileUrl.split('/').pop());
			style.type = 'text/css';
			style.textContent = css.textContent;
			document.body.appendChild(style);
		}
	},
	async handleModule(type, getContentData, path, options) {
		if (type === '.vue') {
			options.addStyle(path);
		}
	},
};

const { loadModule } = window['vue3-sfc-loader'];

Vue.createApp({
	components: {
		SiteNavigation: Vue.defineAsyncComponent(() => loadModule(`${themeUrl}/components/site-header/site-navigation.vue`, vueSfcLoaderOptions)),
	},
	template: '',
	compilerOptions: {},
}).mount('[data-vue-component="site-navigation"]');
