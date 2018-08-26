<script>
import { Pie, mixins } from 'vue-chartjs';
import axios from 'axios';

const { reactiveProp } = mixins;

export default {
  extends: Pie,
  mixins: [ reactiveProp ],
  mounted () {
    this.renderChart(this.chartData, { responsive: true, maintainAspectRatio: false });
  },
};
</script>