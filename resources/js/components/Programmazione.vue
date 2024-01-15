<template>
	<div class="film" v-for="film in films">
		<a :href="'/film/'+film.id" class="wrap" :style="{ 'background': 'linear-gradient(180deg, rgba(0, 0, 0, 0.00) 47.4%, rgba(0, 0, 0, 0.60) 80.21%), url(' + film.img + ')' }">
			<!-- <RouterLink :to="{name: 'FilmDetails', params: { id: film.id }}"><h2>{{ film.titolo }}</h2></RouterLink> -->
            <h2>{{ film.titolo }}</h2>
        </a>
	</div>
</template>
  
  <script>
	import axios from 'redaxios';

	export default {
    data() {
        return {
            eventiID: [],
            films: [],
            sala: [],
        };
    },
    methods: {
        fetchEventiId: function () {
            let eventi_arr = [];
            axios.get('https://services.webtic.it/services/WSC_Webtic.asmx/_getEventListDetail?idcinema=600', {
            })
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
                axios.get(`https://services.webtic.it/services/WSC_Webtic.asmx/_getEventImage?idcinema=600&idevento=${id}`, {
                    // headers: { "Access-Control-Allow-Origin": ["http://fce.test/public", "http://192.168.1.140:5174/"],
                    //             "Access-Control-Allow-Headers": "*",
                    // }
                })
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
        this.fetchEventiId();
    },
}
  </script>