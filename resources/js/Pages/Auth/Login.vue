<script setup>
import Checkbox from "@/Components/Checkbox.vue";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { Head, Link, useForm, router } from "@inertiajs/vue3";
import { ref, onMounted } from "vue";
import axios from "axios";

const props = defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
    captcha: {
        type: Object,
        required: true,
    },
});

const captchaData = ref(props.captcha);
const isRefreshing = ref(false);

const form = useForm({
    email: "",
    password: "",
    remember: false,
    captcha_answer: "",
});

const submit = () => {
    form.post(route("login"), {
        onFinish: () => {
            form.reset("password", "captcha_answer");
        },
        onError: () => {
            // Refresh captcha on error
            refreshCaptcha();
        },
    });
};

const refreshCaptcha = async () => {
    try {
        isRefreshing.value = true;
        const response = await axios.get(route('captcha.refresh'));
        captchaData.value = response.data;
        form.captcha_answer = "";
    } catch (error) {
        console.error('Error refreshing captcha:', error);
    } finally {
        isRefreshing.value = false;
    }
};
</script>

<template>
    <GuestLayout>

        <Head title="Login" />

        <!-- Status Message -->
        <div v-if="status" class="w-full mb-4">
            <div class="text-sm font-medium text-green-600 text-center">
                {{ status }}
            </div>
        </div>

        <!-- Login Form Container -->
        <div class="">
            <div class="bg-white shadow-lg rounded-2xl overflow-hidden">
                <!-- Header -->
                <div class="px-6 py-8 sm:px-8">
                    <div class="text-center">
                        <h3
                            class="text-2xl sm:text-3xl font-bold text-transparent bg-gradient-to-r from-blue-600 to-cyan-400 bg-clip-text mb-2">
                            Welcome back to Monitoring Progress
                        </h3>
                        <p class="text-gray-600 text-sm sm:text-base">
                            Enter your email and password to sign in
                        </p>
                    </div>
                </div>

                <!-- Form -->
                <div class="px-6 pb-8 sm:px-8">
                    <form @submit.prevent="submit" class="space-y-6">
                        <!-- Email Field -->
                        <div>
                            <InputLabel for="email" value="Email" />
                            <TextInput id="email" type="email" class="mt-2 block w-full" v-model="form.email" required
                                autofocus autocomplete="username" placeholder="Email" />
                            <InputError class="mt-2" :message="form.errors.email" />
                        </div>

                        <!-- Password Field -->
                        <div>
                            <InputLabel for="password" value="Password" />
                            <TextInput id="password" type="password" class="mt-2 block w-full" v-model="form.password"
                                required autocomplete="current-password" placeholder="Password" />
                            <InputError class="mt-2" :message="form.errors.password" />
                        </div>

                        <!-- CAPTCHA Section -->
                        <div>
                            <InputLabel for="captcha" value="Verifikasi Keamanan" />

                            <div class="mt-2 p-4 bg-gray-50 rounded-lg border border-gray-200">
                                <div class="flex items-center justify-between mb-3">
                                    <span class="text-lg font-mono font-bold text-gray-700 flex-1">
                                        {{ captchaData.question }}
                                    </span>
                                   
                                </div>

                                <TextInput id="captcha" type="text" inputmode="numeric" pattern="[0-9]*" class="w-full"
                                    v-model="form.captcha_answer" required placeholder="Masukkan jawaban" />
                            </div>

                            <InputError class="mt-2" :message="form.errors.captcha_answer" />
                        </div>

                        <!-- Remember Me and Forgot Password -->
                        <div
                            class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-3 sm:space-y-0">
                            <label class="flex items-center">
                                <Checkbox name="remember" v-model:checked="form.remember"
                                    class="rounded border-gray-300 ml-0 text-indigo-600 shadow-sm focus:ring-indigo-500" />
                                <span class="ml-2 text-sm text-gray-600">Remember me</span>
                            </label>

                            <div>
                                <Link v-if="canResetPassword" :href="route('password.request')"
                                    class="text-sm text-indigo-600 hover:text-indigo-500 underline focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 rounded">
                                Forgot your password?
                                </Link>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div>
                            <PrimaryButton class="w-full justify-center py-3" :class="{ 'opacity-25': form.processing }"
                                :disabled="form.processing">
                                <span v-if="form.processing">Signing in...</span>
                                <span v-else>Sign in</span>
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </GuestLayout>
</template>

<style scoped>
/* Ensure proper scrolling on mobile */
@media (max-height: 700px) {
    .min-h-screen {
        min-height: auto;
    }
}
</style>