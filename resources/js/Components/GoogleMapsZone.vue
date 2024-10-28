<script>
import { ref, onMounted, onUnmounted, computed } from 'vue'
import { Loader } from '@googlemaps/js-api-loader'

const GOOGLE_MAPS_API_KEY = 'AIzaSyBQYG1b_KbXKX1ONj4eu2eWHD7k-qkjNkE'

export default {
  props: {
    lat: {
      type: Number,
      required: true
    },
    lng: {
      type: Number,
      required: true
    },
    clic: {
      type: Boolean,
      required: true,
    }
  },
  setup(props, { emit }) {
    const currPos = computed(() => ({
      lat: props.lat,
      lng: props.lng
    }))

    const mapDiv = ref(null)
    let map = ref(null)
    let drawingManager = null
    let currentOverlay = null  // Almacena la figura dibujada (polígono o rectángulo)

    const loader = new Loader({
      apiKey: GOOGLE_MAPS_API_KEY,
      libraries: ['drawing']  // Asegúrate de incluir la librería 'drawing'
    })

    onMounted(async () => {
      // Cargar Google Maps y la librería 'drawing'
      await loader.load()

      // Inicializar el mapa
      map.value = new google.maps.Map(mapDiv.value, {
        center: currPos.value,
        zoom: 9
      })

      // Inicializar el DrawingManager
      drawingManager = new google.maps.drawing.DrawingManager({
        drawingMode: null,
        drawingControl: true,
        drawingControlOptions: {
          position: google.maps.ControlPosition.TOP_CENTER,
          drawingModes: ['polygon', 'rectangle']
        },
        polygonOptions: {
          editable: false,
          draggable: false,
          fillColor: '#FF0000',
          fillOpacity: 0.35,
          strokeWeight: 2,
          clickable: true,
          zIndex: 1
        },
        rectangleOptions: {
          editable: false,
          draggable: false,
          fillColor: '#FF0000',
          fillOpacity: 0.35,
          strokeWeight: 2,
          clickable: true,
          zIndex: 1
        }
      })

      // Añadir el DrawingManager al mapa
      drawingManager.setMap(map.value)

      // Manejar cuando se completa el dibujo
      google.maps.event.addListener(drawingManager, 'overlaycomplete', function(event) {
        // Guardar la referencia de la figura dibujada
        if (currentOverlay) {
          currentOverlay.setMap(null)  // Elimina la figura anterior si se dibuja una nueva
        }
        currentOverlay = event.overlay

        let coordinates = []
        if (event.type === 'polygon') {
          event.overlay.getPath().forEach((point) => {
            coordinates.push({ lat: point.lat(), lng: point.lng() })
          })
        } else if (event.type === 'rectangle') {
          const bounds = event.overlay.getBounds()
          const ne = bounds.getNorthEast()
          const sw = bounds.getSouthWest()
          coordinates = [
            { lat: ne.lat(), lng: sw.lng() },
            { lat: sw.lat(), lng: ne.lng() },
          ]
        }
        emit('area_selected', coordinates)

        // Desactivar el modo dibujo después de completar el área
        drawingManager.setDrawingMode(null)
      })
    })

    // Función para eliminar la figura dibujada
    const deleteShape = () => {
      if (currentOverlay) {
        currentOverlay.setMap(null)  // Elimina la figura del mapa
        currentOverlay = null        // Resetear la referencia de la figura
      }
    }

    onUnmounted(() => {
      if (drawingManager) {
        google.maps.event.clearInstanceListeners(drawingManager)
      }
    })

    return { mapDiv, deleteShape }
  }
}
</script>

<template>
  <div>
    <div ref="mapDiv" style="width: 100%; height: 70vh" />
    <button @click="deleteShape" style="margin-top: 10px;">Borrar Figura</button>  <!-- Botón para eliminar la figura -->
  </div>
</template>
