<script setup>
import { onMounted, nextTick, onBeforeUnmount, ref } from "vue";
import { Chart } from "chart.js/auto";
import { defineProps } from "vue";

const props = defineProps({
  target: Array,
  upload_rate: Array,
  download_rate: Array,
  upload_byte: Array,
  download_byte: Array,
  type: String,
});
const avgByteUpload = () => {
  // Verificar que sea un arreglo válido
  if (!Array.isArray(props.upload_byte) || props.upload_byte.length === 0) {
    return 0; // Retornar 0 si el arreglo está vacío o no es válido
  }

  // Convertir a números y sumar
  const sum = props.upload_byte.reduce((acc, value) => {
    const numericValue = parseFloat(value); // Convertir a número
    return acc + (isNaN(numericValue) ? 0 : numericValue); // Sumar solo si es un número válido
  }, 0);

  return sum / props.upload_byte.length;
};
const avgByteDownload = () => {
  // Verificar que sea un arreglo válido
  if (!Array.isArray(props.download_byte) || props.download_byte.length === 0) {
    return 0; // Retornar 0 si el arreglo está vacío o no es válido
  }

  // Convertir a números y sumar
  const sum = props.download_byte.reduce((acc, value) => {
    const numericValue = parseFloat(value); // Convertir a número
    return acc + (isNaN(numericValue) ? 0 : numericValue); // Sumar solo si es un número válido
  }, 0);

  return sum / props.download_byte.length;
};
const avgRateDownload = () => {
  // Verificar que sea un arreglo válido
  if (!Array.isArray(props.download_rate) || props.download_rate.length === 0) {
    return 0; // Retornar 0 si el arreglo está vacío o no es válido
  }

  // Convertir a números y sumar
  const sum = props.download_rate.reduce((acc, value) => {
    const numericValue = parseFloat(value); // Convertir a número
    return acc + (isNaN(numericValue) ? 0 : numericValue); // Sumar solo si es un número válido
  }, 0);

  return sum / props.download_rate.length;
};
const avgRateUpload = () => {
  // Verificar que sea un arreglo válido
  if (!Array.isArray(props.upload_rate) || props.upload_rate.length === 0) {
    return 0; // Retornar 0 si el arreglo está vacío o no es válido
  }

  // Convertir a números y sumar
  const sum = props.upload_rate.reduce((acc, value) => {
    const numericValue = parseFloat(value); // Convertir a número
    return acc + (isNaN(numericValue) ? 0 : numericValue); // Sumar solo si es un número válido
  }, 0);

  return sum / props.download_rate.length;
};
// const avgByteUpload = () => {
//   // Validar que el arreglo sea válido y no esté vacío
//   if (!Array.isArray(props.upload_byte) || props.upload_byte.length === 0) {
//     return 0; 
//   }
//   const sum = props.upload_byte.reduce((acc, element) => acc + element, 0);
//   console.log(props.upload_byte);
//   console.log(" SUMA : "+sum);
//   return sum / props.upload_byte.length;
// }
// const avgByteDownload = () => {
//   let sum = 0;
//   props.download_byte.forEach(element => {
//     sum = sum + element
//   });
//   return sum / props.download_byte.length;
// }

// const avgRateUpload = () => {
//   let sum = 0;
//   props.upload_rate.forEach(element => {
//     sum = element + sum
//   });
//   return sum / props.upload_rate.length;
// }

// const avgRateDownload = () => {
//   let sum = 0;
//   props.download_rate.forEach(element => {
//     sum = element + sum
//   });
//   return sum / props.download_rate.length;
// }

const byteMax = () => {
   // console.log(props.upload_byte);
    if(props.upload_byte.length == 0){
      document.getElementById('tasa-byte-upload'+props.type).textContent = "0";
      document.getElementById('avg-byte-upload'+props.type).textContent = "0";
      document.getElementById('current-byte-upload'+props.type).textContent = "0";
    }else{
      const MaxUploadByte = Math.max(...props.upload_byte); 
      const AvgUploadByte = avgByteUpload();
      const CurrentUploadByte = props.upload_byte[props.upload_byte.length-1];
      document.getElementById('avg-byte-upload'+props.type).textContent = (AvgUploadByte).toFixed(5);
      document.getElementById('tasa-byte-upload'+props.type).textContent = (MaxUploadByte).toFixed(5);
      document.getElementById('current-byte-upload'+props.type).textContent = (CurrentUploadByte).toFixed(5);

    }
   
    if(props.download_byte.length == 0){
      document.getElementById('tasa-byte-download'+props.type).textContent = "0";
      document.getElementById('avg-byte-download'+props.type).textContent = "0";
      document.getElementById('current-byte-download'+props.type).textContent = "0";

    }else{
      const MaxDownloadByte = Math.max(...props.download_byte);
      const AvgDownloadByte = avgByteDownload();
      const CurrentDownloadByte = props.download_byte[props.download_byte.length-1];
      document.getElementById('avg-byte-download'+props.type).textContent = (AvgDownloadByte).toFixed(5);
      document.getElementById('tasa-byte-download'+props.type).textContent = (MaxDownloadByte).toFixed(5);
      document.getElementById('current-byte-download'+props.type).textContent = (CurrentDownloadByte).toFixed(5);
    }
}

