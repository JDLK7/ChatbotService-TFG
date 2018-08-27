<template>
  <!-- template for the modal component -->
  <transition name="modal">
    <div class="modal-mask">
      <div class="modal-wrapper">
        <div class="modal-container">

          <div class="modal-header">
            <slot name="header">
              <h3>Detalles del punto</h3>
            </slot>
          </div>

          <div class="modal-body">
            <form name="body">
              <div class="form-group row">
                <label for="coordinates" class="col-sm-4 col-form-label">Coordenadas:</label>
                <div class="col-sm-8">
                  <input
                    :value="`${point.latitude}, ${point.longitude}`"
                    type="text" readonly class="form-control-plaintext" id="coordinates">
                </div>
              </div>
              <div class="form-group row">
                <label for="coordinates" class="col-sm-4 col-form-label">Fecha creación:</label>
                <div class="col-sm-8">
                  <input
                    :value="point.created_at"
                    type="text" readonly class="form-control-plaintext" id="coordinates">
                </div>
              </div>
              <div v-if="point.hasOwnProperty('rating')" class="form-group row">
                <label for="coordinates" class="col-sm-4 col-form-label">Valoración de accesibilidad:</label>
                <div class="col-sm-8">
                  <el-rate
                    v-model="point.rating"
                    disabled
                    show-score
                    allow-half
                    text-color="#ff9900"
                    score-template="{value} puntos" />
                </div>
              </div>
            </form>
          </div>
          
          <template v-if="point.hasOwnProperty('properties')">
            <div class="modal-header">
              <slot name="header">
                <h3>Respuesta de los usuarios al estado del punto</h3>
              </slot>
            </div>

            <div class="modal-body">
              <div class="row">
                <!-- <h4>Respuesta de los usuarios al estado del punto</h4> -->
                <div class="col-sm-4 text-center">
                  <pie-chart :chart-data="curbsPieData" :styles="chartStyles" />
                  <h5>Tiene vados</h5>
                </div>
                <div class="col-sm-4 text-center">
                  <pie-chart :chart-data="semaphorePieData" :styles="chartStyles" />
                  <h5>Tiene semáforo</h5>
                </div>
                <div class="col-sm-4 text-center">
                  <pie-chart :chart-data="visibilityPieData" :styles="chartStyles" />
                  <h5>Visibilidad</h5>
                </div>
              </div>
            </div>
          </template>

          <div class="modal-footer">
            <slot name="footer">
              <button class="btn btn-primary" @click="$emit('close')">
                OK
              </button>
            </slot>
          </div>
        </div>
      </div>
    </div>
  </transition>
</template>

<script>
import PieChart from './charts/PieChart';

export default {
  components: {
    'pie-chart': PieChart,
  },
  props: {
    point: {
      type: Object,
      required: true,
    },
  },
  data: () => ({
    chartStyles: {
      height: '150px',
      position: 'relative',
    },
  }),
  computed: {
    hasCurbRamps() {
     return this.point.properties.hasCurbRamps;
    },
    hasSemaphore() {
     return this.point.properties.hasSemaphore;
    },
    visibility() {
     return this.point.properties.visibility;
    },
    curbsPieData() {
      const total = this.hasCurbRamps.true + this.hasCurbRamps.false;
      const perc1 = (this.hasCurbRamps.true * 100 / total).toFixed(2);
      const perc2 = 100 - perc1;

      return {
        labels: ['Si', 'No'],
        datasets: [{
          data: [perc1, perc2],
          backgroundColor: [
            '#4CAF50',
            '#f44336',
          ],
        }],
      };
    },
    semaphorePieData() {
      const total = this.hasSemaphore.true + this.hasSemaphore.false;
      const perc1 = (this.hasSemaphore.true * 100 / total).toFixed(2);
      const perc2 = 100 - perc1;

      return {
        labels: ['Si', 'No'],
        datasets: [{
          data: [perc1, perc2],
          backgroundColor: [
            '#4CAF50',
            '#f44336',
          ],
        }],
      };
    },
    visibilityPieData() {
      let total = 0;
      let data = [];

      // Las claves del objeto se corresponden con los grados de visibilidad.
      const grades = Object.keys(this.visibility);

      grades.forEach((key) => {
        total += this.visibility[key];
      });
      grades.forEach((key) => {
        data.push((this.visibility[key] * 100 / total).toFixed(2));
      });

      return {
        labels: grades,
        datasets: [{
          data,
          backgroundColor: [
            '#f44336',
            '#FFEB3B',
            '#4CAF50',
          ],
        }],
      };
    },
  },
};
</script>

<style lang="scss" scoped>

label {
  font-weight: bold;
}

.modal-mask {
  position: fixed;
  z-index: 9998;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, .5);
  display: table;
  transition: opacity .3s ease;
}

.modal-wrapper {
  display: table-cell;
  vertical-align: middle;
}

.modal-container {
  width: 700px;
  margin: 0px auto;
  padding: 1.25rem;
  background-color: #fff;
  border-radius: 2px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, .33);
  transition: all .3s ease;
  font-family: Helvetica, Arial, sans-serif;
}

.modal-body {
  margin: 20px 0;
}

.modal-footer {
  button {
    margin: 0 auto;
  }
}

/*
 * The following styles are auto-applied to elements with
 * transition="modal" when their visibility is toggled
 * by Vue.js.
 *
 * You can easily play with the modal transition by editing
 * these styles.
 */

.modal-enter {
  opacity: 0;
}

.modal-leave-active {
  opacity: 0;
}

.modal-enter .modal-container,
.modal-leave-active .modal-container {
  -webkit-transform: scale(1.1);
  transform: scale(1.1);
}
</style>
