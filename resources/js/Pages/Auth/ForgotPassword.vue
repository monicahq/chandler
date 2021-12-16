<template>
  <head title="Forgot Password"></head>

  <div class="mb-4 text-sm text-gray-600">
    Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.
  </div>

  <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
    {{ status }}
  </div>

  <breeze-validation-errors class="mb-4" />

  <form @submit.prevent="submit">
    <div>
      <breeze-label for="email" value="Email" />
      <breeze-input id="email" v-model="form.email" type="email" class="mt-1 block w-full" required
                    autofocus autocomplete="username"
      />
    </div>

    <div class="flex items-center justify-end mt-4">
      <breeze-button :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
        Email Password Reset Link
      </breeze-button>
    </div>
  </form>
</template>

<script>
import BreezeButton from '@/Components/Button.vue';
import BreezeGuestLayout from '@/Shared/Guest.vue';
import BreezeInput from '@/Components/Input.vue';
import BreezeLabel from '@/Components/Label.vue';
import BreezeValidationErrors from '@/Components/ValidationErrors.vue';
import { Head } from '@inertiajs/inertia-vue3';

export default {

  components: {
    BreezeButton,
    BreezeInput,
    BreezeLabel,
    BreezeValidationErrors,
    Head,
  },
  layout: BreezeGuestLayout,

  props: {
    status: String,
  },

  data() {
    return {
      form: this.$inertia.form({
        email: ''
      })
    };
  },

  methods: {
    submit() {
      this.form.post(this.route('password.email'));
    }
  }
};
</script>
