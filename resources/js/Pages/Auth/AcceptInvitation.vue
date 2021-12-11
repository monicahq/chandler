<template>
  <Head title="Register" />

  <BreezeValidationErrors class="mb-4" />

  <form @submit.prevent="submit">
    <h1 class="text-center mb-3 text-xl"><span class="mr-2">👋</span> Welcome to Monica.</h1>
    <p class="mb-4">Please fill this form to finalize your account.</p>

    <div>
      <BreezeLabel for="first_name" value="First name" />
      <BreezeInput id="first_name" type="text" class="mt-1 block w-full" v-model="form.first_name" required autofocus autocomplete="first_name" />
    </div>

    <div class="mt-4">
      <BreezeLabel for="last_name" value="Last name" />
      <BreezeInput id="last_name" type="text" class="mt-1 block w-full" v-model="form.last_name" required autocomplete="last_name" />
    </div>

    <div class="mt-4">
      <BreezeLabel for="email" value="Email" />
      <BreezeInput id="email" type="email" class="mt-1 block w-full" v-model="form.email" required autocomplete="username" />
    </div>

    <div class="mt-4">
      <BreezeLabel for="password" value="Password" />
      <BreezeInput id="password" type="password" class="mt-1 block w-full" v-model="form.password" required autocomplete="new-password" />
    </div>

    <div class="mt-4">
      <BreezeLabel for="password_confirmation" value="Confirm Password" />
      <BreezeInput id="password_confirmation" type="password" class="mt-1 block w-full" v-model="form.password_confirmation" required autocomplete="new-password" />
    </div>

    <div class="flex items-center justify-end mt-4">
      <BreezeButton class="ml-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
        Create account
      </BreezeButton>
    </div>
    </form>
</template>

<script>
import BreezeButton from '@/Components/Button.vue'
import BreezeGuestLayout from '@/Shared/Guest.vue'
import BreezeInput from '@/Components/Input.vue'
import BreezeLabel from '@/Components/Label.vue'
import BreezeValidationErrors from '@/Components/ValidationErrors.vue'
import { Head, Link } from '@inertiajs/inertia-vue3';

export default {
  layout: BreezeGuestLayout,

  components: {
    BreezeButton,
    BreezeInput,
    BreezeLabel,
    BreezeValidationErrors,
    Head,
    Link,
  },

    data() {
      return {
        form: this.$inertia.form({
          first_name: '',
          last_name: '',
          email: '',
          password: '',
          password_confirmation: '',
          terms: false,
        })
      }
    },

    methods: {
      submit() {
        this.form.post(this.route('register'), {
          onFinish: () => this.form.reset('password', 'password_confirmation'),
        })
      }
    }
}
</script>
