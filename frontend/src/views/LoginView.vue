<template>
  <div class="pt-16">
    <h1 class="text-3xl font-semibold mb-4">Enter your phone number</h1>
    <form
      v-if="!waitingOnVerification"
      action="#"
      @submit.prevent="handleLogin"
    >
      <div
        class="overflow-hidden shadow sm:rounded-md max-w-sm mx-auto text-left"
      >
        <div class="bg-white px-4 py-5 sm:p-6">
          <div>
            <input
              v-maska
              data-maska="+###########"
              type="text"
              name="phone"
              id="phone"
              v-model="credentials.phone"
              placeholder="1 (234) 567-8910"
              class="mt-1 block w-full px-3 py-2 rounded-md border border-gray-300 shadow-sm"
            />
          </div>
        </div>
        <div class="bg-gray-50 px-4 py-3 text-right sm:px-6">
          <button
            type="submit"
            @submit.prevent="handleLogin"
            class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          >
            Send
          </button>
        </div>
      </div>
    </form>

    <form v-else action="#" @submit.prevent="handleVerification">
      <div
        class="overflow-hidden shadow sm:rounded-md max-w-sm mx-auto text-left"
      >
        <div class="bg-white px-4 py-5 sm:p-6">
          <div>
            <input
              v-maska
              data-maska="######"
              type="text"
              name="login_code"
              id="login_code"
              v-model="credentials.login_code"
              placeholder="123456"
              class="mt-1 block w-full px-3 py-2 rounded-md border border-gray-300 shadow-sm focus:border-black focus:outline-none"
            />
          </div>
        </div>
        <div class="bg-gray-50 px-4 py-3 text-right sm:px-6">
          <button
            type="submit"
            @submit.prevent="handleVerification"
            class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          >
            Verify
          </button>
        </div>
      </div>
    </form>
  </div>
</template>

<script setup>
import { vMaska } from "maska";
import { onMounted, reactive, ref } from "vue";
import axios from "axios";
import { useRouter } from "vue-router";

const router = useRouter();

const credentials = reactive({
  phone: null,
  login_code: null,
});

const waitingOnVerification = ref(false);

onMounted(() => {
  if (localStorage.getItem("token")) {
    router.push({
      name: "landing",
    });
  }
});

const handleLogin = () => {
  console.log(credentials.phone);
  axios
    .post("http://localhost:8080/api/login", {
      phone: credentials.phone,
    })
    .then((response) => {
      console.log(response.data);
      // waitingOnVerification.value = true;
    })
    .catch((error) => {
      console.error(error);
      // alert(error.response.data.message);
      waitingOnVerification.value = true;
    });
};

const handleVerification = () => {
  axios
    .post("http://localhost:8080/api/login/verify", {
      phone: credentials.phone,
      login_code: credentials.login_code,
    })
    .then((response) => {
      console.log(response.data);
      waitingOnVerification.value = false;

      localStorage.setItem("token", response.data);
      router.push({ name: "landing" });
    })
    .catch((error) => {
      console.error(error);
      alert(error.response.data.message);
    });
};
</script>
