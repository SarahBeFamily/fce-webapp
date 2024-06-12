<template>
	<div class="recommended" v-for="film in films" :id="film.id">
		<div class="wrap" :style="{ 'background': 'linear-gradient(180deg, rgba(0, 0, 0, 0.00) 17.4%, rgba(0, 0, 0, 0.70) 85.21%), url(' + film.img + ')' }">
			<h2>{{ film.titolo }}</h2>
			<div class="meta flex">
				<ul class="genere">
					<li v-for="gen in film.genere">{{ gen }}</li>
				</ul>
				<span>{{ film.durata }}</span>
			</div>
		</div>
	</div>
</template>
  
  <script>
	import axios from 'redaxios'
	import $ from 'jquery'
	import 'slick-carousel'

	let idCinema = localStorage.getItem('idCinema') || 600;

  export default {
	data() {
	  return {
		WebtikBase: import.meta.env.VITE_WEBTIK_SERVICE_BASE,
		idCinema: idCinema,
		eventiID: [],
		films: [],
		sala: [],
	  }
	},
	methods: {
		fetchEventiId: function() {
			let eventi_arr = [];
			axios.get(`${this.WebtikBase}_getEventListDetail?idcinema=${this.idCinema}`)
			.then((res) => {
				const XmlNode = new DOMParser().parseFromString(res.data, 'text/xml'),
                    jsonData = this.xmlToJson(XmlNode);

					let films = jsonData.eventListDetail.eventi.evento;

					for (let i = 0; i < films.length; i++) {
                    let durata = films[i].durata;
                    let genere = films[i].genere;
                    let durata_arr = durata.split(':');

                    eventi_arr.push({
                        id: films[i].idevento,
                        titolo: films[i].titolo,
                        img: '',
                        genere: genere.split(','),
                        durata: `${Math.trunc(durata_arr[0])}h ${durata_arr[1]}min`,
                    });
                }

                this.films = eventi_arr;
                this.addImage();
			});
		},
		addImage: function() {
			let Eventi = this.films;
			for (let i = 0; i < Eventi.length; i++) {

				let id = Eventi[i].id;
				axios.get(`${this.WebtikBase}_getEventImage?idcinema=${this.idCinema}&idevento=${id}`)
				.then((res) => {
					const XmlNode = new DOMParser().parseFromString(res.data, 'text/xml'),
                    	jsonData = this.xmlToJson(XmlNode);
					
					Eventi[i].img = `data:image/png;base64,${jsonData.base64Binary}`;
				});
				
			}

			this.slideContentRec();			
		},
		slideContentRec: function(){
			setTimeout(function() {
				$('.carousel-one').slick({
					dots: false,
					arrows: false,
					infinite: false,
					centerMode: false,
					variableWidth: false,
					slidesToShow: 4.2,
					slidesToScroll: 1,
					autoplay: false,
					autoplaySpeed: 2500,
					cssEase: 'cubic-bezier(0.785, 0.135, 0.150, 0.860)',
					responsive: [
                        {
                        breakpoint: 1280,
                        settings: {
                            slidesToShow: 3.2,
                            slidesToScroll: 1,
                            infinite: true,
                            dots: true
                        }
                        },
                        {
                        breakpoint: 1024,
                        settings: {
                            slidesToShow: 2.2,
                            slidesToScroll: 1,
                            infinite: true,
                            dots: true
                        }
                        },
                        {
                        breakpoint: 600,
                        settings: {
                            slidesToShow: 1.2,
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
		}
	},
	mounted() {
		this.fetchEventiId();
	}
  }
  </script>