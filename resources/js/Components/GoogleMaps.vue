<script>
/* eslint-disable no-undef */
import { computed, ref, onMounted, onUnmounted, watch } from 'vue'
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
    const otherPos = ref(null)
    const marker = ref(null) // Referencia para el marcador

    const loader = new Loader({ apiKey: GOOGLE_MAPS_API_KEY })
    const mapDiv = ref(null)
    let map = ref(null)
    let clickListener = null

    onMounted(async () => {
      await loader.load()
      map.value = new google.maps.Map(mapDiv.value, {
        center: currPos.value,
        zoom: 9
      })
      marker.value = new google.maps.Marker({
          position: currPos.value,
          map: map.value,
          draggable: true
        })

      marker.value = new google.maps.Marker({
        position: currPos.value,
        map: map.value,
        title: "Tu ubicación actual",
        draggable: true
      });
      if(props.clic)
      {
        // Manejar clics en el mapa para colocar o mover el marcador
        clickListener = map.value.addListener('click', ({ latLng }) => {
        otherPos.value = {
          lat: latLng.lat(),
          lng: latLng.lng()
        }


        // Si ya existe un marcador, mueve su posición
        if (marker.value) {
          marker.value.setPosition(otherPos.value)
        } else {
          // Si no existe un marcador, créalo
          marker.value = new google.maps.Marker({
            position: otherPos.value,
            map: map.value,
            draggable: true // Hacer que el marcador sea arrastrable
          })
        }

        // Emitir la posición clicada
        emit('otherPos_clicked', otherPos.value)
      })
        
      }
      
      
    })

    onUnmounted(async () => {
      if (clickListener) clickListener.remove()
    })


    return { mapDiv, otherPos, currPos }
  }
}
</script>

<template>
  <div ref="mapDiv" style="width: 100%; height: 40vh" />
</template>
