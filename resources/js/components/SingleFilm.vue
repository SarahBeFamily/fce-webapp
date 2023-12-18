<template>
	<div class="single-movie">
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
							<div class="thmb" :style="{ 'background-image': 'url(' + attore.foto + ')' }"></div>
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
							<label :for="ora.idOra" v-for="ora in orari" class="orario" :class="{selected: checkedOrario.ora == ora.ora ? 'selected' : ''}">
								<input type="radio" :id="ora.idOra" name="orario" class="orario" @change="choosePerformance(ora.idPerf, ora.idTariffa, ora.ora, ora.idsala, ora.sala)" v-model="checkedOrario.ora" :value="ora.ora">
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
							<input type="number" :name="'qty-'+tar.id" :data-prezzo="tar.prezzo" :data-nome="tar.nome" :id="'qty-'+tar.id" min="0" value="0">
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

					<div class="recap">
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
							<label :for="ora.idOra" v-for="ora in orari" class="orario" :class="{selected: checkedOrario.ora == ora.ora ? 'selected' : ''}">
								<input type="radio" :id="ora.idOra" name="orario" class="orario" @change="choosePerformance(ora.idPerf, ora.idTariffa, ora.ora, ora.idsala, ora.sala)" v-model="checkedOrario.ora" :value="ora.ora">
								<span class="sala">{{ ora.sala }}</span>
								<span class="ora">{{ ora.ora }}</span>
							</label>
						</div>
					</div>
				</div>
			</div>

			<div id="posti" class="cont hidden">
				<h3>Seleziona il posto</h3>

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
							:class="(posto.idposto).replace('/', '-')" 
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
					{{ $t('Prosegui al checkout') }}
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

					<div class="tik" v-for="tik in checkedBiglietto.biglietti">
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
							Prosegui al checkout
							<i class="cart"></i>
						</div>
					</div>
				</div>

				<div id="btn-acquista" class="submit flex" :class="{inactive: activeSubmitCheckout == false ? 'inactive' : '' }" @click="activeSubmitCheckout == true ? saveSessionCookie() : null">
					Acquista i biglietti
					<i class="cart"></i>
				</div>

				<div class="recap">
					{{ recap }}
				</div>
			</div>
		</div>
	</div>
</template>

