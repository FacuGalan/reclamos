<div class="max-w-7xl mx-auto p-6 pt-0 space-y-6">
    
        
        
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div id="mapa-barrios" class="w-full bg-gray-200 dark:bg-gray-600" style="height: 600px;"></div>
    </div>
        

    @push('scripts')
    <script>
        let mapaBarrios;
        let googleMapsLoaded = false;

        function cargarGoogleMaps() {
            if (googleMapsLoaded) {
                return Promise.resolve();
            }

            if (typeof google !== 'undefined' && google.maps) {
                googleMapsLoaded = true;
                return Promise.resolve();
            }

            return new Promise((resolve, reject) => {
                const script = document.createElement('script');
                script.src = 'https://maps.googleapis.com/maps/api/js?key=AIzaSyArpDAi1ugbTSLT4wlr4T_qMmBZLouBfxo&libraries=geometry';
                script.async = true;
                script.defer = true;
                
                script.onload = function() {
                    googleMapsLoaded = true;
                    resolve();
                };
                
                script.onerror = function() {
                    reject(new Error('Error al cargar Google Maps API'));
                };
                
                document.head.appendChild(script);
            });
        }

        function inicializarMapaBarrios() {
            cargarGoogleMaps().then(() => {
                const mapContainer = document.getElementById('mapa-barrios');
                if (!mapContainer) return;

                // Centrar en Mercedes
                const centroMercedes = { lat: -34.6549, lng: -59.4307 };
                
                mapaBarrios = new google.maps.Map(mapContainer, {
                    zoom: 14,
                    center: centroMercedes,
                    mapTypeId: google.maps.MapTypeId.ROADMAP
                });

                // Cargar todos los barrios
                const barrios = @json($barrios->toArray());
                
                barrios.forEach(barrio => {
                    dibujarBarrio(barrio);
                });

                // Ajustar el mapa para mostrar todos los polígonos
                const bounds = new google.maps.LatLngBounds();
                barrios.forEach(barrio => {
                    const coordenadas = parsearWKT(barrio.poligono);
                    coordenadas.forEach(coord => {
                        bounds.extend(coord);
                    });
                });
                
                /*
                if (!bounds.isEmpty()) {
                    mapaBarrios.fitBounds(bounds);
                }
                    */
            });
        }

        function dibujarBarrio(barrio) {
    // Convertir WKT a coordenadas
    const coordenadas = parsearWKT(barrio.poligono);
    
    if (coordenadas.length === 0) return;

    // Crear el polígono
    const poligono = new google.maps.Polygon({
        paths: coordenadas,
        strokeColor: '#77BF43',
        strokeOpacity: 0.8,
        strokeWeight: 2,
        fillColor: '#77BF43',
        fillOpacity: 0.2,
        map: mapaBarrios
    });

    // Agregar eventos de hover
    poligono.addListener('mouseover', function() {
        poligono.setOptions({
            fillColor: '#5A9B2E', // Color más oscuro para hover
            fillOpacity: 0.35,    // Opacidad más alta
            strokeColor: '#5A9B2E',
            strokeWeight: 3       // Borde más grueso
        });
    });

    poligono.addListener('mouseout', function() {
        poligono.setOptions({
            fillColor: '#77BF43',  // Color original
            fillOpacity: 0.2,      // Opacidad original
            strokeColor: '#77BF43',
            strokeWeight: 2        // Borde original
        });
    });

    // Calcular el centro del polígono para posicionar el texto
    const bounds = new google.maps.LatLngBounds();
    coordenadas.forEach(coord => bounds.extend(coord));
    const centro = bounds.getCenter();

    // Crear marcador invisible con etiqueta de texto
    const marcador = new google.maps.Marker({
        position: centro,
        map: mapaBarrios,
        icon: {
            url: 'data:image/svg+xml;charset=UTF-8,' + encodeURIComponent('<svg xmlns="http://www.w3.org/2000/svg"></svg>'),
            scaledSize: new google.maps.Size(1, 1)
        },
        label: {
            text: barrio.nombre,
            color: '#333333',
            fontSize: '8px',
            fontWeight: 'bold',
            className: 'barrio-label'
        }
    });
}

        function parsearWKT(wkt) {
            // Extraer coordenadas del formato POLYGON((lng lat, lng lat, ...))
            const match = wkt.match(/POLYGON\(\((.+)\)\)/);
            if (!match) return [];

            const coordenadas = match[1].split(',').map(punto => {
                const [lng, lat] = punto.trim().split(' ').map(Number);
                return new google.maps.LatLng(lat, lng);
            });

            return coordenadas;
        }

        // Inicializar cuando se carga la página
        document.addEventListener('livewire:init', () => {
            inicializarMapaBarrios();
        });

        // Reinicializar si se navega a la página
        document.addEventListener('livewire:navigated', () => {
            setTimeout(() => {
                inicializarMapaBarrios();
            }, 100);
        });
    </script>
    @endpush
</div>