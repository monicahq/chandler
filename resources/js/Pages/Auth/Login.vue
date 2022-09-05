<script setup>
import { ref, watch, onMounted } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import { Link, useForm } from '@inertiajs/inertia-vue3';
import TextInput from '@/Shared/Form/TextInput.vue';
import BreezeCheckbox from '@/Components/Checkbox.vue';
import BreezeValidationErrors from '@/Components/ValidationErrors.vue';
import PrettyButton from '@/Shared/Form/PrettyButton.vue';
import WebauthnLogin from '@/Pages/Webauthn/WebauthnLogin.vue';
import JetSecondaryButton from '@/Components/Jetstream/SecondaryButton.vue';

const props = defineProps({
  canResetPassword: Boolean,
  status: String,
  wallpaperUrl: String,
  providers: Array,
  providersName: Object,
  publicKey: Object,
  userName: String,
});
const webauthn = ref(true);
const publicKeyRef = ref(null);

const form = useForm({
  email: '',
  password: '',
  remember: false,
});
const providerForm = useForm();

watch(
  () => props.publicKey,
  (value) => {
    publicKeyRef.value = value;
  },
);

onMounted(() => {
  publicKeyRef.value = props.publicKey;
});

const submit = () => {
  form
    .transform((data) => ({
      ...data,
      remember: form.remember ? 'on' : '',
    }))
    .post(route('login'), {
      onFinish: () => form.reset('password'),
    });
};

const open = (provider) => {
  providerForm
    .transform(() => ({
      redirect: location.href,
      remember: form.remember ? 'on' : '',
    }))
    .get(route('login.provider', { driver: provider }), {
      preserveScroll: true,
      onFinish: () => {
        providerForm.reset();
      },
    });
};

const reload = () => {
  publicKeyRef.value = null;
  webauthn.value = true;
  Inertia.reload({ only: ['publicKey'] });
};
</script>

<style scoped>
.auth-provider {
  width: 15px;
  height: 15px;
  margin-right: 8px;
  top: 2px;
}
.w-43 {
  width: 43%;
}
</style>

