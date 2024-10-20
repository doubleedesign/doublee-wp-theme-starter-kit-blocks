<script lang="ts">

export default {
    name: 'SiteNavigation',
    inheritAttrs: false,
    props: {
        sitename: String,
        logourl: String,
        menu: String,
        background: String,
    },
    data() {
        return {
            overlayMenuOpen: false,
            menuItems: [],
            topLevelMenuItems: [],
            theme: {},
            isSmallScreen: false,
        };
    },
    mounted() {
        this.menuItems = JSON.parse(this.menu);
        this.topLevelMenuItems = this.menuItems.filter((item) => item.menu_item_parent === '0');
        this.overlayMenuOpen = false;
        this.fetchTheme().then(() => {
            this.checkScreenSize();
        });

        window.addEventListener('resize', this.debounce(this.checkScreenSize, 200));
    },
    computed: {},
    methods: {
        toggle() {
            this.overlayMenuOpen = !this.overlayMenuOpen;
        },
        async fetchTheme() {
            try {
                const response = await fetch('/wp-content/themes/starter-kit-classic/theme-vars.json');
                this.theme = await response.json();
            } catch (error) {
                console.error('Error fetching theme:', error);
            }
        },
        async checkScreenSize() {
            if (this.theme) {
                this.isSmallScreen = window.matchMedia(`(max-width: ${this.theme['grid-breakpoints'].lg})`).matches;
            } else {
                console.log('Theme not available yet');
            }
        },
        debounce(func, delay) {
            let timerId;
            return function () {
                clearTimeout(timerId);
                timerId = setTimeout(func, delay);
            };
        },
    },
};
</script>

<template>
    <div class="row row--wide">
        <div class="site-header__logo col-2">
            <a href="/">
                <img v-if="this.logourl !== ''" :src="this.logourl" :alt="this.sitename"
                     :class="`has-${background}-background-color`"/>
                <span v-else>{{ this.sitename }}</span>
            </a>
        </div>
        <nav v-if="!this.isSmallScreen" class="site-header__menu-main col-10">
            <ul class="site-header__menu-main__list">
                <li v-for="item in this.topLevelMenuItems" :key="item.id"
                    :class="['site-header__menu-main__list__item',
							...item.classes.map(className => `site-header__menu-main__list__item--${className}`)
							].join(' ')">
                    <a v-if="item.classes.includes('is-button')" class="btn btn--secondary" :href="item.url">
                        <span>{{ item.title }}</span>
                        <i v-if="item.classes.includes('external')"
                           class="fa-sharp fa-solid fa-up-right-from-square"></i>
                    </a>
                    <a v-else :href="item.url">
                        <span>{{ item.title }}</span>
                        <i v-if="item.classes.includes('external')"
                           class="fa-sharp fa-solid fa-up-right-from-square"></i>
                    </a>
                </li>
            </ul>
        </nav>
        <div v-if="this.isSmallScreen" class="site-header__menu-toggle col-4">
            <button id="header-menu-button"
                    class="btn btn--secondary--hollow btn--icon"
                    aria-controls="primary-menu"
                    :aria-expanded="this.overlayMenuOpen"
                    :aria-label="this.overlayMenuOpen ? 'Close primary menu' : 'Open primary menu'"
                    :title="this.overlayMenuOpen ? 'Close menu' : 'Open menu'"
                    @click="this.toggle">
				<span v-if="!this.overlayMenuOpen">
					<i class="fa-solid fa-bars"></i>
				</span>
                <span v-else>
					<i class="fa-solid fa-times"></i>
				</span>
            </button>
        </div>
    </div>
    <Transition>
        <nav v-if="this.overlayMenuOpen && this.isSmallScreen"
             :class="`site-header__menu-overlay has-${background}-background-color`">
            <ul class="site-header__menu-overlay__list">
                <li v-for="item in this.topLevelMenuItems" :key="item.id"
                    :class="['site-header__menu-overlay__list__item',
							...item.classes.map(className => `site-header__menu-overlay__list__item--${className}`)
							].join(' ')">
                    <a v-if="item.classes.includes('is-button')" class="btn btn--secondary btn--large" :href="item.url">
                        <span>{{ item.title }}</span>
                        <i v-if="item.classes.includes('external')"
                           class="fa-sharp fa-solid fa-up-right-from-square"></i>
                    </a>
                    <a v-else :href="item.url">
                        <span>{{ item.title }}</span>
                        <i v-if="item.classes.includes('external')"
                           class="fa-sharp fa-solid fa-up-right-from-square"></i>
                    </a>
                </li>
            </ul>
        </nav>
    </Transition>
</template>

<style lang="css">
.v-enter-active,
.v-leave-active {
    transition: opacity 0.3s ease;
}

.v-enter-from,
.v-leave-to {
    opacity: 0;
}
</style>
