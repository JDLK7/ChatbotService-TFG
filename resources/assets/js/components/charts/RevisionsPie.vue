<script>
import { Pie, mixins } from 'vue-chartjs';
import axios from 'axios';

const { reactiveData } = mixins;

export default {
  extends: Pie,
  mixins: [ reactiveData ],
  beforeMount() {
    axios.get('/api/overall-revisions')
    .then(({ data }) => {
      const revised = (data.revised * 100 / data.total).toFixed(2);
      const notRevised = 100 - revised;

      this.chartData = {
        labels: ['Revisados', 'Sin revisar'],
        datasets: [{
          data: [
            revised,
            notRevised,
          ],
          backgroundColor: [
            '#1fc8db',
            '#fce473',
            '#42afe3',
            '#ed6c63',
            '#97cd76',
          ],
        }],
      };
      this.options = { responsive: true, maintainAspectRatio: false };
    });
  },
  mounted () {
    this.renderChart(this.chartData, { responsive: true, maintainAspectRatio: false });
  },
};
</script>