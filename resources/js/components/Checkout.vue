<template>
	<div id="checkout" class="page">
		<h1>Checkout</h1>

		<div class="meta-film">
			<div class="locandina">
				<img :src="dettagli.locandina" :alt="dettagli.titolo">
			</div>

			<div class="info-film">
				<h1>{{ dettagli.titolo }}</h1>
				<p><span>Anno:</span> {{ dettagli.anno }}</p>
				<p><span>Genere:</span> {{ dettagli.genere }}</p>
				<p><span>Durata:</span> {{ dettagli.durata }}</p>
				<p><span>Lingua:</span> Italiano</p>
			</div>
		</div>

		<div class="scelte">
			<div>
				<span>{{ checkedGiorno }}</span>
				{{ $t('Data') }}
			</div>

			<div>
				<span>{{ checkedOrario.ora }}</span>
				{{ $t('Ora') }}
			</div>

			<div>
				<span>{{ checkedOrario.sala }}</span>
				{{ $t('Sala') }}
			</div>
		</div>

		<div class="biglietti">
			{{ $t('biglietti') }}

			<div class="tik" v-for="tik in checkedBiglietto.carrello">
				<span class="nome" v-if="!tik.qtySnack">{{ tik.nome }}, {{ tik.posto }}</span>
				<span class="nome snack" v-if="tik.qtySnack">{{ tik.nome }}</span>
				<span class="qtySnack" v-if="tik.qtySnack && tik.qty > 1">{{ tik.qtySnack }}</span>
				<span class="prezzo" v-if="!tik.qtySnack">{{ tik.prezzo }}€</span>
				<span class="prezzo" v-if="tik.qtySnack">{{ tik.prezzoTot }}€</span>
			</div>
		</div>

		<div class="totale flex">
			<span>{{ $t('Totale') }}:</span>
			<span class="prezzo">{{ checkedBiglietto.totale }}€</span>
		</div>

		<stripe-checkout
			ref="checkoutRef"
			:pk="publishableKey"
			:sessionId="sessionId"
		/>

		<div id="btn-acquista" class="submit flex" @click="submit()">
			{{ $t('Acquista i biglietti') }}
			<i class="cart"></i>
		</div>
	</div>
</template>

