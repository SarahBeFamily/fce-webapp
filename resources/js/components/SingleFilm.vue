<template>
	<div class="single-movie">
		<div class="meta-wrap">
			<div class="foto"
				:class="{small: activeTab.dataTab != 'info' ? 'small' : ''}"
				:style="{ 'background': 'linear-gradient(180deg, rgba(16, 16, 16, 0.00) 0%, #080808 86.46%), url(' + dettagli.locandina + ')' }">
				<h1 class="title" v-if="!isCheckout">{{ dettagli.titolo }}</h1>
			</div>

			<div class="meta" v-if="!isCheckout">
				<div>
					<span>{{ dettagli.anno }}</span>
					{{ $t('Anno') }}
				</div>
				<div>
					<span>{{ dettagli.genere }}</span>
					{{ $t('Genere') }}
				</div>
				<div>
					<span>{{ dettagli.durata }}</span>
					{{ $t('Durata') }}
				</div>
				<div>
					<span>{{ dettagli.lingua }}</span>
					{{ $t('Lingua') }}
				</div>
			</div>
		</div>

		<div class="tab">
			<ul v-if="!isCheckout">
				<li data-tab="info" :class="{active: activeTab.dataTab == 'info' ? 'active' : ''}" @click="tabChange('info')">{{ $t('Informazioni') }}</li>
				<li data-tab="progr" :class="{active: activeTab.dataTab == 'progr' ? 'active' : ''}" @click="tabChange('progr')">{{ $t('Programmazione') }}</li>
				<li data-tab="private" :class="{active: activeTab.dataTab == 'private' ? 'active' : ''}" @click="tabChange('private')">{{ $t('Private Screening') }}</li>
			</ul>

			<div id="info" class="cont">
				<h2>{{ $t('Descrizione') }}</h2>
				<p>{{ dettagli.trama }}</p>

				<div class="cast-wrap">
					<h2>{{ $t('Cast') }}</h2>

					<div class="carousel-cast">
						<div class="member" v-for="attore in cast">
							<div v-if="attore.foto != ''" class="thmb" :style="{ 'background-image': 'url(' + attore.foto + ')' }"></div>
							<div v-if="attore.foto == ''" class="thmb"><img :alt="attore.nome" class="cast-img" src="../../images/placeholder-person.jpg" width="100" height="" /></div>
							<h3>{{ attore.nome }}</h3>
						</div>
					</div>
				</div>
			</div>

			<div id="progr" class="cont hidden">
				<div class="progr-wrap">
					<div class="data">

						<div class="giorni-wrap">
							<label :for="p.giorno" v-for="p in progr.slice(0, 12)" class="giorno" :class="{selected: checkedGiorno == p.giorno ? 'selected' : ''}">
								<input type="radio" :id="p.giorno" name="giorno" class="giorno" @change="addOrari(p.giorno)" v-model="checkedGiorno" :value="p.giorno">
								<span class="mese">{{ p.mese }}.</span>
								<span class="gg">{{ p.gg }}</span>
							</label>
						</div>

						<div class="ora-wrap">
							<label :for="ora.idPerf" v-for="ora in orari" class="orario" :data-sala="ora.idsala" :class="{selected: (checkedOrario.ora == ora.ora && checkedOrario.idsala == ora.idsala) ? 'selected' : ''}">
								<input type="radio" :id="ora.idPerf" name="orario" class="orario" @change="choosePerformance(ora.idPerf, ora.idTariffa, ora.ora, ora.idsala, ora.sala)" v-model="checkedOrario.ora" :value="ora.ora">
								<span class="sala">{{ ora.sala }}</span>
								<span class="ora">{{ ora.ora }}</span>
							</label>
						</div>
					</div>
				</div>

				<div class="tariffe">
					<div class="type-tar" v-for="tar in prezzi" :id="tar.id">
						<span class="nome">{{ tar.nome }}</span>
						<span class="prezzo">{{ tar.prezzo }}€</span>
						<div class="quantity">
							<button @click.prevent="qtyMinus(tar.id)">
								<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
									<path d="M2 10H18" stroke="white" stroke-opacity="0.9" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
								</svg>
							</button>
							<input type="number" :name="'qty-'+tar.id" :data-prezzo="tar.prezzo" :data-nome="tar.nome" :data-tipoBiglietto="tar.tipo" :data-settore="tar.settore" :id="'qty-'+tar.id" min="0" :value="tar.qty">
							<button @click.prevent="qtyPlus(tar.id)">
								<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
									<path d="M2 10H18M10 18V2" stroke="white" stroke-opacity="0.9" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
								</svg>
							</button>
						</div>
					</div>

					<div class="submit flex" :class="{inactive: activeSubmit == false ? 'inactive' : '' }" @click="activeSubmit == true ? getPostiSala() : null">
						<!-- <input type="submit" name="choose_ticket" value="Scegli i posti"/> -->
						{{ $t('Scegli i posti') }}
						<i class="arrow-right"></i>
					</div>

					<div class="recap" v-if="recap !== ''">
						{{ recap }}
					</div>
				</div>

			</div>

			<div id="private" class="cont hidden">
				<div class="progr-wrap">
					<div class="data">

						<div class="giorni-wrap">
							<label :for="p.giorno" v-for="p in progr.slice(12, 24)" class="giorno" :class="{selected: checkedGiorno == p.giorno ? 'selected' : ''}">
								<input type="radio" :id="p.giorno" name="giorno" class="giorno" @change="addOrari(p.giorno)" v-model="checkedGiorno" :value="p.giorno">
								<span class="mese">{{ p.mese }}.</span>
								<span class="gg">{{ p.gg }}</span>
							</label>
						</div>

						<div class="ora-wrap">
							<label :for="ora.idPerf" v-for="ora in orari" class="orario" :data-sala="ora.idsala" :class="{selected: (checkedOrario.ora == ora.ora && checkedOrario.idsala == ora.idsala) ? 'selected' : ''}">
								<input type="radio" :id="ora.idPerf" name="orario" class="orario" @change="choosePerformance(ora.idPerf, ora.idTariffa, ora.ora, ora.idsala, ora.sala)" v-model="checkedOrario.ora" :value="ora.ora">
								<span class="sala">{{ ora.sala }}</span>
								<span class="ora">{{ ora.ora }}</span>
							</label>
						</div>

						<div id="btn-acquista" class="submit flex" :class="{inactive: activeSubmitPrivate == false ? 'inactive' : '' }" @click="activeSubmitPrivate == true ? privateScreening() : null">
							{{ $t('Prenota questa sala') }}
							<i class="cart"></i>
						</div>
					</div>
				</div>
			</div>

			<div id="posti" class="cont hidden">
				<h3>{{ $t('Seleziona il posto') }}</h3>

				<div><img alt="schermo" class="schermo" src="../../images/schermo.png" width="" height="" /></div>

				<div class="structure-sala"
					:class="(checkedOrario.sala).toLocaleLowerCase().replaceAll(' ', '_')"
					:data-file="fileSala(checkedOrario.sala)"
					:data-colonne="colonneSala(checkedOrario.sala)"
				>
					<div class="posto posto-wrap"
						:class="(posto.idposto).replace('/', '-')"
						v-for="posto in postiSala"
					>
						<i v-if="(posto.idposto).includes('HX')"></i>
						<input type="checkbox" class="posto"
							v-model="posto.checked"
							:class="(posto.idposto).replace('/', '-') + ' ' + (posto.bloccato == true ? 'riservato' : '') "
							:data-posto="posto.idposto"
							:data-fila="posto.fila"
							:data-colonna="posto.colonna"
							@change="scegliPosto($event, posto.idposto)"
						/>
					</div>
				</div>

				<div id="legenda-posti">
					<div class="posto-selezionato">
						<div></div>
						{{ $t('Selezionato') }}
					</div>

					<div class="posto-riservato">
						<div></div>
						{{ $t('Riservato') }}
					</div>

					<div class="posto-disponibile">
						<div></div>
						{{ $t('Disponibile') }}
					</div>
				</div>

				<div id="btn-checkout" class="submit flex" :class="{inactive: activeSubmitCheckout == false ? 'inactive' : '' }" @click="activeSubmitCheckout == true ? tabChange('checkout') : null">
					{{ $t('Avanti') }}
					<i class="cart"></i>
				</div>

				<div class="recap">
					{{ recap }}
				</div>
			</div>

			<div id="checkout" class="cont hidden">
				<div class="meta-film">
					<div class="locandina">
						<img :src="dettagli.locandina" :alt="dettagli.titolo">
					</div>

					<div class="info-film">
						<h1>{{ dettagli.titolo }}</h1>
						<p><span>{{ $t('Anno') }}:</span> {{ dettagli.anno }}</p>
						<p><span>{{ $t('Genere') }}:</span> {{ dettagli.genere }}</p>
						<p><span>{{ $t('Durata') }}:</span> {{ dettagli.durata }}</p>
						<p><span>{{ $t('Lingua') }}:</span> Italiano</p>
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

				<div class="food-wrap">
					<div class="food-cat"
						v-for="cat in foodCat"
						:id-nodo="cat.idnodo"
						:id-nodopadre="cat.idnodoPadre"
						@click.prevent="scegliSnack('apri', cat.idnodo)"
					>
						<span>{{ cat.nodo }}</span>
					</div>
				</div>

				<!-- <div class="btn-snack">
					<label>Qualche snack?
						<svg width="18" height="18" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg" @click.prevent="showInfo('apri')">
							<path d="M11 15.084L11 10.084M11 1.08398C5.5 1.08398 1 5.58398 1 11.084C1 16.584 5.5 21.084 11 21.084C16.5 21.084 21 16.584 21 11.084C21 5.58398 16.5 1.08398 11 1.08398Z" stroke="white" stroke-opacity="0.9" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
							<path d="M11 7.08398H10.991" stroke="white" stroke-opacity="0.9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
						</svg>
					</label>
					<button id="snack" class="switch" :class="{on: snackChoose == true ? 'on' : '' }" @click.prevent="scegliSnack('apri')"><div class="circle" :class="browser"></div></button>
				</div> -->

				<div id="snack-info" class="info" style="display: none;">
					<div class="inner">
						<div>
							<h3>Prenotazione snack</h3>
							<p>
								Prenotando in anticipo gli snack e le bevande che ti accompagneranno durante la visione del tuo film eviterai la coda prima che apra la sala grazie ad una cassa dedicata...
								Fidati, il bar si riempe sempre prima dell’inizio di un film ;)
							</p>
						</div>
						<div class="close" @click.prevent="showInfo('chiudi')">Ok</div>
					</div>
				</div>

				<div id="snacks-tab" class="disabled">
					<div class="inner">
						<ul>
						 	<li
							 	v-for="cat in foodCat"
								:id-nodo="cat.idnodo"
								:id-nodopadre="cat.idnodoPadre"
								:class="{active: activeSnackTab.dataTab == cat.idnodo ? 'active' : ''}"
								@click="tabSnackChange(cat.idnodo)"
							>
								{{ cat.nodo }}
								<span></span>
							</li>
						</ul>

						<div class="food-tab cont"
							v-for="cat in foodCat"
							:id="cat.idnodo"
						>

							<div class="selezione">
								<span>{{ $t('Selezionate') }}</span>
								<div class="data-sel">
									{{ dataSel.qty }}
								</div>
							</div>

							<div class="type-tar"
								v-for="articolo in cat.Nodi"
								:id="articolo.idarticolo"
								:id-nodopadre="articolo.idnodoPadre"
							>
								<div class="foto-food" :style="{'background-image': 'url(http://fce.winticstellar.com/evolution/webapi/Handlers/handlerArticoli.ashx?idnegozio='+idCinema+'&idarticolo='+articolo.idarticolo+'&LOB_SIZE=SMALL&t=20230707084628)'}"></div>
								<p class="nome">{{ articolo.articolo.nome.indexOf('Ingredienti:') > -1 ? articolo.articolo.nome.slice(0, articolo.articolo.nome.indexOf('Ingredienti:')) : articolo.articolo.nome }}</p>

								<div class="quantity">
									<button @click.prevent="qtyMinus(articolo.idarticolo, 'snack')">
										<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
											<path d="M2 10H18" stroke="white" stroke-opacity="0.9" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
										</svg>
									</button>
									<input type="number" :name="'qty-'+articolo.idarticolo" :data-prezzo="articolo.articolo.importo" :data-nome="articolo.articolo.nome" :id="'qty-'+articolo.idarticolo" min="0" value="0">
									<button @click.prevent="qtyPlus(articolo.idarticolo, 'snack')">
										<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
											<path d="M2 10H18M10 18V2" stroke="white" stroke-opacity="0.9" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
										</svg>
									</button>
								</div>
							</div>
						</div>

						<div id="btn-checkout-snack" class="submit flex" @click="scegliSnack('chiudi')">
							{{ $t('Prosegui al checkout') }}
							<i class="cart"></i>
						</div>
					</div>
				</div>

				<div id="btn-acquista" class="submit flex" :class="{inactive: activeSubmitCheckout == false ? 'inactive' : '' }" @click="activeSubmitCheckout == true ? savesessionCart() : null">
					{{ $t('Acquista i biglietti') }}
					<i class="cart"></i>
				</div>

				<div class="recap">
					{{ recap }}
				</div>

				<div class="pagamento" :class="{unseen: paymentPage === false ? 'unseen' : ''}">
					<div class="inner">
						<h4>{{ $t('Metodi di pagamento') }}</h4>


						<!-- Display a Stripe payment form -->
						<form id="payment-form">
							<div id="payment-element">
								<!--Stripe.js injects the Payment Element-->
							</div>
							<button id="pay-now">
								<div class="spinner hidden" id="spinner"></div>
								<span id="button-text">{{ $t('Paga ora') }}</span>
							</button>
							<div id="payment-message" class="hidden"></div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
	// Wiki API
	let WikiBase = 'https://en.wikipedia.org/w/api.php?action=query&format=json&origin=*&prop=pageimages&list=&titles=';

	import axios from 'redaxios'
	import $ from 'jquery'
	import 'slick-carousel'
	import { v4 as uuidv4 } from "uuid"
	import Stripe from 'stripe'
	import { loadStripe } from '@stripe/stripe-js'

	const sessionID = uuidv4().replace(/-/g, '');

	import.meta.glob(['../../images/**',]);
	// import { StripeCheckout } from '@vue-stripe/vue-stripe';

	export default {
		props: ['route', 'cinema', 'path', 'userp', 'client_secret', 'intent_idp'],
		components: {Stripe},
    data() {
        return {
			appUrl: this.path,
			id: this.route,
			user: this.userp,
			userobj: null,
			clientSecret: this.client_secret,
			intent_id: this.intent_idp,
			stripeKey: import.meta.env.VITE_STRIPE_KEY,
			stripeSecretKey: import.meta.env.VITE_STRIPE_SECRET,
			WebtikBase: import.meta.env.VITE_WEBTIK_SERVICE_BASE,
			apiFood: import.meta.env.VITE_API_FOOD,
			WebtikHandler: import.meta.env.VITE_WEBTIK_HANDLE_ARTICOLI,
			idCinema: this.cinema,
			sessionID: sessionID,
			sessionCart: '',
			sessionCheckout: null,
			carrello: '', //(this.carrello).replace('&quot;', '"'),
			spettacolo: '',
			activeTab: {
				dataTab: 'info',
				active: true
			},
			raw_perf: [],
            dettagli: [],
			cast: [],
            progr: [],
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
			checkedBiglietto: {
				spettacolo: '',
				carrello: [],
				// biglietti: [],
				nBiglietti: 0,
				recap: [],
				prezzi: [],
				totString: '',
				totale: 0,
				index: 0,
				isChecked: false,
			},						// console.log(this.postiSala);

			activeSubmit: false,
			activeSubmitPrivate: false,
			Sala: {
				sala: '',
				file: '',
				colonne: ''
			},
			postiSala: [],
			postiOccupati: [],
			postiScelti: [],
			postiBloccati: false,
			postiBloccatiArray: [],
			postiChecked: {
				posti: [],
				checked: true,
			},
			activeSubmitCheckout: false,
			isCheckout: false,
			snackChoose: false,
			snackChosen: [],
			snackInfo: 'hidden',
			activeSnackTab: {
				dataTab: '',
				active: true
			},
			foodCat: [],
			dataSel: {
				cat: '',
				qty: 0
			},
			fiscal_address: '',
			fiscal_port: '',
			browser: '',
			loading: false,
			lineItems: [],
			paymentPage: false,
			token: null,
			paymentMethods: [],
        };
    },
    methods: {
        fetchEvento: function () {
			this.userobj = JSON.parse(this.user);
			console.log(this.userobj);
            axios.get(`${this.WebtikBase}_getPerformanceListDetail?idcinema=${this.idCinema}&idevento=${this.id}`)
                .then((res) => {
				const XmlNode = new DOMParser().parseFromString(res.data, 'text/xml'),
					jsonData = this.xmlToJson(XmlNode),
					evento = jsonData.PerformanceListDetail.evento,
					performances = jsonData.PerformanceListDetail.performances.performance,
					durata = evento.durata,
					attori = evento.attori,
					genere = evento.genere;

				let durata_arr = durata.split(':'),
					cast_array = attori.split(','),
					genere_arr = genere.split(',');

					// console.log(performances)

				this.dettagli = {
					titolo: evento.titolo,
					anno: evento.anno,
					durata: `${Math.trunc(durata_arr[0])}h ${durata_arr[1]}min`,
					generi: genere_arr,
					genere: genere_arr[0],
					cast: cast_array,
					locandina: '',
					trama: this.asciiReplace(evento.trama),
					lingua: 'Italiano',
				};

				// Creo array del cast
				for (let i = 0; i < cast_array.length; i++) {
					let attore = cast_array[i],
						slug = attore.replace(' ', '_'),
						member = {
							nome: attore,
							slug: slug,
							foto: '',
						}

					this.cast.push(member);
				}

				let progr_array = [],
					gg = '';

				// Creo array di giorni e ore
				if (Array.isArray(performances)) {
					for (let i = 0; i < performances.length; i++) {
						const perf = performances[i],
							giorno = perf.giorno,
							g_arr = giorno.split('-'),
							date_f = `${g_arr[2]}-${g_arr[1]}-${g_arr[0]}`,
							g = this.dateFormat(date_f, "dd M"),
							g_format_arr = g.split(' '),
							ora = perf.ora,
							nome_sala = (perf.sala).indexOf('SALA') > -1 ? perf.sala : `Sala ${perf.sala}`;

						if (i==0) {
							this.checkedGiorno = Object.keys(this.sessionCart).length > 0 && this.sessionCart.giorno && this.spettacolo == this.id ? this.sessionCart.giorno : giorno;
						}

						// console.log(this.checkedGiorno)

						if (gg != giorno) {

							progr_array.push({
								giorno: giorno,
								g_front: g,
								mese: g_format_arr[1],
								gg: g_format_arr[0],
								orari: []
							})

							gg = giorno;

						} else if (gg == giorno) {

						} else {
							gg = '';
						}

						this.raw_perf.push({
							giorno: giorno,
							ora: ora,
							n_sala: perf.sala,
							sala: nome_sala,
							idsala: perf.idsala,
							idPerf: perf.idperformance,
							idTariffa: perf.idtariffa
						})
					}
				} else {
					const perf = performances,
						giorno = perf.giorno,
						g_arr = giorno.split('-'),
						date_f = `${g_arr[2]}-${g_arr[1]}-${g_arr[0]}`,
						g = this.dateFormat(date_f, "dd M"),
						g_format_arr = g.split(' '),
						ora = perf.ora,
						nome_sala = (perf.sala).indexOf('SALA') > -1 ? perf.sala : `Sala ${perf.sala}`;

					this.checkedGiorno = Object.keys(this.sessionCart).length > 0 && this.sessionCart.giorno ? this.sessionCart.giorno : giorno;

					if (gg != giorno) {

						progr_array.push({
							giorno: giorno,
							g_front: g,
							mese: g_format_arr[1],
							gg: g_format_arr[0],
							orari: []
						})

						gg = giorno;

					} else if (gg == giorno) {

					} else {
						gg = '';
					}

					this.raw_perf.push({
						giorno: giorno,
						ora: ora,
						n_sala: perf.sala,
						sala: nome_sala,
						idsala: perf.idsala,
						idPerf: perf.idperformance,
						idTariffa: perf.idtariffa
					})
				}

				this.progr = progr_array;
                this.addImage();
				this.addFotoCast();
				this.slideContentRec();
				this.addOrari(this.checkedGiorno);
			});
        },
        addImage: function () {
			axios.get(`${this.WebtikBase}_getEventImage?idcinema=${this.idCinema}&idevento=${this.id}`)
				.then((res) => {
				const XmlNode = new DOMParser().parseFromString(res.data, 'text/xml'),
					jsonData = this.xmlToJson(XmlNode);

				this.dettagli.locandina = `data:image/png;base64,${jsonData.base64Binary}`;
			});
        },
		addFotoCast: function() {
			for (let i = 0; i < this.cast.length; i++) {
				let attore = this.cast[i];

				axios.get(`${WikiBase}${attore.slug}&formatversion=2`)
				.then((res) => {
					let data = res.data;
					let p = data.query.pages;
					let page = Object.values(p)[0];
					let img = page.thumbnail;
					let foto = img != undefined ? (img.source).replace('50px', '200px') : '';

					attore.foto = foto;
				});
			}
        },
		choosePerformance: function(idPerf, idTariffa, ora, idsala, nomesala) {
			// Prezzi per performance
			axios.get(`${this.WebtikBase}_getPriceListFull?idcinema=${this.idCinema}&idPerformance=${idPerf}&idTariffa=${idTariffa}&mode=3`)
                .then((res) => {
					const XmlNode = new DOMParser().parseFromString(res.data, 'text/xml'),
						jsonData = this.xmlToJson(XmlNode),
						details = jsonData.priceList.prices.price,
						tariffe_array = [];

						console.log(details);

					for (let i = 0; i < details.length; i++) {
						const prezzo = details[i];

						let cookieqty = 0;
						this.checkedBiglietto.carrello.map((el_cart) => {
							if (el_cart.type === 'biglietto' && el_cart.tariffa == prezzo.idbiglietto && (this.spettacolo == this.id || this.spettacolo == '')) {
								cookieqty += el_cart.qty;
							}
						})

						tariffe_array.push({
							id: prezzo.idbiglietto,
							tariffa: idTariffa,
							tipo: prezzo.idtipobigl,
							nome: (prezzo.descr_biglietto).toLowerCase(),
							prezzo: prezzo.prezzo,
							supplemento: prezzo.supplemento,
							settore: prezzo.settore,
							qty: cookieqty,
						})
					}

				this.prezzi = tariffe_array;
				this.checkedOrario.ora = ora;
				this.checkedOrario.sala = nomesala;
				this.checkedOrario.idsala = idsala;
				this.checkedOrario.idPerf = idPerf;
				this.checkedOrario.idTariffa = idTariffa;

				// Se private screening
				// Controlla se c'è almeno un posto occupato
				// Se non ci sono posti occupati, mostra il form per la prenotazione
				if ($('.tab li.active').attr('data-tab') == 'private') {
					this.getPostiSala('private');
				}
			});
		},
		addOrari: function(daySel) {
			const raw = this.raw_perf;
			const progr = this.progr;
			let orari_array = [];

			const ora = raw.map((el) => {
				if(daySel === el.giorno) {
					let idOra = el.ora.replace(':', '');

					orari_array.push({
						ora: el.ora,
						sala: el.sala,
						idsala: el.idsala,
						idOra: idOra,
						idPerf: el.idPerf,
						idTariffa: el.idTariffa
					})
				}

				progr.find(g => {
					if(g.giorno === el.giorno) {
						let idOra = el.ora.replace(':', '');

						g.orari.push({
							ora: el.ora,
							sala: el.sala,
							idsala: el.idsala,
							idOra: idOra,
							idPerf: el.idPerf,
							idTariffa: el.idTariffa
						})
					}
				})

				return orari_array;
			});

			this.orari = orari_array;
			// console.log(this.orari)
			// console.log(this.progr)
		},
		slideContentRec: function () {
            setTimeout(function () {
                $('.carousel-cast').slick({
                    dots: false,
                    arrows: false,
                    infinite: false,
                    centerMode: false,
                    variableWidth: false,
                    slidesToShow: 6,
                    slidesToScroll: 1,
                    autoplay: false,
                    autoplaySpeed: 2500,
                    cssEase: 'cubic-bezier(0.785, 0.135, 0.150, 0.860)',
					responsive: [
                        {
                        breakpoint: 1280,
                        settings: {
                            slidesToShow: 4.5,
                            slidesToScroll: 1,
                            infinite: true,
                            dots: true
                        	}
                        },
                        {
                        breakpoint: 1024,
                        settings: {
                            slidesToShow: 3.5,
                            slidesToScroll: 1,
                            infinite: true,
                            dots: true
                        	}
                        },
                        {
                        breakpoint: 600,
                        settings: {
                            slidesToShow: 2.5,
                            slidesToScroll: 1
                        	}
                        },
                        // You can unslick at a given breakpoint now by adding:
                        // settings: "unslick"
                        // instead of a settings object
                    ]
                });
            });
        },
		tabChange: function(data) {
			if (data == 'checkout') {
				this.isCheckout = true;
			} else {
				this.isCheckout = false;
			}

			$(`.tab .cont#${data}`).removeClass('hidden');
			$(`.tab .cont:not(#${data})`).addClass('hidden');
			this.activeTab.dataTab = data;
		},
		qtyMinus: function(id, type = '') {
			const targetInput = $(`#qty-${id}`),
				targetNome = targetInput.attr('data-nome').indexOf('Ingredienti:') > -1 ? targetInput.attr('data-nome').slice(0, targetInput.attr('data-nome').indexOf('Ingredienti:')) : targetInput.attr('data-nome'),
				targetSlug = targetNome.replaceAll(' ', '-').toLowerCase(),
				targetPrezzo = targetInput.attr('data-prezzo');

			let tot = 0;

			if (type == '') {

				let targetQty = 0;
				let minus = 0;

				// Aggiorno la qty dell'input della tariffa
				for (let i = 0; i < this.prezzi.length; i++) {
					const el = this.prezzi[i];
					if (el.id === id) {
						targetQty = el.qty > 0 ? el.qty-1 : 0;
						minus = targetQty;
					}
				}

				// Aggiorno il recap
				// Se la qty è 0 elimino il biglietto dal recap
				// Se la qty è > 0 aggiorno il biglietto nel recap togliendo il dato già presente e reinserendolo con la nuova qty
				let rrecap = this.checkedBiglietto.recap.findIndex(function(o){
					return o.id === `${targetSlug}-${targetQty+1}`;
				})
				if (rrecap !== -1) this.checkedBiglietto.recap.splice(rrecap, 1);

				if (minus > 0) {
					this.checkedBiglietto.recap.push({
						type: 'biglietto',
						id: `${targetSlug}-${minus}`,
						string: `${targetNome} x${minus}`,
						qty: minus,
					})
				}

				this.checkedBiglietto.nBiglietti = this.checkedBiglietto.nBiglietti > 0 ? this.checkedBiglietto.nBiglietti -1 : 0;
				let index = this.checkedBiglietto.index;

				let biglietti = this.checkedBiglietto.carrello.findIndex(function(o){
					return o.id === `${targetSlug}-${index}`;
				})
				if (biglietti !== -1) this.checkedBiglietto.carrello.splice(biglietti, 1);

				this.removeItemOnce(this.checkedBiglietto.prezzi, parseFloat(targetPrezzo).toFixed(2).replace('.', ','))

				// cerco se esiste già un biglietto con lo stesso id
				for (let i = 0; i < this.prezzi.length; i++) {
					const el = this.prezzi[i];
					if (el.id === id) {
						el.qty = el.qty > 0 ? el.qty-1 : 0;
					}
				}

				this.checkedBiglietto.index = this.checkedBiglietto.index > 0 ? this.checkedBiglietto.index-1 : 0;
				this.activeSubmitCheckout = this.nBiglietti !== this.postiScelti.length ? false : true;

			} else if (type == 'snack') {
				let targetQtySnack = parseInt(targetInput.val());
				let minus = 0;

				if(targetQtySnack > 0) {
					minus = targetQtySnack-1
				}

				targetInput.val(minus)

				let snacks = this.snackChosen.findIndex(function(o){
					return o.id === targetSlug;
				})
				if (snacks !== -1) this.snackChosen.splice(snacks, 1);

				let snacksTik = this.checkedBiglietto.carrello.findIndex(function(o){
					return o.id === targetSlug;
				})
				if (snacksTik !== -1) {
					this.checkedBiglietto.carrello.splice(snacksTik, 1);
				}

				let rrecap = this.checkedBiglietto.recap.findIndex(function(o){
					return o.id === targetSlug;
				})
				if (rrecap !== -1) this.checkedBiglietto.recap.splice(rrecap, 1);

				let prezzo_precedente = (targetPrezzo * targetQtySnack).toString();
				let prezzo_attuale = (targetPrezzo * minus).toString();
				this.removeItemOnce(this.checkedBiglietto.prezzi, parseFloat(prezzo_precedente).toFixed(2).replace('.', ','))

				if (minus > 0) {
					let el = {
						type: 'snack',
						id: targetSlug,
						nome: targetNome,
						prodotto: targetNome,
						idnodo: id,
						prezzo: parseFloat(targetPrezzo).toFixed(2).replace('.', ','),
						prezzoTot: parseFloat(targetPrezzo * minus).toFixed(2).replace('.', ','),
						qty: minus,
						qtySnack: parseFloat(targetPrezzo).toFixed(2).replace('.', ',')+'€ x '+minus,
					};

					this.snackChosen.push(el)
					this.snackChoose = true
					this.checkedBiglietto.carrello.push(el)

					this.checkedBiglietto.recap.push({
						type: 'snack',
						id: targetSlug,
						string: `${targetNome} x${minus}`,
						qty: minus,
					})

					this.checkedBiglietto.prezzi.push(parseFloat(prezzo_attuale).toFixed(2).replace('.', ','));

				} else {
					if (this.snackChosen.length === 0)
						this.snackChoose = false
				}

				if (targetQtySnack > 0)
					this.dataSel.qty = this.snackChosen.length > 0 ? this.dataSel.qty -1 : this.snackChosen.length

			}

			if (this.checkedBiglietto.recap.length > 0) {
				$.each(this.checkedBiglietto.prezzi,function(){tot+=parseFloat(this) || 0;});
				this.checkedBiglietto.totString = `Totale: ${parseFloat(tot).toFixed(2).replace('.', ',')}€`;
				this.checkedBiglietto.isChecked = true;
				this.checkedBiglietto.totale = parseFloat(tot).toFixed(2).replace('.', ',');
			} else {
				this.checkedBiglietto.isChecked = false;
				this.checkedBiglietto.totString = '';
				this.checkedBiglietto.totale = 0;
				this.checkedBiglietto.prezzi = [];
			}

			console.log(this.checkedBiglietto)

			this.nBiglietti = this.checkedBiglietto.nBiglietti;
			this.recap = this.checkedBiglietto.recap.map(function(elem){
				return elem.string;
			}).join(' | ');

			this.recap += ` | ${this.checkedBiglietto.totString}`;

			if(this.checkedGiorno != '' && this.checkedOrario != '' && this.checkedBiglietto.isChecked === true) {
				this.activeSubmit = true
			}
		},
		qtyPlus: function(id, type = '') {
			this.checkedBiglietto.index = this.checkedBiglietto.index+1;

			const targetInput = $(`#qty-${id}`),
				targetNome = targetInput.attr('data-nome').indexOf('Ingredienti:') > -1 ? targetInput.attr('data-nome').slice(0, targetInput.attr('data-nome').indexOf('Ingredienti:')) : targetInput.attr('data-nome'),
				targetSlug = targetNome.replaceAll(' ', '-').toLowerCase(),
				targetPrezzo = targetInput.attr('data-prezzo');

			let targetQty = 0;
			// aggiungo 1 alla qty dell'input di tariffa
			for (let i = 0; i < this.prezzi.length; i++) {
				const el = this.prezzi[i];
				if (el.id === id) {
					targetQty = el.qty+1;
				}
			}

			let tot = 0;
			targetQty = type == 'snack' ? parseInt(targetInput.val())+1 : targetQty;

			if (targetQty > 0) {
				// se type è biglietto
				if (type == '') {

					let rrecap = this.checkedBiglietto.recap.findIndex(function(o){
						return o.id === `${targetSlug}-${targetQty-1}`;
					})
					if (rrecap !== -1) this.checkedBiglietto.recap.splice(rrecap, 1);

					this.checkedBiglietto.recap.push({
						type: 'biglietto',
						id: `${targetSlug}-${targetQty}`,
						string: `${targetNome} x${targetQty}`,
						qty: targetQty,
					})

					this.checkedBiglietto.prezzi.push(parseFloat(targetPrezzo).toFixed(2).replace('.', ','));

					let el = {
						type: 'biglietto',
						index: this.checkedBiglietto.index,
						tariffa: parseInt(id),
						tipoBiglietto: parseInt(targetInput.attr('data-tipoBiglietto')),
						settore: targetInput.attr('data-settore'),
						id: `${targetSlug}-${this.checkedBiglietto.index}`,
						nome: targetNome,
						prezzo: parseFloat(targetPrezzo).toFixed(2).replace('.', ','),
						qty: targetQty / targetQty,
						posto: '',
						titolo: this.dettagli.titolo,
						data: `${this.checkedGiorno} ${this.checkedOrario.ora}`,
						cat: this.dettagli.genere,
						sala: this.checkedOrario.sala,
						idPerf: this.checkedOrario.idPerf,
						idSala: this.checkedOrario.idsala,
					};

					this.checkedBiglietto.carrello.push(el)
					this.checkedBiglietto.nBiglietti = this.checkedBiglietto.nBiglietti +1;

					// Aggiorno la qty dell'input di tariffa
					for (let i = 0; i < this.prezzi.length; i++) {
						const el = this.prezzi[i];
						if (el.id === id) {
							el.qty = el.qty+1;
						}
					}

					this.activeSubmitCheckout = this.nBiglietti !== this.postiScelti.length ? false : true;

				// se type appartiene al food & beverage
				} else if (type == 'snack') {

					let rrecap = this.checkedBiglietto.recap.findIndex(function(o){
						return o.id === targetSlug;
					})
					if (rrecap !== -1) this.checkedBiglietto.recap.splice(rrecap, 1);

					this.checkedBiglietto.recap.push({
						type: 'snack',
						id: targetSlug,
						string: `${targetNome} x${targetQty}`,
						qty: targetQty,
					})

					let biglietti = this.checkedBiglietto.carrello.findIndex(function(o){
						return o.id === targetSlug;
					})
					if (biglietti !== -1) this.checkedBiglietto.carrello.splice(biglietti, 1);

					let prezzo_remove = (targetPrezzo * (targetQty-1)).toString();
					let prezzo_tot = (targetPrezzo * targetQty).toString();
					this.removeItemOnce(this.checkedBiglietto.prezzi, parseFloat(prezzo_remove).toFixed(2).replace('.', ','));
					this.checkedBiglietto.prezzi.push(parseFloat(prezzo_tot).toFixed(2).replace('.', ','));

					let el = {
						type: 'snack',
						id: targetSlug,
						nome: targetNome,
						prodotto: targetNome,
						idnodo: id,
						prezzo: parseFloat(targetPrezzo).toFixed(2).replace('.', ','),
						prezzoTot: parseFloat(targetPrezzo * targetQty).toFixed(2).replace('.', ','),
						qty: targetQty,
						qtySnack: parseFloat(targetPrezzo).toFixed(2).replace('.', ',')+'€ x '+targetQty,
					};

					this.snackChosen.push(el)
					this.snackChoose = true
					this.dataSel.qty+=1
					this.checkedBiglietto.carrello.push(el)

					// modifico il valore dell'input appena incrementato
					targetInput.val(targetQty);
				}
			}

			if (this.checkedBiglietto.recap.length > 0) {
				$.each(this.checkedBiglietto.prezzi,function(){tot+=parseFloat(this) || 0;});
				this.checkedBiglietto.totString = `Totale: ${parseFloat(tot).toFixed(2).replace('.', ',')}€`;
				this.checkedBiglietto.isChecked = true;
				this.checkedBiglietto.totale = parseFloat(tot).toFixed(2).replace('.', ',');
			} else {
				this.checkedBiglietto.isChecked = false;
				this.checkedBiglietto.totale = 0;
			}

			this.nBiglietti = this.checkedBiglietto.nBiglietti;
			this.recap = this.checkedBiglietto.recap.map(function(elem){
				return elem.string;
			}).join(' | ');

			this.recap += ` | ${this.checkedBiglietto.totString}`;

			if(this.checkedGiorno != '' && this.checkedOrario != '' && this.checkedBiglietto.isChecked === true)
				this.activeSubmit = true

			console.log(this.checkedBiglietto)
		},
		getPostiSala: function($path) {
			// se il path non è private screening cambio tab alla scelta dei posti
			if ($path != 'private') {

				// Momentaneo check delle tariffe
				// axios.get(`${this.WebtikBase}_getPriceListFull?idcinema=${this.idCinema}&idperformance=${this.checkedOrario.idPerf}&idtariffa=${this.checkedOrario.idTariffa}&MODE=0`)
				// 	.then((res) => {
				// 		let xmldata = res.data;
				// 		let XmlNode = new DOMParser().parseFromString(xmldata, 'text/xml');
				// 		let jsonData = this.xmlToJson(XmlNode);

				// 		console.log(jsonData);
				// });

				axios.get(`${this.WebtikBase}_getMappaSale?idcinema=${this.idCinema}&idsala=${this.checkedOrario.idsala}`)
					.then((res) => {
						let xmldata = res.data;
						let XmlNode = new DOMParser().parseFromString(xmldata, 'text/xml');
						let jsonData = this.xmlToJson(XmlNode);

						this.postiSala = jsonData.Sala.posti.posto;

						for (let i = 0; i < this.postiSala.length; i++) {
							const posto = this.postiSala[i];
							this.postiSala[i].bloccato = false;
							this.postiSala[i].checked = Object.keys(this.sessionCart).length > 0 && this.sessionCart.spettacolo === this.id && this.postiChecked.posti.includes(posto.idposto) ? true : false;
						}

						// console.log(this.postiSala);
						this.getPostiOccupati($path);
				});
			} else {
				this.getPostiOccupati($path);
			}
		},
		getPostiOccupati: function($path) {
			// To Do: posti occupati
			axios.get(`${this.WebtikBase}_getOccupancy?idcinema=${this.idCinema}&idperformance=${this.checkedOrario.idPerf}`)
                .then((res) => {
					let xmldata = res.data;
					let XmlNode = new DOMParser().parseFromString(xmldata, 'text/xml');
					let jsonData = this.xmlToJson(XmlNode);
					let postiOccupati = jsonData.OccupancySala.posti;

					// console.log(jsonData);

					this.postiOccupati = postiOccupati;
					console.log(this.postiOccupati)

					// Controllo se il posto è bloccato dallo stesso utente
					for (let i = 0; i < this.postiOccupati.length; i++) {
						const postoID = this.postiOccupati.posto.idposto;
						console.log(postoID)

						if (!this.postiScelti.map((el) => {return el.postoid}).includes(postoID)) {
							console.log('non c\'è')

							this.postiSala.map((postoSala) => {
								if (postoSala.idposto === postoID) {
									postoSala.bloccato = true;
								}
							});
						}
					}

					// console.log(this.postiSala);

					// Controlla se c'è almeno un posto occupato
					// Se non ci sono posti occupati, attiva il bottone per la prenotazione della sala
					if (this.postiOccupati.length === 0 && $path == 'private') {
						this.activeSubmitPrivate = true;
					}

					if ($path != 'private') this.tabChange('posti');
            });
		},
		fileSala: function(sala) {
			let file = '';
			switch (sala) {
				case '2':
					file = '3';
					break;

				default:
					break;
			}
			return file;
		},
		colonneSala: function(sala) {
			let colonne = '';
			switch (sala) {
				case '2':
					colonne = '9';
					break;

				default:
					break;
			}

			return colonne;
		},
		/**
		 * Scelta dei posti nella piantina della sala
		 * @param {*} $event
		 * @param {int} id // id del posto
		 */
		scegliPosto: function($event, id) {
			if (this.postiScelti > 0 && this.checkedBiglietto.spettacolo == this.id) {
				this.postiScelti.push(this.checkedBiglietto.posti);
			}

			if (!this.postiScelti.map((el) => {return el.postoid}).includes(id)) {
				if(this.postiScelti.length < parseInt(this.nBiglietti)) {
					let posto = {
						postoid: id,
						giorno: this.checkedGiorno,
						sala: this.checkedOrario.idsala,
						ora: this.checkedOrario.ora,
					}

					this.postiScelti.push(posto);
					this.postiChecked.posti.push(id);
				} else {
					alert("Hai già selezionato tutti i posti disponibili");
					$event.target.checked = false;
				}
			} else {
				// this.postiScelti.splice(this.postiScelti.indexOf(id), 1);
				this.postiScelti = this.postiScelti.filter((el) => {return el.postoid != id});
			}

			// Assegno il posto al biglietto
			for (let index = 0; index < this.checkedBiglietto.carrello.length; index++) {
				const element = this.checkedBiglietto.carrello[index];

				let assegnato = false;

				if (assegnato === false) {
					if (!Array.isArray(element) && element.type == 'biglietto' && element.posto == '') {

						for (let i = (element.index-1); i < this.postiScelti.length; i++) {
							const postoID = this.postiScelti[i].postoid,
								posto_arr = postoID.split('/'),
								fila = posto_arr[0],
								posto = posto_arr[1];

							if (assegnato === false) {
								element.postoid = postoID;
								element.posto = `Fila ${fila} Posto ${posto}`;
								element.prodotto = `${element.nome} ${element.posto}`;
								assegnato = true;
							}
						}
					}
				}
			}

			if (parseInt(this.nBiglietti) === this.postiScelti.length) {
				this.activeSubmitCheckout = true;
			} else {
				this.activeSubmitCheckout = false;
			}
		},
		privateScreening: function() {
			// to do: private screening
			// Aggiungo ai posti scelti tutti quelli disponibili per la sala
			// Aggiungo al carrello la sala intera con il totale dei biglietti
			// Aggiungo al recap la sala intera con il totale dei biglietti
		},
		fetchFood: function() {
			axios.post(this.apiFood,
			{
				"idnegozio": "600",
				"web_box": "demo",
				"web_operator": "crea",
				"sessionId": "azttmxtdaa_100",
				"trackid": "2200"
			})
			.then((res) => {this.foodCat = res.data.TastieraArticoli.Nodi;});
		},
		fetchsessionCart: function() {
			let sessionCart = localStorage.getItem('sessionCart') ? JSON.parse(localStorage.getItem('sessionCart')) : {},
				snackChosen = Object.keys(sessionCart).length > 0 && Array.isArray(sessionCart.snacksArray) ? sessionCart.snacksArray : [],
				foodQty = 0,
				scelte = [];

			if (sessionCart.recap)
				this.recap = sessionCart.recap;

			snackChosen.map(function(el){
				if (! Array.isArray(el))
					scelte.push(el);
			});

			this.snackChosen = scelte;
			this.snackChoose = this.snackChosen.length > 0 ? true : false;

			if (this.snackChosen.length > 0) {
				$.each(this.snackChosen,function(){foodQty+=parseInt(this.qty) || 0;});
				this.dataSel.qty = foodQty;
			} else {
				this.dataSel.qty = 0;
			}

			console.log(sessionCart);
			this.sessionCart = sessionCart;

			if (Object.keys(this.sessionCart).length > 0) {
				this.sessionID = this.sessionCart.sessionCartID ? this.sessionCart.sessionCartID : sessionID;
				this.spettacolo = this.sessionCart.spettacolo ? this.sessionCart.spettacolo : '';
				this.nBiglietti = this.sessionCart.nBiglietti && this.sessionCart.spettacolo === this.id ? this.sessionCart.nBiglietti : 0;
				this.postiScelti = this.sessionCart.posti && this.sessionCart.spettacolo === this.id ? this.sessionCart.posti : [];
				this.postiChecked = this.sessionCart.postiChecked && this.sessionCart.spettacolo === this.id ? this.sessionCart.postiChecked : {posti: [], checked: true};
				
				this.checkedGiorno = this.sessionCart.giorno ? this.sessionCart.giorno : '';
				this.checkedOrario.ora = this.sessionCart.ora ? this.sessionCart.ora : '';
				this.checkedOrario.sala = this.sessionCart.sala ? this.sessionCart.sala : '';
				this.checkedOrario.idsala = this.sessionCart.idsala ? this.sessionCart.idsala : '';
				this.checkedOrario.idPerf = this.sessionCart.idPerf ? this.sessionCart.idPerf : '';
				this.checkedOrario.idTariffa = this.sessionCart.idTariffa ? this.sessionCart.idTariffa : '';

				this.carrello = this.sessionCart.carrello ? this.sessionCart.carrello : {};
				this.checkedBiglietto.carrello = this.sessionCart.carrello ? this.sessionCart.carrello : [];
				this.checkedBiglietto.recap = this.sessionCart.recap ? this.sessionCart.recap : [];
				this.checkedBiglietto.prezzi = this.sessionCart.prezzi && this.sessionCart.spettacolo === this.id ? this.sessionCart.prezzi : [];
				this.checkedBiglietto.totString = this.sessionCart.totString ? this.sessionCart.totString : '';
				this.checkedBiglietto.totale = this.sessionCart.totale ? this.sessionCart.totale : 0;
				this.checkedBiglietto.index = this.sessionCart.index && this.sessionCart.spettacolo === this.id ? this.sessionCart.index : 0;
				this.checkedBiglietto.nBiglietti = this.sessionCart.nBiglietti && this.sessionCart.spettacolo === this.id ? this.sessionCart.nBiglietti : 0;
				this.checkedBiglietto.isChecked = this.sessionCart.isChecked && this.sessionCart.spettacolo === this.id ? this.sessionCart.isChecked : false;

				this.recap = this.sessionCart.recapString ? this.sessionCart.recapString : '';
				this.activeSubmit = this.sessionCart.activeSubmit && this.sessionCart.spettacolo === this.id ? this.sessionCart.activeSubmit : false;
				this.activeSubmitCheckout = parseInt(this.nBiglietti) === this.postiScelti.length && this.sessionCart.spettacolo === this.id ? true : false;
			}

			if (this.id != this.spettacolo) {
				// tolgo i biglietti scelti per un altro spettacolo dal carrello
				let carrello = this.checkedBiglietto.carrello,
					nuovo_carrello = [];

				for (let i = 0; i < carrello.length; i++) {
					const element = carrello[i];

					if (element.type != 'biglietto') {
						nuovo_carrello.push({
							type: element.type,
							id: element.id,
							nome: element.nome,
							idnodo: element.idnodo,
							prezzo: element.prezzo,
							prezzoTot: element.prezzoTot,
							qty: element.qty,
							qtySnack: element.qtySnack,
						});

						this.checkedBiglietto.prezzi.push(element.prezzoTot);
					}
				}

				this.checkedBiglietto.carrello = nuovo_carrello;

				// tolgo i biglietti scelti per un altro spettacolo dal recap
				let recap = this.checkedBiglietto.recap,
					nuovo_recap = [];

				for (let i = 0; i < recap.length; i++) {
					const element = recap[i];

					if (element.type != 'biglietto') {
						nuovo_recap.push({
							type: element.type,
							id: element.id,
							string: element.string,
						});
					}
				}
				this.checkedBiglietto.recap = nuovo_recap;
				this.recap = this.checkedBiglietto.recap.map(function(elem){
					return elem.string;
				});

				if (this.recap != '') {
					this.recap = this.recap.join(' | ');
				}

				if (this.checkedBiglietto.recap.length > 0) {
					let tot = 0;
					$.each(this.checkedBiglietto.prezzi,function(){tot+=parseFloat(this) || 0;});
					this.checkedBiglietto.totString = `Totale: ${parseFloat(tot).toFixed(2).replace('.', ',')}€`;
					this.checkedBiglietto.totale = parseFloat(tot).toFixed(2).replace('.', ',');
				}
				this.recap += ` | ${this.checkedBiglietto.totString}`;
			}

			if (Object.keys(sessionCart).length > 0)
				this.choosePerformance(this.checkedOrario.idPerf, this.checkedOrario.idTariffa, this.checkedOrario.ora, this.checkedOrario.idsala, this.checkedOrario.sala);
		},
		savesessionCart: function() {
			let sessionSnack = this.snackChosen.map(function(elem){
					return elem.nome;
				}).join(' | '),
				snacksArray = this.checkedBiglietto.carrello.map(function(elem){
					return elem.type === 'snack' ? elem : [];
				});

			let sessionCart = {
				sessionCartID: sessionID,
				carrello: this.checkedBiglietto.carrello,
				spettacolo: this.id,
				giorno: this.checkedGiorno,
				ora: this.checkedOrario.ora,
				sala: this.checkedOrario.sala,
				idsala: this.checkedOrario.idsala,
				idPerf: this.checkedOrario.idPerf,
				idTariffa: this.checkedOrario.idTariffa,
				snack: sessionSnack,
				snacksArray: snacksArray,
				posti: this.postiScelti,
				postiChecked: this.postiChecked,
				totale: this.checkedBiglietto.totale,
				nBiglietti: this.checkedBiglietto.nBiglietti,
				recap: this.checkedBiglietto.recap,
				recapString: this.recap,
				prezzi: this.checkedBiglietto.prezzi,
				isChecked: this.checkedBiglietto.isChecked,
				totString: this.checkedBiglietto.totString,
				index: this.checkedBiglietto.index,
				activeSubmit: this.activeSubmit,
				fiscal_address: this.fiscal_address,
				fiscal_port: this.fiscal_port,
			}

			let sessionCartStr = JSON.stringify(sessionCart);
			localStorage.setItem('sessionCart', sessionCartStr);

			console.log(sessionCart);
			// this.addToCart(sessionID, this.checkedBiglietto.carrello)

			// Controllo se l'utente è loggato
			if (this.user !== 'null') {
				this.checkout();
			} else {
				location.href = `${this.appUrl}login`;
			}
		},
		addToCart: function(id, items, data) {
			axios.get('addToCart',
			{
				"id": id,
				"items": items
			})
			.then((res) => {
				let result = res.data;

				if (result !== '' && data === 'checkout') {
					$(`.tab .cont#${data}`).removeClass('hidden');
					$(`.tab .cont:not(#${data})`).addClass('hidden');

					this.isCheckout = true;
					this.activeTab.dataTab = data;
				}

				console.log(result);
			}).catch((err) => {
				console.log(err);
			});
		},
		scegliSnack: function(action, id) {

			switch (action) {
				case 'apri':

					$('#snacks-tab').removeClass('disabled');
					$(`#snacks-tab ul li[id-nodo="${id}"`).click();
					break;

				case 'chiudi':
					$('#snacks-tab').addClass('disabled');
					break;

				default:
					break;
			}

			if(this.snackChosen.length > 0) {
				this.snackChoose = true
			} else {
				this.snackChoose = false
			}
		},
		tabSnackChange: function(data) {
			$(`.food-tab#${data}`).removeClass('hidden');
			$(`.food-tab:not(#${data})`).addClass('hidden');

			this.activeSnackTab.dataTab = data;
		},
		showInfo: function(action) {
			if( action == 'chiudi') {
				this.snackInfo = 'hidden';
				$('#snack-info').fadeOut();
			} else {
				this.snackInfo = '';
				$('#snack-info').fadeIn();
			}
		},
		fetchFiscalAddress: function() {
			axios.get(`${this.WebtikBase}_getFiscalAddress?idcinema=${this.idCinema}`)
				.then((res) => {
				const XmlNode = new DOMParser().parseFromString(res.data, 'text/xml'),
					jsonData = this.xmlToJson(XmlNode),
					fiscal_address = jsonData.FiscalAddress.fiscal_address,
					fiscal_port = jsonData.FiscalAddress.fiscal_port;

					console.log(jsonData);
					console.log(fiscal_address);

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
		sbloccaPosti: function() {
			let posti = this.postiScelti.map((el) => {return el.postoid}).join('&aiProgPosti=');
			
			// Sblocca i posti in caso di errori
			axios.get(`${this.WebtikBase}setSbloccoPosti?idcinema=${this.idCinema}&idperformance=${this.checkedOrario.idPerf}&sSessionId=${this.sessionID}&aiProgPosti=${posti}`)
				.then((res) => {
				const XmlNode = new DOMParser().parseFromString(res.data, 'text/xml'),
					jsonData = this.xmlToJson(XmlNode);

				console.log('posti sbloccati');
				if (jsonData.short != 0) {
					alert('Errore nello sblocco dei posti');
				}
			});
		},
		checkout: function() {
			let posti = this.postiScelti.map((el) => {return el.postoid}).join('&aiProgPosti=');

			console.log(posti);
			
			// Blocca i posti prima della transazione
			axios.get(`${this.WebtikBase}setBloccoPosti?idcinema=${this.idCinema}&idperformance=${this.checkedOrario.idPerf}&sSessionId=${this.sessionID}&aiProgPosti=${posti}`)
				.then((res) => {
				const XmlNode = new DOMParser().parseFromString(res.data, 'text/xml'),
					jsonData = this.xmlToJson(XmlNode);

				console.log(jsonData);
				if (jsonData.short == 0) {
					this.activeSubmitCheckout = false;
					this.activeSubmit = false;
				
					// Check del server fiscale
					axios.get(`${this.WebtikBase}_checkFiscalServer?fiscal_address=${this.fiscal_address}&fiscal_port=${this.fiscal_port}`)
						.then((res) => {
						const XmlNode = new DOMParser().parseFromString(res.data, 'text/xml'),
							jsonData = this.xmlToJson(XmlNode);

						console.log(jsonData);
						if (jsonData.int === '666') {
							// posso procedere con il pagamento
							this.payment();
						} else {
							// Sblocco i posti
							this.sbloccaPosti();
							alert('Server fiscale non pronto');
						}
					});
				} else {
					alert('Errore: posti già bloccati');
				}
			});
		},
		setLineItems() {
			let tipoBiglietti_array = [],
				tariffa = '',
				qty = 0;

			// divido le tipologie di biglietto richieste
			this.checkedBiglietto.map((item) => {
				if (item.type === 'biglietto') {
					tariffa = item.tariffa;

					if (tariffa === item.tariffa)
						qty += item.qty;
				}
				if (item.type === 'biglietto' && tipoBiglietti_array.findIndex(function(o){ return o.id === item.tariffa }) === -1)
					tipoBiglietti_array.push({id: item.tariffa, qty: qty});
			});

			let posti = this.postiScelti.join(';');
			let items = [];
			this.checkedBiglietto.map((item) => {
				let nome = item.type === 'biglietto' ? `${item.nome} ${item.posto}` : item.nome;
				items.push(`eur // ${(item.prezzo).replace(',', '')} // ${nome} // ${item.qty} // ${this.spettacolo}`)
			});

			this.lineItems = items;
			console.log(this.lineItems);
			// this.getSession(this.lineItems);
		},
		getSession(items) {
			axios.get('getSession', {params: {items}})
				.then((res) => {
				this.sessionCheckout = res.data.id;
				console.log(this.sessionCheckout);
			}).catch((err) => {
				console.log(err);
			});
		},
		async payment() {
			this.paymentPage = true;

			let clientSecret = this.clientSecret;
			let intentID = this.intent_id;
			const stripeKey = this.stripeKey;
			let Totale = parseFloat(this.checkedBiglietto.totale) * 100;

			const appearance = { 
				theme: 'night',
  				labels: 'floating',
				variables: {
					colorPrimary: '#0570de',
					colorDanger: '#df1b41',
					spacingUnit: '2px',
					borderRadius: '4px',
					// See all possible variables below
				}
			};
			const options = { 
				layout: "tabs",
				paymentMethodOrder: ['apple_pay', 'google_pay', 'card']
			};
			let defaultValues = {
				billingDetails: {
					name: `${this.user.user_firstname} ${this.user.user_lastname}`,
					email: this.user.user_email,
				},
			};

			const stripe = await loadStripe(stripeKey);
			const elements = stripe.elements({ clientSecret: clientSecret, appearance, defaultValues });
			const paymentElement = elements.create('payment', options);
			paymentElement.mount('#payment-element');

			let formPayment = $('#payment-form'),
				loadingTrue = this.setLoading(true),
				loadingFalse = this.setLoading(false),
				params = {
					sessionID: this.sessionCart.sessionCartID,
					performance: this.checkedOrario.idPerf,
					idCinema: this.idCinema,
					cart: encodeURIComponent(JSON.stringify(this.carrello)),
					orderType: 'tickets',
					film: this.id,
					total: parseFloat(this.checkedBiglietto.totale),
				};

			formPayment.on('submit', async (e) => {
				e.preventDefault();
				loadingTrue;

				axios.get('/createOrder', {
					params
				}).then(response => {
					loadingFalse;
					// console.log(response);
					let success = response.data.request.return_url;
					this.orderID = response.data.request.metadata.order_id;
					alert(response.data.message);
					console.log(success);

					// Aggiorno il payment intent
					axios.get('/updatePaymentIntent', {
						params: {
							payment_intent: intentID,
							order_id: this.orderID,
							cinema: this.idCinema,
							customer: this.userobj.stripe_id,
							items: this.recap,
							amount: Totale,
							show: `${this.dettagli.titolo} - ${this.checkedGiorno} - ${this.checkedOrario.ora} - ${this.checkedOrario.sala}`,
						}
					}).then(response => {
						// console.log(response);

						stripe.confirmPayment({
							elements,
							confirmParams: {
								// Return URL where the customer should be redirected after the PaymentIntent is confirmed.
								return_url: success,
							},
						})
						.then(function(result) {
							if (result.error) {
								// Inform the customer that there was an error.
								console.log(result.error.message);
								this.sbloccaPosti();
							} else {
								// The PaymentIntent was successful. Proceed to the success page.
								window.location.href = success;
							}
						});
					}).catch(error => {
						console.log(error);
						loadingFalse;
						this.sbloccaPosti();
					});

				}).catch(error => {
					// throw error
					console.log(error);
					loadingFalse;
					this.sbloccaPosti();
				});
			});
		},
		addCard() {
			// this will trigger the process
			this.$refs.elementRef.submit();
		},
		tokenCreated (token) {
			console.log(token);
			// handle the token
			// send it to your server
		},
		setLoading(isLoading) {
			if (isLoading) {
				// Disable the button and show a spinner
				document.querySelector("#pay-now").disabled = true;
				document.querySelector("#spinner").classList.remove("hidden");
				document.querySelector("#button-text").classList.add("hidden");
			} else {
				document.querySelector("#pay-now").disabled = false;
				document.querySelector("#spinner").classList.add("hidden");
				document.querySelector("#button-text").classList.remove("hidden");
			}
		},
		removeItemOnce: function(arr, value) {
			if (arr !== undefined) {
				let index = arr.indexOf(value);
				if (index > -1) {
					arr.splice(index, 1);
				}
				return arr;
			}
		},
		removeItemAll: function(arr, value) {
			let i = 0;
			while (i < arr.length) {
				if (arr[i] === value) {
					arr.splice(i, 1);
				} else {
					++i;
				}
			}
			return arr;
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
		dateFormat: function(input_D, format_D) {
			// input date parsed
			const date = new Date(input_D);

			//extracting parts of date string
			const day = date.getDate();
			const month = date.getMonth() + 1;
			const year = date.getFullYear();

			// Month names
			const Mesi = ['Gen', 'Feb', 'Mar', 'Apr', 'Mag', 'Giu', 'Lug', 'Ago', 'Set', 'Ott', 'Nov', 'Dic'];

			//to replace month
			// format_D = format_D.replace("M", month.toString().padStart(2,"0"));
			format_D = format_D.replace("M", Mesi[month -1]);
			// console.log(Mesi[month -1]);

			// to replace year
			if (format_D.indexOf("yyyy") > -1) {
				format_D = format_D.replace("yyyy", year.toString());
			} else if (format_D.indexOf("yy") > -1) {
				format_D = format_D.replace("yy", year.toString().substr(2,2));
			}

			// //to replace day
			format_D = format_D.replace("dd", day.toString().padStart(2,"0"));

			return format_D;
		},
		asciiReplace: function(str) {
			let cleanStr = str.replaceAll('&#39;', '’')
				.replaceAll('&#242;', 'ò')
				.replaceAll('&#249;', 'ù')
				.replaceAll('&#224;', 'à')
				.replaceAll('&#236;', 'ì')
				.replaceAll('&#232;', 'è')
				.replaceAll('&#233;', 'é')
				.replaceAll('&#200;', 'È')
				.replaceAll('&#201;', 'É')
				.replaceAll('&quot;', '"')

			return cleanStr;
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
		}
    },
    mounted() {
		this.fetchsessionCart();
        this.fetchEvento();
		this.isSafari();
		this.fetchFood();
		this.fetchFiscalAddress();
    },
}

</script>