<template>
	<div class="film" v-for="film in films">
		<a :href="`/${idCinema}/film/${film.id}`" class="wrap" :style="{ 'background': 'linear-gradient(180deg, rgba(0, 0, 0, 0.00) 47.4%, rgba(0, 0, 0, 0.60) 80.21%), url(' + film.img + ')' }">
            <h2>{{ film.titolo }}</h2>
        </a>
	</div>

    <p v-if="programmazione === false">
        {{ $t('Nessun film in programmazione per questo cinema') }}
    </p>
</template>
  
  <script>
	import axios from 'redaxios';
    let idCinema = localStorage.getItem('idCinema') || 600;

	export default {
        props: ['route', 'path'],
    data() {
        return {
            appUrl: this.path,
			idCinema: idCinema,
            WebtikBase: import.meta.env.VITE_WEBTIK_SERVICE_BASE,
            eventiID: [],
            films: [],
            sala: [],
            programmazione: true,
        };
    },
    methods: {
        checkCinema: function () {
            axios.get(`${this.WebtikBase}_getEventListDetail?idcinema=${this.idCinema}`)
            .then((res) => {
                const XmlNode = new DOMParser().parseFromString(res.data, 'text/xml'),
                    jsonData = this.xmlToJson(XmlNode);
                // faccio partire la funzione per caricare i film se l'idCinema è valido
                if(jsonData.eventListDetail.eventi !== undefined){
                    this.fetchEventiId();
                } else {
                    this.programmazione = false;
                }
            }).catch(error=>{
                console.log(error);
            })
        },
        fetchEventiId: function () {
            let eventi_arr = [];
            axios.get(`${this.WebtikBase}_getEventListDetail?idcinema=${this.idCinema}`)
                .then((res) => {
                const XmlNode = new DOMParser().parseFromString(res.data, 'text/xml'),
                    jsonData = this.xmlToJson(XmlNode);
                let films = jsonData.eventListDetail.eventi.evento;

                if (films.length === undefined) {
                    let durata = films.durata;
                    let genere = films.genere;
                    let durata_arr = durata.split(':');

                    eventi_arr.push({
                        id: films.idevento,
                        titolo: films.titolo,
                        img: '',
                        genere: genere.split(','),
                        durata: `${Math.trunc(durata_arr[0])}h ${durata_arr[1]}min`,
                    });
                } else {
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
                }
                
                this.films = eventi_arr;
                this.addImage();

                console.log(films);
            }).catch(error=>{
                console.log(error);
            })
        },
        addImage: function () {
            let Eventi = this.films;
            for (let i = 0; i < Eventi.length; i++) {
                let id = Eventi[i].id;
                axios.get(`${this.WebtikBase}_getEventImage?idcinema=${this.idCinema}&idevento=${id}`)
                    .then((res) => {
                    const XmlNode = new DOMParser().parseFromString(res.data, 'text/xml'),
                        jsonData = this.xmlToJson(XmlNode);

                    Eventi[i].img = `data:image/png;base64,${jsonData.base64Binary}`;
                }).catch(error=>{
                    console.log(error);
                })
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
		}
    },
    mounted() {
        this.checkCinema();
    },
}
  </script>