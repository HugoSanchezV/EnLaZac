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


const groupedData = props.target.reduce((acc, currentTarget, index) => {
  // Verificar si el target actual ya existe en acc
  const existing = acc.find(item => item.target === currentTarget);

  if (existing) {
    // Si existe, sumar los valores de upload y download correspondientes
    existing.upload_rate += props.upload_rate[index];
    existing.download_rate += props.download_rate[index];
  } else {
    // Si no existe, crear una nueva entrada en acc
    acc.push({
      target: currentTarget,
      upload_rate: props.upload_rate[index],
      download_rate: props.download_rate[index]
    });
  }

  return acc;
}, []);

const chartRateInstance = ref(null);
const chartByteInstance = ref(null);

onMounted(async () => {
  await nextTick();
  const ctxRate = document.getElementById("rateGraphic" + props.type);
  const ctxByte = document.getElementById("byteGraphic" + props.type);

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
  <div>
    <canvas :id="'rateGraphic' + type"></canvas>
  </div>
  <div>
    <canvas :id="'byteGraphic' + type"></canvas>
  </div>
</template>
