<template>
  <el-row>
    <el-col :span="12" :offset="6">
      <el-upload
        class="upload"
        drag
        :headers="{ 'X-CSRF-TOKEN': $root.csrf }"
        action="/import">
          <i class="el-icon-upload"></i>
          <div class="el-upload__text">Arrastra un archivo o <em>haz click para importarlo</em></div>
          <div class="el-upload__tip" slot="tip">Archivos kml/kmz con un tamaño inferior a 2MB</div>
      </el-upload>
    </el-col>
  </el-row>
</template>

<script>
export default {
  data: () => ({
  }),
};
</script>

<style scoped>
.upload {
  text-align: center;
}

.el-upload__tip,
.el-upload-list {
  text-align: left
}
</style>

