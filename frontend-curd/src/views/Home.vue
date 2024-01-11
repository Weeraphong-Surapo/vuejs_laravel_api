<template>
  <div class="container py-3">
    <h1 class="border-bottom border-primary border-3 d-inline">Vue js 3 + Laravel Api</h1>
    <div class=""></div>
    <div class="row mt-3">
      <div class="col-12 col-lg-8">
        <table class="table mt-2">
          <thead>
            <tr>
              <th scope="col">#</th>
              <th scope="col">รหัสนักศึกษา</th>
              <th scope="col">ชื่อ</th>
              <th scope="col">สกุล</th>
              <th scope="col">จัดการ</th>
            </tr>
          </thead>
          <tr v-if="loading" class="text-center">
            <td colspan="5">
              <div class="spinner-border text-primary" style="background:#181818;" role="status">
                <span class="visually-hidden">Loading...</span>
              </div>
            </td>
          </tr>
          <tbody v-else>
            <tr v-for="(student, index) in students" :key="index" >
              <td scope="row">{{ ++index }}</td>
              <td>{{ student.student_id }}</td>
              <td>{{ student.first_name }}</td>
              <td>{{ student.last_name }}</td>
              <td width="20%">
                <div class="btn-group">
                  <button class="btn btn-warning" @click="handleEdit(student.id)">แก้ไข</button>
                  <button class="btn btn-danger" @click="handleDelete(student.id)">ลบ</button>
                </div>
              </td>
            </tr>
            
            <tr v-if="students.length <= 0" class="text-center">
              <td colspan="5">ไม่มีข้อมูล</td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="col-12 col-lg-4 mt-2">
        <div class="card shadow border-0 rounded-0">
          <form @submit.prevent="handleCheckType">
            <div class="card-body">
            <div class="alert alert-danger" v-if="errors">
              <ul>
                <li v-for="(error,index) in errors" :key="index">{{ error[0] }} !!</li>
              </ul>
            </div>
              <h3 class="border-bottom border-primary border-3">ฟอร์มเพิ่มข้อมูล</h3>
              <div class="form-floating my-3">
                <input type="test" class="form-control" placeholder="รหัสนักศึกษา" v-model="form.student_id">
                <label for="floatingInput">รหัสนักศึกษา</label>
              </div>
              <div class="form-floating mb-3">
                <input type="text" class="form-control" placeholder="ชื่อ" v-model="form.first_name">
                <label for="floatingInput">ชื่อ</label>
              </div>
              <div class="form-floating">
                <input type="text" class="form-control" placeholder="สกุล" v-model="form.last_name">
                <label for="floatingInput">สกุล</label>
              </div>
            </div>
            <div class="card-footer text-body-secondary">
              <button type="submit" class="btn btn-success w-100" :disabled="loadingBtn">
                <div v-if="loadingBtn" class="spinner-border spinner-border-sm" role="status">
                  <span class="visually-hidden">Loading...</span>
                </div>
                <span v-else>บันทึก</span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue';
import axios from "axios"

// url api
const url_api = "https://apivuejsdemo.innovation-develop.com/api"

// ตัวแปร
const form = ref({
  student_id: '',
  first_name: '',
  last_name: ''
})
const loadingBtn = ref(false)
const loading = ref(false)
const editMode = ref(false)
const students = ref([])
const errors = ref(null)

// เช็คว่าเป็นการเพิ่มหรือแก้ไข
const handleCheckType = computed(() => {
  return editMode.value ? handleSubmitUpdate : handleSubmit
})

// funciton

// มีการกด submit form เพิ่มข้อมูล
const handleSubmit = async () => {
  try {
    loadingBtn.value = true
    const response = await axios.post(`${url_api}/student`, form.value)
    await getAllStudents()
    form.value = {
      student_id: '',
      first_name: '',
      last_name: ''
    }
    notify('บันทึกสำเร็จ')
    errors.value = null

  } catch (e) {
    console.log(e);
    if(e.response.status == 422){
      errors.value = e.response.data.errors
    }
  } finally {
    loadingBtn.value = false
  }
}

// แก้ไขข้อมูล
const handleEdit = async (id) => {
  try {
    editMode.value = true
    errors.value = null
    const response = await axios.get(`${url_api}/student/${id}`)
    form.value = response.data.data
  } catch (e) {
    notify('เกิดข้อผิดพลาดบางอย่าง','error')
    console.log(e);
  }
}

// ลบข้อมูล
const handleDelete = async (id) => {
  try {
    const response = await axios.delete(`${url_api}/student/${id}`)
    notify('ลบสำเร็จ')
  } catch (e) {
    notify('เกิดข้อผิดพลาดบางอย่าง','error')
    console.log(e);
  } finally {
    await getAllStudents()
  }
}

// อัพเดตข้อมูล
const handleSubmitUpdate = async () => {
  try {
    loadingBtn.value = true
    const response = await axios.post(`${url_api}/student/${form.value.id}/update`, form.value)
    await getAllStudents()
    form.value = {
      student_id: '',
      first_name: '',
      last_name: ''
    }
    editMode.value = false
    errors.value = null
    notify('อัพเดตสำเร็จ')
  } catch (e) {
    notify('เกิดข้อผิดพลาดบางอย่าง','error')
    console.log(e);
    if(e.response.status == 422){
      errors.value = e.response.data.errors
    }
  } finally {
    loadingBtn.value = false
  }
}

// fetch data ทั้งหมด
const getAllStudents = async () => {
  try {
    loading.value = true
    const response = await axios.get(`${url_api}/student`)
    students.value = response.data.data
  } catch (e) {
    notify('เกิดข้อผิดพลาดบางอย่าง','error')
    console.log(e);
  } finally {
    loading.value = false
  }
}

// notify sweet alert2
const notify = (title,icon="success") => {
  const Toast = Swal.mixin({
    toast: true,
    position: "top-end",
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    didOpen: (toast) => {
      toast.onmouseenter = Swal.stopTimer;
      toast.onmouseleave = Swal.resumeTimer;
    }
  });
  Toast.fire({
    icon,
    title
  });
}

// โหลดข้อมูลทุกอย่างที่โหลดเว็บ
onMounted(() => {
  getAllStudents()
})

</script>

<style scoped></style>