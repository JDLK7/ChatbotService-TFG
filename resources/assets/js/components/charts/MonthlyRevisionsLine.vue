<script>
import { Line, mixins } from 'vue-chartjs';
import axios from 'axios';

const { reactiveData } = mixins;

export default {
  extends: Line,
  mixins: [ reactiveData ],
  data: () => ({
    series: ['Total revisados'],
    backgroundColor: [
      'rgba(31, 200, 219, 1)',
      'rgba(151, 205, 118, 1)',
    ],
  }),
  beforeMount() {
    axios.get('/api/monthly-revisions')
    .then(({ data }) => {
      let lineData = {
        labels: data.months,
      };

      lineData.datasets = this.series.map((e, i) => {
        return {
          data: data.totalPerMonth,
          label: this.series[i],
          borderColor: this.backgroundColor[i].replace(/1\)$/, '.5)'),
          pointBackgroundColor: this.backgroundColor[i],
          backgroundColor: this.backgroundColor[i].replace(/1\)$/, '.5)')
        }
      });

      this.chartData = lineData;
      this.options = { responsive: true, maintainAspectRatio: false };
    });
  },
  mounted () {
    this.renderChart(this.chartData, { responsive: true, maintainAspectRatio: false });
  },
};
</script>