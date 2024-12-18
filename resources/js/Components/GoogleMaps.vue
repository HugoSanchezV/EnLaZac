<script>
/* eslint-disable no-undef */
import { computed, ref, onMounted, onUnmounted, watch } from 'vue'
import { Loader } from '@googlemaps/js-api-loader'


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
    },
    mapKey: {
      type: String,
      required: true,
    }
  },
  setup(props, { emit }) {
    const currPos = computed(() => ({
      lat: props.lat,
      lng: props.lng
    }))

    const otherPos = ref(null)
    const marker = ref(null) // Referencia para el marcador

    const loader = new Loader({ apiKey : props.mapKey })
    const mapDiv = ref(null)
    let map = ref(null)
    let clickListener = null

    // Función para centrar el mapa en la posición actual
    const centerMap = () => {
      if (map.value) {
        map.value.setCenter(currPos.value) // Centra el mapa en la posición actual
        if (marker.value) {
          marker.value.setPosition(currPos.value) // Mueve el marcador a la posición actual
        }
      }
    }

    onMounted(async () => {
      await loader.load()
      map.value = new google.maps.Map(mapDiv.value, {
        center: currPos.value,
        zoom: 9
      })

      // Crear el marcador en la posición actual
      marker.value = new google.maps.Marker({
        position: currPos.value,
        map: map.value,
        title: "Tu ubicación actual",
        draggable: true
      })

      // Manejar clics en el mapa si se habilita la opción 'clic'
      if (props.clic) {
        clickListener = map.value.addListener('click', ({ latLng }) => {
          otherPos.value = {
            lat: latLng.lat(),
            lng: latLng.lng()
          }

          // Si ya existe un marcador, mueve su posición
          marker.value.setPosition(otherPos.value)

          // Emitir la nueva posición clicada
          emit('otherPos_clicked', otherPos.value)
        })
      }
    })
   
    // Limpieza del evento al desmontar
    onUnmounted(() => {
      if (clickListener) clickListener.remove()
    })

    return { mapDiv, otherPos, currPos }
  }
}
</script>

<template>

  <div ref="mapDiv" style="width: 100%; height: 40vh" />
</template>