const rateMax = () => {
    if(props.upload_rate.length == 0){
      document.getElementById('tasa-rate-upload'+props.type).textContent = "0";
      document.getElementById('avg-rate-upload'+props.type).textContent = "0";
      document.getElementById('current-rate-upload'+props.type).textContent = "0";
    }else{

      const MaxUploadRate = Math.max(...props.upload_rate); 
      const AvgUploadRate = avgRateUpload();
      const CurrentUploadRate = props.upload_rate[props.upload_rate.length-1];
      document.getElementById('avg-rate-upload'+props.type).textContent = (AvgUploadRate).toFixed(5);
      document.getElementById('tasa-rate-upload'+props.type).textContent = (MaxUploadRate).toFixed(5);
      document.getElementById('current-rate-upload'+props.type).textContent = (CurrentUploadRate).toFixed(5);
    }

    if(props.download_rate.length == 0){
      document.getElementById('tasa-rate-download'+props.type).textContent = "0";
      document.getElementById('avg-rate-download'+props.type).textContent = "0";
      document.getElementById('current-rate-download'+props.type).textContent = "0";
    }else{
      const MaxDownloadRate = Math.max(...props.download_rate);
      const AvgDownloadRate = avgRateDownload();
      const CurrentDownloadRate = props.download_rate[props.download_rate.length-1];
      document.getElementById('avg-rate-download'+props.type).textContent = (AvgDownloadRate).toFixed(5);
      document.getElementById('tasa-rate-download'+props.type).textContent =( MaxDownloadRate).toFixed(5);
      document.getElementById('current-rate-download'+props.type).textContent = (CurrentDownloadRate).toFixed(5);
    }
}

const setFomartTarget = () =>{
  let puntero;
  for(let i = 0; i < props.target.length; i++){
      
      if(i != 0)
      { 
        if(props.target[puntero] == props.target[i])
        {
          props.target[i-1] = "";
        }
    }
    puntero = i;
  }
}

const chartRateInstance = ref(null);
const chartByteInstance = ref(null);

onMounted(async () => {
  await nextTick();

  if(props.type == 'Year'){

    console.log(props.target);
  }
  const ctxRate = document.getElementById("rateGraphic" + props.type);
  const ctxByte = document.getElementById("byteGraphic" + props.type);

 
      setFomartTarget();
    

  rateMax();
  byteMax();

  // Destruir cualquier instancia previa para evitar duplicados
  if (chartRateInstance.value) chartRateInstance.value.destroy();
  if (chartByteInstance.value) chartByteInstance.value.destroy();

  // Crear gráfico para Rate
  chartRateInstance.value = new Chart(ctxRate, {
    type: "line",
    data: {
      labels: props.target,
      datasets: [
        {
          label: "Rate de subida (MB)",
          data: props.upload_rate,
          fill: false,
          borderColor: "rgb(75, 192, 192)",
          tension: 0.2,
        },
        {
          label: "Rate de descarga (MB)",
          data: props.download_rate,
          fill: false,
          borderColor: "rgb(254, 0, 0)",
          tension: 0.2,
        },
      ],
    },
    options: {
      responsive: true,
      scales: {
        y: {
          beginAtZero: true,
        },
      },
    },
  });

  // Crear gráfico para Byte
  chartByteInstance.value = new Chart(ctxByte, {
    type: "line",
    data: {
      labels: props.target,
      datasets: [
        {
          label: "Byte de subida (MB)",
          data: props.upload_byte,
          fill: false,
          borderColor: "rgb(75, 192, 192)",
          tension: 0.2,
        },
        {
          label: "Byte de descarga (MB)",
          data: props.download_byte,
          fill: false,
          borderColor: "rgb(254, 0, 0)",
          tension: 0.2,
        },
      ],
    },
    options: {
      responsive: true,
      scales: {
        y: {
          beginAtZero: true,
        },
      },
    },
  });
});

