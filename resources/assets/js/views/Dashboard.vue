<template>
  <div class="container-fluid">
    <div class="col-md-12">

      <div class="row card">
        <div class="card-body">
          <div class="card-title card-heading" :style="{ marginBottom: isAlertsVisible ? '0.75rem' : 0 }">
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
              :class="[ alert.category == 'problem' ? 'alert-danger' : 'alert-warning' ]"
              @click="showOnMap(alert.type)">
                <span><strong>{{ alert.title }}</strong> {{ alert.text }}</span>
                <i class="float-right">Pulsa para ver en el mapa</i>
            </div>
          </div>
        </div>
      </div>

      <div v-if="isMapVisible" class="row card">
        <div class="card-body">
          <div class="card-title card-heading">
            <h4>Mapa</h4>
            <button class="btn btn-default" @click="isMapVisible = !isMapVisible">
              <i class="fas fa-times"></i>
            </button>
          </div>

          <GmapMap
            :zoom="map.zoom"
            :center="map.center"
            :options="map.options"
            style="width: 100%; height: 600px">
            <GmapMarker
              v-for="point in map.points"
              :key="point.id"
              :position="{ lat: point.latitude, lng: point.longitude }"
              :clickable="true"
              :draggable="false"
              @click="pointProperties(point)"/>
          </GmapMap>

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

      <modal-details
        v-if="detailedPoint && isModalVisible"
        :point="detailedPoint" @close="isModalVisible = false" />

    </div>
  </div>
</template>

<script>
import axios from 'axios';
import ModalDetails from '../components/ModalDetails';
import RevisionsPie from '../components/charts/RevisionsPie';
import MonthlyRevisionsLine from '../components/charts/MonthlyRevisionsLine';

export default {
  components: {
    'modal-details': ModalDetails,
    'revisions-pie': RevisionsPie,
    'monthly-revisions-line': MonthlyRevisionsLine,
  },
  data: () => ({
    isMapVisible: false,
    isModalVisible: true,
    isAlertsVisible: true,
    chartStyles: {
      height: '400px',
      position: 'relative',
    },
    alerts: [],
    map: {
      zoom: 16,
      center: { lat: 37.978056, lng: -0.678444 },
        options: {
          fullscreenControl: false,
          streetViewControl: false,
      },
      points: [],
    },
    detailedPoint: null,
  }),
  methods: {
    showOnMap(type) {
      this.isMapVisible = true;
      this.isAlertsVisible = false;

      axios.get('/points-by-alert-type', {
        params: { type },
      })
      .then(({ data }) => {
        this.map.points = data;
      });
    },
    pointProperties(point) {
      axios.get(`/points/${point.id}`)
      .then(({ data }) => {
        this.isModalVisible = true;
        this.detailedPoint = data;
      });
    },
  },
  beforeMount() {
    axios.get('/alerts')
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

.card-heading {
  display: flex;
  flex-direction: row;
  align-items: baseline;
  justify-content: space-between;
}

</style>
