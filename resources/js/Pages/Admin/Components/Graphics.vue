<script setup>

import { onMounted, nextTick  } from "vue";
import { usePage } from "@inertiajs/vue3";
import Chart from 'chart.js/auto';
import { defineProps } from "vue";

const props = defineProps({
    index: Number,
    target:  Array,
    upload_rate: Array,
    download_rate: Array,
    upload_byte: Array,
    download_byte: Array,
      
})
const rateMax = () => {
    const MaxUploadRate = Math.max(...props.upload_rate); 
    const maxIndexRateUpload = props.upload_rate.indexOf(MaxUploadRate);

    document.getElementById('tasa-rate-upload'+props.index).textContent = MaxUploadRate;
    document.getElementById('device-rate-upload'+props.index).textContent = props.target[maxIndexRateUpload];
   
    //alert(MaxUploadRate+" : "+maxIndex);

    const MaxDownloadRate = Math.max(...props.download_rate);
    const maxIndexRateDownload = props.download_rate.indexOf(MaxDownloadRate);

    document.getElementById('tasa-rate-download'+props.index).textContent = MaxDownloadRate;
    document.getElementById('device-rate-download'+props.index).textContent = props.target[maxIndexRateDownload];
}
const byteMax = () => {
    const MaxUploadByte = Math.max(...props.upload_byte); 
    const maxIndexByteUpload = props.upload_byte.indexOf(MaxUploadByte);

    document.getElementById('tasa-byte-upload'+props.index).textContent = MaxUploadByte;
    document.getElementById('device-byte-upload'+props.index).textContent = props.target[maxIndexByteUpload];
   
    //alert(MaxUploadRate+" : "+maxIndex);

    const MaxDownloadByte = Math.max(...props.download_byte);
    const maxIndexByteDownload = props.download_byte.indexOf(MaxDownloadByte);

    document.getElementById('tasa-byte-download'+props.index).textContent = MaxDownloadByte;
    document.getElementById('device-byte-download'+props.index).textContent = props.target[maxIndexByteDownload];
}
onMounted( async () =>{
    await nextTick();
    const ctxRate = document.getElementById('myChartRate'+props.index);
    const ctxByte = document.getElementById('myChartByte'+props.index);

    rateMax();
    byteMax();

    new Chart(ctxRate, {
    type: 'line',
    data: {
    labels: props.target,
    datasets: [{
        label: 'Rate de subida (MB)',
        data: props.upload_rate,
        fill: false,
        borderColor: 'rgb(75, 192, 192)',
        tension: 0.2
    },{
        label: 'Rate de descarga (MB)',
        data: props.download_rate,
        fill: false,
        borderColor: 'rgb(254, 0, 0)',
        tension: 0.2}]
    },
    options: {
        scales: {
        y: {
            beginAtZero: true
        }
        }
    }
    });

    new Chart(ctxByte, {
    type: 'line',
    data: {
    labels: props.target,
    datasets: [{
        label: 'Byte de subida (MB)',
        data: props.upload_byte,
        fill: false,
        borderColor: 'rgb(75, 192, 192)',
        tension: 0.2
    },{
        label: 'Byte de descarga (MB)',
        data: props.download_byte,
        fill: false,
        borderColor: 'rgb(254, 0, 0)',
        tension: 0.2}]
    },
    options: {
        scales: {
        y: {
            beginAtZero: true
        }
        }
    }
    });
});

</script>

<template>
        <div 
        class=" flex justify-between graficas gap-2 rounded-lg">

            <div class="rate rounded-lg shadow-lg overflow-hidden bg-white">
                <canvas  :id= "'myChartRate'+ index"></canvas>
                <div class="p-4">
                    <h3 class="text-lg font-semibold text-gray-800">Tasa de Transferencia Actual</h3>
                    <p class="text-green-500 flex items-center">
                        <p :id="'tasa-rate-upload'+index" class="text-xl font-bold mr-1"></p>
                        <span  class="text-xl font-bold mr-1">MB</span>
                        <p class="text-gray-500">Tasa máxima subida de: </p>
                        <p :id="'device-rate-upload'+index" class="text-gray-500"> </p>
                    </p>
                    <p class="text-red-500 flex items-center">
                        <p :id="'tasa-rate-download'+index" class="text-xl font-bold mr-1"></p>
                        <span  class="text-xl font-bold mr-1">MB</span>
                        <p class="text-gray-500">Tasa máxima de descargar de: </p>
                        <p :id="'device-rate-download'+index" class="text-gray-500"> </p>
                    </p>
                </div>
            </div>
            <div class="byte rounded-lg shadow-lg overflow-hidden bg-white">
                <canvas :id= "'myChartByte'+ index"></canvas>
                <div class="p-4">
                    <h3 class="text-lg font-semibold text-gray-800">Tráfico total en MB</h3>
                    <p class="text-green-500 flex items-center">
                        <p :id="'tasa-byte-upload'+index" class="text-xl font-bold mr-1"></p>
                        <span  class="text-xl font-bold mr-1">MB</span>
                        <p class="text-gray-500">Tráfico total subida de: </p>
                        <p :id="'device-byte-upload'+index" class="text-gray-500"> </p>
                    </p>
                    <p class="text-red-500 flex items-center">
                        <p :id="'tasa-byte-download'+index" class="text-xl font-bold mr-1"></p>
                        <span  class="text-xl font-bold mr-1">MB</span>
                        <p class="text-gray-500">Tráfico total de descargar de: </p>
                        <p :id="'device-byte-download'+index" class="text-gray-500"></p>
                    </p>
                </div>
            </div>
            
        </div>

    </template>
<script>
export default {
    props: {
        target: {
          type: Array,required: true,
        },
        upload_rate:{
          type: Array,
        },
        download_rate: {
          type: Array,required: true,
        },
        upload_byte: {
          type: Array,required: true,
        },
        download_byte: {
          type: Array,required: true,
        },
        index:{
            type: Number,required: true,
        }
      },
}
</script>
