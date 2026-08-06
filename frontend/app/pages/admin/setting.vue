<script setup lang="ts">
definePageMeta({
  layout: "admin",
});

// State management
const activeTab = ref('general');
const saving = ref(false);
const successMessage = ref('');

// General Settings
const generalSettings = reactive({
  siteName: 'Alumni Connect',
  siteDescription: 'ប្រព័ន្ធគ្រប់គ្រងអតីតនិស្សិត',
  adminEmail: 'admin@alumni.edu',
  contactPhone: '+855 12 345 678',
  address: 'ភ្នំពេញ, កម្ពុជា',
  timezone: 'Asia/Phnom_Penh',
  dateFormat: 'DD/MM/YYYY',
});

// Email Settings
const emailSettings = reactive({
  smtpHost: 'smtp.gmail.com',
  smtpPort: 587,
  smtpUsername: 'alumni@edu.com',
  smtpPassword: '********',
  encryption: 'tls',
  fromEmail: 'no-reply@alumni.edu',
  fromName: 'Alumni System',
  sendWelcomeEmail: true,
  sendNewsletter: true,
});

// Notification Settings
const notificationSettings = reactive({
  emailNotifications: true,
  pushNotifications: false,
  eventReminders: true,
  birthdayWishes: true,
  monthlyNewsletter: true,
  adminAlerts: true,
});

// User Management Settings
const userSettings = reactive({
  allowRegistration: true,
  emailVerification: true,
  allowSocialLogin: true,
  defaultRole: 'member',
  minPasswordLength: 8,
  requireStrongPassword: true,
  sessionTimeout: 60, // minutes
});

// Security Settings
const securitySettings = reactive({
  twoFactorAuth: false,
  recaptchaEnabled: true,
  ipWhitelisting: false,
  rateLimit: true,
  maxLoginAttempts: 5,
  passwordExpiry: 90, // days
});

// Social Media Links
const socialSettings = reactive({
  facebook: 'https://facebook.com/alumni',
  twitter: 'https://twitter.com/alumni',
  linkedin: 'https://linkedin.com/company/alumni',
  instagram: 'https://instagram.com/alumni',
  youtube: 'https://youtube.com/alumni',
});

// Save settings
const saveSettings = async () => {
  saving.value = true;
  successMessage.value = '';
  
  try {
    // Simulate API call
    await new Promise(resolve => setTimeout(resolve, 1500));
    
    successMessage.value = 'ការកំណត់ត្រូវបានរក្សាទុកដោយជោគជ័យ!';
    
    // Clear success message after 3 seconds
    setTimeout(() => {
      successMessage.value = '';
    }, 3000);
  } catch (error) {
    console.error('Error saving settings:', error);
  } finally {
    saving.value = false;
  }
};

// Reset settings to default
const resetSettings = () => {
  if (confirm('តើអ្នកចង់កំណត់ការកំណត់ឡើងវិញទៅជាលំនាំដើមទេ?')) {
    // Reset logic here
    successMessage.value = 'ការកំណត់ត្រូវបានកំណត់ឡើងវិញជោគជ័យ!';
  }
};

// Tabs configuration
const tabs = [
  { id: 'general', label: 'ទូទៅ', icon: 'i-heroicons-cog-6-tooth' },
  { id: 'email', label: 'អ៊ីមែល', icon: 'i-heroicons-envelope' },
  { id: 'notification', label: 'ការជូនដំណឹង', icon: 'i-heroicons-bell' },
  { id: 'user', label: 'អ្នកប្រើប្រាស់', icon: 'i-heroicons-users' },
  { id: 'security', label: 'សន្តិសុខ', icon: 'i-heroicons-shield-check' },
  { id: 'social', label: 'បណ្តាញសង្គម', icon: 'i-heroicons-share' },
];
</script>

