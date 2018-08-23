<template>
  <div class="container-fluid chart-container">
    <!-- Porcentaje total de revisiones -->
    <div class="card">
        <div class="card-body">
          <h4 class="card-title">Porcentaje de revisiones</h4>
          <revisions-pie :styles="chartStyles" />
        </div>
    </div>

    <!-- Revisiones mensuales -->
    <div class="card">
        <div class="card-body">
          <h4 class="card-title">Revisiones mensuales</h4>
          <monthly-revisions-line :styles="chartStyles" />
        </div>
    </div>
  </div>
</template>

<script>
import RevisionsPie from '../components/charts/RevisionsPie';
import MonthlyRevisionsLine from '../components/charts/MonthlyRevisionsLine';

export default {
  components: {
    'revisions-pie': RevisionsPie,
    'monthly-revisions-line': MonthlyRevisionsLine,
  },
  data: () => ({
    chartStyles: {
      height: '400px',
      position: 'relative',
    },
  }),
};
</script>

<style lang="scss" scoped>
.chart-container {
  display: grid;
  grid-template-columns: 30% auto;

  .card:not(:last-child) {
    margin-right: 1.25rem;
  }
}
</style>