onBeforeUnmount(() => {
  // Destruir gráficos cuando se desmonte el componente
  if (chartRateInstance.value) chartRateInstance.value.destroy();
  if (chartByteInstance.value) chartByteInstance.value.destroy();
});
</script>

<template>
  <div 
  class=" flex justify-between graficas gap-2 rounded-lg">
    <div class="graficas gap-2">
      <div class="rate rounded-lg overflow-hidden bg-white mt-3">
        <canvas :id="'rateGraphic' + type"></canvas>
        <div class="p-4">
            <div class="border-b-2">
              <h3 class="text-lg font-semibold text-gray-800">Tasa de Transferencia Actual</h3>
            </div>
            <div class="justify-between">
              <div class="border-b-2">
                <p class="text-green-500 flex items-center">
                  <p :id="'tasa-rate-upload'+type" class="font-bold mr-1"></p>
                  <span  class="font-bold mr-1">MB</span>
                  <p class="text-gray-500">Máxima subida</p>
                </p>
                <p class="text-red-500 flex items-center">
                    <p :id="'tasa-rate-download'+type" class="font-bold mr-1"></p>
                    <span  class="font-bold mr-1">MB</span>
                    <p class="text-gray-500">Máxima descarga</p>
                </p>
              </div>

              <div class="border-b-2">
                <p class="text-green-500 flex items-center">
                  <p :id="'avg-rate-upload'+type" class="font-bold mr-1"></p>
                  <span  class="font-bold mr-1">KB</span>
                  <p class="text-gray-500">Promedio subida</p>
                </p>
                <p class="text-red-500 flex items-center">
                    <p :id="'avg-rate-download'+type" class="font-bold mr-1"></p>
                    <span  class="font-bold mr-1">KB</span>
                    <p class="text-gray-500">Promedio descarga</p>
                </p>
              </div>
              <div>
                <p class="text-green-500 flex items-center">
                  <p :id="'current-rate-upload'+type" class="font-bold mr-1"></p>
                  <span  class="font-bold mr-1">MB</span>
                  <p class="text-gray-500">Actual subida</p>
                </p>
                <p class="text-red-500 flex items-center">
                    <p :id="'current-rate-download'+type" class="font-bold mr-1"></p>
                    <span  class="font-bold mr-1">MB</span>
                    <p class="text-gray-500">Actual descarga</p>
                </p>
              </div>
            </div>
            
        </div>
      </div>
      <div class="byte rounded-lg overflow-hidden bg-white mt-3">
        <canvas :id="'byteGraphic' + type"></canvas>
        <div class="p-4">
            <div class="border-b-2">
              <h3 class="text-lg font-semibold text-gray-800">Tráfico total</h3>
            </div>
            <div class="justify-center">
              <div class="border-b-2">
                <p class="text-green-500 flex items-center">
                  <p :id="'tasa-byte-upload'+type" class=" font-bold mr-1"></p>
                  <span  class="font-bold mr-1">MB</span>
                  <p class="text-gray-500">Total subida</p>
                </p>
                <p class="text-red-500 flex items-center">
                    <p :id="'tasa-byte-download'+type" class="font-bold mr-1"></p>
                    <span  class="font-bold mr-1">MB</span>
                    <p class="text-gray-500">Total descarga</p>
                </p>
              </div>

              <div class="border-b-2">
                <p class="text-green-500 flex items-center">
                  <p :id="'avg-byte-upload'+type" class="font-bold mr-1"></p>
                  <span  class="font-bold mr-1">KB</span>
                  <p class="text-gray-500">Promedio subida</p>
                </p>
                <p class="text-red-500 flex items-center">
                    <p :id="'avg-byte-download'+type" class="font-bold mr-1"></p>
                    <span  class="font-bold mr-1">KB</span>
                    <p class="text-gray-500">Promedio descarga</p>
                </p>
              </div>

              <div>
                <p class="text-green-500 flex items-center">
                  <p :id="'current-byte-upload'+type" class="font-bold mr-1"></p>
                  <span  class="font-bold mr-1">MB</span>
                  <p class="text-gray-500">Actual subida</p>
                </p>
                <p class="text-red-500 flex items-center">
                    <p :id="'current-byte-download'+type" class="font-bold mr-1"></p>
                    <span  class="font-bold mr-1">MB</span>
                    <p class="text-gray-500">Actual descarga</p>
                </p>
              </div>
            </div>
        </div>
      </div>
    </div>
  </div>
</template>