<script>
	import axios from 'redaxios'
	// import $ from 'jquery'
	// import { v4 as uuidv4 } from "uuid"
	import { StripeCheckout } from '@vue-stripe/vue-stripe';

	// const sessionID = uuidv4().replace(/-/g, '');

	import.meta.glob(['../../images/**',]);

	export default {
		props: ['route', 'path'],
		components: {
			StripeCheckout,
		},
		data() {
			return {
				publishableKey: import.meta.env.VITE_STRIPE_KEY,
				appUrl: this.path,
				id: this.route,
				WebtikBase: import.meta.env.WEBTIK_SERVICE_BASE,
				sessionId: null,
				idCinema: 600,
				spettacolo: '',
				dettagli: {},
				orari: [],
				prezzi: [],
				biglietti: [],
				nBiglietti: 0,
				recap: '',
				checkedGiorno: '',
				checkedOrario: {
					ora: '',
					n_sala: '',
					sala: '',
					idsala: '',
					idPerf: '',
					idTariffa: ''
				},
				carrello: {
					spettacolo: '',
					carrello: [],
					nBiglietti: 0,
					recap: [],
					prezzi: [],
					totString: '',
					totale: 0,
					index: 0,
					isChecked: false,
				},
				postiScelti: [],
				fiscal_address: '',
				fiscal_port: '',
				browser: '',
				loading: false,
				lineItems: [],
			};
		},
		methods: {
			fetchCookiedata: function() {
				let sessionCookie = localStorage.getItem('sessionCookie') ? JSON.parse(localStorage.getItem('sessionCookie')) : {};
				
				console.log(sessionCookie);
				this.cookieData = sessionCookie;
				
				if (Object.keys(this.cookieData).length > 0) {
					this.sessionID = this.cookieData.sessionCookieID ? this.cookieData.sessionCookieID : sessionID;
					this.spettacolo = this.cookieData.spettacolo ? this.cookieData.spettacolo : '';
					this.nBiglietti = this.cookieData.nBiglietti ? this.cookieData.nBiglietti : 0;
					this.postiScelti = this.cookieData.posti ? this.cookieData.posti : [];

					this.checkedGiorno = this.cookieData.giorno ? this.cookieData.giorno : '';
					this.checkedOrario.ora = this.cookieData.ora ? this.cookieData.ora : '';
					this.checkedOrario.sala = this.cookieData.sala ? this.cookieData.sala : '';
					this.checkedOrario.idsala = this.cookieData.idsala ? this.cookieData.idsala : '';
					this.checkedOrario.idPerf = this.cookieData.idPerf ? this.cookieData.idPerf : '';
					this.checkedOrario.idTariffa = this.cookieData.idTariffa ? this.cookieData.idTariffa : '';

					this.carrello = this.cookieData.carrello ? this.cookieData.carrello : {};
					this.checkedBiglietto.recap = this.cookieData.recap ? this.cookieData.recap : [];
					this.checkedBiglietto.prezzi = this.cookieData.prezzi? this.cookieData.prezzi : [];
					this.checkedBiglietto.totString = this.cookieData.totString ? this.cookieData.totString : '';
					this.checkedBiglietto.totale = this.cookieData.totale ? this.cookieData.totale : 0;
					this.checkedBiglietto.nBiglietti = this.cookieData.nBiglietti ? this.cookieData.nBiglietti : 0;
					this.checkedBiglietto.isChecked = this.cookieData.isChecked ? this.cookieData.isChecked : false;

					this.recap = this.cookieData.recapString ? this.cookieData.recapString : '';
					this.activeSubmit = this.cookieData.activeSubmit && this.cookieData.spettacolo === this.id ? this.cookieData.activeSubmit : false;
				}

				console.log(this.checkedBiglietto)
				this.fetchEvento(this.spettacolo);
			},
			fetchEvento: function (idevento) {
				axios.get(`${this.WebtikBase}_getPerformanceListDetail?idcinema=${idCinema}&idevento=${idevento}`)
					.then((res) => {
					const XmlNode = new DOMParser().parseFromString(res.data, 'text/xml'),
						jsonData = this.xmlToJson(XmlNode),
						evento = jsonData.PerformanceListDetail.evento,
						performances = jsonData.PerformanceListDetail.performances.performance,
						durata = evento.durata,
						genere = evento.genere;

					let durata_arr = durata.split(':'),
						genere_arr = genere.split(',');

					this.dettagli = {
						titolo: evento.titolo,
						anno: evento.anno,
						durata: `${Math.trunc(durata_arr[0])}h ${durata_arr[1]}min`,
						generi: genere_arr,
						genere: genere_arr[0],
						locandina: '',
						lingua: 'Italiano',
					};

					this.addImage(idevento);
				});
			},
			addImage: function (idevento) {
				axios.get(`${this.WebtikBase}_getEventImage?idcinema=${this.idCinema}&idevento=${idevento}`)
					.then((res) => {
					const XmlNode = new DOMParser().parseFromString(res.data, 'text/xml'),
						jsonData = this.xmlToJson(XmlNode);

					this.dettagli.locandina = `data:image/png;base64,${jsonData.base64Binary}`;
				});
			},
			fetchFiscalAddress: function() {
				axios.get(`${this.WebtikBase}_getFiscalAddress?idcinema=${this.idCinema}`)
					.then((res) => {
					const XmlNode = new DOMParser().parseFromString(res.data, 'text/xml'),
						jsonData = this.xmlToJson(XmlNode);

					this.fiscal_address = jsonData.fiscal_address;
					this.fiscal_port = jsonData.fiscal_port;
				});
			},
			checkout: function() {
				let tipoBiglietti_array = [],
					tariffa = '',
					qty = 0;

				// divido le tipologie di biglietto richieste
				this.checkedBiglietto.carrello.map((item) => {
					if (item.type === 'biglietto') {
						tariffa = item.tariffa;

						if (tariffa === item.tariffa)
							qty += item.qty;
					}
					if (item.type === 'biglietto' && tipoBiglietti_array.findIndex(function(o){ return o.id === item.tariffa }) === -1)
						tipoBiglietti_array.push({id: item.tariffa, qty: qty});
				});

				let posti = this.postiScelti.join(';');

				// Blocca i posti prima della transazione
				axios.get(`${this.WebtikBase}setBloccoPosti?idcinema=${this.idCinema}&idperformance=${this.checkedOrario.idPerf}&sSessionId=${this.sessionID}&aiProgPosti=${posti}`)
					.then((res) => {
					const XmlNode = new DOMParser().parseFromString(res.data, 'text/xml'),
						jsonData = this.xmlToJson(XmlNode);

					console.log(jsonData);
					// if (jsonData.boolean) {
					// 	this.activeSubmitCheckout = false;
					// 	this.activeSubmit = false;
					// 	this.saveSessionCookie();
					// 	this.$router.push({ name: 'checkout' });
					// } else {
					// 	alert('Errore');
					// }
				});

				// Passo i prodotti a Stripe
				// [
				//     [
				//         'price_data' => [
				//             'currency' => 'eur',
				//             'unit_amount' => 100,
				//             'product_data' => [
				//                 'name' => 'FCE - First Class Entertainment',
				//             ],
				//         ],
				//         'quantity' => 1,
				//     ],
				// ],
				let items = [];
				this.checkedBiglietto.carrello.map((item) => {
					let nome = item.type === 'biglietto' ? `${item.nome} ${item.posto}` : item.nome;

					// items.push({
					// 	currency: 'eur',
					// 	unit_amount: (item.prezzo).replace(',', ''), //parseInt(item.prezzo) * 100,
					// 	name: nome,
					// 	quantity: item.qty,
					// 	// price_data: {
					// 	// 	currency: 'eur',
					// 	// 	unit_amount: (item.prezzo).replace(',', ''), //parseInt(item.prezzo) * 100,
					// 	// 	product_data: {
					// 	// 		name: nome,
					// 	// 	},
					// 	// },
					// 	// quantity: item.qty,
					// });

					items.push(`eur // ${(item.prezzo).replace(',', '')} // ${nome} // ${item.qty} // ${this.spettacolo}`)
				});

				this.lineItems = items; //this.propertiesToArray(items);
				console.log(this.lineItems);
				this.getSession(this.lineItems);
			},
			xmlToJson: function(xml) {
				// Create the return object
				let obj = {};

				if (xml.nodeType == 1) {
					// element
					// do attributes
					if (xml.attributes.length > 0) {
					obj = {};
					for (let j = 0; j < xml.attributes.length; j++) {
						let attribute = xml.attributes.item(j);
						obj[attribute.nodeName] = attribute.nodeValue;
					}
					}
				} else if (xml.nodeType == 3) {
					// text
					obj = xml.nodeValue;
				}

				// do children
				// If all text nodes inside, get concatenated text from them.
				let textNodes = [].slice.call(xml.childNodes).filter(function(node) {
					return node.nodeType === 3;
				});
				if (xml.hasChildNodes() && xml.childNodes.length === textNodes.length) {
					obj = [].slice.call(xml.childNodes).reduce(function(text, node) {
					return text + node.nodeValue;
					}, "");
				} else if (xml.hasChildNodes()) {
					for (let i = 0; i < xml.childNodes.length; i++) {
					let item = xml.childNodes.item(i);
					let nodeName = item.nodeName;
					if (typeof obj[nodeName] == "undefined") {
						obj[nodeName] = this.xmlToJson(item);
					} else {
						if (typeof obj[nodeName].push == "undefined") {
						let old = obj[nodeName];
						obj[nodeName] = [];
						obj[nodeName].push(old);
						}
						obj[nodeName].push(this.xmlToJson(item));
					}
					}
				}
				return obj;
			},
			propertiesToArray: function(val) {
				//By default (val is not object or array, return the original value)
				let result = val;
				// If object passed the result is the return value of Object.entries()
				if (typeof val === 'object' && !Array.isArray(val)) {
					result = Object.entries(val);
					// result = Object.keys(val).map((key) => [key, val[key]]); //Object.entries(val);
					// If one of the results is an array or object, run this function on them again recursively
					result.forEach((attr) => {
						attr[1] = this.propertiesToArray(attr[1]);
					});
				}
				//If array passed, run this function on each value in it recursively
				else if (Array.isArray(val)) {
					val.forEach((v, i, a) => {
						a[i] = this.propertiesToArray(v)
					});
				}
				// Return the result
				return result;
			},
			isSafari: function() {
				let ua = navigator.userAgent.toLowerCase(); 
				if (ua.indexOf('safari') != -1) { 
					if (ua.indexOf('chrome') > -1) {
						// Chrome
						this.browser = 'chrome';
					} else {
						// Safari
						this.browser = 'safari';
					}
				}
			},
			getSession(items) {
				axios.get('getSession', {params: {items}})
					.then((res) => {
					console.log(res.data);
					this.sessionId = res.data.id;
				}).catch((err) => {
					console.log(err);
				});
			},
			submit() {
				this.$refs.checkoutRef.redirectToCheckout();
			}
		},
		mounted() {
			this.fetchCookiedata();
			this.isSafari();
			this.fetchFiscalAddress();
			this.checkout();
		}
	}
</script>