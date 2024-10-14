<script setup>

import { onMounted, nextTick  } from "vue";
import { usePage } from "@inertiajs/vue3";

const { props } = usePage();
onMounted( async () =>{
    await nextTick();
    const ctxRate = document.getElementById('myChartRate'+props.index);
    const ctxByte = document.getElementById('myChartByte'+props.index);
    console.log("index: "+props.index);
    console.log("target "+props.target)

    new Chart(ctxRate, {
    type: 'line',
    data: {
    labels: props.target,
    datasets: [{
        label: 'Rate de subida (MB)',
        data: props.upload_rate,
        fill: false,
        borderColor: 'rgb(75, 192, 192)',
        tension: 0.1
    },{
        label: 'Rate de descarga (MB)',
        data: props.download_rate,
        fill: false,
        borderColor: 'rgb(254, 0, 0)',
        tension: 0.1}]
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
        tension: 0.1
    },{
        label: 'Byte de descarga (MB)',
        data: props.download_byte,
        fill: false,
        borderColor: 'rgb(254, 0, 0)',
        tension: 0.1}]
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
    <div>
         
        <div 
        class="bg-white flex justify-between" 
        style="width: 500px;">
            
            <canvas :id="'myChartRate'+ index"></canvas>
            <canvas :id="'myChartByte'+ index"></canvas>
        </div>
    </div>{{ index }}
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
