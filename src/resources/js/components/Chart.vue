<script>
import { Bar } from 'vue-chartjs'

export default {
  props: [],

  extends: Bar,

  mounted() {
    // Overwriting base render method with actual data.
    this.renderChart(
      {
        labels: [
          'D00',
          'D00',
          'D00',
          'D00',
          'D00',
          'D00',
          'D00',
          'D00',
          'D00',
          'D00',
          'D00',
          'D00',
          'D00',
          'D00',
          'D00',
          'D00',
          'D00',
          'D00',
          'D00',
          'D00',
          'D00',
          'D00',
          'D00',
          'D00',
        ],
        datasets: [
          {
            label: 'GitHub Commits',
            backgroundColor: '#8799FF',
            data: [
              40,
              20,
              12,
              39,
              10,
              40,
              39,
              80,
              40,
              20,
              12,
              11,
              40,
              20,
              12,
              39,
              10,
              40,
              39,
              80,
              40,
              20,
              12,
              11,
            ],
          },
        ],
      },
      {
        barThickness: 5,
        maintainAspectRatio: false,
        legend: {
          display: false,
        },
        elements: { point: { radius: 0 } },
        scales: {
          xAxes: [
            {
              display: false,
              scaleLabel: {
                display: false,
              },
            },
          ],
          yAxes: [
            {
              display: false,
              scaleLabel: {
                display: false,
              },
            },
          ],
        },
      },
    )
  },
}
</script>
