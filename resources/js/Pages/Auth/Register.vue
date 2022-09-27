<script setup>
import { Link, useForm } from '@inertiajs/inertia-vue3';
import JetGuestLayout from '@/Shared/Guest.vue';
import JetInput from '@/Components/Input.vue';
import JetLabel from '@/Components/Label.vue';
import JetValidationErrors from '@/Components/ValidationErrors.vue';
import PrettyButton from '@/Shared/Form/PrettyButton.vue';

const form = useForm({
  first_name: '',
  last_name: '',
  email: '',
  password: '',
  password_confirmation: '',
  terms: false,
});

const submit = () => {
  form.post(route('register'), {
    onFinish: () => form.reset('password', 'password_confirmation'),
  });
};
</script>

<template>
  <JetGuestLayout>
    <JetValidationErrors class="mb-4" />

    <p class="mb-2 text-lg font-bold">Sign up for an account</p>
    <p class="mb-8 text-sm text-gray-500">Your name here will be used to add yourself as a contact.</p>

    <form @submit.prevent="submit">
      <div>
        <JetLabel for="first_name" value="First name" />
        <JetInput
          id="first_name"
          v-model="form.first_name"
          type="text"
          class="mt-1 block w-full"
          required
          autofocus
          autocomplete="first_name" />
      </div>

      <div class="mt-4">
        <JetLabel for="last_name" value="Last name" />
        <JetInput
          id="last_name"
          v-model="form.last_name"
          type="text"
          class="mt-1 block w-full"
          required
          autocomplete="last_name" />
      </div>

      <div class="mt-4">
        <JetLabel for="email" value="Email" />
        <JetInput
          id="email"
          v-model="form.email"
          type="email"
          class="mt-1 block w-full"
          required
          autocomplete="username" />
      </div>

      <div class="mt-4">
        <JetLabel for="password" value="Password" />
        <JetInput
          id="password"
          v-model="form.password"
          type="password"
          class="mt-1 block w-full"
          required
          autocomplete="new-password" />
      </div>

      <div class="mt-4 mb-8">
        <JetLabel for="password_confirmation" value="Confirm Password" />
        <JetInput
          id="password_confirmation"
          v-model="form.password_confirmation"
          type="password"
          class="mt-1 block w-full"
          required
          autocomplete="new-password" />
      </div>

      <div class="mt-4 flex items-center justify-end">
        <Link :href="route('login')" class="mr-4 text-sm text-blue-500 hover:underline dark:text-gray-400">
          Already registered?
        </Link>

        <PrettyButton :text="'Register'" :state="loadingState" :classes="'save'" />
      </div>
    </form>
  </JetGuestLayout>
</template>
