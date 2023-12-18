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

		<p>Prezzo <br>
			<b>{{ Prezzo }}€</b>
		</p>
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
				snackChosen = sessionCookie.snack.length > 0 ? sessionCookie.snack : [],
				foodQty = 0;

			this.recap = sessionCookie.recap;
			this.snackChosen = snackChosen;
			this.snackChoose = snackChosen.length > 0 ? true : false;

			if (snackChosen.length > 0) {
				$.each(snackChosen,function(){foodQty+=parseInt(this.qty) || 0;});
				this.dataSel.qty = foodQty;
			} else {
				this.dataSel.qty = 0;
			}
			
			console.log(sessionCookie);
			this.cookieData = sessionCookie;
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
			this.snackChosen.findIndex(function(o){
				if( o.idnodo === id) value = o.qty || 0;
			});
			return value;
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