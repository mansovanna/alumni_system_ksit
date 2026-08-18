<script setup lang="ts">
definePageMeta({
  layout: "default",
});
</script>

<template>
  <div class="max-w-6xl mx-auto font-sans text-slate-800">
    <!-- Title & Subtitle -->
    <!-- Header -->
    <div class="w-full flex justify-between items-center mb-6">
      <div>
        <h1 class="text-2xl font-semibold text-slate-900">
          Event Details
        </h1>
        <p class="text-slate-400 text-sm mt-0.5">
          Keep your alumni network informed about your professional journey.
        </p>
      </div>

      <button
        @click="$router.back"
        type="button"
        class="p-1.5 px-4 flex justify-center items-center gap-1.5 bg-slate-900 text-white hover:bg-slate-800 rounded-lg active:bg-slate-700 transition cursor-pointer shadow-xs"
        aria-label="Go back"
      >
        <!-- ArrowLeft Icon SVG -->
        <svg
          class="w-4 h-4"
          fill="none"
          stroke="currentColor"
          stroke-width="2"
          stroke-linecap="round"
          stroke-linejoin="round"
          viewBox="0 0 24 24"
        >
          <path d="m12 19-7-7 7-7" />
          <path d="M19 12H5" />
        </svg>
        <span class="text-sm font-medium">Back</span>
      </button>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
      <!-- ផ្នែកខាងឆ្វេង (រូបភាព + About Section) -->
      <div class="lg:col-span-8 space-y-6">
        <!-- រូបភាព Main Banner -->
        <div
          class="rounded-2xl overflow-hidden shadow-xs border border-slate-100"
        >
          <img
            src="https://images.unsplash.com/photo-1511578314322-379afb476865?auto=format&fit=crop&w=1200&q=80"
            alt="Tech Alumni Mixer 2024"
            class="w-full h-[360px] object-cover"
          />
        </div>

        <!-- ប្លុក About the Event -->
        <div
          class="bg-white rounded-2xl p-8 border border-slate-100 shadow-xs space-y-5"
        >
          <h2 class="text-2xl font-bold text-slate-900 tracking-tight">
            About the Event
          </h2>

          <div
            class="space-y-4 text-[15px] text-slate-600 leading-relaxed font-normal"
          >
            <p>
              Join us for the annual Tech Alumni Mixer! Reconnect with fellow
              graduates who are making waves in the technology sector.
            </p>
            <p>
              This event is a fantastic opportunity to expand your network,
              share industry insights, and foster new collaborations in a
              relaxed, professional environment.
            </p>
            <p>
              Highlights include a keynote address by distinguished alumni,
              interactive panel discussions on emerging tech trends, and ample
              time for open networking with hors d'oeuvres and refreshments.
            </p>
          </div>
        </div>
      </div>

      <!-- ផ្នែកខាងស្ដាំ (Sidebar Card / Event Details) -->
      <div
        class="lg:col-span-4 bg-white rounded-2xl p-7 border border-slate-100 shadow-xs space-y-6"
      >
        <!-- Header នៃ Card -->
        <div class="space-y-2">
          <h1
            class="text-2xl font-bold text-slate-900 tracking-tight leading-snug"
          >
            Tech Alumni Mixer 2024
          </h1>
          <p class="text-sm text-slate-500 leading-normal">
            A premier networking event for tech professionals.
          </p>
        </div>

        <hr class="border-slate-100" />

        <!-- ព័ត៌មានលម្អិត (Date, Time, Location) -->
        <div class="space-y-5">
          <!-- Date -->
          <div class="flex items-start space-x-3.5">
            <div
              class="p-2 rounded-lg bg-teal-50 text-teal-700 shrink-0 mt-0.5"
            >
              <svg
                class="w-5 h-5"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
                stroke-width="2"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                />
              </svg>
            </div>
            <div class="space-y-0.5">
              <span class="text-xs font-medium text-slate-400 block">Date</span>
              <span class="text-sm font-semibold text-slate-800 block"
                >Thursday, October 24, 2024</span
              >
            </div>
          </div>

          <!-- Time -->
          <div class="flex items-start space-x-3.5">
            <div
              class="p-2 rounded-lg bg-teal-50 text-teal-700 shrink-0 mt-0.5"
            >
              <svg
                class="w-5 h-5"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
                stroke-width="2"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                />
              </svg>
            </div>
            <div class="space-y-0.5">
              <span class="text-xs font-medium text-slate-400 block">Time</span>
              <span class="text-sm font-semibold text-slate-800 block"
                >6:00 PM - 9:00 PM PST</span
              >
            </div>
          </div>

          <!-- Location -->
          <div class="flex items-start space-x-3.5">
            <div
              class="p-2 rounded-lg bg-teal-50 text-teal-700 shrink-0 mt-0.5"
            >
              <svg
                class="w-5 h-5"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
                stroke-width="2"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"
                />
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"
                />
              </svg>
            </div>
            <div class="space-y-0.5">
              <span class="text-xs font-medium text-slate-400 block"
                >Location</span
              >
              <span class="text-sm font-semibold text-slate-800 block"
                >Innovation Hub, San Francisco, CA</span
              >
            </div>
          </div>
        </div>

        <!-- ប៊ូតុង Register -->
        <button
          type="button"
          class="w-full bg-[#009688] hover:bg-[#00897b] active:bg-[#00796b] text-white font-medium py-3 px-4 rounded-xl transition duration-150 shadow-xs text-center text-sm cursor-pointer"
        >
          Register Now
        </button>

        <!-- Organizer -->
        <div class="flex items-center space-x-3 pt-1">
          <div class="p-2 rounded-xl bg-teal-50 text-teal-700 shrink-0">
            <svg
              class="w-5 h-5"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
              stroke-width="2"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h4m-4 0V11m0 0h4m-4 0H9m4 0V7m0 0h4m-4 0H9"
              />
            </svg>
          </div>
          <div class="space-y-0.5">
            <span class="text-xs text-slate-400 font-normal block"
              >Organized by</span
            >
            <span class="text-sm font-bold text-slate-900 block"
              >Alumni Relations Dept</span
            >
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
