<template>
	<div class="single-food">
		<div class="foto" 
			:style="{ 'background': 'linear-gradient(180deg, rgba(16, 16, 16, 0.00) 0%, #080808 86.46%), url(' + foto + ')' }">
		</div>

		<h1 class="title">{{ Nome }}</h1>
		<p>{{ descrizione }}</p>

		<div class="quantity">
			<button @click.prevent="qtyMinus(id, 'snack')">
				<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M2 10H18" stroke="white" stroke-opacity="0.9" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
				</svg> 
			</button>
			<input type="number" :name="'qty-'+id" :data-prezzo="thisfood.importo" :data-nome="Nome" :id="'qty-'+id" min="0" :value="foodVal(id)">
			<button @click.prevent="qtyPlus(id, 'snack')">
				<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M2 10H18M10 18V2" stroke="white" stroke-opacity="0.9" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
				</svg> 
			</button>
		</div>

		<p>{{ $t('Prezzo') }} <br>
			<b>{{ Prezzo }}€</b>
		</p>

		<div id="snacks">
			<div class="inner">

				<div class="food-footer" :class="{hidden: snackChoose == false ? 'hidden' : '' }">
					<div class="selezione">
						<span>{{ $t('Selezionate') }}</span>
						<div class="data-sel">
							{{ dataSel.qty }}
						</div>
					</div>

					<div class="buttons-submit">
						<div id="btn-checkout-snack" class="submit flex" @click="saveSessionCookie('checkout')">
							{{ $t('Prosegui al checkout') }}
							<i class="cart"></i>
						</div>

						<div id="btn-film" class="submit flex" @click="saveSessionCookie('film')">
							{{ $t('Scegli film') }}
							<i class="film"></i>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
	let idCinema = 600;

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
			recap: '',
			thisfood: '',
			Nome: '',
			Prezzo: 0,
			descrizione: '',
			foto: '',
			snackChoose: false,
			snackChosen: [],
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
				carrello: [],
				biglietti: [],
				nBiglietti: 0,
				recap: [],
				prezzi: [],
				totString: '',
				totale: 0,
				index: 0,
				isChecked: false,
			},
			foodCat: [],
			dataSel: {
				cat: '',
				qty: 0
			},
			browser: '',
        };
    },
    methods: {
		fetchCookiedata: function() {
			let sessionCookie = localStorage.getItem('sessionCookie') ? JSON.parse(localStorage.getItem('sessionCookie')) : {},
				snackChosen = Object.keys(sessionCookie).length > 0 && Array.isArray(sessionCookie.snacksArray) ? sessionCookie.snacksArray : [],
				foodQty = 0,
				scelte = [];

			this.recap = sessionCookie.recap ? sessionCookie.recap : '';

			console.log(scelte);
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
			
			console.log(sessionCookie);
			this.cookieData = sessionCookie;

			if (Object.keys(this.cookieData).length > 0) {
				this.checkedGiorno = this.cookieData.giorno ? this.cookieData.giorno : '';
				this.checkedOrario.ora = this.cookieData.ora ? this.cookieData.ora : '';
				this.checkedOrario.sala = this.cookieData.sala ? this.cookieData.sala : '';
				this.checkedOrario.idsala = this.cookieData.idsala ? this.cookieData.idsala : '';
				this.checkedOrario.idPerf = this.cookieData.idPerf ? this.cookieData.idPerf : '';
				this.checkedOrario.idTariffa = this.cookieData.idTariffa ? this.cookieData.idTariffa : '';
				this.checkedBiglietto = this.cookieData.checkedBiglietto ? this.cookieData.checkedBiglietto : {};
				this.checkedBiglietto.carrello = this.cookieData.carrello ? this.cookieData.carrello : [];
				this.checkedBiglietto.biglietti = this.cookieData.biglietti ? this.cookieData.biglietti : [];
				this.checkedBiglietto.nBiglietti = this.cookieData.nBiglietti ? this.cookieData.nBiglietti : 0;
				this.checkedBiglietto.recap = this.cookieData.recap ? this.cookieData.recap : [];
				this.checkedBiglietto.posti = this.cookieData.posti ? this.cookieData.posti : [];
				this.checkedBiglietto.prezzi = this.cookieData.prezzi ? this.cookieData.prezzi : [];
				this.checkedBiglietto.totString = this.cookieData.totString ? this.cookieData.totString : '';
				this.checkedBiglietto.totale = this.cookieData.totale ? this.cookieData.totale : 0;
				this.checkedBiglietto.index = this.cookieData.index ? this.cookieData.index : 0;
				this.checkedBiglietto.isChecked = this.cookieData.isChecked ? this.cookieData.isChecked : false;
				this.recap = this.cookieData.recapString ? this.cookieData.recapString : '';
			}
			console.log(Object.keys(this.cookieData).length)
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
				this.checkedBiglietto.carrello.push(el)

				this.checkedBiglietto.recap.push({
					type: 'snack',
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

			this.nBiglietti = this.checkedBiglietto.nBiglietti;
			this.recap = this.checkedBiglietto.recap.map(function(elem){
				return elem.string;
			}).join(' | ');

			this.recap += ` | ${this.checkedBiglietto.totString}`;

			if(this.checkedGiorno != '' && this.checkedOrario != '' && this.checkedBiglietto.isChecked === true) {
				this.activeSubmit = true
			}

			this.saveSessionCookie();
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

				let rrecap = this.checkedBiglietto.recap.findIndex(function(o){
					return o.id === targetSlug;
				})
				if (rrecap !== -1) this.checkedBiglietto.recap.splice(rrecap, 1);

				this.checkedBiglietto.recap.push({
					type: 'snack',
					id: targetSlug,
					string: `${targetNome} x${targetQty}`
				})

				let biglietti = this.checkedBiglietto.carrello.findIndex(function(o){
					return o.id === targetSlug;
				})
				if (biglietti !== -1) this.checkedBiglietto.carrello.splice(biglietti, 1);

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
				this.checkedBiglietto.carrello.push(el)
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

			if(this.checkedGiorno != '' && this.checkedOrario != '' && this.checkedBiglietto.isChecked === true) {
				this.activeSubmit = true
			}

			this.saveSessionCookie();
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
					this.foto = `http://fce.winticstellar.com/evolution/webapi/Handlers/handlerArticoli.ashx?idnegozio=${idCinema}&idarticolo=${this.id}&LOB_SIZE=SMALL&t=20230707084628`;


					categorie.map((elem) => {
						elem.Nodi.map((el) => {
							if (el.idarticolo == this.id)
							this.thisfood = el.articolo;
						})
					})

					this.Nome = this.thisfood.nome.indexOf('Ingredienti:') > -1 ? this.thisfood.nome.slice(0,this.thisfood.nome.indexOf('Ingredienti:')) : this.thisfood.nome;
					this.descrizione = this.thisfood.nome.indexOf('Ingredienti:') > -1 ? this.thisfood.nome.slice(-this.thisfood.nome.indexOf('Ingredienti:')) : '';
            		this.Prezzo = parseFloat(this.thisfood.importo).toFixed(2).replace('.', ',');
			});
		},
		foodVal: function(id) {
			let value = 0;
			for (let i = 0; i < this.snackChosen.length; i++) {
				if (this.snackChosen[i].idnodo == id) {
					value = this.snackChosen[i].qty;
				}
			}
			return value;
		},
		saveSessionCookie: function($path) {
			let sessionSnack = this.snackChosen.map(function(elem){
					return elem.nome;
				}).join(' | '),
				posti = this.checkedBiglietto.carrello.map(function(elem){
					return elem.posto;
				}),
				snacksArray = this.checkedBiglietto.carrello.map(function(elem){
					return elem.type === 'snack' ? elem : [];
				});

			let sessionCookie = {
				giorno: this.checkedGiorno,
				ora: this.checkedOrario.ora,
				sala: this.checkedOrario.sala,
				idsala: this.checkedOrario.idsala,
				idPerf: this.checkedOrario.idPerf,
				idTariffa: this.checkedOrario.idTariffa,
				carrello: this.checkedBiglietto.carrello,
				biglietti: this.checkedBiglietto.biglietti,
				snack: sessionSnack,
				snacksArray: snacksArray,
				posti: posti,
				totale: this.totale,
				nBiglietti: 0,
				recap: this.checkedBiglietto.recap,
				recapString: this.recapString
			}

			let sessionCookieStr = JSON.stringify(sessionCookie);
			localStorage.setItem('sessionCookie', sessionCookieStr);

			console.log(sessionCookie);
			if ($path !== undefined)
				location.href = `${this.appUrl}${$path}`;
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
		this.fetchCookiedata();
		this.isSafari();
		this.fetchFood();
    },
    // components: { RouterLink }
	}

</script>