<script>
	// Wiki API
	let WikiBase = 'https://en.wikipedia.org/w/api.php?action=query&format=json&origin=*&prop=pageimages&list=&titles=',
		WebtikBase = 'https://services.webtic.it/services/WSC_Webtic.asmx/',
		idCinema = 600;

	import axios from 'redaxios'
	import $ from 'jquery'
	import 'slick-carousel'

	import.meta.glob(['../../images/**',]);

	export default {
		props: ['route', 'path'],
    data() {
        return {
			appUrl: this.path,
			id: this.route,
			idCinema: idCinema,
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
				idPerf: ''
			},
			checkedBiglietto: {
				biglietti: [],
				nBiglietti: 0,
				recap: [],
				prezzi: [],
				totString: '',
				totale: 0,
				index: 0,
				isChecked: false,
			},
			activeSubmit: false,
			Sala: {
				sala: '',
				file: '',
				colonne: ''
			},
			postiSala: [],
			postiScelti: [],
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
			snacks: {
				food: [
					{
						id: 'popcorn-salati',
						nome: 'PopCorn Salati',
						prezzo: 4,
					},
					{
						id: 'popcorn-caramello',
						nome: 'PopCorn Caramello',
						prezzo: 4,
					},
					{
						id: 'mms',
						nome: 'M&Ms',
						prezzo: 3,
					},
					{
						id: 'caramelle',
						nome: 'Caramelle',
						prezzo: 5,
					},
					{
						id: 'nachos',
						nome: 'Nachos',
						prezzo: 7,
					},
				],
				drink: [
					{
						id: 'acqua',
						nome: 'Acqua',
						prezzo: 1,
					},
					{
						id: 'coca-cola',
						nome: 'Coca Cola',
						prezzo: 3,
					},
					{
						id: 'pepsi',
						nome: 'Pepsi',
						prezzo: 3,
					},
					{
						id: 'sprite',
						nome: 'Sprite',
						prezzo: 3,
					},
					{
						id: 'fanta',
						nome: 'Fanta',
						prezzo: 3,
					},
					{
						id: 'te-al-limone',
						nome: 'Té al Limone',
						prezzo: 3,
					},
					{
						id: 'te-alla-pesca',
						nome: 'Té alla Pesca',
						prezzo: 3,
					},
				]
			},
			browser: '',
        };
    },
    methods: {
        fetchEvento: function () {
            axios.get(`${WebtikBase}_getPerformanceListDetail?idcinema=${idCinema}&idevento=${this.id}`)
                .then((res) => {
				const XmlNode = new DOMParser().parseFromString(res.data, 'text/xml'),
					jsonData = this.xmlToJson(XmlNode),
					evento = jsonData.PerformanceListDetail.evento,
					performances = jsonData.PerformanceListDetail.performances.performance,
					durata = evento.durata,
					attori = evento.attori,
					genere = evento.genere;

					console.log(performances)

				let durata_arr = durata.split(':'),
					cast_array = attori.split(','),
					genere_arr = genere.split(',');

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
							this.checkedGiorno = giorno;
						}

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

					this.checkedGiorno = giorno;

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

				console.log(this.progr)
            });
        },
        addImage: function () {
			axios.get(`https://services.webtic.it/services/WSC_Webtic.asmx/_getEventImage?idcinema=${idCinema}&idevento=${this.id}`)
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
					let foto = img != undefined ? (img.source).replace('50px', '200px') : asset_url+'/placeholder-person-f691e76b.jpg';

					attore.foto = foto;
				});
			}

			console.log(this.cast)
        },
		choosePerformance: function(idPerf, idTariffa, ora, idsala, nomesala) {
			// Prezzi per performance
			axios.get(`${WebtikBase}_getPriceListFull?idcinema=${idCinema}&idPerformance=${idPerf}&idTariffa=${idTariffa}&mode=3`)
                .then((res) => {
					const XmlNode = new DOMParser().parseFromString(res.data, 'text/xml'),
						jsonData = this.xmlToJson(XmlNode),
						details = jsonData.priceList.prices.price,
						tariffe_array = [];

					for (let i = 0; i < details.length; i++) {
						const prezzo = details[i];
						
						tariffe_array.push({
							id: prezzo.idbiglietto,
							nome: (prezzo.descr_biglietto).toLowerCase(),
							prezzo: prezzo.prezzo,
							supplemento: prezzo.supplemento,
						})
					}

				this.prezzi = tariffe_array;
				this.checkedOrario.ora = ora;
				this.checkedOrario.sala = nomesala;
				this.checkedOrario.idsala = idsala;
				this.checkedOrario.idPerf = idPerf;
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
			
			return cleanStr;
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
			$(`.tab .cont#${data}`).removeClass('hidden');
			$(`.tab .cont:not(#${data})`).addClass('hidden');

			if (data == 'checkout') {
				this.isCheckout = true;
			} else {
				this.isCheckout = false;
			}

			this.activeTab.dataTab = data;
		},
		qtyMinus: function(id, type = '') {
			const targetInput = $(`#qty-${id}`),
				targetNome = targetInput.attr('data-nome').indexOf('Ingredienti:') > -1 ? targetInput.attr('data-nome').slice(0, targetInput.attr('data-nome').indexOf('Ingredienti:')) : targetInput.attr('data-nome'),
				targetSlug = targetNome.replaceAll(' ', '-').toLowerCase(),
				targetPrezzo = targetInput.attr('data-prezzo');

			let targetQty = parseInt(targetInput.val());
			let minus = targetQty;
			let tot = 0;

			if(targetQty > 0) {
				minus = targetQty-1
			} else 
				minus = 0

			targetInput.val(minus)

			if (type == '') {

				let rrecap = this.checkedBiglietto.recap.findIndex(function(o){
					return o.id === `${targetSlug}-${targetQty}`;
				})
				if (rrecap !== -1) this.checkedBiglietto.recap.splice(rrecap, 1);

				if (minus > 0) {
					this.checkedBiglietto.recap.push({
						id: `${targetSlug}-${minus}`,
						string: `${targetNome} x${minus}`
					})
				}
				
				this.checkedBiglietto.nBiglietti = this.checkedBiglietto.nBiglietti -1;
				let index = this.checkedBiglietto.index;
				
				let biglietti = this.checkedBiglietto.biglietti.findIndex(function(o){
					return o.id === `${targetSlug}-${index}`;
				})
				if (biglietti !== -1) this.checkedBiglietto.biglietti.splice(biglietti, 1);

				this.checkedBiglietto.index = this.checkedBiglietto.index > 0 ? this.checkedBiglietto.index-1 : 0;

				this.removeItemOnce(this.checkedBiglietto.prezzi, targetPrezzo)

			} else if (type == 'snack') {
				let snacks = this.snackChosen.findIndex(function(o){
					return o.id === targetSlug;
				})
				if (snacks !== -1) this.snackChosen.splice(snacks, 1);

				let snacksTik = this.checkedBiglietto.biglietti.findIndex(function(o){
					return o.id === targetSlug;
				})
				if (snacksTik !== -1) {
					this.checkedBiglietto.biglietti.splice(snacksTik, 1);
				}

				let rrecap = this.checkedBiglietto.recap.findIndex(function(o){
					return o.id === targetSlug;
				})
				if (rrecap !== -1) this.checkedBiglietto.recap.splice(rrecap, 1);

				let prezzo_precedente = (targetPrezzo * targetQty).toString();
				let prezzo_attuale = (targetPrezzo * minus).toString();
				this.removeItemOnce(this.checkedBiglietto.prezzi, prezzo_precedente)

				if (minus > 0) {
					let el = {
						type: 'snack',
						id: targetSlug,
						nome: targetNome,
						idnodo: id,
						prezzo: parseFloat(targetPrezzo).toFixed(2).replace('.', ','),
						prezzoTot: parseFloat(targetPrezzo * minus).toFixed(2).replace('.', ','),
						qty: minus,
						qtySnack: parseFloat(targetPrezzo).toFixed(2).replace('.', ',')+'€ x '+minus,
					};

					this.snackChosen.push(el)
					this.snackChoose = true
					this.checkedBiglietto.biglietti.push(el)

					this.checkedBiglietto.recap.push({
						id: targetSlug,
						string: `${targetNome} x${minus}`
					})

					this.checkedBiglietto.prezzi.push(prezzo_attuale);
				
				} else {
					if (this.snackChosen.length === 0)
						this.snackChoose = false
				}

				if (targetQty > 0)
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

			let targetQty = parseInt(targetInput.val());
			let plus = targetQty+1;
			let tot = 0;

			targetInput.val(plus);
			targetQty = parseInt(targetInput.val());

			if (targetQty > 0) {
				
				if (type == '') {

					let rrecap = this.checkedBiglietto.recap.findIndex(function(o){
						return o.id === `${targetSlug}-${targetQty-1}`;
					})
					if (rrecap !== -1) this.checkedBiglietto.recap.splice(rrecap, 1);

					this.checkedBiglietto.recap.push({
						id: `${targetSlug}-${targetQty}`,
						string: `${targetNome} x${targetQty}`
					})

					this.checkedBiglietto.prezzi.push(targetPrezzo)
					
					let el = {
						type: 'biglietto',
						id: `${targetSlug}-${this.checkedBiglietto.index}`,
						nome: targetNome,
						prezzo: parseFloat(targetPrezzo).toFixed(2).replace('.', ','),
						qty: targetQty / targetQty,
						posto: '',
					};

					this.checkedBiglietto.biglietti.push(el)

					this.checkedBiglietto.nBiglietti = this.checkedBiglietto.nBiglietti +1;

				} else if (type == 'snack') {

					let rrecap = this.checkedBiglietto.recap.findIndex(function(o){
						return o.id === targetSlug;
					})
					if (rrecap !== -1) this.checkedBiglietto.recap.splice(rrecap, 1);

					this.checkedBiglietto.recap.push({
						id: targetSlug,
						string: `${targetNome} x${targetQty}`
					})

					let biglietti = this.checkedBiglietto.biglietti.findIndex(function(o){
						return o.id === targetSlug;
					})
					if (biglietti !== -1) this.checkedBiglietto.biglietti.splice(biglietti, 1);

					// let snacks = this.snackChosen.findIndex(function(o){
					// 	return o.id === targetSlug;
					// })
					// if (snacks !== -1) this.snackChosen.splice(snacks, 1);

					let prezzo_remove = (targetPrezzo * (targetQty-1)).toString();
					let prezzo_tot = (targetPrezzo * targetQty).toString();
					this.removeItemOnce(this.checkedBiglietto.prezzi, prezzo_remove);
					this.checkedBiglietto.prezzi.push(prezzo_tot);

					let el = {
						type: 'snack',
						id: targetSlug,
						nome: targetNome,
						idnodo: id,
						prezzo: parseFloat(targetPrezzo).toFixed(2).replace('.', ','),
						prezzoTot: parseFloat(targetPrezzo * targetQty).toFixed(2).replace('.', ','),
						qty: targetQty,
						qtySnack: parseFloat(targetPrezzo).toFixed(2).replace('.', ',')+'€ x '+targetQty,
					};

					this.snackChosen.push(el)
					this.snackChoose = true
					this.dataSel.qty+=1
					this.checkedBiglietto.biglietti.push(el)

					console.log(this.snackChosen)
				}
			}
			
			console.log(this.checkedBiglietto)

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

			if(this.checkedGiorno != '' && this.checkedOrario != '' && this.checkedBiglietto.isChecked === true) {
				this.activeSubmit = true
			}
		},
		removeItemOnce: function(arr, value) {
			let index = arr.indexOf(value);
			if (index > -1) {
				arr.splice(index, 1);
			}
			return arr;
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
		getPostiSala: function() {
			axios.get(`${WebtikBase}_getMappaSale?idcinema=${idCinema}&idsala=${this.checkedOrario.idsala}`)
                .then((res) => {
					let xmldata = res.data;
					let XmlNode = new DOMParser().parseFromString(xmldata, 'text/xml');
					let jsonData = this.xmlToJson(XmlNode);
					
					console.log(jsonData);
					
					this.postiSala = jsonData.Sala.posti.posto;
					console.log(this.postiSala);

				this.tabChange('posti');
            });

			// posti occupati
			axios.get(`${WebtikBase}_getOccupancy?idcinema=${idCinema}&idperformance=${this.checkedOrario.idPerf}`)
                .then((res) => {
					let xmldata = res.data;
					let XmlNode = new DOMParser().parseFromString(xmldata, 'text/xml');
					let jsonData = this.xmlToJson(XmlNode);
					
					// console.log(jsonData);
					
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
		scegliPosto: function($event, id) {

			if (!this.postiScelti.includes(id)) {
				if(this.postiScelti.length < parseInt(this.nBiglietti)) {
					this.postiScelti.push(id);
				} else {
					alert("Hai già selezionato tutti i posti disponibili");
					$event.target.checked = false;
				}
			} else {
				this.postiScelti.splice(this.postiScelti.indexOf(id), 1);
			}

			for (let i = 0; i < this.postiScelti.length; i++) {
				const postoID = this.postiScelti[i],
					posto_arr = postoID.split('/'),
					fila = posto_arr[0],
					posto = posto_arr[1];

				if (!Array.isArray(this.checkedBiglietto.biglietti[i])) {
					this.checkedBiglietto.biglietti[i].posto = `Fila ${fila} Posto ${posto}`
				}
				
				// this.checkedBiglietto.biglietti[i].posto = `Fila ${fila} Posto ${posto}`
			}

			console.log(this.postiScelti)
			console.log(this.postiScelti.length)
			console.log(parseInt(this.nBiglietti))

			console.log(this.checkedBiglietto.biglietti)

			if (parseInt(this.nBiglietti) === this.postiScelti.length) {
				this.activeSubmitCheckout = true;
			} else {
				this.activeSubmitCheckout = false;
			}
		},
		fetchFood: function() {
			axios.post(`http://fce.winticstellar.com/evolution/webapi/api/getArticoliNegozio`,
			{
				"idnegozio": "600",
				"web_box": "demo",
				"web_operator": "crea",
				"sessionId": "azttmxtdaa_100",
				"trackid": "2200"
			})
			.then((res) => {
					let categorie = res.data.TastieraArticoli.Nodi;
					
					console.log(categorie);
					// foto
					//`http://fce.winticstellar.com/evolution/webapi/Handlers/handlerArticoli.ashx?idnegozio=${idCinema}&idarticolo=${idArticolo}&LOB_SIZE=SMALL&t=20230707084628`;
					this.foodCat = categorie;
            });
		},
		saveSessionCookie: function() {
			let posti = this.checkedBiglietto.biglietti.map(function(elem){
				return elem.posto;
			}).join(' | '),
				snacks = this.checkedBiglietto.biglietti.map(function(elem){
					return elem.type === 'snack' ? elem : [];
				});

			let sessionCookie = {
				giorno: this.checkedGiorno,
				ora: this.checkedOrario.ora,
				sala: this.checkedOrario.sala,
				idsala: this.checkedOrario.idsala,
				idPerf: this.checkedOrario.idPerf,
				idTariffa: this.checkedOrario.idTariffa,
				biglietti: this.checkedBiglietto.biglietti,
				snack: snacks,
				posti: posti,
				totale: this.checkedBiglietto.totale,
				nBiglietti: this.checkedBiglietto.nBiglietti,
				recap: this.checkedBiglietto.recap,
				checkedBiglietto: this.checkedBiglietto,
			}

			let sessionCookieStr = JSON.stringify(sessionCookie);
			localStorage.setItem('sessionCookie', sessionCookieStr);

			console.log(sessionCookie);
			location.href = `${this.appUrl}checkout`;
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
        this.fetchEvento();
		this.isSafari();
		this.fetchFood();
    },
    // components: { RouterLink }
	}

</script>