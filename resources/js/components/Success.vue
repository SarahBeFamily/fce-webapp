<template>
	<div id="success" class="page" :style="{ 'background': 'linear-gradient(180deg, rgba(16, 16, 16, 0.00) 0%, rgba(16, 16, 16, 0.5) 30%, #080808 76.46%), url(' + dettagli.locandina + ')' }">
		
		<i class="check-success"></i>

		<h1>{{ $t('Pagamento andato a buon fine') }}</h1>
		<p v-if="device == 'mobile'">
			{{ $t('Ora puoi visualizzare direttamente il tuo biglietto oppure aggiungerlo al Wallet per averlo a portata di mano') }}
		</p>

		<div class="actions">
			<button id="btn-download-tickets" @click="getTickets()" class="with-icon primary-black">
				{{ $t('Visualizza biglietti') }}
				<i class="tickets white"></i>
			</button>

			<button v-if="device == 'mobile'" id="btn-wallet" @click="addWallet()" class="with-icon primary-black border">
				{{ $t('Aggiungi al Wallet') }}
				<i class="wallet"></i>
			</button>
		</div>
	</div>
</template>

<script>
	import axios from 'redaxios'
	import $ from 'jquery'

	import.meta.glob(['../../images/**',]);

	export default {
		props: ['film', 'order'],
    data() {
        return {
			id: this.film,
			order: this.order,
			sessionId: null,
			idCinema: null,
			orderObj: null,
			stripeKey: import.meta.env.VITE_STRIPE_KEY,
			stripeSecretKey: import.meta.env.VITE_STRIPE_SECRET,
			WebtikBase: import.meta.env.VITE_WEBTIK_SERVICE_BASE,
			apiFood: import.meta.env.VITE_API_FOOD,
			WebtikHandler: import.meta.env.VITE_WEBTIK_HANDLE_ARTICOLI,
			spettacolo: '',
			dettagli: {},
			fiscal_address: '',
			fiscal_port: '',
			browser: '',
			device: $(window).width() < 768 ? 'mobile' : 'desktop',
        };
    },
	methods: {
		fetchData: function() {
			this.orderObj = JSON.parse(this.order);
			this.idCinema = this.orderObj.order_ref_cinema;
			console.log(this.orderObj);

			this.fetchEvento();
		},
		fetchEvento: function () {
            axios.get(`${this.WebtikBase}_getPerformanceListDetail?idcinema=${this.idCinema}&idevento=${this.id}`)
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

				this.addImage(this.id);
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
					jsonData = this.xmlToJson(XmlNode),
					fiscal_address = jsonData.FiscalAddress.fiscal_address,
					fiscal_port = jsonData.FiscalAddress.fiscal_port;

					if (fiscal_address !== 'x' && fiscal_port !== 'x') {
						this.fiscal_address = fiscal_address;
						this.fiscal_port = fiscal_port;
					} else {
						// dati di test
						this.fiscal_address = '212.161.77.100';
						this.fiscal_port = '5001';
					}
			});
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
    },
    mounted() {
		this.fetchData();
		this.isSafari();
		this.fetchFiscalAddress();
		console.log(this.device);
    }
}

</script>