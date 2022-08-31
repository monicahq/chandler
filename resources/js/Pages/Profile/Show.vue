<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import DeleteUserForm from '@/Pages/Profile/Partials/DeleteUserForm.vue';
import JetSectionBorder from '@/Jetstream/SectionBorder.vue';
import LogoutOtherBrowserSessionsForm from '@/Pages/Profile/Partials/LogoutOtherBrowserSessionsForm.vue';
import TwoFactorAuthenticationForm from '@/Pages/Profile/Partials/TwoFactorAuthenticationForm.vue';
import UpdatePasswordForm from '@/Pages/Profile/Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from '@/Pages/Profile/Partials/UpdateProfileInformationForm.vue';
import UpdateProviders from '@/Pages/Profile/Partials/UpdateProviders.vue';
import WebauthnKeys from '@/Pages/Webauthn/WebauthnKeys.vue';

defineProps({
  confirmsTwoFactorAuthentication: Boolean,
  sessions: Array,
  providers: Array,
  providersName: Object,
  userTokens: Array,
  locales: Array,
  webauthnKeys: Array,
});
</script>

<template>
  <AppLayout title="Settings">
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
        {{ $t('Settings') }}
      </h2>
    </template>

    <div>
      <div class="mx-auto max-w-7xl py-10 sm:px-6 lg:px-8">
        <div v-if="$page.props.jetstream.canUpdateProfileInformation">
          <UpdateProfileInformationForm :user="$page.props.user" :locales="locales" />

          <JetSectionBorder />
        </div>

        <div v-if="$page.props.jetstream.canUpdatePassword">
          <UpdatePasswordForm class="mt-10 sm:mt-0" />

          <JetSectionBorder />
        </div>

        <div v-if="providers.length > 0">
          <UpdateProviders
            :user="$page.props.user"
            :providers="providers"
            :providersName="providersName"
            :userTokens="userTokens" />

          <JetSectionBorder />
        </div>

        <div v-if="$page.props.jetstream.canManageTwoFactorAuthentication">
          <TwoFactorAuthenticationForm :requires-confirmation="confirmsTwoFactorAuthentication" class="mt-10 sm:mt-0" />

          <JetSectionBorder />
        </div>

        <div>
          <WebauthnKeys :webauthnKeys="webauthnKeys" />

          <JetSectionBorder />
        </div>

        <LogoutOtherBrowserSessionsForm :sessions="sessions" class="mt-10 sm:mt-0" />

        <template v-if="$page.props.jetstream.hasAccountDeletionFeatures">
          <JetSectionBorder />

          <DeleteUserForm class="mt-10 sm:mt-0" />
        </template>
      </div>
    </div>
  </AppLayout>
</template>