<template>
  <div class="flex min-h-screen flex-col items-center bg-gray-100 pt-6 dark:bg-gray-900 sm:justify-center sm:pt-0">
    <div>
      <Link href="/">
        <svg
          class="h-20 w-20 fill-current text-gray-500"
          viewBox="0 0 390 353"
          fill="none"
          xmlns="http://www.w3.org/2000/svg">
          <path
            fill-rule="evenodd"
            clip-rule="evenodd"
            d="M198.147 353C289.425 353 390.705 294.334 377.899 181.5C365.093 68.6657 289.425 10 198.147 10C106.869 10 31.794 61.4285 12.2144 181.5C-7.36527 301.571 106.869 353 198.147 353Z"
            fill="#2C2B29" />
          <path
            fill-rule="evenodd"
            clip-rule="evenodd"
            d="M196.926 320C270.146 320 351.389 272.965 341.117 182.5C330.844 92.0352 270.146 45 196.926 45C123.705 45 63.4825 86.2328 47.7763 182.5C32.0701 278.767 123.705 320 196.926 320Z"
            fill="white" />
          <path
            fill-rule="evenodd"
            clip-rule="evenodd"
            d="M52.4154 132C62.3371 132.033 66.6473 96.5559 84.3659 80.4033C100.632 65.5752 138 60.4908 138 43.3473C138 7.52904 99.1419 0 64.8295 0C30.517 0 0 36.3305 0 72.1487C0 107.967 33.3855 131.937 52.4154 132Z"
            fill="#2C2B29" />
          <path
            fill-rule="evenodd"
            clip-rule="evenodd"
            d="M337.585 132C327.663 132.033 323.353 96.5559 305.634 80.4033C289.368 65.5752 252 60.4908 252 43.3473C252 7.52904 290.858 0 325.171 0C359.483 0 390 36.3305 390 72.1487C390 107.967 356.615 131.937 337.585 132Z"
            fill="#2C2B29" />
          <path
            fill-rule="evenodd"
            clip-rule="evenodd"
            d="M275.651 252.546C301.619 246.357 312.447 235.443 312.447 200.175C312.447 164.907 295.905 129.098 267.423 129.098C238.941 129.098 220.028 154.564 220.028 189.832C220.028 225.1 249.682 258.734 275.651 252.546Z"
            fill="#2B2A28" />
          <path
            fill-rule="evenodd"
            clip-rule="evenodd"
            d="M266.143 207.804C273.209 207.804 275.311 206.002 278.505 201.954C282.087 197.416 284.758 192.151 283.885 181.278C282.426 163.109 274.764 154.752 259.773 154.752C244.783 154.752 241.859 166.27 241.859 181.278C241.859 196.286 251.152 207.804 266.143 207.804Z"
            fill="white" />
          <path
            fill-rule="evenodd"
            clip-rule="evenodd"
            d="M115.349 252.546C89.3806 246.357 78.5526 235.443 78.5526 200.175C78.5526 164.907 95.0948 129.098 123.577 129.098C152.059 129.098 170.972 154.564 170.972 189.832C170.972 225.1 141.318 258.734 115.349 252.546Z"
            fill="#2B2A28" />
          <path
            fill-rule="evenodd"
            clip-rule="evenodd"
            d="M124.857 207.804C117.791 207.804 115.689 206.002 112.495 201.954C108.913 197.416 106.242 192.151 107.115 181.278C108.574 163.109 116.236 154.752 131.227 154.752C146.217 154.752 149.141 166.27 149.141 181.278C149.141 196.286 139.848 207.804 124.857 207.804Z"
            fill="white" />
          <path
            fill-rule="evenodd"
            clip-rule="evenodd"
            d="M196.204 276C210.316 276 224 255.244 224 246.342C224 237.441 210.112 236 196 236C181.888 236 168 237.441 168 246.342C168 255.244 182.092 276 196.204 276Z"
            fill="#2C2B29" />
        </svg>
      </Link>
    </div>

    <div class="mt-6 flex w-full overflow-hidden bg-white shadow-md dark:bg-gray-800 sm:max-w-4xl sm:rounded-lg">
      <img :src="wallpaperUrl" class="w-10/12 sm:invisible md:visible" :alt="'Wallpaper'" />
      <div class="w-full">
        <div class="border-b border-gray-200 px-6 pt-14 pb-10 dark:border-gray-700">
          <h1 class="mb-6 text-center text-xl text-gray-800 dark:text-gray-200">
            <span class="mr-2"> 👋 </span>
            Sign in to your account
          </h1>

          <BreezeValidationErrors class="mb-4" />

          <div v-if="status" class="mb-4 text-sm font-medium text-green-600 dark:text-green-400">
            {{ status }}
          </div>

          <div v-if="publicKey && webauthn">
            <div class="mb-4 text-center text-lg text-gray-900">
              {{ userName }}
            </div>
            <div class="mb-4 max-w-xl text-gray-600 dark:text-gray-400">
              {{ $t('Connect with your security key') }}
            </div>

            <WebauthnLogin :remember="true" :public-key="publicKeyRef" />

            <JetSecondaryButton class="mr-2 mt-4" @click.prevent="webauthn = false">
              {{ $t('Use your password') }}
            </JetSecondaryButton>
          </div>

          <form v-else @submit.prevent="submit" class="dark:text-gray-800">
            <div class="mb-4">
              <TextInput
                v-model="form.email"
                :label="'Email'"
                :type="'email'"
                :autofocus="true"
                :input-class="'block w-full'"
                :required="true"
                :autocomplete="false"
                :maxlength="255" />
            </div>

            <div class="mb-4">
              <TextInput
                v-model="form.password"
                :label="'Password'"
                :type="'password'"
                :autofocus="true"
                :input-class="'block w-full'"
                :required="true"
                :autocomplete="false"
                :maxlength="255" />
            </div>

            <div class="mb-4 block">
              <label class="flex items-center">
                <BreezeCheckbox v-model:checked="form.remember" name="remember" />
                <span class="ml-2 text-sm text-gray-600 dark:text-gray-400"> Remember me </span>
              </label>
            </div>

            <div class="flex items-center justify-end">
              <Link
                v-if="canResetPassword"
                :href="route('password.request')"
                class="text-sm text-blue-500 hover:underline">
                Forgot password?
              </Link>

              <PrettyButton :text="'Log in'" :state="loadingState" :classes="'save ml-4'" />
            </div>

            <div class="mt-4 block">
              <p v-if="providers.length > 0" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                {{ $t('Login with:') }}
              </p>
              <div class="flex flex-wrap">
                <JetSecondaryButton
                  v-for="provider in providers"
                  :key="provider"
                  class="mr-2"
                  :href="route('login.provider', { driver: provider })"
                  @click.prevent="open(provider)">
                  <img :src="`/img/auth/${provider}.svg`" alt="" class="auth-provider relative" />
                  {{ providersName[provider] }}
                </JetSecondaryButton>
              </div>
            </div>

            <div v-if="publicKeyRef" class="mt-4 block">
              <JetSecondaryButton class="mr-2" @click.prevent="reload">
                {{ $t('Use your security key') }}
              </JetSecondaryButton>
            </div>
          </form>
        </div>

        <div class="px-6 py-6 text-sm dark:text-gray-50">
          New to Monica?
          <Link :href="route('register')" class="text-blue-500 hover:underline"> Create an account </Link>
        </div>
      </div>
    </div>
  </div>
</template>