<template>
  <div class="min-h-screen bg-gray-50 p-6">
    <div class="max-w-7xl mx-auto">
      <!-- Header -->
      <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
        <div>
          <h1 class="text-3xl font-bold text-gray-900">ការកំណត់ប្រព័ន្ធ</h1>
          <p class="text-gray-600 mt-1">គ្រប់គ្រងការកំណត់ទាំងអស់នៃប្រព័ន្ធ Alumni</p>
        </div>
        <div class="flex gap-3 w-full sm:w-auto">
          <UButton
            color="gray"
            variant="outline"
            @click="resetSettings"
            class="flex-1 sm:flex-none"
          >
            <UIcon name="i-heroicons-arrow-path" class="mr-2" />
            កំណត់ឡើងវិញ
          </UButton>
          <UButton
            color="primary"
            :loading="saving"
            @click="saveSettings"
            class="flex-1 sm:flex-none"
          >
            <UIcon name="i-heroicons-check" class="mr-2" />
            រក្សាទុក
          </UButton>
        </div>
      </div>

      <!-- Success Message -->
      <div v-if="successMessage" class="mb-6">
        <UAlert
          color="green"
          variant="subtle"
          :title="successMessage"
          icon="i-heroicons-check-circle"
        />
      </div>

      <!-- Tabs Navigation -->
      <div class="border-b border-gray-200 mb-6">
        <nav class="flex flex-wrap gap-2 -mb-px">
          <button
            v-for="tab in tabs"
            :key="tab.id"
            @click="activeTab = tab.id"
            class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-medium border-b-2 transition-colors duration-200"
            :class="[
              activeTab === tab.id
                ? 'border-primary-500 text-primary-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
            ]"
          >
            <UIcon :name="tab.icon" class="w-5 h-5" />
            {{ tab.label }}
          </button>
        </nav>
      </div>

      <!-- Tab Content -->
      <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <!-- General Settings -->
        <div v-if="activeTab === 'general'">
          <h2 class="text-xl font-semibold text-gray-900 mb-6">ការកំណត់ទូទៅ</h2>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                ឈ្មោះគេហទំព័រ <span class="text-red-500">*</span>
              </label>
              <UInput v-model="generalSettings.siteName" placeholder="បញ្ចូលឈ្មោះគេហទំព័រ" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                ការពណ៌នា
              </label>
              <UInput v-model="generalSettings.siteDescription" placeholder="បញ្ចូលការពណ៌នា" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                អ៊ីមែលអ្នកគ្រប់គ្រង <span class="text-red-500">*</span>
              </label>
              <UInput v-model="generalSettings.adminEmail" type="email" placeholder="admin@alumni.edu" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                លេខទូរស័ព្ទ
              </label>
              <UInput v-model="generalSettings.contactPhone" placeholder="+855 12 345 678" />
            </div>
            <div class="md:col-span-2">
              <label class="block text-sm font-medium text-gray-700 mb-1">
                អាសយដ្ឋាន
              </label>
              <UTextarea v-model="generalSettings.address" placeholder="បញ្ចូលអាសយដ្ឋាន" rows="2" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                តំបន់ពេលវេលា
              </label>
              <USelect
                v-model="generalSettings.timezone"
                :options="[
                  { label: 'Asia/Phnom_Penh', value: 'Asia/Phnom_Penh' },
                  { label: 'Asia/Bangkok', value: 'Asia/Bangkok' },
                  { label: 'UTC', value: 'UTC' },
                ]"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                ទម្រង់កាលបរិច្ឆេទ
              </label>
              <USelect
                v-model="generalSettings.dateFormat"
                :options="[
                  { label: 'DD/MM/YYYY', value: 'DD/MM/YYYY' },
                  { label: 'MM/DD/YYYY', value: 'MM/DD/YYYY' },
                  { label: 'YYYY-MM-DD', value: 'YYYY-MM-DD' },
                ]"
              />
            </div>
          </div>
        </div>

        <!-- Email Settings -->
        <div v-if="activeTab === 'email'">
          <h2 class="text-xl font-semibold text-gray-900 mb-6">ការកំណត់អ៊ីមែល</h2>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                SMTP Host <span class="text-red-500">*</span>
              </label>
              <UInput v-model="emailSettings.smtpHost" placeholder="smtp.gmail.com" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                SMTP Port <span class="text-red-500">*</span>
              </label>
              <UInput v-model.number="emailSettings.smtpPort" type="number" placeholder="587" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                SMTP Username <span class="text-red-500">*</span>
              </label>
              <UInput v-model="emailSettings.smtpUsername" placeholder="alumni@edu.com" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                SMTP Password <span class="text-red-500">*</span>
              </label>
              <UInput v-model="emailSettings.smtpPassword" type="password" placeholder="********" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                ការអ៊ិនគ្រីប
              </label>
              <USelect
                v-model="emailSettings.encryption"
                :options="[
                  { label: 'TLS', value: 'tls' },
                  { label: 'SSL', value: 'ssl' },
                  { label: 'None', value: 'none' },
                ]"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                អ៊ីមែលផ្ញើចេញ <span class="text-red-500">*</span>
              </label>
              <UInput v-model="emailSettings.fromEmail" placeholder="no-reply@alumni.edu" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                ឈ្មោះអ្នកផ្ញើ <span class="text-red-500">*</span>
              </label>
              <UInput v-model="emailSettings.fromName" placeholder="Alumni System" />
            </div>
            <div class="md:col-span-2 space-y-3">
              <label class="flex items-center gap-3 cursor-pointer">
                <input type="checkbox" v-model="emailSettings.sendWelcomeEmail" class="w-4 h-4 text-primary-600 rounded border-gray-300 focus:ring-primary-500" />
                <span class="text-sm text-gray-700">ផ្ញើអ៊ីមែលស្វាគមន៍ដល់អ្នកប្រើប្រាស់ថ្មី</span>
              </label>
              <label class="flex items-center gap-3 cursor-pointer">
                <input type="checkbox" v-model="emailSettings.sendNewsletter" class="w-4 h-4 text-primary-600 rounded border-gray-300 focus:ring-primary-500" />
                <span class="text-sm text-gray-700">ផ្ញើព្រឹត្តិបត្រប្រចាំខែ</span>
              </label>
            </div>
          </div>
        </div>

        <!-- Notification Settings -->
        <div v-if="activeTab === 'notification'">
          <h2 class="text-xl font-semibold text-gray-900 mb-6">ការកំណត់ការជូនដំណឹង</h2>
          <div class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                <div class="flex items-center justify-between">
                  <div>
                    <h3 class="font-medium text-gray-900">ការជូនដំណឹងតាមអ៊ីមែល</h3>
                    <p class="text-sm text-gray-500">ទទួលការជូនដំណឹងតាមអ៊ីមែល</p>
                  </div>
                  <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" v-model="notificationSettings.emailNotifications" class="sr-only peer" />
                    <div class="w-11 h-6 bg-gray-200 peer-focus:ring-2 peer-focus:ring-primary-300 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary-600"></div>
                  </label>
                </div>
              </div>
              <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                <div class="flex items-center justify-between">
                  <div>
                    <h3 class="font-medium text-gray-900">ការជូនដំណឹង Push</h3>
                    <p class="text-sm text-gray-500">ទទួលការជូនដំណឹង Push នៅលើកម្មវិធី</p>
                  </div>
                  <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" v-model="notificationSettings.pushNotifications" class="sr-only peer" />
                    <div class="w-11 h-6 bg-gray-200 peer-focus:ring-2 peer-focus:ring-primary-300 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary-600"></div>
                  </label>
                </div>
              </div>
              <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                <div class="flex items-center justify-between">
                  <div>
                    <h3 class="font-medium text-gray-900">ការរំលឹកព្រឹត្តិការណ៍</h3>
                    <p class="text-sm text-gray-500">ទទួលការរំលឹកអំពីព្រឹត្តិការណ៍នានា</p>
                  </div>
                  <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" v-model="notificationSettings.eventReminders" class="sr-only peer" />
                    <div class="w-11 h-6 bg-gray-200 peer-focus:ring-2 peer-focus:ring-primary-300 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary-600"></div>
                  </label>
                </div>
              </div>
              <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                <div class="flex items-center justify-between">
                  <div>
                    <h3 class="font-medium text-gray-900">ជូនពរថ្ងៃកំណើត</h3>
                    <p class="text-sm text-gray-500">ផ្ញើជូនពរថ្ងៃកំណើតដល់សមាជិក</p>
                  </div>
                  <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" v-model="notificationSettings.birthdayWishes" class="sr-only peer" />
                    <div class="w-11 h-6 bg-gray-200 peer-focus:ring-2 peer-focus:ring-primary-300 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary-600"></div>
                  </label>
                </div>
              </div>
              <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                <div class="flex items-center justify-between">
                  <div>
                    <h3 class="font-medium text-gray-900">ព្រឹត្តិបត្រប្រចាំខែ</h3>
                    <p class="text-sm text-gray-500">ផ្ញើព្រឹត្តិបត្រប្រចាំខែដល់សមាជិក</p>
                  </div>
                  <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" v-model="notificationSettings.monthlyNewsletter" class="sr-only peer" />
                    <div class="w-11 h-6 bg-gray-200 peer-focus:ring-2 peer-focus:ring-primary-300 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary-600"></div>
                  </label>
                </div>
              </div>
              <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                <div class="flex items-center justify-between">
                  <div>
                    <h3 class="font-medium text-gray-900">ការជូនដំណឹងដល់អ្នកគ្រប់គ្រង</h3>
                    <p class="text-sm text-gray-500">ទទួលការជូនដំណឹងអំពីសកម្មភាពសំខាន់ៗ</p>
                  </div>
                  <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" v-model="notificationSettings.adminAlerts" class="sr-only peer" />
                    <div class="w-11 h-6 bg-gray-200 peer-focus:ring-2 peer-focus:ring-primary-300 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary-600"></div>
                  </label>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- User Management Settings -->
        <div v-if="activeTab === 'user'">
          <h2 class="text-xl font-semibold text-gray-900 mb-6">ការកំណត់អ្នកប្រើប្រាស់</h2>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                តួនាទីលំនាំដើម
              </label>
              <USelect
                v-model="userSettings.defaultRole"
                :options="[
                  { label: 'សមាជិក', value: 'member' },
                  { label: 'អ្នកគ្រប់គ្រង', value: 'admin' },
                  { label: 'អ្នកកែសម្រួល', value: 'editor' },
                ]"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                រយៈពេលផុតកំណត់សម័យ (នាទី)
              </label>
              <UInput v-model.number="userSettings.sessionTimeout" type="number" min="5" max="480" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                ប្រវែងពាក្យសម្ងាត់អប្បបរមា
              </label>
              <UInput v-model.number="userSettings.minPasswordLength" type="number" min="6" max="20" />
            </div>
            <div class="md:col-span-2 space-y-3">
              <label class="flex items-center gap-3 cursor-pointer">
                <input type="checkbox" v-model="userSettings.allowRegistration" class="w-4 h-4 text-primary-600 rounded border-gray-300 focus:ring-primary-500" />
                <span class="text-sm text-gray-700">អនុញ្ញាតឱ្យចុះឈ្មោះអ្នកប្រើប្រាស់ថ្មី</span>
              </label>
              <label class="flex items-center gap-3 cursor-pointer">
                <input type="checkbox" v-model="userSettings.emailVerification" class="w-4 h-4 text-primary-600 rounded border-gray-300 focus:ring-primary-500" />
                <span class="text-sm text-gray-700">តម្រូវឱ្យផ្ទៀងផ្ទាត់អ៊ីមែល</span>
              </label>
              <label class="flex items-center gap-3 cursor-pointer">
                <input type="checkbox" v-model="userSettings.allowSocialLogin" class="w-4 h-4 text-primary-600 rounded border-gray-300 focus:ring-primary-500" />
                <span class="text-sm text-gray-700">អនុញ្ញាតឱ្យចូលប្រើតាមបណ្តាញសង្គម</span>
              </label>
              <label class="flex items-center gap-3 cursor-pointer">
                <input type="checkbox" v-model="userSettings.requireStrongPassword" class="w-4 h-4 text-primary-600 rounded border-gray-300 focus:ring-primary-500" />
                <span class="text-sm text-gray-700">តម្រូវឱ្យមានពាក្យសម្ងាត់រឹងមាំ</span>
              </label>
            </div>
          </div>
        </div>

        <!-- Security Settings -->
        <div v-if="activeTab === 'security'">
          <h2 class="text-xl font-semibold text-gray-900 mb-6">ការកំណត់សន្តិសុខ</h2>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                ការព្យាយាមចូលប្រើអតិបរមា
              </label>
              <UInput v-model.number="securitySettings.maxLoginAttempts" type="number" min="3" max="10" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                រយៈពេលផុតកំណត់ពាក្យសម្ងាត់ (ថ្ងៃ)
              </label>
              <UInput v-model.number="securitySettings.passwordExpiry" type="number" min="30" max="365" />
            </div>
            <div class="md:col-span-2 space-y-3">
              <label class="flex items-center gap-3 cursor-pointer">
                <input type="checkbox" v-model="securitySettings.twoFactorAuth" class="w-4 h-4 text-primary-600 rounded border-gray-300 focus:ring-primary-500" />
                <span class="text-sm text-gray-700">បើកការផ្ទៀងផ្ទាត់ពីរជាន់ (2FA)</span>
              </label>
              <label class="flex items-center gap-3 cursor-pointer">
                <input type="checkbox" v-model="securitySettings.recaptchaEnabled" class="w-4 h-4 text-primary-600 rounded border-gray-300 focus:ring-primary-500" />
                <span class="text-sm text-gray-700">បើក ReCAPTCHA សម្រាប់ទម្រង់</span>
              </label>
              <label class="flex items-center gap-3 cursor-pointer">
                <input type="checkbox" v-model="securitySettings.ipWhitelisting" class="w-4 h-4 text-primary-600 rounded border-gray-300 focus:ring-primary-500" />
                <span class="text-sm text-gray-700">បើកការអនុញ្ញាត IP (Whitelisting)</span>
              </label>
              <label class="flex items-center gap-3 cursor-pointer">
                <input type="checkbox" v-model="securitySettings.rateLimit" class="w-4 h-4 text-primary-600 rounded border-gray-300 focus:ring-primary-500" />
                <span class="text-sm text-gray-700">កំណត់ចំនួនសំណើ (Rate Limiting)</span>
              </label>
            </div>
          </div>
        </div>

        <!-- Social Media Settings -->
        <div v-if="activeTab === 'social'">
          <h2 class="text-xl font-semibold text-gray-900 mb-6">តំណភ្ជាប់បណ្តាញសង្គម</h2>
          <div class="grid grid-cols-1 gap-6">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                Facebook
              </label>
              <UInput v-model="socialSettings.facebook" placeholder="https://facebook.com/alumni" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                Twitter / X
              </label>
              <UInput v-model="socialSettings.twitter" placeholder="https://twitter.com/alumni" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                LinkedIn
              </label>
              <UInput v-model="socialSettings.linkedin" placeholder="https://linkedin.com/company/alumni" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                Instagram
              </label>
              <UInput v-model="socialSettings.instagram" placeholder="https://instagram.com/alumni" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                YouTube
              </label>
              <UInput v-model="socialSettings.youtube" placeholder="https://youtube.com/alumni" />
            </div>
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="mt-8 pt-6 border-t border-gray-200 flex flex-col sm:flex-row justify-end gap-3">
          <UButton
            color="gray"
            variant="outline"
            @click="resetSettings"
            class="w-full sm:w-auto"
          >
            កំណត់ឡើងវិញ
          </UButton>
          <UButton
            color="primary"
            :loading="saving"
            @click="saveSettings"
            class="w-full sm:w-auto"
          >
            <UIcon name="i-heroicons-check" class="mr-2" />
            រក្សាទុកការកំណត់
          </UButton>
        </div>
      </div>
    </div>
  </div>
</template>