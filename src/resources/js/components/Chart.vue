<script>
import { Bar, mixins } from 'vue-chartjs'
const { reactiveProp } = mixins

export default {
  props: ['labels', 'data', 'backgrounds'],

  mixins: [reactiveProp],

  extends: Bar,

  mounted() {
    var me = this

    this.renderChart(this.chartData, {
      onClick: function(event) {
        me.$emit('bar-clicked', this.getElementsAtEvent(event))
      },

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
    })
  },
}
</script>
