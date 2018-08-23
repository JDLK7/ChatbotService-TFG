<template>
  <div class="container-fluid">
    <div class="col-md-12">

      <div class="row card">
        <div class="card-body">
          <div class="card-title alerts-heading" :style="{ marginBottom: isAlertsVisible ? '0.75rem' : 0 }">
            <h4>Problemas y avisos encontrados</h4>
            <button class="btn btn-default" @click="isAlertsVisible = !isAlertsVisible">
              <i class="fas" :class="[ isAlertsVisible ? 'fa-chevron-up' : 'fa-chevron-down' ]"></i>
            </button>
          </div>
          <div v-show="alerts && isAlertsVisible" class="alerts-container">
            <div
              v-for="(alert, i) in alerts"
              :key="i"
              class="alert"
              role="alert"
              :class="[ alert.category == 'problem' ? 'alert-danger' : 'alert-warning' ]">
                <span><strong>{{ alert.title }}</strong> {{ alert.text }}</span>
                <i class="float-right">Pulsa para ver en el mapa</i>
            </div>
          </div>
        </div>
      </div>

      <div class="row revisions-container">
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

    </div>
  </div>
</template>

<script>
import axios from 'axios';
import RevisionsPie from '../components/charts/RevisionsPie';
import MonthlyRevisionsLine from '../components/charts/MonthlyRevisionsLine';

export default {
  components: {
    'revisions-pie': RevisionsPie,
    'monthly-revisions-line': MonthlyRevisionsLine,
  },
  data: () => ({
    isAlertsVisible: false,
    chartStyles: {
      height: '400px',
      position: 'relative',
    },
    alerts: [],
  }),
  beforeMount() {
    axios.get('/api/alerts')
    .then(({ data }) => {
      this.alerts = this.alerts.concat(data);
    });
  },
};
</script>

<style lang="scss" scoped>

.row:not(:last-child) {
  margin-bottom: 1.25rem;
}

.revisions-container {
  display: grid;
  grid-template-columns: 30% auto;

  .card:not(:last-child) {
    margin-right: 1.25rem;
  }
}

.alerts-container {
  .alert {
    width: 100%;
    cursor: pointer;
  }
}

.alerts-heading {
  display: flex;
  flex-direction: row;
  align-items: baseline;
  justify-content: space-between;
}

</style>
