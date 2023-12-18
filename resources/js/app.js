import './bootstrap';
import { createApp } from 'vue';
import { i18nVue } from 'laravel-vue-i18n'; 

import Recommended from './components/Recommended.vue';
import AlCinema from './components/AlCinema.vue';
import SingleFilm from './components/SingleFilm.vue';
import Food from './components/Food.vue';
import SingleFood from './components/SingleFood.vue';

const app = createApp({})

app.use(i18nVue, { 
	resolve: async lang => {
		const langs = import.meta.glob('../../lang/*.json');
		return await langs[`../../lang/${lang}.json`]();
	}
})

app.component('recommended', Recommended)
app.component('cmp-alcinema', AlCinema)
app.component('cmp-singlefilm', SingleFilm)
app.component('cmp-food', Food)
app.component('cmp-singlefood', SingleFood)

app.mount('#app')

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();