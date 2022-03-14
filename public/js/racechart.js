const labels = [
  'Lap 1',
  'Lap 2',
  'Lap 3'
];
const data = {
  labels: labels,
  datasets: [
    {
      label: 'Position',
      backgroundColor: 'rgb(255, 99, 132)',
      borderColor: 'rgb(255, 99, 132)',
      data: [3, 4, 1],
      yAxisID: 'y',
      reverse: true,
    },
    {
      label: 'Fuel',
      backgroundColor: 'rgb(255, 99, 255)',
      borderColor: 'rgb(255, 99, 255)',
      data: [92.61, 90.36, 88.18],
      yAxisID: 'y1',
    },
    {
      label: 'Time Lap',
      backgroundColor: 'rgb(0, 99, 255)',
      borderColor: 'rgb(0, 99, 255)',
      data: [137.322, 121, 113],
      yAxisID: 'y2',
    }
  ]
};

const config = {
  type: 'line',
  data: data,
  options: {
    responsive: true,
    /*interaction: {
      mode: 'index',
      intersect: false,
    },*/
    stacked: false,
    plugins: {
      title: {
        display: true,
        text: 'Race graph'
      }
    },
    scales: {
      y: {
        type: 'linear',
        display: true,
        position: 'left',
        reverse: true,
        min: 1,
        text: 'Pos.'
      },
      y1: {
        type: 'linear',
        display: true,
        position: 'left',
        max: 100,
        min: 0,
        text: 'Fuel'
      },
      y2: {
        type: 'linear',
        display: true,
        position: 'left',
        text: 'Time'
      },
    }
  }
};

const myChart = new Chart(
    document.getElementById('chart'),
    config
 );